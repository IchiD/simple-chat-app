<template>
  <div class="bg-gray-50" style="height: calc(100vh - 7.5rem)">
    <div class="flex flex-col justify-center h-full py-12 px-4 sm:px-6 lg:px-8">
      <div
        class="mx-auto w-full max-w-md bg-white rounded-lg shadow-md p-6 text-center"
      >
        <h1 class="text-xl font-semibold mb-6">メール認証</h1>

        <div class="py-4">
          <template v-if="loading">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="text-5xl mb-4 text-blue-500 animate-spin mx-auto h-12 w-12"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                clip-rule="evenodd"
              />
            </svg>
            <p class="mb-4">認証中です。しばらくお待ちください...</p>
          </template>

          <template v-else-if="error">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="text-5xl mb-4 text-red-500 mx-auto h-12 w-12"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                clip-rule="evenodd"
              />
            </svg>
            <p class="mb-4 text-red-600">{{ error }}</p>
            <NuxtLink
              to="/auth/login"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              ログインページへ
            </NuxtLink>
          </template>

          <template v-else-if="success">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="text-5xl mb-4 text-green-500 mx-auto h-12 w-12"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
            <p class="mb-4">{{ message }}</p>
            <p class="text-sm text-gray-500">
              ユーザーページに自動的に移動します...
            </p>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useAuthStore } from "~/stores/auth";
import { useRoute, useRouter } from "vue-router";
const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const loading = ref(true);
const error = ref<string | null>(null);
const success = ref(false);
const message = ref("");

const verifyToken = async (token: string) => {
  try {
    const result = await authStore.verifyEmail(token);

    if (result.success) {
      success.value = true;
      message.value = result.message || "メールアドレスの認証が完了しました";

      // 成功したら3秒後にユーザーページへリダイレクト
      setTimeout(() => {
        router.push("/user");
      }, 3000);
    } else {
      error.value = result.message || "認証に失敗しました";
    }
  } catch (err: unknown) {
    console.error("Verification error:", err);
    error.value =
      err instanceof Error ? err.message : "認証処理中にエラーが発生しました";
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  const token = route.query.token as string;

  if (!token) {
    error.value = "認証トークンが見つかりません";
    loading.value = false;
    return;
  }

  verifyToken(token);
});
</script>
