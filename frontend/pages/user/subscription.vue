<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- ページヘッダー -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">プラン管理</h1>
        <p class="mt-2 text-gray-600">現在のプラン状況と履歴を確認できます</p>
      </div>

      <!-- ローディング状態 -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div
          class="h-12 w-12 mx-auto border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"
        />
      </div>

      <!-- エラー状態 -->
      <div
        v-else-if="error"
        class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8"
      >
        <div class="flex">
          <div class="flex-shrink-0">
            <svg
              class="h-5 w-5 text-red-400"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">
              エラーが発生しました
            </h3>
            <p class="mt-1 text-sm text-red-700">{{ error }}</p>
            <button
              class="mt-3 text-sm bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded transition-colors"
              @click="loadSubscriptionData"
            >
              再試行
            </button>
          </div>
        </div>
      </div>

      <!-- メインコンテンツ -->
      <div v-else class="space-y-8">
        <!-- 現在のプラン情報 -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">現在のプラン</h2>
            <!-- リフレッシュボタン -->
            <button
              :disabled="loading"
              class="text-sm bg-gray-100 hover:bg-gray-200 disabled:bg-gray-50 text-gray-700 px-3 py-1 rounded transition-colors"
              @click="loadSubscriptionData"
            >
              <span v-if="loading">更新中...</span>
              <span v-else>最新情報に更新</span>
            </button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- プラン詳細 -->
            <div class="space-y-4">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div
                    class="w-12 h-12 rounded-lg flex items-center justify-center"
                    :class="
                      subscriptionData.has_subscription
                        ? subscriptionData.subscription_status === 'active'
                          ? 'bg-green-100'
                          : 'bg-yellow-100'
                        : 'bg-blue-100'
                    "
                  >
                    <svg
                      class="w-6 h-6"
                      :class="
                        subscriptionData.has_subscription
                          ? subscriptionData.subscription_status === 'active'
                            ? 'text-green-600'
                            : 'text-yellow-600'
                          : 'text-blue-600'
                      "
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-medium text-gray-900">
                    {{ getPlanDisplayName(subscriptionData.plan) }}プラン
                  </h3>
                  <p
                    class="text-sm"
                    :class="
                      subscriptionData.subscription_status === 'active'
                        ? 'text-green-600'
                        : subscriptionData.subscription_status === 'canceled'
                        ? 'text-orange-600'
                        : 'text-gray-500'
                    "
                  >
                    {{
                      subscriptionData.subscription_status
                        ? getStatusDisplayName(
                            subscriptionData.subscription_status
                          )
                        : "未設定"
                    }}
                  </p>
                </div>
              </div>

              <div v-if="subscriptionData.has_subscription" class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-sm font-medium text-gray-500"
                    >次回請求日</span
                  >
                  <span class="text-sm text-gray-900">
                    {{ formatDate(subscriptionData.next_billing_date) }}
                  </span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm font-medium text-gray-500"
                    >月額料金</span
                  >
                  <span class="text-sm text-gray-900">
                    {{ getPlanPrice(subscriptionData.plan) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- アクションボタン -->
            <div class="space-y-4">
              <div>
                <NuxtLink
                  to="/pricing"
                  class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-block text-center"
                >
                  プランの変更・キャンセル
                </NuxtLink>
                <p class="mt-2 text-xs text-gray-500">
                  プランの変更やキャンセルはこちらで行えます
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- プラン変更履歴 -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">
            プラン変更履歴
          </h2>

          <div v-if="historyLoading" class="flex justify-center py-8">
            <div
              class="h-8 w-8 mx-auto border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"
            />
          </div>

          <div v-else-if="historyError" class="text-center py-8">
            <p class="text-red-600 mb-4">履歴の読み込みに失敗しました</p>
            <button
              class="text-blue-600 hover:text-blue-800 font-medium"
              @click="loadHistory"
            >
              再試行
            </button>
          </div>

          <div v-else-if="history.length === 0" class="text-center py-8">
            <div
              class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
            >
              <svg
                class="w-8 h-8 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
              </svg>
            </div>
            <p class="text-gray-500">プラン変更履歴がありません</p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="item in history"
              :key="item.id"
              class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                      <div
                        class="w-8 h-8 rounded-full flex items-center justify-center"
                        :class="getActionIconClass(item.action)"
                      >
                        <svg
                          class="w-4 h-4"
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path
                            v-if="item.action === 'created'"
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"
                          />
                          <path
                            v-else-if="item.action === 'upgraded'"
                            fill-rule="evenodd"
                            d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 6a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 14a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z"
                            clip-rule="evenodd"
                          />
                          <path
                            v-else-if="item.action === 'canceled'"
                            fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                          />
                          <path
                            v-else
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </div>
                    </div>
                    <div>
                      <h3 class="text-sm font-medium text-gray-900">
                        {{ getActionDisplayName(item.action) }}
                      </h3>
                      <p class="text-sm text-gray-600">
                        {{ getHistoryDescription(item) }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm text-gray-500">
                    {{ formatDate(item.created_at) }}
                  </p>
                  <p
                    v-if="item.amount"
                    class="text-sm font-medium text-gray-900"
                  >
                    {{ item.formatted_amount || formatAmount(item.amount) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- ページネーション -->
            <div
              v-if="pagination.last_page > 1"
              class="flex justify-center mt-6"
            >
              <nav class="flex space-x-2">
                <button
                  v-for="page in getPageNumbers()"
                  :key="page"
                  :disabled="historyLoading"
                  class="px-3 py-1 text-sm rounded-md transition-colors"
                  :class="
                    page === pagination.current_page
                      ? 'bg-blue-600 text-white'
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  "
                  @click="() => loadHistory(page)"
                >
                  {{ page }}
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// ページメタデータ
definePageMeta({
  layout: "default",
});

// 型定義
interface SubscriptionData {
  has_subscription: boolean;
  plan: string;
  subscription_status: string | null;
  current_period_end: string | null;
  next_billing_date: string | null;
  can_cancel: boolean;
  stripe_subscription_id?: string;
  stripe_customer_id?: string;
}

interface HistoryItem {
  id: number;
  action: string;
  from_plan: string | null;
  to_plan: string;
  amount: number | null;
  currency: string;
  notes: string | null;
  created_at: string;
  formatted_amount?: string;
}

interface Pagination {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

// Composables
const { api } = useApi();

// デバッグログ
console.log("[subscription] ページスクリプトが実行されました");

// リアクティブデータ
const loading = ref(true);
const error = ref<string | null>(null);
const subscriptionData = ref<SubscriptionData>({
  has_subscription: false,
  plan: "free",
  subscription_status: null,
  current_period_end: null,
  next_billing_date: null,
  can_cancel: false,
});

const history = ref<HistoryItem[]>([]);
const historyLoading = ref(false);
const historyError = ref<string | null>(null);
const pagination = ref<Pagination>({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});

// プラン料金マッピング
const PLAN_PRICES = {
  free: "¥0",
  standard: "¥2,980",
  premium: "¥5,980",
};

// メソッド
const loadSubscriptionData = async () => {
  try {
    loading.value = true;
    error.value = null;

    const response = await api<{
      data?: SubscriptionData;
      message?: string;
      status?: string;
      plan?: string;
      subscription_status?: string;
      has_subscription?: boolean;
      current_period_end?: string;
      next_billing_date?: string;
      can_cancel?: boolean;
      stripe_subscription_id?: string;
      stripe_customer_id?: string;
    }>("/stripe/subscription");

    // 新しいAPI形式（直接データが返される場合）
    if (response.status === "success" && response.plan) {
      subscriptionData.value = {
        has_subscription: response.has_subscription || false,
        plan: response.plan,
        subscription_status: response.subscription_status || null,
        current_period_end: response.current_period_end || null,
        next_billing_date: response.next_billing_date || null,
        can_cancel: response.can_cancel || false,
        stripe_subscription_id: response.stripe_subscription_id,
        stripe_customer_id: response.stripe_customer_id,
      };
    }
    // 従来のAPI形式（dataプロパティ内にある場合）
    else if (response.data) {
      subscriptionData.value = response.data;
    }
    // サブスクリプションがない場合
    else if (response.message === "no_subscription") {
      subscriptionData.value = {
        has_subscription: false,
        plan: "free",
        subscription_status: null,
        current_period_end: null,
        next_billing_date: null,
        can_cancel: false,
      };
    }
    // フォールバック
    else {
      subscriptionData.value = {
        has_subscription: false,
        plan: "free",
        subscription_status: null,
        current_period_end: null,
        next_billing_date: null,
        can_cancel: false,
      };
    }
  } catch (err: unknown) {
    console.error("Subscription data load error:", err);
    const errorResponse = err as { data?: { data?: SubscriptionData } };
    if (errorResponse?.data?.data) {
      subscriptionData.value = errorResponse.data.data;
      error.value = null;
    } else {
      error.value =
        (err as Error).message || "サブスクリプション情報の取得に失敗しました";
    }
  } finally {
    loading.value = false;
  }
};

const loadHistory = async (page: number = 1) => {
  try {
    historyLoading.value = true;
    historyError.value = null;

    const response = await api<{
      data: HistoryItem[];
      pagination: Pagination;
    }>("/stripe/subscription/history", {
      params: { page: page.toString() },
    });

    history.value = response.data;
    pagination.value = response.pagination;
  } catch (err: unknown) {
    console.error("History load error:", err);
    historyError.value = (err as Error).message || "履歴の取得に失敗しました";
  } finally {
    historyLoading.value = false;
  }
};

// ユーティリティ関数
const getPlanDisplayName = (plan: string): string => {
  return (
    {
      free: "FREE",
      standard: "STANDARD",
      premium: "PREMIUM",
    }[plan] || plan.toUpperCase()
  );
};

const getStatusDisplayName = (status: string): string => {
  return (
    {
      active: "アクティブ",
      canceled: "キャンセル済み",
      past_due: "支払い遅延",
      trialing: "トライアル中",
      incomplete: "不完全",
      incomplete_expired: "期限切れ",
      unpaid: "未払い",
    }[status] || status
  );
};

const getPlanPrice = (plan: string): string => {
  return PLAN_PRICES[plan as keyof typeof PLAN_PRICES] || "¥0";
};

const getActionDisplayName = (action: string): string => {
  return (
    {
      created: "プラン開始",
      upgraded: "変更",
      downgraded: "変更",
      canceled: "キャンセル",
      renewed: "更新",
      reactivated: "再開",
    }[action] || action
  );
};

const getActionIconClass = (action: string): string => {
  return (
    {
      created: "bg-green-100 text-green-600",
      upgraded: "bg-blue-100 text-blue-600",
      downgraded: "bg-yellow-100 text-yellow-600",
      canceled: "bg-red-100 text-red-600",
      renewed: "bg-purple-100 text-purple-600",
      reactivated: "bg-indigo-100 text-indigo-600",
    }[action] || "bg-gray-100 text-gray-600"
  );
};

const getHistoryDescription = (item: HistoryItem): string => {
  const fromPlan = item.from_plan ? getPlanDisplayName(item.from_plan) : null;
  const toPlan = getPlanDisplayName(item.to_plan);

  switch (item.action) {
    case "created":
      return `${toPlan}プランを開始しました`;
    case "upgraded":
      return `${fromPlan}プランから${toPlan}プランに変更しました`;
    case "downgraded":
      return `${fromPlan}プランから${toPlan}プランに変更しました`;
    case "canceled":
      return `${fromPlan}プランをキャンセルしました`;
    case "renewed":
      return `${toPlan}プランを更新しました`;
    case "reactivated":
      return `${toPlan}プランを再開しました`;
    default:
      return `プランを${toPlan}に変更しました`;
  }
};

const formatDate = (dateString: string | null): string => {
  if (!dateString) return "-";

  const date = new Date(dateString);
  return date.toLocaleDateString("ja-JP", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
};

const formatAmount = (amount: number | null): string => {
  if (!amount) return "¥0";
  return `¥${amount.toLocaleString()}`;
};

const getPageNumbers = (): number[] => {
  const pages: number[] = [];
  const start = Math.max(1, pagination.value.current_page - 2);
  const end = Math.min(
    pagination.value.last_page,
    pagination.value.current_page + 2
  );

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  return pages;
};

// ライフサイクル
onMounted(async () => {
  console.log("[subscription] ページがマウントされました");
  await Promise.all([loadSubscriptionData(), loadHistory()]);
  console.log("[subscription] データ読み込み完了");
});
</script>
