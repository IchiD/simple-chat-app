<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <h3 class="text-lg font-medium text-gray-900 mb-4">プッシュ通知設定</h3>

    <div v-if="!state.isSupported" class="bg-yellow-50 p-4 rounded-md mb-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg
            class="h-5 w-5 text-yellow-400"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-yellow-700">
            お使いのブラウザはプッシュ通知をサポートしていません。
          </p>
        </div>
      </div>
    </div>

    <div v-else-if="state.error" class="bg-red-50 p-4 rounded-md mb-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg
            class="h-5 w-5 text-red-400"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-red-700">
            {{ state.error }}
          </p>
        </div>
      </div>
    </div>

    <div
      v-if="state.permissionState === 'denied'"
      class="bg-red-50 p-4 rounded-md mb-4"
    >
      <div class="flex">
        <div class="flex-shrink-0">
          <svg
            class="h-5 w-5 text-red-400"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-red-700">
            通知がブロックされています。ブラウザの設定から通知を許可してください。
          </p>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <div class="flex items-center justify-between flex-col gap-4">
        <div>
          <p class="text-xs text-gray-500">
            チャットメッセージ、フレンド申請などの通知を受け取ります
          </p>
        </div>

        <button
          :disabled="state.isPending || state.permissionState === 'denied'"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 cursor-pointer hover:scale-105 transition-transform duration-200"
          :class="[
            state.isPending || state.permissionState === 'denied'
              ? 'opacity-50 cursor-not-allowed'
              : '',
          ]"
          :style="
            state.isSubscribed
              ? 'background-color: #ef4444;'
              : 'background-color: var(--primary);'
          "
          @click="toggleSubscription"
        >
          <svg
            v-if="state.isPending"
            class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
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
          {{ state.isSubscribed ? "通知を無効にする" : "通知を有効にする" }}
        </button>
      </div>
    </div>

    <div
      v-if="state.isSubscribed && isDev"
      class="mt-6 pt-4 border-t border-gray-200"
    >
      <h4 class="text-sm font-medium text-gray-700 mb-2">デバッグ</h4>
      <button
        class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
        style="--tw-ring-color: var(--primary)"
        @click="testNotification"
      >
        テスト通知を送信
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from "vue";
import { usePushNotification } from "../composables/usePushNotification";

const _props = defineProps<{
  // 開発モードかどうか（テスト機能の表示制御に使用）
  isDev?: boolean;
}>();

// プッシュ通知機能の初期化
const { state, initialize, subscribe, unsubscribe, sendTestNotification } =
  usePushNotification();

// 購読切り替え処理
const toggleSubscription = async () => {
  if (state.value.isSubscribed) {
    await unsubscribe();
  } else {
    await subscribe();
  }
};

// テスト通知送信
const testNotification = async () => {
  await sendTestNotification();
};

// コンポーネントマウント時に初期化
onMounted(async () => {
  await initialize();
});
</script>
