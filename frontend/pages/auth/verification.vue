<template>
  <div class="bg-gray-50" style="height: calc(100vh - 7.5rem)">
    <div class="flex flex-col justify-center h-full py-12 px-4 sm:px-6 lg:px-8">
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
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useToast } from "~/composables/useToast";
import { useApi } from "~/composables/useApi";

definePageMeta({
  layout: "default",
  title: "メール確認",
});

const toast = useToast();
const loading = ref(false);

// 新規登録時のメールアドレスを取得
const email = ref("");

// レスポンス型定義
interface ResendVerificationResponse {
  status: string;
  message: string;
}

// ページマウント時に登録時のメールアドレスを取得（sessionStorageから）
onMounted(() => {
  if (import.meta.client) {
    const registeredEmail = sessionStorage.getItem("registered_email");
    if (registeredEmail) {
      email.value = registeredEmail;
    } else {
      // sessionStorageにメールアドレスがない場合は登録ページにリダイレクト
      toast.add({
        title: "エラー",
        description: "登録情報が見つかりません。再度登録してください。",
        color: "error",
      });
      navigateTo("/auth/register");
    }
  }
});

// 確認メール再送信
const resendVerification = async () => {
  if (!email.value) {
    toast.add({
      title: "エラー",
      description: "メールアドレスが取得できません。再度登録してください。",
      color: "error",
    });
    return;
  }

  loading.value = true;

  try {
    const { api } = useApi();
    const response = await api<ResendVerificationResponse>(
      "/resend-verification",
      {
        method: "POST",
        body: { email: email.value },
      }
    );

    if (response.status === "success") {
      toast.add({
        title: "送信完了",
        description: response.message,
        color: "success",
      });
    } else {
      toast.add({
        title: "エラー",
        description: response.message || "確認メールの送信に失敗しました",
        color: "error",
      });
    }
  } catch (error: unknown) {
    console.error("メール再送信エラー:", error);

    let errorMessage = "確認メールの送信に失敗しました";
    if (error && typeof error === "object" && "data" in error) {
      const errorData = error.data as { message?: string };
      if (errorData.message) {
        errorMessage = errorData.message;
      }
    }

    toast.add({
      title: "エラー",
      description: errorMessage,
      color: "error",
    });
  } finally {
    loading.value = false;
  }
};
</script>
