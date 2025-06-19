<template>
  <div
    class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center p-4"
  >
    <div class="max-w-md w-full">
      <!-- 成功アイコンとアニメーション -->
      <div class="text-center mb-8">
        <div
          class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6 animate-bounce"
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

      <!-- 決済詳細カード -->
      <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
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
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
          <svg
            class="inline h-5 w-5 text-blue-600 mr-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M13 10V3L4 14h7v7l9-11h-7z"
            />
          </svg>
          今すぐ利用可能な機能
        </h2>

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

// URLパラメータから選択されたプランを取得
const route = useRoute();
const selectedPlan = ref<string | null>(null);

// プラン料金の定義
const PLAN_PRICES: Record<string, string> = {
  free: "¥0",
  standard: "¥2,980",
  premium: "¥5,980",
};

// プラン表示名の定義
const PLAN_DISPLAY_NAMES: Record<string, string> = {
  free: "フリー",
  standard: "スタンダード",
  premium: "プレミアム",
};

// プラン料金取得関数
const getPlanPrice = (plan: string | null): string => {
  if (!plan) return "¥0";
  return PLAN_PRICES[plan] || "¥0";
};

// プラン表示名取得関数
const getPlanDisplayName = (plan: string | null): string => {
  if (!plan) return "フリー";
  return PLAN_DISPLAY_NAMES[plan] || "フリー";
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

onMounted(() => {
  // URLパラメータからプラン情報を取得
  const planParam = route.query.plan as string;

  // プランパラメータが存在し、有効なプランの場合のみ設定
  if (planParam && (planParam === "standard" || planParam === "premium")) {
    selectedPlan.value = planParam;
  } else {
    // 無効なプランまたはプランが指定されていない場合はstandard（決済完了ページはFREEでは使用しないため）
    selectedPlan.value = "standard";
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
