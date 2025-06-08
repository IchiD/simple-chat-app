<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8 text-center">
      <div class="mb-6">
        <div
          class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4"
        >
          <svg
            class="w-8 h-8 text-red-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.962-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
            />
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
          ゲスト機能は廃止されました
        </h1>
        <p class="text-gray-600 mb-6">
          グループチャットをご利用いただくには、ログインが必要になりました。
        </p>
      </div>

      <div class="space-y-4">
        <NuxtLink
          to="/auth/login"
          class="w-full inline-block py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-center"
        >
          ログインページへ
        </NuxtLink>

        <NuxtLink
          to="/auth/register"
          class="w-full inline-block py-2 px-4 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-center"
        >
          アカウント登録
        </NuxtLink>

        <NuxtLink
          to="/"
          class="w-full inline-block py-2 px-4 text-gray-500 hover:text-gray-700 text-center"
        >
          ホームページに戻る
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: "default",
  title: "ゲスト機能は廃止されました",
  auth: false,
});
</script>
