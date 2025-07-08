<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- ヘッダー -->
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-4">お問い合わせ</h1>
    </div>

    <!-- メインコンテンツ -->
    <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
      <!-- お問い合わせチャットの説明 -->
      <div class="mb-8">
        <p class="text-gray-700 leading-relaxed">
          LumoChatサービス運営チームとの直接チャットです。
          サービスに関するご質問、不具合のご報告、機能改善のご提案など、
          お気軽にお声かけください。
        </p>
      </div>

      <!-- 受付内容 -->
      <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          お気軽にご相談ください
        </h3>
        <div class="grid md:grid-cols-2 gap-4">
          <div class="flex items-start">
            <div
              class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-1"
            >
              <svg
                class="w-4 h-4 text-blue-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <h4 class="font-medium text-gray-900">ご質問・お問い合わせ</h4>
              <p class="text-sm text-gray-600">サービスの使い方やご不明な点</p>
            </div>
          </div>
          <div class="flex items-start">
            <div
              class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3 mt-1"
            >
              <svg
                class="w-4 h-4 text-red-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                />
              </svg>
            </div>
            <div>
              <h4 class="font-medium text-gray-900">バグ報告</h4>
              <p class="text-sm text-gray-600">
                不具合や予期しない動作のご報告
              </p>
            </div>
          </div>
          <div class="flex items-start">
            <div
              class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-1"
            >
              <svg
                class="w-4 h-4 text-green-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
                />
              </svg>
            </div>
            <div>
              <h4 class="font-medium text-gray-900">改善・修正案</h4>
              <p class="text-sm text-gray-600">サービス向上のためのご提案</p>
            </div>
          </div>
          <div class="flex items-start">
            <div
              class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3 mt-1"
            >
              <svg
                class="w-4 h-4 text-purple-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"
                />
              </svg>
            </div>
            <div>
              <h4 class="font-medium text-gray-900">その他のご相談</h4>
              <p class="text-sm text-gray-600">上記以外のご相談事項</p>
            </div>
          </div>
        </div>
      </div>

      <!-- 返答について -->
      <div class="mb-8 p-4 border border-yellow-200 rounded-lg">
        <div class="space-y-2 text-sm text-yellow-700">
          <p class="flex items-start">
            <svg
              class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            お問い合わせいただいたメッセージには順次対応させていただきます
          </p>
          <p class="flex items-start">
            <svg
              class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            内容によってはお時間をいただく場合がございます
          </p>
          <p class="flex items-start">
            <svg
              class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"
              />
            </svg>
            有料プランをご利用のお客様には優先的に対応いたします
          </p>
        </div>
      </div>

      <!-- お問い合わせチャット開始ボタン -->
      <div class="text-center">
        <button
          class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
          :disabled="isLoading"
          @click="openSupportChat"
        >
          <svg
            v-if="!isLoading"
            class="w-5 h-5 mr-2"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
            />
          </svg>
          <svg
            v-else
            class="animate-spin w-5 h-5 mr-2"
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
          {{ isLoading ? "チャットを開始中..." : "チャットを開始" }}
        </button>
      </div>

      <!-- 戻るリンク -->
      <div class="text-center mt-6">
        <button
          class="text-gray-600 hover:text-gray-800 text-sm underline"
          @click="goBack"
        >
          前のページに戻る
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useAuthStore } from "~/stores/auth";
import { useRouter } from "vue-router";

// ページメタデータを設定してdefaultレイアウトを使用
definePageMeta({
  layout: "default",
});

// 認証ストアとルーター
const authStore = useAuthStore();
const router = useRouter();

// ローディング状態
const isLoading = ref(false);

// 戻る関数
const goBack = () => {
  if (typeof window !== "undefined" && window.history.length > 1) {
    window.history.back();
  } else {
    router.push("/user");
  }
};

// お問い合わせチャットを開く関数
const openSupportChat = async () => {
  try {
    isLoading.value = true;

    // 認証チェック
    if (!authStore.isAuthenticated) {
      // 認証されていない場合はログインページにリダイレクト
      router.push("/auth/login");
      return;
    }

    // お問い合わせチャットを作成または取得
    const { api } = useApi();
    const conversation = await api<{ room_token: string }>(
      "/support/conversation",
      {
        method: "POST",
      }
    );

    if (conversation && conversation.room_token) {
      // チャットページに遷移
      router.push(`/chat/${conversation.room_token}/`);
    }
  } catch (error) {
    console.error("お問い合わせチャットの開始に失敗しました:", error);
    // エラーが発生した場合はログインページに遷移
    router.push("/auth/login");
  } finally {
    isLoading.value = false;
  }
};

// メタ情報の設定
useHead({
  title: "お問い合わせ - LumoChat",
  meta: [
    {
      name: "description",
      content:
        "LumoChatサービス運営チームへのお問い合わせページです。バグ報告、改善提案、ご質問などお気軽にご相談ください。",
    },
  ],
});
</script>
