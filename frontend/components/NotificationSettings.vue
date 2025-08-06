<template>
  <div class="bg-white rounded-lg">
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

    <!-- 詳細な通知設定 -->
    <div class="space-y-6">
      <!-- メール通知設定 -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3">メール通知</h4>
        <div class="space-y-2">
          <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div>
              <span class="text-sm font-medium text-gray-700">メッセージ受信</span>
              <p class="text-xs text-gray-500 mt-1">新しいメッセージを受信したときにメールで通知</p>
            </div>
            <input
              v-model="preferences.email.messages"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              @change="updatePreferences"
            />
          </label>
          
          <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div>
              <span class="text-sm font-medium text-gray-700">友達申請</span>
              <p class="text-xs text-gray-500 mt-1">友達申請を受けたときにメールで通知</p>
            </div>
            <input
              v-model="preferences.email.friend_requests"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              @change="updatePreferences"
            />
          </label>
          
          <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div>
              <span class="text-sm font-medium text-gray-700">グループ招待</span>
              <p class="text-xs text-gray-500 mt-1">グループに招待されたときにメールで通知</p>
            </div>
            <input
              v-model="preferences.email.group_invites"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              @change="updatePreferences"
            />
          </label>
          
          <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div>
              <span class="text-sm font-medium text-gray-700">グループメッセージ</span>
              <p class="text-xs text-gray-500 mt-1">グループで新しいメッセージがあったときにメールで通知</p>
            </div>
            <input
              v-model="preferences.email.group_messages"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              @change="updatePreferences"
            />
          </label>
        </div>
      </div>

      <!-- プッシュ通知設定 -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3">プッシュ通知</h4>
        
        <!-- プッシュ通知の有効化ボタン -->
        <div class="mb-4">
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
            {{ state.isSubscribed ? "プッシュ通知を無効にする" : "プッシュ通知を有効にする" }}
          </button>
        </div>
        
        <!-- プッシュ通知の詳細設定（プッシュ通知が有効な場合のみ表示） -->
        <div v-if="state.isSubscribed" class="space-y-2">
          <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div>
              <span class="text-sm font-medium text-gray-700">メッセージ受信</span>
              <p class="text-xs text-gray-500 mt-1">新しいメッセージを受信したときにプッシュ通知</p>
            </div>
            <input
              v-model="preferences.push.messages"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              @change="updatePreferences"
            />
          </label>
          
          <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div>
              <span class="text-sm font-medium text-gray-700">友達申請</span>
              <p class="text-xs text-gray-500 mt-1">友達申請を受けたときにプッシュ通知</p>
            </div>
            <input
              v-model="preferences.push.friend_requests"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              @change="updatePreferences"
            />
          </label>
          
          <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div>
              <span class="text-sm font-medium text-gray-700">グループ招待</span>
              <p class="text-xs text-gray-500 mt-1">グループに招待されたときにプッシュ通知</p>
            </div>
            <input
              v-model="preferences.push.group_invites"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              @change="updatePreferences"
            />
          </label>
          
          <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div>
              <span class="text-sm font-medium text-gray-700">グループメッセージ</span>
              <p class="text-xs text-gray-500 mt-1">グループで新しいメッセージがあったときにプッシュ通知</p>
            </div>
            <input
              v-model="preferences.push.group_messages"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              @change="updatePreferences"
            />
          </label>
        </div>
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
import { onMounted, ref, reactive } from "vue";
import { usePushNotification } from "../composables/usePushNotification";
import { useApi } from "../composables/useApi";
import { useToast } from "../composables/useToast";

const { isDev } = defineProps<{
  isDev?: boolean;
}>();

const { api } = useApi();
const toast = useToast();

// プッシュ通知機能の初期化
const { state, initialize, subscribe, unsubscribe, sendTestNotification } =
  usePushNotification();

// 通知設定の状態
const preferences = reactive({
  email: {
    messages: true,
    friend_requests: true,
    group_invites: true,
    group_messages: true,
  },
  push: {
    messages: true,
    friend_requests: true,
    group_invites: true,
    group_messages: true,
  },
});

const isUpdating = ref(false);

// 通知設定を取得
const fetchPreferences = async () => {
  try {
    const response = await api("/notifications/preferences");
    if (response.success && response.preferences) {
      Object.assign(preferences, response.preferences);
    }
  } catch (error) {
    console.error("通知設定の取得に失敗しました:", error);
  }
};

// 通知設定を更新
const updatePreferences = async () => {
  if (isUpdating.value) return;
  
  try {
    isUpdating.value = true;
    await api("/notifications/preferences", {
      method: "PUT",
      body: {
        preferences: {
          email: preferences.email,
          push: preferences.push,
        },
      },
    });
    
    toast.add({
      title: "成功",
      description: "通知設定を更新しました",
      color: "success",
    });
  } catch (error) {
    console.error("通知設定の更新に失敗しました:", error);
    toast.add({
      title: "エラー",
      description: "通知設定の更新に失敗しました",
      color: "error",
    });
  } finally {
    isUpdating.value = false;
  }
};

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
  await fetchPreferences();
});
</script>