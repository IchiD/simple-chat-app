import { useApi } from "./useApi";
import { useAuthStore } from "~/stores/auth";
import { useToast } from "~/composables/useToast";
import { encryptToken } from "~/utils/security";

interface TokenResponse {
  access_token: string;
  token_type?: string;
  expires_in?: number;
  [key: string]: unknown;
}

// バックエンドは { message: 'valid' } 形式を返す
interface VerifyResponse {
  message: string;
  [key: string]: unknown;
}

export const useExternalAuth = () => {
  const { api } = useApi();
  const toast = useToast();
  const authStore = useAuthStore();

  const requestToken = async (clientId: string, clientSecret: string) => {
    try {
      const response = await api<TokenResponse>("/auth/external/token", {
        method: "POST",
        body: { client_id: clientId, client_secret: clientSecret },
        skipAuthRedirect: true,
      });

      const jwt = response.access_token;
      if (jwt) {
        authStore.token = jwt;
        authStore.isAuthenticated = true;
        if (import.meta.client) {
          const encrypted = encryptToken(jwt);
          sessionStorage.setItem("auth_token", encrypted);
        }
      }

      return { success: true, token: jwt };
    } catch (error: unknown) {
      let message = "外部認証に失敗しました";
      if (
        typeof error === "object" &&
        error !== null &&
        "message" in error &&
        typeof (error as any).message === "string"
      ) {
        message = (error as any).message;
      }

      toast.add({
        title: "エラー",
        description: message,
        color: "error",
      });
      return { success: false, message };
    }
  };

  const verifyToken = async (token: string) => {
    try {
      const response = await api<VerifyResponse>("/auth/external/verify", {
        method: "POST",
        headers: { Authorization: `Bearer ${token}` },
        skipAuthRedirect: true,
      });
      return response;
    } catch (error) {
      throw error;
    }
  };

  return {
    requestToken,
    verifyToken,
  };
};
