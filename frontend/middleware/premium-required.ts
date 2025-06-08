export default defineNuxtRouteMiddleware(async (_to) => {
  const authStore = useAuthStore();

  // SSRの場合は認証チェックをスキップ
  if (import.meta.server) {
    return;
  }

  // 認証されていない場合は先にauth.global.tsで処理される
  if (!authStore.isAuthenticated) {
    return;
  }

  // ユーザー情報が読み込まれていない場合は認証チェックを実行
  if (!authStore.user) {
    await authStore.checkAuth();
  }

  // 認証チェック後もユーザー情報がない場合は認証エラー
  if (!authStore.user) {
    return;
  }

  // フリープランユーザーはpricingページにリダイレクト
  if (!authStore.user.plan || authStore.user.plan === "free") {
    return navigateTo("/pricing");
  }
});
