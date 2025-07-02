declare module "vue-router" {
  import type { RouteLocationNormalized } from "vue-router";

  export const useRouter: () => {
    push: (to: string) => Promise<void>;
    currentRoute: {
      value: RouteLocationNormalized;
    };
  };

  export const useRoute: () => RouteLocationNormalized;

  export interface RouteLocationNormalized {
    path: string;
    fullPath: string;
    name: string | null | undefined;
    params: Record<string, string>;
    query: Record<string, string>;
    hash: string;
    matched: any[];
    meta: Record<string, any>;
  }

  export interface Router {
    push: (to: string) => Promise<void>;
    currentRoute: {
      value: RouteLocationNormalized;
    };
  }
}
