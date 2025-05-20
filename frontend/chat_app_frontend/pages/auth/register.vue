<!-- pages/auth/register.vue -->
<template>
  <div
    class="min-h-screen flex flex-col justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8"
  >
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="text-center text-3xl font-extrabold text-gray-900">
        アカウント登録
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        チャットアプリに新規登録
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form class="space-y-6" @submit.prevent="onRegister">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700"
              >名前</label
            >
            <div class="mt-1">
              <input
                id="name"
                v-model="form.name"
                type="text"
                autocomplete="name"
                required
                placeholder="あなたの名前"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
            </div>
            <p v-if="nameError" class="mt-2 text-sm text-red-600">
              {{ nameError }}
            </p>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700"
              >メールアドレス</label
            >
            <div class="mt-1">
              <input
                id="email"
                v-model="form.email"
                type="email"
                autocomplete="email"
                required
                placeholder="your@email.com"
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
                v-model="form.password"
                type="password"
                autocomplete="new-password"
                required
                placeholder="8文字以上のパスワード"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
            </div>
            <p v-if="passwordError" class="mt-2 text-sm text-red-600">
              {{ passwordError }}
            </p>
          </div>

          <div>
            <label
              for="password_confirmation"
              class="block text-sm font-medium text-gray-700"
              >パスワード（確認）</label
            >
            <div class="mt-1">
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                autocomplete="new-password"
                required
                placeholder="同じパスワードを入力"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
            </div>
            <p v-if="confirmPasswordError" class="mt-2 text-sm text-red-600">
              {{ confirmPasswordError }}
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
              <span>アカウント登録</span>
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
            <p class="text-sm text-gray-600">
              すでにアカウントをお持ちですか？
              <NuxtLink
                to="/auth/login"
                class="font-medium text-blue-600 hover:text-blue-500"
              >
                ログイン
              </NuxtLink>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from "vue";
import { useAuthStore } from "~/stores/auth";
import { useToast } from "~/composables/useToast";
import { useRouter } from "vue-router";

// ページメタデータの設定
definePageMeta({
  title: "アカウント登録",
  layout: "default",
});

// エラーメッセージ
const nameError = ref("");
const emailError = ref("");
const passwordError = ref("");
const confirmPasswordError = ref("");

// フォームの状態
const form = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const authStore = useAuthStore();
const loading = computed(() => authStore.loading);
const toast = useToast();
const router = useRouter();

// バリデーション関数
function validateForm() {
  let isValid = true;

  // リセット
  nameError.value = "";
  emailError.value = "";
  passwordError.value = "";
  confirmPasswordError.value = "";

  if (!form.name.trim()) {
    nameError.value = "名前を入力してください";
    isValid = false;
  }

  if (!form.email.trim()) {
    emailError.value = "メールアドレスを入力してください";
    isValid = false;
  } else if (!/^\S+@\S+\.\S+$/.test(form.email)) {
    emailError.value = "有効なメールアドレスを入力してください";
    isValid = false;
  }

  if (form.password.length < 8) {
    passwordError.value = "パスワードは8文字以上で入力してください";
    isValid = false;
  }

  if (form.password !== form.password_confirmation) {
    confirmPasswordError.value = "パスワードが一致しません";
    isValid = false;
  }

  return isValid;
}

// 登録処理
async function onRegister() {
  if (!validateForm()) {
    return;
  }

  try {
    const result = await authStore.register(
      form.name,
      form.email,
      form.password,
      form.password_confirmation
    );

    if (result.success) {
      toast.add({
        title: "登録成功",
        description: result.message,
        color: "success",
      });

      // 確認ページに遷移
      router.push("/auth/verification");
    } else {
      toast.add({
        title: "登録エラー",
        description: result.message,
        color: "error",
      });
    }
  } catch (error: any) {
    toast.add({
      title: "エラー",
      description: error.message || "予期せぬエラーが発生しました",
      color: "error",
    });
  }
}
</script>
