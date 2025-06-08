export default defineNuxtRouteMiddleware((_to) => {
  const authStore = useAuthStore();

  // SSRの場合は認証チェックをスキップ
  if (import.meta.server) {
    return;
  }

  // 認証されていない場合は先にauth.global.tsで処理される
  if (!authStore.isAuthenticated) {
    return;
  }

  // ユーザー情報が読み込まれていない場合は待機
  if (!authStore.user) {
    return;
  }

  // フリープランユーザーはpricingページにリダイレクト
  if (!authStore.user.plan || authStore.user.plan === "free") {
    return navigateTo("/pricing");
  }
});
