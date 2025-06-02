<template>
  <div class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center">
      <div v-if="loading" class="flex flex-col items-center">
        <svg
          class="animate-spin h-12 w-12 text-blue-600"
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
        <p class="mt-4 text-lg text-gray-600">Google認証を処理中...</p>
      </div>

      <div v-else-if="error" class="flex flex-col items-center">
        <div class="rounded-full bg-red-100 p-4">
          <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.962-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>
        <h2 class="mt-4 text-lg font-semibold text-gray-900">認証エラー</h2>
        <p class="mt-2 text-gray-600">{{ error }}</p>
        <NuxtLink
          to="/auth/login"
          class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          ログインページに戻る
        </NuxtLink>
      </div>

      <div v-else class="flex flex-col items-center">
        <div class="rounded-full bg-green-100 p-4">
          <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h2 class="mt-4 text-lg font-semibold text-gray-900">認証完了</h2>
        <p class="mt-2 text-gray-600">Googleアカウントでのログインが完了しました。</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../../../stores/auth';
import { useToast } from '../../../composables/useToast';
import { useRouter, useRoute } from 'vue-router';

definePageMeta({
  layout: 'default',
  title: 'Google認証処理中',
});

const authStore = useAuthStore();
const toast = useToast();
const router = useRouter();
const route = useRoute();

const loading = ref(true);
const error = ref<string | null>(null);

onMounted(async () => {
  const route = useRoute();
  const router = useRouter();
  const authStore = useAuthStore();

  // URLパラメータからトークンとユーザーデータを取得
  const token = route.query.token as string;
  const user = route.query.user as string;

  if (!token || !user) {
    // パラメータが不足している場合はエラーページへ
    toast.add({
      title: "認証エラー",
      description: "認証情報が不足しています",
      color: "error",
    });
    router.push("/auth/login");
    return;
  }

  try {
    // 認証ストアにGoogle認証情報を保存
    const result = await authStore.handleGoogleCallback(token, user);

    if (result.success) {
      toast.add({
        title: "ログイン成功",
        description: "Google認証でログインしました",
        color: "success",
      });
      // 認証成功後はユーザーページへリダイレクト
      router.push("/user");
    } else {
      throw new Error(result.message || "Google認証に失敗しました");
    }
  } catch (error) {
    console.error("Google認証エラー:", error);
    toast.add({
      title: "認証エラー",
      description: "Google認証処理中にエラーが発生しました",
      color: "error",
    });
    router.push("/auth/login");
  } finally {
    loading.value = false;
  }
});
</script>