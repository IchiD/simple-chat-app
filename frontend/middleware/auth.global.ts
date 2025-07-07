export default defineNuxtRouteMiddleware(async (to, _from) => {
  // SSRの場合またはルートのリロード時は認証チェックをスキップ
  if (import.meta.server || to.matched.length === 0) {
    return;
  }

  console.log("[auth.global] アクセス先パス:", to.path);

  const authStore = useAuthStore();
  const toast = useToast();

  // 認証免除パス - 認証不要のパスリスト
  const exemptPaths = [
    "/", // トップページ
    "/auth/login", // ログインページ
    "/auth/register", // 登録ページ
    "/auth/register-and-join", // グループ参加用登録ページ
    "/auth/verification", // メール確認ページ
    "/auth/verify", // メール認証リンク（本登録）
    "/auth/verify-email", // メール認証ページ
    "/auth/forgot-password", // パスワードリセットページ
    "/auth/reset-password", // パスワードリセットページ
    "/auth/verify-email-change", // メール変更認証ページ
    "/auth/google/callback", // Google認証コールバックページ
    "/join", // QRコード参加ページ
    "/legal", // 法的情報ページ（特定商取引法、プライバシーポリシー等）
    // "/guest", // ゲストユーザー専用ページ（廃止）
  ];

  // 任意のパスが免除パスのプレフィックスで始まるかチェック
  const isExemptPath = (path: string) => {
    return exemptPaths.some(
      (exemptPath) => path === exemptPath || path.startsWith(`${exemptPath}/`)
    );
  };

  const isExempt = isExemptPath(to.path);
  console.log("[auth.global] 認証免除対象:", isExempt, "パス:", to.path);

  // 認証不要のパスはチェックをスキップ
  if (isExempt) {
    console.log("[auth.global] 認証チェックをスキップ");
    // ログイン状態で認証ページにアクセスしている場合はリダイレクト
    if (
      to.path.startsWith("/auth/") &&
      to.path !== "/auth/verify-email-change" &&
      !to.path.startsWith("/auth/register-and-join/")
    ) {
      await authStore.checkAuth();

      if (authStore.isAuthenticated) {
        return navigateTo("/user");
      }
    }

    return;
  }

  console.log("[auth.global] 認証チェック開始");

  // 認証状態をチェック（ページの保護）
  if (!authStore.isAuthenticated) {
    await authStore.checkAuth();

    if (!authStore.isAuthenticated) {
      console.log("[auth.global] 未認証のためリダイレクト");
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
