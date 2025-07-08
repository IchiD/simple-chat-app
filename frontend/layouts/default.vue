<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <!-- 共通ヘッダー (chat/[room_token] ページとルートページでは非表示) -->
    <nav
      v-if="!shouldHideNavFooter"
      class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0"
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
              v-if="authStore.isAuthenticated"
              :to="currentPage === 'user' ? '#' : '/user'"
              :class="[
                'inline-flex items-center px-2 py-2 sm:px-3 text-xs sm:text-sm font-medium rounded-lg transition duration-150 ease-in-out',
                currentPage === 'user'
                  ? 'text-emerald-600 bg-emerald-100 cursor-not-allowed'
                  : 'text-gray-700 bg-gray-100 hover:bg-gray-200',
              ]"
              @click="currentPage === 'user' ? $event.preventDefault() : null"
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
              v-if="authStore.isAuthenticated"
              :to="currentPage === 'friends' ? '#' : '/friends'"
              :class="[
                'inline-flex items-center px-2 py-2 sm:px-3 text-xs sm:text-sm font-medium rounded-lg transition duration-150 ease-in-out',
                currentPage === 'friends'
                  ? 'text-emerald-600 bg-emerald-100 cursor-not-allowed'
                  : 'text-gray-700 bg-gray-100 hover:bg-gray-200',
              ]"
              @click="
                currentPage === 'friends' ? $event.preventDefault() : null
              "
            >
              <svg
                class="w-4 h-4"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                  clip-rule="evenodd"
                />
              </svg>
              <span class="hidden sm:inline">友達</span>
            </NuxtLink>

            <!-- グループボタン（有料プランユーザーのみ） -->
            <NuxtLink
              v-if="authStore.isAuthenticated && isPaidUser"
              :to="currentPage === 'groups' ? '#' : '/user/groups'"
              :class="[
                'inline-flex items-center px-2 py-2 sm:px-3 text-xs sm:text-sm font-medium rounded-lg transition duration-150 ease-in-out',
                currentPage === 'groups'
                  ? 'text-emerald-600 bg-emerald-100 cursor-not-allowed'
                  : 'text-gray-700 bg-gray-100 hover:bg-gray-200',
              ]"
              @click="currentPage === 'groups' ? $event.preventDefault() : null"
            >
              <svg
                class="w-4 h-4"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                />
              </svg>
              <span class="hidden sm:inline">グループ</span>
            </NuxtLink>

            <!-- チャットボタン -->
            <NuxtLink
              v-if="authStore.isAuthenticated"
              :to="currentPage === 'chat' ? '#' : '/chat'"
              :class="[
                'inline-flex items-center px-2 py-2 sm:px-3 text-xs sm:text-sm font-medium rounded-lg transition duration-150 ease-in-out',
                currentPage === 'chat'
                  ? 'text-emerald-600 bg-emerald-100 cursor-not-allowed'
                  : 'text-gray-700 bg-gray-100 hover:bg-gray-200',
              ]"
              @click="currentPage === 'chat' ? $event.preventDefault() : null"
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

    <main class="flex-grow">
      <slot />
    </main>

    <!-- 共通フッター (chat/[room_token] ページとルートページでは非表示) -->
    <footer
      v-if="!shouldHideNavFooter"
      class="bg-white py-4 border-t border-gray-200 flex-shrink-0"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-500">
            &copy; {{ new Date().getFullYear() }} LumoChat. All Rights Reserved.
          </div>
          <div>
            <NuxtLink
              to="/support"
              class="inline-flex items-center px-3 py-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition duration-150 ease-in-out"
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
              お問い合わせ
            </NuxtLink>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "~/stores/auth";

// 認証ストアの初期化
const authStore = useAuthStore();

// ルートを取得して現在のページを判断
const route = useRoute();

// 現在のページを判断する関数
const currentPage = computed(() => {
  const path = route.path;
  // 完全一致でuserページかどうかを判定
  if (path === "/user") return "user";
  if (path === "/friends" || path.startsWith("/friends/")) return "friends";
  if (path === "/chat" || path.startsWith("/chat/")) return "chat";
  if (path === "/user/groups" || path.startsWith("/user/groups/"))
    return "groups";
  return null;
});

// ヘッダーとフッターを非表示にするページかどうかを判断
const shouldHideNavFooter = computed(() => {
  // chat/[room_token] ページ または ルートページ
  return route.path.match(/^\/chat\/[^/]+\/?$/) || route.path === "/";
});

// 有料プランユーザーかどうかを判定
const isPaidUser = computed(() => {
  const userPlan = authStore.user?.plan;
  return userPlan && userPlan !== "free";
});
</script>
