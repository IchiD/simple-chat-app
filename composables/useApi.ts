import { useFetch, useRuntimeConfig } from "#app";
import { useAuthStore } from "~/stores/auth";

interface ApiOptions {
  method?: string;
  body?: any;
  params?: Record<string, string>;
  headers?: Record<string, string>;
}

export const useApi = () => {
  const config = useRuntimeConfig();
  const baseURL = config.public.apiBase || "http://localhost:8000/api";
  const authStore = useAuthStore();
  console.log("Current API baseURL:", baseURL);

  // API呼び出し用の関数
  const api = async <T>(
    endpoint: string,
    options: ApiOptions = {}
  ): Promise<T> => {
    console.log(
      "[useApi] Calling endpoint:",
      endpoint,
      "with options:",
      options
    );
    const { method = "GET", body, params, headers = {} } = options;

    // URLの先頭の / を削除し、baseURLと結合
    const url = `${baseURL}/${endpoint.replace(/^\//, "")}`;

    // HTTPヘッダーを設定
    const fetchHeaders: Record<string, string> = {
      "Content-Type": "application/json",
      Accept: "application/json",
      ...headers,
    };

    // 認証トークンがあれば追加 (復号化されたトークンを使用)
    const decryptedToken = authStore.token || authStore.getStoredToken();

    if (decryptedToken) {
      fetchHeaders["Authorization"] = `Bearer ${decryptedToken}`;
    }
    console.log("[useApi] fetchHeaders:", fetchHeaders);

    // API呼び出し実行
    const { data, error } = await useFetch<T>(url, {
      method,
      headers: fetchHeaders,
      body: body ? JSON.stringify(body) : undefined,
      params,
    });

    // エラーハンドリング
    if (error.value) {
      console.error(`API Error (${url}):`, error.value);
      throw error.value;
    }

    return data.value as T;
  };

  return { api };
};
