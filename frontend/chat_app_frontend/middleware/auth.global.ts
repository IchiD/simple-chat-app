export default defineNuxtRouteMiddleware(async (to, _from) => {
  console.log("グローバル認証ミドルウェア実行:", {
    to: to.path,
    isAuthPath: to.path.startsWith("/auth/"),
  });

  // SSRの場合またはルートのリロード時は認証チェックをスキップ
  if (import.meta.server || to.matched.length === 0) {
    console.log("SSRまたはリロード時のため認証チェックをスキップ");
    return;
  }

  const authStore = useAuthStore();

  // /auth/ で始まるパス（認証関連ページ）へのアクセスで、かつ認証済みの場合はユーザーページにリダイレクト
  if (to.path.startsWith("/auth/")) {
    await authStore.checkAuth();

    if (authStore.isAuthenticated) {
      console.log(
        "認証済みユーザーの認証ページアクセスを/userにリダイレクト:",
        to.path
      );
      return navigateTo("/user");
    }

    // 認証ページでは認証チェックをスキップ
    console.log("認証ページなのでチェックをスキップ:", to.path);
    return;
  }

  // インデックスページには認証チェックしない
  if (to.path === "/") {
    console.log("認証チェックをスキップ:", to.path);
    return;
  }

  console.log("ミドルウェア認証チェック開始:", {
    isAuthenticated: authStore.isAuthenticated,
  });

  // 認証状態をチェック
  if (!authStore.isAuthenticated) {
    console.log("認証がまだ済んでいないため、checkAuth()を実行");
    await authStore.checkAuth();
    console.log("checkAuth()完了:", {
      isAuthenticated: authStore.isAuthenticated,
    });

    if (!authStore.isAuthenticated) {
      // 認証されていない場合はログインページにリダイレクト
      console.log("未認証のため、ログインページにリダイレクト");
      return navigateTo("/auth/login");
    }
  }

  console.log("認証済み - 通常のナビゲーションを続行");
});
