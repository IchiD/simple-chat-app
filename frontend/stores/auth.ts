import { defineStore } from "pinia";
import { encryptToken, decryptToken } from "~/utils/security";

interface User {
  id: number;
  name: string;
  email: string;
  friend_id?: string; // フレンドID追加（オプション）
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

  // ローカルストレージからトークンを取得（復号化）
  const getStoredToken = (): string | null => {
    if (!import.meta.client) return null;

    const encryptedToken = localStorage.getItem("auth_token");
    if (!encryptedToken) return null;

    return decryptToken(encryptedToken);
  };

  // トークンをローカルストレージに保存（暗号化）
  const storeToken = (tokenValue: string): void => {
    if (!import.meta.client || !tokenValue) return;

    const encryptedToken = encryptToken(tokenValue);
    localStorage.setItem("auth_token", encryptedToken);
    console.log("トークンを暗号化して保存しました");
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
      error.value = errorResp.message || "ユーザー登録中にエラーが発生しました";
      return { success: false, message: error.value };
    } finally {
      loading.value = false;
    }
  }

  // メール認証処理
  async function verifyEmail(verifyToken: string) {
    loading.value = true;
    error.value = null;

    try {
      console.log("メール認証処理を開始します:", verifyToken);
      const { api } = useApi();
      const response = await api<AuthResponse>("/verify", {
        method: "GET",
        params: { token: verifyToken },
      });

      console.log("メール認証レスポンス:", response);

      // バックエンドから返されるのはaccess_tokenフィールド
      token.value = response.access_token || null;
      user.value = (response.user as User) || {
        id: 0, // ユーザーIDはまだ不明
        name: "", // 名前はまだ不明
        email: (response.email as string) || "", // メールアドレスだけ設定
      };
      isAuthenticated.value = true;

      console.log("認証トークンをlocalStorageに保存します:", token.value);

      // トークンをlocalStorageに暗号化して保存
      if (token.value && import.meta.client) {
        console.log("トークン保存処理を実行します");
        storeToken(token.value);
        console.log("トークン保存後のlocalStorage確認: [暗号化済み]");
      } else {
        console.log("トークン保存処理をスキップします:", {
          hasToken: !!token.value,
          isClient: import.meta.client,
        });
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

      // トークンをlocalStorageに暗号化して保存
      if (token.value) {
        storeToken(token.value);
        console.log("ログイン成功: トークンを保存しました");
      }

      return { success: true };
    } catch (err: unknown) {
      const errorResp = err as ErrorResponse;
      console.error("Login error:", errorResp);
      error.value = errorResp.message || "ログイン中にエラーが発生しました";
      return { success: false, message: error.value };
    } finally {
      loading.value = false;
    }
  }

  // 認証状態のチェック
  async function checkAuth() {
    // クライアントサイドでのみlocalStorageにアクセス
    console.log("認証状態チェック開始:", { isClient: import.meta.client });

    if (!import.meta.client) {
      console.log("サーバーサイドでの認証チェックをスキップします");
      return;
    }

    try {
      // 暗号化されたトークンを取得・復号
      const savedToken = getStoredToken();
      console.log(
        "保存されたトークン:",
        savedToken ? `${savedToken.substring(0, 10)}...` : null
      );

      if (savedToken) {
        token.value = savedToken;
        console.log(
          "token.value設定:",
          token.value ? `${token.value.substring(0, 10)}...` : null
        );

        console.log("/users/meエンドポイントにリクエスト開始");
        const { api } = useApi();
        const userData = await api<User>("/users/me");
        console.log("ユーザーデータ取得成功:", userData);

        user.value = userData;
        isAuthenticated.value = true;
        console.log("認証状態を更新:", {
          isAuthenticated: isAuthenticated.value,
        });
      } else {
        console.log("保存されたトークンがありません");
        token.value = null;
        user.value = null;
        isAuthenticated.value = false;
      }
    } catch (error) {
      console.error("/users/meエンドポイントでエラー:", error);
      // トークンが無効な場合はクリア
      token.value = null;
      user.value = null;
      isAuthenticated.value = false;
      localStorage.removeItem("auth_token");
      console.log("認証情報をクリアしました");
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
    localStorage.removeItem("auth_token");
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
  };
});
