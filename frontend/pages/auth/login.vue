<template>
  <div class="bg-gray-50" style="height: calc(100vh - 7.5rem)">
    <div class="flex flex-col justify-center h-full py-12 px-4 sm:px-6 lg:px-8">
      <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="text-center text-3xl font-extrabold text-gray-900">
          ログイン
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          アカウント情報でログインしてください
        </p>
      </div>

      <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
          <form class="space-y-6" @submit.prevent="onSubmit">
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700"
                >メールアドレス</label
              >
              <div class="mt-1">
                <input
                  id="email"
                  v-model="formState.email"
                  type="email"
                  autocomplete="email"
                  required
                  placeholder="メールアドレス"
                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
              </div>
              <p v-if="emailError" class="mt-2 text-sm text-red-600">
                {{ emailError }}
              </p>
            </div>

            <div>
              <label
                for="password"
                class="block text-sm font-medium text-gray-700"
                >パスワード</label
              >
              <div class="mt-1">
                <input
                  id="password"
                  v-model="formState.password"
                  type="password"
                  autocomplete="current-password"
                  required
                  placeholder="パスワード"
                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
              </div>
              <p v-if="passwordError" class="mt-2 text-sm text-red-600">
                {{ passwordError }}
              </p>
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
                <span>ログイン</span>
              </button>
            </div>
          </form>

          <div class="mt-6">
            <div class="relative">
              <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300" />
              </div>
              <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">または</span>
              </div>
            </div>

            <div class="mt-6 text-center">
              <p class="text-sm text-gray-600 mb-2">
                <NuxtLink
                  to="/auth/forgot-password"
                  class="font-medium text-blue-600 hover:text-blue-500"
                >
                  パスワードをお忘れの方はこちら
                </NuxtLink>
              </p>
              <p class="text-sm text-gray-600">
                アカウントをお持ちでないですか？
                <NuxtLink
                  to="/auth/register"
                  class="font-medium text-blue-600 hover:text-blue-500"
                >
                  アカウント登録
                </NuxtLink>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from "vue";
import { useAuthStore } from "../../stores/auth";
import { useToast } from "../../composables/useToast";
import { useRouter } from "vue-router";
import { FetchError } from "ofetch";

definePageMeta({
  layout: "default",
  title: "ログイン",
});

const toast = useToast();
const authStore = useAuthStore();
const loading = ref(false);
const router = useRouter();

// エラーメッセージ
const emailError = ref("");
const passwordError = ref("");

// ログイン済みの場合はユーザーページにリダイレクト
onMounted(async () => {
  loading.value = true;

  try {
    // 認証状態をチェック
    await authStore.checkAuth();

    // 既にログイン済みの場合はユーザーページにリダイレクト
    if (authStore.isAuthenticated) {
      console.log("既にログイン済みのため/userにリダイレクト");
      router.push("/user");
    }
  } catch (error) {
    console.error("認証チェックエラー:", error);
  } finally {
    loading.value = false;
  }
});

// フォームの状態
const formState = reactive({
  email: "",
  password: "",
});

// バリデーション
const validateForm = () => {
  let isValid = true;
  emailError.value = "";
  passwordError.value = "";

  if (!formState.email) {
    emailError.value = "メールアドレスを入力してください";
    isValid = false;
  } else if (!/^\S+@\S+\.\S+$/.test(formState.email)) {
    emailError.value = "有効なメールアドレスを入力してください";
    isValid = false;
  }

  if (!formState.password) {
    passwordError.value = "パスワードを入力してください";
    isValid = false;
  }

  return isValid;
};

// フォーム送信処理
const onSubmit = async () => {
  if (!validateForm()) {
    return;
  }

  loading.value = true;

  try {
    const result = await authStore.login(formState.email, formState.password);

    if (result.success) {
      toast.add({
        title: "ログイン成功",
        description: "ログインに成功しました",
        color: "success",
      });

      // ユーザーホームにリダイレクト
      router.push("/user");
    } else {
      toast.add({
        title: "ログインエラー",
        description: result.message,
        color: "error",
      });
    }
  } catch (error) {
    let message = "ログイン処理中にエラーが発生しました";
    if (error instanceof FetchError && error.data) {
      const errorData = error.data as { message?: string };
      if (errorData.message) {
        message = errorData.message;
      }
    } else if (error instanceof Error) {
      message = error.message;
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
