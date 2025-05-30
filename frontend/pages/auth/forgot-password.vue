<template>
  <div class="bg-gray-50" style="height: calc(100vh - 7.5rem)">
    <div class="flex flex-col justify-center h-full py-12 px-4 sm:px-6 lg:px-8">
      <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="text-center text-3xl font-extrabold text-gray-900">
          パスワード再設定
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          登録済みのメールアドレスを入力してください。
          <br />パスワード再設定用のリンクを送信します。
        </p>
      </div>

      <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
          <form class="space-y-6" @submit.prevent="handleSubmit">
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700"
                >メールアドレス</label
              >
              <div class="mt-1">
                <input
                  id="email"
                  v-model="email"
                  type="email"
                  autocomplete="email"
                  required
                  placeholder="メールアドレス"
                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  :disabled="loading"
                />
              </div>
              <p v-if="emailError" class="mt-2 text-sm text-red-600">
                {{ emailError }}
              </p>
            </div>

            <div>
              <button
                type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                :class="{
                  'opacity-75 cursor-not-allowed': loading || emailSent,
                }"
                :disabled="loading || emailSent"
              >
                <svg
                  v-if="loading"
                  class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
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
                <span v-if="!emailSent">再設定メールを送信</span>
                <span v-else>送信済み</span>
              </button>
            </div>
          </form>
          <div
            v-if="emailSent"
            class="mt-4 p-3 bg-green-50 rounded-md border border-green-200"
          >
            <div class="flex">
              <div class="flex-shrink-0">
                <svg
                  class="h-5 w-5 text-green-400"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                  再設定メールを送信しました。メールボックスをご確認ください。
                </p>
              </div>
            </div>
          </div>

          <div class="mt-6 text-center">
            <NuxtLink
              to="/auth/login"
              class="font-medium text-blue-600 hover:text-blue-500 text-sm"
            >
              ログインページへ戻る
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useApi } from "../../composables/useApi"; // composablesのパスを修正
import { useToast } from "../../composables/useToast"; // composablesのパスを修正
import { FetchError } from "ofetch";

definePageMeta({
  layout: "default",
  title: "パスワード再設定",
});

const { api } = useApi();
const toast = useToast();
const email = ref("");
const emailError = ref("");
const loading = ref(false);
const emailSent = ref(false);

const validateEmail = () => {
  emailError.value = "";
  if (!email.value) {
    emailError.value = "メールアドレスを入力してください";
    return false;
  } else if (!/^\S+@\S+\.\S+$/.test(email.value)) {
    emailError.value = "有効なメールアドレスを入力してください";
    return false;
  }
  return true;
};

const handleSubmit = async () => {
  if (!validateEmail()) {
    return;
  }
  loading.value = true;
  emailSent.value = false;

  try {
    await api("/password/email", {
      // バックエンドのAPIエンドポイント
      method: "POST",
      body: { email: email.value },
    });
    emailSent.value = true;
    toast.add({
      title: "メール送信成功",
      description: "パスワード再設定用のメールを送信しました。",
      color: "success",
    });
  } catch (error) {
    console.error("パスワード再設定メール送信エラー:", error);
    let message =
      "メールの送信に失敗しました。時間をおいて再度お試しください。";
    if (error instanceof FetchError && error.data) {
      const errorData = error.data as {
        message?: string;
        error_type?: string;
        errors?: Record<string, string[]>;
      };
      if (errorData.error_type === "google_user") {
        toast.add({
          title: "Google認証アカウントです",
          description:
            errorData.message ||
            "このアカウントはGoogle認証でログインしてください。",
          color: "info",
          timeout: 8000,
        });
        return;
      }
      if (errorData.errors) {
        emailError.value = errorData.errors.email
          ? errorData.errors.email[0]
          : errorData.message;
      } else if (errorData.message) {
        message = errorData.message;
      }
    }
    toast.add({
      title: "エラー",
      description: message,
      color: "error",
    });
  } finally {
    loading.value = false;
  }
};
</script>
