<template>
  <div class="min-h-screen flex flex-col justify-center">
    <div
      class="mx-auto w-full max-w-md bg-white rounded-lg shadow-md p-6 text-center"
    >
      <h1 class="text-xl font-semibold mb-6">メール確認</h1>

      <div class="py-4">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="text-5xl mb-4 mx-auto h-12 w-12"
          style="color: var(--primary)"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
          />
          <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
        </svg>
        <p class="mb-4">
          登録したメールアドレスに確認メールを送信しました。<br />
          メール内のリンクをクリックして登録を完了してください。
        </p>

        <button
          :disabled="loading"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
          @click="resendVerification"
        >
          <svg
            v-if="loading"
            xmlns="http://www.w3.org/2000/svg"
            class="animate-spin h-4 w-4 mr-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
              clip-rule="evenodd"
            />
          </svg>
          確認メールを再送信
        </button>
      </div>

      <div class="mt-6 pt-4 border-t border-gray-200">
        <NuxtLink
          to="/auth/login"
          class="text-blue-600 hover:underline text-sm"
        >
          ログインページへ戻る
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useToast } from "~/composables/useToast";

definePageMeta({
  layout: "default",
  title: "メール確認",
});

const toast = useToast();
const loading = ref(false);

// 確認メール再送信（実際の実装ではAPIとの連携が必要）
const resendVerification = async () => {
  loading.value = true;

  try {
    // APIリクエストをここに実装
    // 仮の実装として1秒待機
    await new Promise((resolve) => setTimeout(resolve, 1000));

    toast.add({
      title: "送信完了",
      description: "確認メールを再送信しました",
      color: "success",
    });
  } catch (err: unknown) {
    console.error(err);
    toast.add({
      title: "エラー",
      description: "確認メールの送信に失敗しました",
      color: "error",
    });
  } finally {
    loading.value = false;
  }
};
</script>
