<!-- pages/auth/register.vue -->
<template>
  <div class="bg-gradient-to-br min-h-full">
    <div class="relative flex antialiased text-gray-800 min-h-full">
      <div class="flex min-h-full w-full">
        <div class="w-full min-h-full overflow-y-auto">
          <div class="flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
              <h2 class="text-center text-3xl font-extrabold text-gray-900">
                アカウント登録
              </h2>
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
              <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <!-- Googleで登録ボタン（推奨） -->
                <div class="mb-6">
                  <button
                    type="button"
                    :disabled="loading"
                    class="w-full flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    @click="onGoogleRegister"
                  >
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                      <path
                        fill="#4285F4"
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                      />
                      <path
                        fill="#34A853"
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                      />
                      <path
                        fill="#FBBC05"
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                      />
                      <path
                        fill="#EA4335"
                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                      />
                    </svg>
                    Googleで登録
                  </button>
                </div>

                <!-- 区切り線 -->
                <div class="mb-6">
                  <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                      <div class="w-full border-t border-gray-300" />
                    </div>
                    <div class="relative flex justify-center text-sm">
                      <span class="px-2 bg-white text-gray-500">または</span>
                    </div>
                  </div>
                </div>

                <!-- 通常の登録フォーム -->
                <form class="space-y-6" @submit.prevent="onRegister">
                  <div>
                    <label
                      for="name"
                      class="block text-sm font-medium text-gray-700"
                      >名前</label
                    >
                    <div class="mt-1">
                      <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        autocomplete="name"
                        required
                        placeholder="10文字以内"
                        :class="[
                          'appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none sm:text-sm',
                          nameError
                            ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                            : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500',
                        ]"
                      />
                    </div>
                    <p v-if="nameError" class="mt-2 text-sm text-red-600">
                      {{ nameError }}
                    </p>
                  </div>

                  <div>
                    <label
                      for="email"
                      class="block text-sm font-medium text-gray-700"
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
                    <p
                      v-if="confirmPasswordError"
                      class="mt-2 text-sm text-red-600"
                    >
                      {{ confirmPasswordError }}
                    </p>
                  </div>

                  <div>
                    <button
                      type="submit"
                      class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                      :disabled="loading || isFormInvalid"
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

                <!-- ログインへのリンク -->
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
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from "vue";
import { useAuthStore } from "../../stores/auth";
import { useToast } from "../../composables/useToast";
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

// name を監視してリアルタイムバリデーション
watch(
  () => form.name,
  (newName) => {
    if (newName.trim().length > 10) {
      nameError.value = "名前は10文字以内で入力してください";
    } else if (
      !newName.trim() &&
      nameError.value === "名前は10文字以内で入力してください"
    ) {
      // 10文字超過のエラーが出ている状態で空になった場合は、"入力してください" のエラーに任せるか、クリアする
      // ここでは一旦クリアし、validateFormに任せる
      nameError.value = "";
    } else if (nameError.value === "名前は10文字以内で入力してください") {
      // 10文字超過エラーから復帰した場合
      nameError.value = "";
    }
    // 空かどうかのチェックはvalidateFormに任せるため、ここでは10文字超過のみを主に扱う
  }
);

const authStore = useAuthStore();
const loading = computed(() => authStore.loading);
const toast = useToast();
const router = useRouter();

// フォームの無効状態を判定する算出プロパティ
const isFormInvalid = computed(() => {
  // validateFormを呼び出してエラー状態を更新し、その結果を利用することもできるが、
  // ここでは各エラーrefと主要な入力フィールドの空チェックを直接行う。
  // watchやvalidateFormによってエラーrefは更新される前提。
  return (
    !form.name.trim() ||
    !form.email.trim() ||
    !form.password || // passwordはtrim不要
    !form.password_confirmation || // password_confirmationもtrim不要
    nameError.value !== "" ||
    emailError.value !== "" ||
    passwordError.value !== "" ||
    confirmPasswordError.value !== ""
  );
});

interface RegisterResult {
  success: boolean;
  message: string;
  // 必要に応じて他のプロパティも定義
}

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
  } else if (form.name.trim().length > 10) {
    nameError.value = "名前は10文字以内で入力してください";
    isValid = false;
  } else {
    if (
      nameError.value !== "名前は10文字以内で入力してください" &&
      nameError.value !== "名前を入力してください"
    ) {
      nameError.value = "";
    }
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
    const result = (await authStore.register(
      form.name,
      form.email,
      form.password,
      form.password_confirmation
    )) as RegisterResult;

    if (result.success) {
      // sessionStorageにメールアドレスを保存（確認メール再送信で使用）
      if (import.meta.client) {
        sessionStorage.setItem("registered_email", form.email);
      }

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
  } catch (error: unknown) {
    let message = "予期せぬエラーが発生しました";
    if (error instanceof Error) {
      message = error.message;
    }
    toast.add({
      title: "エラー",
      description: message,
      color: "error",
    });
  }
}

// Google登録処理
function onGoogleRegister() {
  authStore.startGoogleLogin();
}
</script>
