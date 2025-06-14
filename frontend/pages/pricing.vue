<template>
  <div class="max-w-3xl mx-auto p-4 space-y-6">
    <h1 class="text-2xl font-bold text-center">プランを選択</h1>

    <!-- 認証状態の表示 -->
    <div
      v-if="!authStore.isAuthenticated"
      class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6"
    >
      <div class="flex items-center">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-yellow-600 mr-2"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
            clip-rule="evenodd"
          />
        </svg>
        <p class="text-yellow-800">
          プランを選択するには、まずログインが必要です。
          <NuxtLink
            to="/auth/login"
            class="text-yellow-900 underline font-medium ml-1"
          >
            こちらからログイン
          </NuxtLink>
          してください。
        </p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="border rounded-lg p-4 flex flex-col items-center">
        <h2 class="text-xl font-semibold mb-2">Standard</h2>
        <p class="mb-4">月額料金 - 最大50名</p>
        <button
          class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          :disabled="isLoading || !authStore.isAuthenticated"
          @click="checkout('standard')"
        >
          <template v-if="isLoading">
            <svg
              class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              />
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              />
            </svg>
            処理中...
          </template>
          <template v-else> このプランを選択 </template>
        </button>
      </div>
      <div class="border rounded-lg p-4 flex flex-col items-center">
        <h2 class="text-xl font-semibold mb-2">Premium</h2>
        <p class="mb-4">月額料金 - 最大200名</p>
        <button
          class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          :disabled="isLoading || !authStore.isAuthenticated"
          @click="checkout('premium')"
        >
          <template v-if="isLoading">
            <svg
              class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              />
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              />
            </svg>
            処理中...
          </template>
          <template v-else> このプランを選択 </template>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useToast } from "@/composables/useToast";
import { useApi } from "@/composables/useApi";
import { useAuthStore } from "~/stores/auth";
const toast = useToast();
const { api } = useApi();
const authStore = useAuthStore();

// ローディング状態を管理
const isLoading = ref(false);

const checkout = async (plan: "standard" | "premium") => {
  // 1. 認証チェック
  if (!authStore.isAuthenticated) {
    toast.add({
      title: "認証が必要です",
      description: "プランを選択するには、まずログインしてください",
      color: "warning",
    });

    // ログインページにリダイレクト（現在のページをreturn_urlとして保持）
    await navigateTo({
      path: "/auth/login",
      query: { return_url: "/pricing" },
    });
    return;
  }

  // 2. ユーザー情報が取得できていない場合の処理
  if (!authStore.user) {
    toast.add({
      title: "ユーザー情報を確認中",
      description: "ユーザー情報を取得しています。しばらくお待ちください",
      color: "info",
    });

    try {
      await authStore.checkAuth();
      if (!authStore.user) {
        throw new Error("ユーザー情報の取得に失敗しました");
      }
    } catch {
      toast.add({
        title: "エラー",
        description:
          "ユーザー情報の取得に失敗しました。再度ログインしてください",
        color: "error",
      });
      await navigateTo("/auth/login");
      return;
    }
  }

  // 3. 既に有料プランの場合の確認
  if (authStore.user.plan && authStore.user.plan !== "free") {
    const currentPlan = authStore.user.plan;
    const isUpgrade = currentPlan === "standard" && plan === "premium";
    const isSamePlan = currentPlan === plan;

    if (isSamePlan) {
      toast.add({
        title: "既に同じプラン",
        description: `既に${plan.toUpperCase()}プランをご利用中です`,
        color: "info",
      });
      return;
    }

    if (!isUpgrade) {
      toast.add({
        title: "プラン変更について",
        description:
          "プランの変更・ダウングレードについては、サポートまでお問い合わせください",
        color: "warning",
      });
      return;
    }
  }

  // 4. 決済処理開始
  isLoading.value = true;

  try {
    toast.add({
      title: "決済ページを準備中",
      description: "Stripeの安全な決済ページに移動しています...",
      color: "info",
    });

    const res = await api<{ url: string }>("/stripe/create-checkout-session", {
      method: "POST",
      body: { plan },
    });

    if (res.url) {
      // 成功メッセージを表示してからリダイレクト
      toast.add({
        title: "決済ページに移動します",
        description: "数秒後に決済ページに移動します",
        color: "success",
      });

      // 少し遅延してからリダイレクト（ユーザーにメッセージを見せるため）
      setTimeout(() => {
        window.location.href = res.url;
      }, 2000);
    } else {
      throw new Error("決済URLが取得できませんでした");
    }
  } catch (error) {
    console.error("checkout error", error);

    // より詳細なエラーメッセージ
    let errorMessage = "決済処理でエラーが発生しました";
    let errorTitle = "エラー";

    if (error instanceof Error) {
      // 特定のエラーパターンに応じてメッセージを調整
      if (error.message.includes("subscription")) {
        errorTitle = "サブスクリプションエラー";
        errorMessage =
          "既にアクティブなサブスクリプションがあります。プラン変更については、サポートまでお問い合わせください";
      } else if (
        error.message.includes("network") ||
        error.message.includes("fetch")
      ) {
        errorTitle = "ネットワークエラー";
        errorMessage =
          "ネットワークエラーが発生しました。インターネット接続を確認してもう一度お試しください";
      } else if (error.message.includes("auth")) {
        errorTitle = "認証エラー";
        errorMessage = "認証に問題があります。再度ログインしてお試しください";
      } else if (error.message) {
        errorMessage = error.message;
      }
    }

    toast.add({
      title: errorTitle,
      description: errorMessage,
      color: "error",
    });
  } finally {
    isLoading.value = false;
  }
};

// ページ読み込み時に認証状態をチェック
onMounted(async () => {
  if (!authStore.user && authStore.isAuthenticated) {
    try {
      await authStore.checkAuth();
    } catch (error) {
      console.error("認証チェックエラー:", error);
    }
  }
});
</script>
