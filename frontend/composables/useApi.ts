import { FetchError } from "ofetch";
import {
  decryptToken,
  generateBrowserFingerprint,
  sanitizeInput,
} from "~/utils/security";

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
  console.log("Runtime config apiBase:", config.public.apiBase);

  // 認証トークンをヘッダーに追加する関数
  const getAuthHeader = (): Record<string, string> => {
    // クライアントサイドでのみlocalStorageにアクセス
    if (import.meta.client) {
      // 暗号化されたトークンを取得して復号
      const encryptedToken = localStorage.getItem("auth_token");
      const token = encryptedToken ? decryptToken(encryptedToken) : null;

      // ブラウザフィンガープリントを生成（追加のセキュリティ対策）
      const fingerprint = generateBrowserFingerprint();

      console.log("APIリクエスト用の認証トークン取得:", {
        token: token ? `${token.substring(0, 10)}...` : null,
        isClient: import.meta.client,
        fingerprint: fingerprint ? "[生成済み]" : null,
      });

      if (token) {
        // トークンが存在する場合のみ認証ヘッダーを返す
        return {
          Authorization: `Bearer ${token}`,
          "X-Client-Fingerprint": fingerprint,
          "X-Client-Info": navigator.userAgent, // トークン窃取時の検証用
        };
      }
    }
    console.log("認証ヘッダーなしでリクエストを実行します");
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

  // 認証エラー時のハンドリング
  const handleAuthError = () => {
    if (import.meta.client) {
      // ローカルストレージの認証情報をクリア
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");

      // エラーメッセージを表示
      toast.add({
        title: "認証エラー",
        description: "ログインが必要です。ログインページに移動します。",
        color: "error",
      });

      // ログインページへリダイレクト
      router.push("/auth/login");
    }
  };

  // APIクライアントの作成
  const api = async <T = unknown>(
    endpoint: string,
    options: ApiOptions = {}
  ) => {
    const baseUrl = config.public.apiBase || "http://localhost:8000/api";
    console.log("Effective baseUrl:", baseUrl);

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
      console.log(`APIリクエスト: ${options.method || "GET"} ${url}`, {
        hasAuthHeader: !!authHeaders.Authorization,
        hasBody: !!body,
      });

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
        // エラーの詳細情報を追加
        const enhancedError = error as FetchError<unknown>;

        console.error(`API Error (${url}):`, error.message, error.data);

        // 認証エラー（401）の場合、ログインページにリダイレクト
        if (error.status === 401 && !options.skipAuthRedirect) {
          console.log("認証エラーを検出: ログインページへリダイレクトします");
          handleAuthError();
        }

        throw enhancedError;
      }

      // その他のエラー
      console.error(`Unexpected API Error (${url}):`, error);
      throw error;
    }
  };

  return { api };
}
