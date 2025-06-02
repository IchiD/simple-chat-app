export default defineNuxtRouteMiddleware(async (to, _from) => {
  // SSRの場合またはルートのリロード時は認証チェックをスキップ
  if (import.meta.server || to.matched.length === 0) {
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
    // ログイン状態で認証ページにアクセスしている場合はリダイレクト
    if (
      to.path.startsWith("/auth/") &&
      to.path !== "/auth/verify-email-change"
    ) {
      await authStore.checkAuth();

      if (authStore.isAuthenticated) {
        return navigateTo("/user");
      }
    }

    return;
  }

  // 認証状態をチェック（ページの保護）
  if (!authStore.isAuthenticated) {
    await authStore.checkAuth();

    if (!authStore.isAuthenticated) {
      // 認証されていない場合はエラーメッセージを表示してログインページにリダイレクト
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
});
