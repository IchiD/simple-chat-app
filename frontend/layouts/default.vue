<template>
  <div>
    <!-- デバッグ用ダークモードインジケーター -->
    <div class="dark-mode-indicator">
      ダークモード有効
    </div>
    
    <!-- 共通ヘッダー -->
    <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
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
              class="inline-flex items-center px-2 py-2 sm:px-3 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition duration-150 ease-in-out"
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
              class="inline-flex items-center px-2 py-2 sm:px-3 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition duration-150 ease-in-out"
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
              class="inline-flex items-center px-2 py-2 sm:px-3 text-xs sm:text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-800 rounded-lg transition duration-150 ease-in-out"
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

            <!-- ダークモード切り替えボタン -->
            <button
              @click="toggleDarkMode"
              class="inline-flex items-center px-2 py-2 sm:px-3 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition duration-150 ease-in-out"
              :title="isDarkMode ? 'ライトモードに切り替え' : 'ダークモードに切り替え'"
            >
              <!-- ライトモード時（太陽アイコン） -->
              <svg
                v-if="!isDarkMode"
                class="w-4 h-4"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                  clip-rule="evenodd"
                />
              </svg>
              <!-- ダークモード時（月アイコン） -->
              <svg
                v-else
                class="w-4 h-4"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"
                />
              </svg>
              <span class="hidden sm:inline ml-1">{{ isDarkMode ? 'ライト' : 'ダーク' }}</span>
            </button>
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
      class="bg-white dark:bg-gray-800 py-4 border-t border-gray-200 dark:border-gray-700"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-500 dark:text-gray-400">
            &copy; {{ new Date().getFullYear() }} LumoChat. All Rights Reserved.
          </div>
          <div>
            <button
              class="inline-flex items-center px-3 py-1 text-sm font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition duration-150 ease-in-out"
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
import { ref, onMounted } from 'vue'
import { useAuthStore } from "~/stores/auth";

// ダークモード状態
const isDarkMode = ref(false)

// ダークモード切り替え関数
const toggleDarkMode = () => {
  alert('ダークモード切り替えボタンがクリックされました！')
  console.log('ダークモード切り替え前:', isDarkMode.value)
  isDarkMode.value = !isDarkMode.value
  console.log('ダークモード切り替え後:', isDarkMode.value)
  
  // HTMLクラスを更新
  if (typeof document !== 'undefined') {
    if (isDarkMode.value) {
      document.documentElement.classList.add('dark')
      console.log('ダーククラス追加')
    } else {
      document.documentElement.classList.remove('dark')
      console.log('ダーククラス削除')
    }
    
    // ローカルストレージに保存
    localStorage.setItem('darkMode', isDarkMode.value.toString())
  }
}

// 初期化
onMounted(() => {
  console.log('ダークモード初期化開始')
  
  // ローカルストレージから読み込み
  if (typeof window !== 'undefined') {
    const stored = localStorage.getItem('darkMode')
    if (stored !== null) {
      isDarkMode.value = stored === 'true'
    } else {
      // システム設定を参照
      isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }
    
    // HTMLクラスを設定
    if (isDarkMode.value) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
    
    console.log('ダークモード初期化完了:', isDarkMode.value)
  }
})

// 認証ストア
const authStore = useAuthStore();

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
    // 認証チェック
    if (!authStore.isAuthenticated) {
      // 認証されていない場合はログインページにリダイレクト
      router.push("/auth/login");
      return;
    }

    // サポート会話を作成または取得
    const conversation = await $fetch<any>(
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
