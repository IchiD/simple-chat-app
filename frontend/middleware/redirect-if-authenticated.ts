import { useAuthStore } from "~/stores/auth";

export default defineNuxtRouteMiddleware(async (to) => {
  // このミドルウェアは `/` (ホームページ) にアクセスした時のみ動作させる
  if (to.path === "/") {
    const authStore = useAuthStore();

    // 認証状態をチェック (checkAuthがストアの初期化を含むことを想定)
    // ストアが既に初期化済みかどうか、またはcheckAuthが複数回呼ばれても問題ない設計であるか確認が必要
    // ここでは、checkAuthを呼ぶことで最新の認証状態を取得・確認できると仮定します。
    await authStore.checkAuth();

    if (authStore.isAuthenticated) {
      // 認証済みであれば /user にリダイレクト
      // Nuxt 3 のミドルウェアでは navigateTo を使用する
      return navigateTo("/user", { replace: true });
    }
  }
});
