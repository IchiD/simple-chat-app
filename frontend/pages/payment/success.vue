<template>
  <div
    class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center p-4"
  >
    <div class="max-w-md w-full">
      <!-- 成功アイコンとアニメーション -->
      <div class="text-center mb-8">
        <div
          class="mx-auto flex items-center justify-center h-20 animate-bounce"
        >
          <svg
            class="h-10 w-10 text-green-600"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">決済完了</h1>
        <p class="text-lg text-gray-600">プランの変更が完了しました</p>
      </div>

      <!-- ローディング表示 -->
      <div
        v-if="isLoadingPlan"
        class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-6"
      >
        <h2 class="text-lg font-semibold text-blue-800 mb-2">
          プラン情報を取得中...
        </h2>
        <p class="text-blue-600">
          決済完了後の最新プラン情報を取得しています。しばらくお待ちください。
        </p>
      </div>

      <!-- プラン情報が取得できない場合のエラー表示 -->
      <div
        v-else-if="!selectedPlan"
        class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6"
      >
        <h2 class="text-lg font-semibold text-red-800 mb-2">
          プラン情報エラー
        </h2>
        <p class="text-red-600">
          プラン情報を正しく取得できませんでした。決済処理後にプラン情報の同期に時間がかかっている可能性があります。<br />
          数分後にマイページで最新のプラン状況をご確認ください。
        </p>
      </div>

      <!-- 決済詳細カード -->
      <div v-else class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">決済詳細</h2>

        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <span class="text-gray-600">プラン</span>
            <span class="font-semibold text-gray-900 uppercase">
              {{ getPlanDisplayName(selectedPlan) }}
            </span>
          </div>

          <div class="flex justify-between items-center">
            <span class="text-gray-600">決済日時</span>
            <span class="text-gray-900">
              {{ formatDate(new Date()) }}
            </span>
          </div>

          <div class="flex justify-between items-center">
            <span class="text-gray-600">次回請求日</span>
            <span class="text-gray-900">
              {{ formatDate(getNextBillingDate()) }}
            </span>
          </div>

          <div class="border-t pt-3 mt-3">
            <div class="flex justify-between items-center font-semibold">
              <span class="text-gray-900">月額料金</span>
              <span class="text-green-600">
                {{ getPlanPrice(selectedPlan) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- 利用可能機能 -->
      <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">利用可能な機能</h2>

        <ul class="space-y-2">
          <li
            v-for="feature in getPlanFeatures"
            :key="feature"
            class="flex items-center text-sm text-gray-700"
          >
            <svg
              class="h-4 w-4 text-green-500 mr-2 flex-shrink-0"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                clip-rule="evenodd"
              />
            </svg>
            {{ feature }}
          </li>
        </ul>
      </div>

      <!-- アクションボタン -->
      <div class="space-y-3">
        <NuxtLink
          to="/user"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors text-center block"
        >
          マイページへ移動
        </NuxtLink>

        <NuxtLink
          to="/pricing"
          class="w-full border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-3 px-6 rounded-lg transition-colors text-center block"
        >
          プラン詳細を確認
        </NuxtLink>
      </div>

      <!-- サポート情報 -->
      <div class="text-center mt-8 text-sm text-gray-500">
        <p>ご不明な点がございましたら、</p>
        <NuxtLink
          to="/support"
          class="text-blue-600 hover:text-blue-800 underline"
        >
          サポートまでお問い合わせください
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from "vue";
import { useAuthStore } from "~/stores/auth";
import { usePricing } from "@/composables/usePricing";

// URLパラメータから選択されたプランを取得
const route = useRoute();
const selectedPlan = ref<string | null>(null);
const isLoadingPlan = ref(false);
const pricing = usePricing();

// プラン料金取得関数（usePricingから取得）
const getPlanPrice = (plan: string | null): string => {
  if (!plan) return "¥0";
  if (!pricing.pricingData.value?.plans) return "¥0";
  return pricing.getPlanPrice(
    plan as keyof typeof pricing.pricingData.value.plans
  );
};

// プラン表示名取得関数（usePricingから取得）
const getPlanDisplayName = (plan: string | null): string => {
  if (!plan) return "フリー";
  if (!pricing.pricingData.value?.plans) return "フリー";
  return pricing.getPlanDisplayName(
    plan as keyof typeof pricing.pricingData.value.plans
  );
};

// プランごとの利用可能機能
const getPlanFeatures = computed(() => {
  const plan = selectedPlan.value;

  if (plan === "standard") {
    return [
      "グループチャット機能（50人/グループ）が利用可能になりました",
      "メンバー管理機能が利用可能になりました",
      "優先サポートが利用可能になりました",
    ];
  } else if (plan === "premium") {
    return [
      "グループチャット機能（200人/グループ）が利用可能になりました",
      "一括配信機能が利用可能になりました",
      "優先サポートが利用可能になりました",
    ];
  } else {
    // free プランまたは不明なプラン
    return ["基本的なチャット機能が利用可能です", "友達追加機能が利用可能です"];
  }
});

// 日付フォーマット関数
const formatDate = (date: Date): string => {
  return date.toLocaleDateString("ja-JP", {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

// 次回請求日計算
const getNextBillingDate = (): Date => {
  const nextMonth = new Date();
  nextMonth.setMonth(nextMonth.getMonth() + 1);
  return nextMonth;
};

// ページタイトル設定
useHead({
  title: "決済完了 - Chat App",
  meta: [
    {
      name: "description",
      content: "決済が正常に完了しました。有料プランの機能をお楽しみください。",
    },
  ],
});

onMounted(async () => {
  // 価格情報を初期化
  await pricing.initializePricing();

  // URLパラメータからプラン情報を取得
  const planParam = route.query.plan as string;
  const sessionId = route.query.session_id as string;

  // プランパラメータが存在し、有効なプランの場合のみ設定
  if (planParam && (planParam === "standard" || planParam === "premium")) {
    selectedPlan.value = planParam;
    console.log("決済成功: プラン設定完了", planParam);
  } else if (sessionId) {
    // session_idがある場合はAPIからユーザーのプラン情報を取得（リトライ機能付き）
    isLoadingPlan.value = true;

    // Webhook処理完了を待つためのリトライ処理
    let retryCount = 0;
    const maxRetries = 10; // 最大10回（約50秒間）
    const retryDelay = 5000; // 5秒間隔

    const fetchPlanWithRetry = async () => {
      try {
        const authStore = useAuthStore();

        if (authStore.isAuthenticated && authStore.token) {
          // ユーザー情報を再取得してプラン情報を更新
          await authStore.checkAuth();

          if (authStore.user?.plan && authStore.user.plan !== "free") {
            selectedPlan.value = authStore.user.plan;
            console.log(
              "決済成功: APIからプラン情報を取得",
              authStore.user.plan
            );
            return true; // 成功
          }
        }
        return false; // 失敗またはまだ未更新
      } catch (error) {
        console.error("ユーザー情報の取得に失敗しました:", error);
        return false;
      }
    };

    // 初回試行
    const firstAttempt = await fetchPlanWithRetry();

    if (!firstAttempt) {
      // 初回で取得できない場合、リトライを実行
      console.log("プラン情報の取得をリトライします...");

      const retryInterval = setInterval(async () => {
        retryCount++;
        console.log(
          `プラン情報取得を再試行中... (${retryCount}/${maxRetries})`
        );

        const success = await fetchPlanWithRetry();

        if (success || retryCount >= maxRetries) {
          clearInterval(retryInterval);

          if (!success) {
            selectedPlan.value = null;
            console.warn(
              "最大リトライ回数に達しましたが、プラン情報を取得できませんでした"
            );
          }

          isLoadingPlan.value = false;
        }
      }, retryDelay);
    } else {
      isLoadingPlan.value = false;
    }
  } else {
    // URLパラメータにプラン情報もsession_idもない場合
    selectedPlan.value = null;
    console.warn(
      "決済成功ページにプラン情報またはsession_idが含まれていません"
    );
    console.warn("planParam:", planParam, "sessionId:", sessionId);
  }

  // 成功メッセージをトーストで表示（もしtoastが利用可能な場合）
  try {
    const toast = useToast();
    toast.add({
      title: "決済完了",
      description: `${getPlanDisplayName(
        selectedPlan.value
      )}プランへの変更が完了しました！`,
      color: "success",
      timeout: 5000,
    });
  } catch {
    // toastが利用できない場合は無視
    console.log("Toast not available");
  }
});

onUnmounted(() => {
  // 自動リダイレクトの処理は削除
});
</script>
