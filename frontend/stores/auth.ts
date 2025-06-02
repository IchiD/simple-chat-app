import { defineStore } from "pinia";
import { encryptToken, decryptToken } from "~/utils/security";

interface User {
  id: number;
  name: string;
  email: string;
  friend_id?: string; // フレンドID追加（オプション）
  google_id?: string; // Google ID追加（オプション）
  avatar?: string; // プロフィール画像URL追加（オプション）
  social_type?: string; // ソーシャルログインの種類追加（オプション）
}

// 認証状態の型定義
interface _AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  loading: boolean;
  error: string | null;
}

// 認証レスポンスの型定義
interface AuthResponse {
  token?: string;
  access_token?: string;
  token_type?: string;
  email?: string;
  user?: User;
  [key: string]: unknown;
}

// エラーレスポンスの型定義
interface ErrorResponse {
  message?: string;
  errors?: Record<string, string[]>;
  data?: unknown;
  [key: string]: unknown;
}

export const useAuthStore = defineStore("auth", () => {
  const user = ref<User | null>(null);
  const token = ref<string | null>(null);
  const isAuthenticated = ref(false);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // セッションストレージからトークンを取得（復号化）
  const getStoredToken = (): string | null => {
    if (!import.meta.client) return null;

    const encryptedToken = sessionStorage.getItem("auth_token");
    if (!encryptedToken) return null;

    return decryptToken(encryptedToken);
  };

  // トークンをセッションストレージに保存（暗号化）
  const storeToken = (tokenValue: string): void => {
    if (!import.meta.client || !tokenValue) return;

    const encryptedToken = encryptToken(tokenValue);
    sessionStorage.setItem("auth_token", encryptedToken);
  };

  // ユーザー登録処理
  async function register(
    name: string,
    email: string,
    password: string,
    password_confirmation: string
  ) {
    loading.value = true;
    error.value = null;

    try {
      const { api } = useApi();
      // バックエンドが期待するのは name, email, password, password_confirmation
      const response = await api("/register", {
        method: "POST",
        body: { name, email, password, password_confirmation },
      });

      return {
        success: true,
        message:
          "確認メールを送信しました。メールを確認して登録を完了してください。",
        data: response,
      };
    } catch (err: unknown) {
      const errorResp = err as ErrorResponse;
      console.error("Registration error:", errorResp);

      // エラータイプに基づいて適切なメッセージを設定
      let errorMessage = "ユーザー登録中にエラーが発生しました";

      if (errorResp.message) {
        if (
          errorResp.message.includes(
            "利用停止されており、新規登録できません"
          ) ||
          errorResp.message.includes("email_banned")
        ) {
          errorMessage =
            "このメールアドレスは利用停止されており、新規登録できません。別のメールアドレスをお試しください。";
        } else if (
          errorResp.message.includes("既に登録されています") ||
          errorResp.message.includes("already_registered")
        ) {
          errorMessage =
            "このメールアドレスは既に登録されています。ログインページからログインしてください。";
        } else {
          errorMessage = errorResp.message;
        }
      }

      error.value = errorMessage;
      return { success: false, message: errorMessage };
    } finally {
      loading.value = false;
    }
  }

  // メール認証処理
  async function verifyEmail(verifyToken: string) {
    loading.value = true;
    error.value = null;

    try {
      const { api } = useApi();
      const response = await api<AuthResponse>("/verify", {
        method: "GET",
        params: { token: verifyToken },
      });

      // バックエンドから返されるのはaccess_tokenフィールド
      token.value = response.access_token || null;
      user.value = (response.user as User) || {
        id: 0, // ユーザーIDはまだ不明
        name: "", // 名前はまだ不明
        email: (response.email as string) || "", // メールアドレスだけ設定
      };
      isAuthenticated.value = true;

      // トークンをsessionStorageに暗号化して保存
      if (token.value && import.meta.client) {
        storeToken(token.value);
      }

      return {
        success: true,
        message: "メール認証が完了しました。自動的にログインします。",
      };
    } catch (err: unknown) {
      const errorResp = err as ErrorResponse;
      console.error("Email verification error:", errorResp);
      error.value = errorResp.message || "メール認証中にエラーが発生しました";
      return { success: false, message: error.value };
    } finally {
      loading.value = false;
    }
  }

  // ログイン処理
  async function login(email: string, password: string) {
    loading.value = true;
    error.value = null;

    try {
      const { api } = useApi();
      const response = await api<AuthResponse>("/login", {
        method: "POST",
        body: { email, password },
      });

      // ログイン成功した場合、ユーザー情報とトークンを保存
      // バックエンドから返されるのはaccess_tokenまたはtokenフィールド
      token.value = response.access_token || response.token || null;
      user.value = response.user || null;
      isAuthenticated.value = true;

      // トークンをsessionStorageに暗号化して保存
      if (token.value) {
        storeToken(token.value);
      }

      return { success: true };
    } catch (err: unknown) {
      const errorResp = err as ErrorResponse;
      console.error("Login error:", errorResp);

      // エラータイプに基づいて適切なメッセージを設定
      let errorMessage = "ログイン中にエラーが発生しました";

      if (errorResp.message) {
        if (
          errorResp.message.includes("アカウントは削除されています") ||
          errorResp.message.includes("account_deleted")
        ) {
          errorMessage =
            "このアカウントは削除されています。新しいアカウントで登録してください。";
        } else if (
          errorResp.message.includes("利用停止されています") ||
          errorResp.message.includes("account_banned")
        ) {
          errorMessage =
            "このアカウントは利用停止されています。サポートにお問い合わせください。";
        } else if (
          errorResp.message.includes("メール認証がお済みでない") ||
          errorResp.message.includes("not_verified")
        ) {
          errorMessage =
            "メール認証が完了していません。登録時に送信されたメールを確認してください。";
        } else if (
          errorResp.message.includes(
            "メールアドレスまたはパスワードが正しくありません"
          ) ||
          errorResp.message.includes("invalid_credentials")
        ) {
          errorMessage = "メールアドレスまたはパスワードが正しくありません。";
        } else {
          errorMessage = errorResp.message;
        }
      }

      error.value = errorMessage;
      return { success: false, message: errorMessage };
    } finally {
      loading.value = false;
    }
  }

  // 認証状態のチェック
  async function checkAuth() {
    // クライアントサイドでのみsessionStorageにアクセス
    if (!import.meta.client) {
      return;
    }

    try {
      // 暗号化されたトークンを取得・復号
      const savedToken = getStoredToken();

      if (savedToken) {
        token.value = savedToken;

        const { api } = useApi();

        // skipAuthRedirectを使ってリダイレクトを制御
        const userData = await api<User>("/users/me", {
          skipAuthRedirect: true,
        });

        user.value = userData;
        isAuthenticated.value = true;
      } else {
        token.value = null;
        user.value = null;
        isAuthenticated.value = false;
      }
    } catch (error: any) {
      console.error("/users/meエンドポイントでエラー:", error);

      // useApiコンポーザブルで既にアカウント削除・バンエラーは処理されているため、
      // ここでは通常のトークン無効エラーのみを処理
      token.value = null;
      user.value = null;
      isAuthenticated.value = false;
      sessionStorage.removeItem("auth_token");
    }
  }

  // Googleログイン開始
  function startGoogleLogin() {
    if (!import.meta.client) return;

    // Google認証ページにリダイレクト
    window.location.href = "http://localhost/api/auth/google/redirect";
  }

  // Googleコールバック処理
  async function handleGoogleCallback(tokenParam: string, userData: string) {
    loading.value = true;
    error.value = null;

    try {
      // トークンとユーザーデータを設定
      token.value = tokenParam;
      user.value = JSON.parse(decodeURIComponent(userData));
      isAuthenticated.value = true;

      // トークンをsessionStorageに暗号化して保存
      if (tokenParam) {
        storeToken(tokenParam);
      }

      return { success: true };
    } catch (err: unknown) {
      console.error("Google認証コールバック処理エラー:", err);
      error.value = "Google認証処理中にエラーが発生しました";
      return { success: false, message: error.value };
    } finally {
      loading.value = false;
    }
  }

  // ログアウト処理
  async function logout() {
    if (token.value) {
      try {
        const { api } = useApi();
        await api("/logout", {
          method: "POST",
        });
      } catch (err) {
        console.error("Logout error:", err);
      }
    }

    // 認証情報をクリア
    token.value = null;
    user.value = null;
    isAuthenticated.value = false;
    sessionStorage.removeItem("auth_token");
  }

  return {
    user,
    token,
    isAuthenticated,
    loading,
    error,
    register,
    verifyEmail,
    login,
    checkAuth,
    logout,
    getStoredToken,
    startGoogleLogin,
    handleGoogleCallback,
  };
});
