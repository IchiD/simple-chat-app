import { FetchError } from "ofetch";
import {
  decryptToken,
  generateBrowserFingerprint,
  sanitizeInput,
} from "~/utils/security";
import { handleAuthError } from "~/utils/error-handler";
import type { Toast } from "~/composables/useToast";

// HTTP メソッドの型定義
type HttpMethod =
  | "GET"
  | "POST"
  | "PUT"
  | "DELETE"
  | "PATCH"
  | "HEAD"
  | "OPTIONS"
  | "get"
  | "post"
  | "put"
  | "delete"
  | "patch"
  | "head"
  | "options";

interface ApiOptions {
  method?: HttpMethod;
  headers?: Record<string, string>;
  body?: Record<string, unknown>;
  params?: Record<string, string>;
  skipAuthRedirect?: boolean; // 認証リダイレクトをスキップするオプション
  [key: string]: unknown;
}

export function useApi() {
  const config = useRuntimeConfig();
  const router = useRouter();
  const toast = useToast();

  // 認証トークンをヘッダーに追加する関数
  const getAuthHeader = (): Record<string, string> => {
    // クライアントサイドでのみsessionStorageにアクセス
    if (import.meta.client) {
      // 暗号化されたトークンを取得して復号
      const encryptedToken = sessionStorage.getItem("auth_token");
      const token = encryptedToken ? decryptToken(encryptedToken) : null;

      // ブラウザフィンガープリントを生成（追加のセキュリティ対策）
      const fingerprint = generateBrowserFingerprint();

      if (token) {
        // トークンが存在する場合のみ認証ヘッダーを返す
        return {
          Authorization: `Bearer ${token}`,
          "X-Client-Fingerprint": fingerprint,
          "X-Client-Info": navigator.userAgent, // トークン窃取時の検証用
        };
      }
    }
    return {};
  };

  // CSRFトークンを取得する関数
  const getCsrfToken = (): string | null => {
    if (!import.meta.client) return null;

    // metaタグからCSRFトークンを取得
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.getAttribute("content") : null;
  };

  // 入力データをサニタイズする関数
  const sanitizeData = (
    data: Record<string, unknown>
  ): Record<string, unknown> => {
    const sanitized: Record<string, unknown> = {};

    Object.entries(data).forEach(([key, value]) => {
      if (typeof value === "string") {
        sanitized[key] = sanitizeInput(value);
      } else {
        sanitized[key] = value;
      }
    });

    return sanitized;
  };

  // APIクライアントの作成
  const api = async <T = unknown>(
    endpoint: string,
    options: ApiOptions = {}
  ) => {
    const baseUrl = config.public.apiBase || "http://localhost/api";

    // URLの組み立て
    let url = `${baseUrl}${
      endpoint.startsWith("/") ? endpoint : `/${endpoint}`
    }`;

    // GETリクエストの場合、パラメータをURLに追加
    if (
      options.params &&
      (options.method === "GET" || options.method === "get" || !options.method)
    ) {
      const queryParams = new URLSearchParams();
      Object.entries(options.params).forEach(([key, value]) => {
        if (value) queryParams.append(key, value);
      });
      const queryString = queryParams.toString();
      if (queryString) {
        url = `${url}?${queryString}`;
      }
    }

    // CSRFトークンを取得
    const csrfToken = getCsrfToken();

    // 認証ヘッダーを取得
    const authHeaders = getAuthHeader();

    // デフォルトのヘッダー
    const headers = {
      "Content-Type": "application/json",
      Accept: "application/json",
      ...authHeaders, // 認証ヘッダーを追加
      ...(csrfToken ? { "X-CSRF-TOKEN": csrfToken } : {}),
      ...(options.headers || {}),
    };

    // リクエストボディをサニタイズ（XSS対策）
    const body = options.body ? sanitizeData(options.body) : undefined;

    try {
      // $fetchを使ってAPIリクエストを送信
      const response = await $fetch<T>(url, {
        ...options,
        headers,
        // JSON本文を自動的に変換
        body: body,
        // GETリクエストではparamsを使わないようにする
        params: undefined,
      });

      return response;
    } catch (error) {
      // エラーの処理と変換
      if (error instanceof FetchError) {
        const enhancedError = error as FetchError<unknown>;
        console.error(`API Error (${url}):`, {
          status: error.status,
          message: error.message,
          data: error.data,
          headers: error.response?.headers,
          url: url,
          options: options,
        });

        // アカウント削除・バンエラーの特別処理
        if (
          error.status === 403 &&
          enhancedError.data &&
          typeof enhancedError.data === "object"
        ) {
          const errorData = enhancedError.data as {
            error_type?: string;
            message?: string;
          };

          if (
            errorData.error_type === "account_deleted" ||
            errorData.error_type === "account_banned"
          ) {
            // セッションストレージからトークンを削除
            if (import.meta.client) {
              sessionStorage.removeItem("auth_token");
            }

            // ユーザーに通知
            const message =
              errorData.error_type === "account_deleted"
                ? "アカウントが削除されました。"
                : "アカウントが利用停止されました。";

            toast.add({
              title: "アカウント状態エラー",
              description: message,
              color: "error",
            } as Omit<Toast, "id">);

            // ログインページにリダイレクト
            if (import.meta.client && !options.skipAuthRedirect) {
              setTimeout(() => {
                router.push("/auth/login");
              }, 1000);
            }

            throw enhancedError;
          }
        }

        if (error.status === 401 && !options.skipAuthRedirect) {
          handleAuthError(router, toast); // 共通の認証エラーハンドリング
          // 認証エラーの場合は、ここで処理を中断させるか、特定のメッセージをthrowすることも検討
          throw enhancedError; // or throw new Error("認証が必要です。ログインしてください。");
        } else if (error.status === 429) {
          toast.add({
            title: "リクエスト上限超過",
            description:
              "メッセージの送信回数が上限に達しました。しばらくしてから再度お試しください。",
            color: "warning",
          } as Omit<Toast, "id">);
          throw enhancedError; // 必要に応じてエラーを再スロー
        } else {
          // その他のFetchError (400, 500系など)
          let errorMessage = "サーバーとの通信に失敗しました。";
          if (enhancedError.data && typeof enhancedError.data === "object") {
            const errorData = enhancedError.data as {
              message?: string;
              errors?: Record<string, string[]>;
            };
            if (errorData.message) {
              errorMessage = errorData.message;
            } else if (errorData.errors) {
              const firstErrorKey = Object.keys(errorData.errors)[0];
              if (firstErrorKey && errorData.errors[firstErrorKey][0]) {
                errorMessage = errorData.errors[firstErrorKey][0];
              }
            }
          }
          toast.add({
            title: "エラー",
            description: errorMessage,
            color: "error",
          } as Omit<Toast, "id">);
          throw enhancedError;
        }
      }

      // FetchError以外の予期せぬエラー
      console.error(`Unexpected API Error (${url}):`, error);
      toast.add({
        title: "予期せぬエラー",
        description:
          "予期せぬエラーが発生しました。時間をおいて再度お試しください。",
        color: "error",
      } as Omit<Toast, "id">);
      throw error;
    }
  };

  return { api };
}
