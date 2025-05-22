import { useAuthStore } from "~/stores/auth";
import { useToast } from "~/composables/useToast";
import { useRouter } from "vue-router";
import { FetchError } from "ofetch";

/**
 * アプリケーション全体で統一したエラー処理を行うためのコンポーザブル
 */
export const useErrorHandler = () => {
  const authStore = useAuthStore();
  const toast = useToast();
  const router = useRouter();

  /**
   * API呼び出しで発生したエラーを処理する
   * @param error 発生したエラー
   * @param defaultMessage デフォルトのエラーメッセージ
   * @param showToast トーストメッセージを表示するかどうか
   * @returns 処理されたエラーメッセージ
   */
  const handleApiError = (
    error: unknown,
    defaultMessage = "エラーが発生しました",
    showToast = true
  ): string => {
    console.error("API呼び出しエラー:", error);

    let errorMessage = defaultMessage;

    // FetchErrorの場合は詳細な情報を抽出
    if (error instanceof FetchError) {
      // 401エラー（認証エラー）の処理
      if (error.status === 401) {
        handleAuthError();
        errorMessage =
          "セッションが切れたか、認証に問題があります。再度ログインしてください。";
      }
      // バックエンドからのエラーメッセージがあれば使用
      else if (error.data && typeof error.data === "object") {
        const errorData = error.data as {
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
    } else if (error instanceof Error) {
      errorMessage = error.message;
    }

    // トースト表示が有効な場合
    if (showToast) {
      toast.add({
        title: "エラー",
        description: errorMessage,
        color: "error",
      });
    }

    return errorMessage;
  };

  /**
   * 認証エラー（401）を処理する
   */
  const handleAuthError = () => {
    // すでにログインページにいる場合は何もしない
    if (router.currentRoute.value.path.includes("/auth/login")) {
      return;
    }

    console.warn(
      "[ErrorHandler] 認証エラーを検出。ログアウト処理を実行します。"
    );

    // 認証情報をクリア
    authStore.clearAuth();

    // ログインページへリダイレクト
    router.push("/auth/login");
  };

  return {
    handleApiError,
    handleAuthError,
  };
};
