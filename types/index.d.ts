declare module "#app" {
  export * from "@nuxt/schema";
  export {
    useRouter,
    useRoute,
    navigateTo,
    defineNuxtRouteMiddleware,
  } from "nuxt/app";
  export { useFetch, useRuntimeConfig } from "nuxt/app";
}

declare module "~/stores/auth" {
  export const useAuthStore: () => {
    user: any;
    token: string | null;
    isAuthenticated: boolean;
    login: (email: string, password: string) => Promise<void>;
    logout: () => Promise<void>;
    checkAuth: () => Promise<boolean>;
    getStoredToken: () => string | null;
    clearAuth: () => void;
  };
}

declare module "~/composables/useToast" {
  export const useToast: () => {
    add: (toast: { title: string; description: string; color: string }) => void;
  };
}

declare module "~/composables/useErrorHandler" {
  export const useErrorHandler: () => {
    handleApiError: (
      error: unknown,
      defaultMessage?: string,
      showToast?: boolean
    ) => string;
    handleAuthError: () => void;
  };
}

// Nuxt特有の型を拡張
declare module "nuxt/schema" {
  interface RuntimeConfig {
    public: {
      apiBase: string;
    };
  }
}
