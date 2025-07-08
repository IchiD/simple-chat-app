<template>
  <div
    class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-emerald-50 to-emerald-100"
  >
    <div class="max-w-md w-full mx-auto px-6">
      <!-- エラーコンテンツ -->
      <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="mb-6">
          <!-- 404アイコン -->
          <div
            class="mx-auto w-24 h-24 bg-emerald-100 rounded-full flex items-center justify-center mb-4"
          >
            <svg
              class="w-12 h-12 text-emerald-600"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>

          <!-- エラーメッセージ -->
          <h1 class="text-6xl font-bold text-gray-800 mb-2">404</h1>
          <h2 class="text-2xl font-semibold text-gray-700 mb-4">
            ページが見つかりません
          </h2>
          <p class="text-gray-600 mb-8">
            お探しのページは存在しないか、移動された可能性があります。<br />
            URLをもう一度ご確認ください。
          </p>
        </div>

        <!-- アクションボタン -->
        <div class="space-y-4">
          <NuxtLink
            to="/user"
            class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-150 ease-in-out"
          >
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path
                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
              />
            </svg>
            ホームに戻る
          </NuxtLink>

          <button
            class="w-full inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-150 ease-in-out"
            @click="goBack"
          >
            <svg
              class="w-5 h-5 mr-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10 19l-7-7m0 0l7-7m-7 7h18"
              />
            </svg>
            前のページに戻る
          </button>
        </div>

        <!-- お問い合わせリンク -->
        <div class="mt-8 pt-6 border-t border-gray-200">
          <p class="text-sm text-gray-500 mb-3">
            問題が解決しない場合は、お気軽にお問い合わせください。
          </p>
          <NuxtLink
            to="/support"
            class="inline-flex items-center text-sm font-medium text-emerald-600 hover:text-emerald-700 transition duration-150 ease-in-out"
          >
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path
                fill-rule="evenodd"
                d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                clip-rule="evenodd"
              />
            </svg>
            お問い合わせ
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// エラー情報の受け取り
const _props = defineProps<{
  error?: {
    statusCode: number;
    statusMessage?: string;
  };
}>();

// ルーター
const router = useRouter();

// 前のページに戻る関数
const goBack = () => {
  // ブラウザの履歴がある場合は前のページに戻る
  if (window.history.length > 1) {
    window.history.back();
  } else {
    // 履歴がない場合はホームページに遷移
    router.push("/user");
  }
};

// メタ情報の設定
useHead({
  title: "ページが見つかりません - LumoChat",
  meta: [
    { name: "description", content: "お探しのページは見つかりませんでした。" },
  ],
});
</script>
