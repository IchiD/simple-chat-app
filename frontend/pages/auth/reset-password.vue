<template>
  <div class="bg-gray-50" style="height: calc(100vh - 7.5rem)">
    <div class="flex flex-col justify-center h-full py-12 px-4 sm:px-6 lg:px-8">
      <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="text-center text-3xl font-extrabold text-gray-900">
          新しいパスワードの設定
        </h2>
      </div>

      <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
          <form
            v-if="!resetSuccess"
            class="space-y-6"
            @submit.prevent="handleSubmit"
          >
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700"
                >メールアドレス</label
              >
              <div class="mt-1">
                <p
                  class="px-3 py-2 block w-full rounded-md border border-gray-200 bg-gray-50 text-gray-700 sm:text-sm"
                >
                  {{ form.email || "読み込み中..." }}
                </p>
              </div>
            </div>
            <div>
              <label
                for="password"
                class="block text-sm font-medium text-gray-700"
                >新しいパスワード</label
              >
              <div class="mt-1">
                <input
                  id="password"
                  v-model="form.password"
                  type="password"
                  required
                  placeholder="新しいパスワード"
                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  :disabled="loading"
                />
              </div>
              <p v-if="errors.password" class="mt-2 text-sm text-red-600">
                {{ errors.password[0] }}
              </p>
            </div>

            <div>
              <label
                for="password_confirmation"
                class="block text-sm font-medium text-gray-700"
                >新しいパスワード（確認）</label
              >
              <div class="mt-1">
                <input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  type="password"
                  required
                  placeholder="新しいパスワードを再入力"
                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  :disabled="loading"
                />
              </div>
            </div>

            <div>
              <button
                type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                :class="{ 'opacity-75 cursor-not-allowed': loading }"
                :disabled="loading"
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
                <span>パスワードを再設定</span>
              </button>
            </div>
          </form>
          <div
            v-if="resetSuccess"
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
                  パスワードが正常に再設定されました。
                </p>
                <NuxtLink
                  to="/auth/login"
                  class="font-medium text-blue-600 hover:text-blue-500 text-sm block mt-1"
                >
                  ログインページへ進む
                </NuxtLink>
              </div>
            </div>
          </div>
          <div class="mt-6 text-center">
            <NuxtLink
              to="/auth/forgot-password"
              class="font-medium text-blue-600 hover:text-blue-500 text-sm"
            >
              再設定メールをもう一度送信
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useApi } from "../../composables/useApi";
import { useToast } from "../../composables/useToast";
import { FetchError } from "ofetch";

definePageMeta({
  layout: "default",
  title: "新しいパスワードの設定",
});

const route = useRoute();
const router = useRouter();
const { api } = useApi();
const toast = useToast();

const form = reactive({
  token: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const errors = ref<Record<string, string[]>>({});
const loading = ref(false);
const resetSuccess = ref(false);

onMounted(() => {
  const tokenFromQuery = route.query.token as string;
  const emailFromQuery = route.query.email as string;

  if (tokenFromQuery) {
    form.token = tokenFromQuery;
  } else {
    toast.add({
      title: "エラー",
      description:
        "無効なトークンです。パスワード再設定メールを再度送信してください。",
      color: "error",
    });
    router.push("/auth/forgot-password");
    return;
  }

  if (emailFromQuery) {
    form.email = emailFromQuery;
  } else {
    toast.add({
      title: "エラー",
      description: "無効なリンクです。メールアドレス情報が含まれていません。",
      color: "error",
    });
    router.push("/auth/forgot-password");
    return;
  }
});

const handleSubmit = async () => {
  loading.value = true;
  errors.value = {};
  resetSuccess.value = false;

  try {
    await api("/password/reset", {
      // バックエンドのAPIエンドポイント
      method: "POST",
      body: { ...form },
    });
    resetSuccess.value = true;
    toast.add({
      title: "成功",
      description: "パスワードが正常に再設定されました。",
      color: "success",
    });
    // フォームをクリア (任意)
    form.email = "";
    form.password = "";
    form.password_confirmation = "";
    // router.push("/auth/login"); // 自動でログインページに遷移させる場合
  } catch (error) {
    console.error("パスワードリセットエラー:", error);
    if (error instanceof FetchError && error.data) {
      const errorData = error.data as {
        message?: string;
        errors?: Record<string, string[]>;
      };
      if (errorData.message && !errorData.errors) {
        // errorsがない場合は一般的なメッセージとして表示
        toast.add({
          title: "エラー",
          description: errorData.message,
          color: "error",
        });
      }
      if (errorData.errors) {
        errors.value = errorData.errors;
        toast.add({
          title: "入力エラー",
          description: "入力内容を確認してください。",
          color: "error",
        });
      } else if (!errorData.message && !errorData.errors) {
        toast.add({
          title: "エラー",
          description: "パスワードの再設定に失敗しました。",
          color: "error",
        });
      }
    } else {
      toast.add({
        title: "エラー",
        description: "予期せぬエラーが発生しました。",
        color: "error",
      });
    }
  } finally {
    loading.value = false;
  }
};
</script>
