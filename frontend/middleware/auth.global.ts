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
  const toast = useToast();

  // 認証免除パス - 認証不要のパスリスト
  const exemptPaths = [
    "/", // トップページ
    "/auth/login", // ログインページ
    "/auth/register", // 登録ページ
    "/auth/verification", // メール確認ページ
    "/auth/verify", // メール認証リンク（本登録）
    "/auth/verify-email", // メール認証ページ
    "/auth/forgot-password", // パスワードリセットページ
    "/auth/reset-password", // パスワードリセットページ
    "/auth/verify-email-change", // メール変更認証ページ
    "/auth/google/callback", // Google認証コールバックページ
  ];

  // 任意のパスが免除パスのプレフィックスで始まるかチェック
  const isExemptPath = (path: string) => {
    return exemptPaths.some(
      (exemptPath) => path === exemptPath || path.startsWith(`${exemptPath}/`)
    );
  };

  // 認証不要のパスはチェックをスキップ
  if (isExemptPath(to.path)) {
    console.log("認証チェック免除パスのためスキップ:", to.path);

    // ログイン状態で認証ページにアクセスしている場合はリダイレクト
    if (
      to.path.startsWith("/auth/") &&
      to.path !== "/auth/verify-email-change"
    ) {
      await authStore.checkAuth();

      if (authStore.isAuthenticated) {
        console.log(
          "認証済みユーザーの認証ページアクセスを/userにリダイレクト:",
          to.path
        );
        return navigateTo("/user");
      }
    }

    return;
  }

  // 認証状態をチェック（ページの保護）
  console.log("ミドルウェア認証チェック開始:", {
    isAuthenticated: authStore.isAuthenticated,
  });

  if (!authStore.isAuthenticated) {
    console.log("認証がまだ済んでいないため、checkAuth()を実行");
    await authStore.checkAuth();
    console.log("checkAuth()完了:", {
      isAuthenticated: authStore.isAuthenticated,
    });

    if (!authStore.isAuthenticated) {
      // 認証されていない場合はエラーメッセージを表示してログインページにリダイレクト
      console.log("未認証のため、ログインページにリダイレクト");

      if (import.meta.client) {
        toast.add({
          title: "認証エラー",
          description: "ログインが必要です。ログインページに移動します。",
          color: "error",
        });
      }

      return navigateTo("/auth/login");
    }
  }

  console.log("認証済み - 通常のナビゲーションを続行");
});
