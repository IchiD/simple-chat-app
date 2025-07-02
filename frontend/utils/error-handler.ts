/**
 * アプリケーション全体で一貫したエラー処理を提供するユーティリティ
 */

import { FetchError } from "ofetch";
import type { Router } from "vue-router";
import type { Toast } from "~/composables/useToast";

// useToast composableが返すオブジェクトの型を定義
interface ToastService {
  add: (toast: Omit<Toast, "id">) => number;
}

/**
 * エラーメッセージを抽出するユーティリティ関数
 * APIレスポンスやJavaScriptエラーから適切なメッセージを取得します
 *
 * @param error 処理するエラーオブジェクト
 * @param defaultMessage エラーメッセージが取得できない場合のデフォルトメッセージ
 * @returns エラーメッセージ
 */
export function extractErrorMessage(
  error: unknown,
  defaultMessage: string
): string {
  // FetchErrorの場合、APIからのエラーデータを確認
  if (error instanceof FetchError && error.data) {
    // APIが返すエラーの構造に応じた処理
    if (
      typeof error.data === "object" &&
      error.data !== null &&
      "message" in error.data
    ) {
      return error.data.message as string;
    }
  }

  // 標準のErrorオブジェクトの場合
  if (error instanceof Error) {
    return error.message;
  }

  // エラーオブジェクトの構造を確認（データプロパティを持つオブジェクト）
  if (
    typeof error === "object" &&
    error !== null &&
    "data" in error &&
    typeof error.data === "object" &&
    error.data !== null &&
    "message" in error.data
  ) {
    return error.data.message as string;
  }

  // その他のケースではデフォルトメッセージを返す
  return defaultMessage;
}

/**
 * 認証エラーを処理するユーティリティ関数
 * 401エラー時にユーザーをログインページにリダイレクトします
 *
 * @param router Nuxtルーターインスタンス
 * @param toast トーストコンポーネントインスタンス
 */
export function handleAuthError(router: Router, toast: ToastService): void {
  if (import.meta.client) {
    // セッションストレージの認証情報をクリア
    sessionStorage.removeItem("auth_token");
    sessionStorage.removeItem("user");

    // エラーメッセージを表示
    toast.add({
      title: "認証エラー",
      description: "ログインが必要です。ログインページに移動します。",
      color: "error",
    });

    // ログインページへリダイレクト
    router.push("/auth/login");
  }
}

/**
 * APIエラーを処理し、適切なトーストメッセージを表示する
 *
 * @param error 発生したエラー
 * @param toast トーストコンポーネントインスタンス
 * @param router Nuxtルーターインスタンス
 * @param defaultMessage デフォルトのエラーメッセージ
 * @param title エラータイトル（デフォルト: "エラー"）
 * @param handleAuth 認証エラーを自動的に処理するかどうか（デフォルト: true）
 */
export function handleApiError(
  error: unknown,
  toast: ToastService,
  router: Router,
  defaultMessage: string,
  title: string = "エラー",
  handleAuth: boolean = true
): void {
  console.error("API error:", error);

  // 401エラーの場合、認証エラー処理
  if (handleAuth && error instanceof FetchError && error.status === 401) {
    handleAuthError(router, toast);
    return;
  }

  // エラーメッセージをトーストで表示
  toast.add({
    title,
    description: extractErrorMessage(error, defaultMessage),
    color: "error",
  });
}
