import { defineNuxtRouteMiddleware, navigateTo } from "#app";
import { useAuthStore } from "~/stores/auth";

/**
 * グローバル認証ミドルウェア
 * すべてのルートでの認証状態を確認し、保護されたルートへの未認証アクセスをブロック
 */
export default defineNuxtRouteMiddleware((to) => {
  const authStore = useAuthStore();

  // 認証が不要な公開パス一覧
  const publicPaths = [
    "/auth/login",
    "/auth/register",
    "/auth/forgot-password",
    "/auth/reset-password",
    "/auth/verify-email",
    "/auth/verify-email-change",
  ];

  // 現在のパスが公開パスでなく、かつ認証されていない場合
  if (
    !publicPaths.some((path) => to.path.startsWith(path)) &&
    !authStore.isAuthenticated
  ) {
    console.log(
      `[auth.global] 認証が必要なルート ${to.path} に未認証アクセスを検出。ログインページへリダイレクト。`
    );

    // 認証後に元のページに戻るための情報を保存（任意）
    // sessionStorage.setItem('intendedRoute', to.fullPath);

    return navigateTo("/auth/login");
  }
});
