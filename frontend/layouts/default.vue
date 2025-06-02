<template>
  <div>
    <!-- 共通ヘッダー (chat/[room_token] ページでは非表示) -->
    <nav
      v-if="!isChatRoomPage"
      class="bg-white shadow-sm border-b border-gray-200"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <NuxtLink to="/user" class="flex items-center">
              <img src="/images/rogo.png" alt="LumoChat" class="h-10 w-auto" />
            </NuxtLink>
          </div>
          <div class="flex items-center space-x-2 sm:space-x-3">
            <!-- ユーザー（ホーム）ボタン -->
            <NuxtLink
              v-if="currentPage !== 'user'"
              to="/user"
              class="inline-flex items-center px-2 py-2 sm:px-3 text-xs sm:text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-150 ease-in-out"
            >
              <svg
                class="w-4 h-4"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                />
              </svg>
              <span class="hidden sm:inline">ホーム</span>
            </NuxtLink>

            <!-- 友達ボタン -->
            <NuxtLink
              v-if="currentPage !== 'friends'"
              to="/friends"
              class="inline-flex items-center px-2 py-2 sm:px-3 text-xs sm:text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-150 ease-in-out"
            >
              <svg
                class="w-4 h-4"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"
                />
              </svg>
              <span class="hidden sm:inline">友達</span>
            </NuxtLink>

            <!-- チャットボタン -->
            <NuxtLink
              v-if="currentPage !== 'chat'"
              to="/chat"
              class="inline-flex items-center px-2 py-2 sm:px-3 text-xs sm:text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition duration-150 ease-in-out"
            >
              <svg
                class="w-4 h-4"
                viewBox="0 0 20 20"
                fill="currentColor"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"
                />
                <path
                  d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"
                />
              </svg>
              <span class="hidden sm:inline">チャット</span>
            </NuxtLink>
          </div>
        </div>
      </div>
    </nav>

    <main class="">
      <slot />
    </main>

    <!-- 共通フッター (chat/[room_token] ページでは非表示) -->
    <footer
      v-if="!isChatRoomPage"
      class="bg-white py-4 border-t border-gray-200"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-500">
            &copy; {{ new Date().getFullYear() }} LumoChat. All Rights Reserved.
          </div>
          <div>
            <button
              class="inline-flex items-center px-3 py-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition duration-150 ease-in-out"
              @click="openSupportChat"
            >
              <svg
                class="w-4 h-4 mr-1"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                  clip-rule="evenodd"
                />
              </svg>
              Support
            </button>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "~/stores/auth";

// ルートを取得して現在のページを判断
const route = useRoute();
const router = useRouter();
const config = useRuntimeConfig();

// 現在のページを判断する関数
const currentPage = computed(() => {
  const path = route.path;
  if (path === "/user" || path.startsWith("/user/")) return "user";
  if (path === "/friends" || path.startsWith("/friends/")) return "friends";
  if (path === "/chat" || path.startsWith("/chat/")) return "chat";
  return null;
});

// chat/[room_token] ページかどうかを判断
const isChatRoomPage = computed(() => {
  return route.path.match(/^\/chat\/[^/]+\/?$/);
});

// サポートチャットを開く関数
const openSupportChat = async () => {
  try {
    const authStore = useAuthStore();

    // 認証チェック
    if (!authStore.isAuthenticated) {
      // 認証されていない場合はログインページにリダイレクト
      router.push("/auth/login");
      return;
    }

    // サポート会話を作成または取得
    const conversation = await $fetch<{ room_token: string }>(
      `${config.public.apiBase}/support/conversation`,
      {
        method: "POST",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${authStore.token}`,
        },
      }
    );

    if (conversation && conversation.room_token) {
      // チャットページに遷移
      router.push(`/chat/${conversation.room_token}/`);
    }
  } catch (error) {
    console.error("サポートチャットの開始に失敗しました:", error);
    // エラーハンドリング（必要に応じてトーストメッセージを表示）
  }
};
</script>
