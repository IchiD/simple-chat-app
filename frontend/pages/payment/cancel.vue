<template>
  <div
    class="min-h-screen bg-gradient-to-br from-yellow-50 to-orange-50 flex items-center justify-center p-4"
  >
    <div class="max-w-md w-full">
      <!-- キャンセルアイコン -->
      <div class="text-center mb-8">
        <div
          class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-yellow-100 mb-6"
        >
          <svg
            class="h-10 w-10 text-yellow-600"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"
            />
          </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          決済がキャンセルされました
        </h1>
        <p class="text-lg text-gray-600">決済処理が中断されました</p>
      </div>

      <!-- キャンセル理由・状況説明 -->
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
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          キャンセルについて
        </h2>

        <div class="space-y-3 text-sm text-gray-700">
          <p>決済処理は完了していません。以下のような理由が考えられます：</p>
          <ul class="list-disc list-inside space-y-1 ml-4">
            <li>ユーザーによる決済のキャンセル</li>
            <li>決済情報の入力中断</li>
            <li>ブラウザの戻るボタンの使用</li>
            <li>セッションのタイムアウト</li>
          </ul>
          <p class="mt-3 text-blue-600 font-medium">
            ご安心ください。料金は一切発生していません。
          </p>
        </div>
      </div>

      <!-- 選択されていたプラン情報 -->
      <div v-if="selectedPlan" class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
          選択されていたプラン
        </h2>

        <div class="bg-gray-50 rounded-lg p-4">
          <div class="flex justify-between items-center mb-2">
            <span class="font-medium text-gray-900">プラン</span>
            <span class="text-lg font-bold text-blue-600 uppercase">
              {{ selectedPlan }}
            </span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-600">月額料金</span>
            <span class="font-semibold text-gray-900">
              {{ getPlanPrice(selectedPlan) }}
            </span>
          </div>
        </div>
      </div>

      <!-- よくある質問 -->
      <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">よくある質問</h2>

        <div class="space-y-4">
          <details class="group">
            <summary
              class="flex justify-between items-center cursor-pointer text-sm font-medium text-gray-900 hover:text-blue-600"
            >
              <span>料金は発生していますか？</span>
              <svg
                class="w-4 h-4 transition-transform group-open:rotate-180"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"
                />
              </svg>
            </summary>
            <p class="mt-2 text-sm text-gray-600">
              いいえ、決済が完了していないため料金は一切発生していません。
            </p>
          </details>

          <details class="group">
            <summary
              class="flex justify-between items-center cursor-pointer text-sm font-medium text-gray-900 hover:text-blue-600"
            >
              <span>再度決済を試すことはできますか？</span>
              <svg
                class="w-4 h-4 transition-transform group-open:rotate-180"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"
                />
              </svg>
            </summary>
            <p class="mt-2 text-sm text-gray-600">
              はい、いつでも再度お試しいただけます。プラン選択ページから再度お手続きください。
            </p>
          </details>

          <details class="group">
            <summary
              class="flex justify-between items-center cursor-pointer text-sm font-medium text-gray-900 hover:text-blue-600"
            >
              <span>決済に問題がある場合は？</span>
              <svg
                class="w-4 h-4 transition-transform group-open:rotate-180"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"
                />
              </svg>
            </summary>
            <p class="mt-2 text-sm text-gray-600">
              決済に関する問題がございましたら、サポートまでお気軽にお問い合わせください。
            </p>
          </details>
        </div>
      </div>

      <!-- アクションボタン -->
      <div class="space-y-3">
        <NuxtLink
          :to="selectedPlan ? `/pricing?retry=${selectedPlan}` : '/pricing'"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors text-center block"
        >
          <svg
            class="inline h-5 w-5 mr-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
            />
          </svg>
          再度決済を試す
        </NuxtLink>

        <NuxtLink
          to="/pricing"
          class="w-full border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-3 px-6 rounded-lg transition-colors text-center block"
        >
          プラン選択に戻る
        </NuxtLink>

        <NuxtLink
          to="/"
          class="w-full border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-3 px-6 rounded-lg transition-colors text-center block"
        >
          ホームに戻る
        </NuxtLink>
      </div>

      <!-- サポート情報 -->
      <div class="text-center mt-8 text-sm text-gray-500">
        <p>決済に関してご不明な点がございましたら、</p>
        <NuxtLink
          to="/support"
          class="text-blue-600 hover:text-blue-800 underline"
        >
          サポートまでお問い合わせください
        </NuxtLink>
      </div>

      <!-- 自動リダイレクト情報 -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
        <div class="flex items-center">
          <svg
            class="h-5 w-5 text-blue-600 mr-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <div class="text-sm text-blue-800">
            <p class="font-medium">自動リダイレクト</p>
            <p>{{ countdown }}秒後にプラン選択ページに移動します</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";

// URLパラメータから選択されたプランを取得
const route = useRoute();
const selectedPlan = ref<string | null>(null);

// カウントダウン用
const countdown = ref(15);
let countdownInterval: NodeJS.Timeout | null = null;

// プラン料金の定義
const PLAN_PRICES: Record<string, string> = {
  free: "¥0",
  standard: "¥2,980",
  premium: "¥5,980",
};

// プラン料金取得関数
const getPlanPrice = (plan: string | null): string => {
  if (!plan) return "¥0";
  return PLAN_PRICES[plan] || "¥0";
};

// 自動リダイレクト
const startCountdown = () => {
  countdownInterval = setInterval(() => {
    countdown.value--;
    if (countdown.value <= 0) {
      const redirectUrl = selectedPlan.value
        ? `/pricing?retry=${selectedPlan.value}`
        : "/pricing";
      navigateTo(redirectUrl);
    }
  }, 1000);
};

// ページタイトル設定
useHead({
  title: "決済キャンセル - Chat App",
  meta: [
    {
      name: "description",
      content: "決済がキャンセルされました。料金は発生していません。",
    },
  ],
});

onMounted(() => {
  // URLパラメータからプラン情報を取得
  selectedPlan.value = (route.query.plan as string) || null;

  // 自動リダイレクトを開始
  startCountdown();

  // キャンセルメッセージをトーストで表示（もしtoastが利用可能な場合）
  try {
    const toast = useToast();
    toast.add({
      title: "決済キャンセル",
      description: "決済処理がキャンセルされました。料金は発生していません。",
      color: "warning",
      timeout: 5000,
    });
  } catch {
    // toastが利用できない場合は無視
    console.log("Toast not available");
  }
});

onUnmounted(() => {
  if (countdownInterval) {
    clearInterval(countdownInterval);
  }
});
</script>
