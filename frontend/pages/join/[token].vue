<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">グループへ参加</h1>
        <p class="text-gray-600">ログインしてグループに参加してください</p>
      </div>

      <!-- エラーメッセージ -->
      <div
        v-if="errorMessage"
        class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg"
      >
        {{ errorMessage }}
      </div>

      <!-- 成功メッセージ -->
      <div
        v-if="successMessage"
        class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg"
      >
        {{ successMessage }}
      </div>

      <!-- 認証済みの場合の自動参加 -->
      <div v-if="isAuthenticated && !joining" class="text-center">
        <div class="mb-6">
          <div
            class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4"
          >
            <svg
              class="w-8 h-8 text-green-600"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
              />
            </svg>
          </div>
          <p class="text-gray-700 mb-4">
            ログイン済みです。グループに参加しますか？
          </p>
        </div>
        <button
          class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          @click="joinGroup"
        >
          グループに参加
        </button>
      </div>

      <!-- 参加処理中 -->
      <div v-else-if="joining" class="text-center">
        <div class="mb-6">
          <div
            class="w-16 h-16 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
          />
          <p class="mt-4 text-gray-600">グループに参加中...</p>
        </div>
      </div>

      <!-- 未認証の場合のログインフォーム -->
      <div v-else class="space-y-6">
        <h2 class="text-lg font-semibold text-gray-900 text-center">
          ログインが必要です
        </h2>

        <!-- 通常ログインフォーム -->
        <form class="space-y-4" @submit.prevent="loginAndJoin">
          <div>
            <label
              for="email"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              メールアドレス
            </label>
            <input
              id="email"
              v-model="loginForm.email"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="メールアドレス"
            />
          </div>
          <div>
            <label
              for="password"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              パスワード
            </label>
            <input
              id="password"
              v-model="loginForm.password"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="パスワード"
            />
          </div>
          <button
            type="submit"
            :disabled="loginPending"
            class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loginPending ? "ログイン中..." : "ログインして参加" }}
          </button>
        </form>

        <!-- Googleログイン -->
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300" />
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">または</span>
          </div>
        </div>

        <button
          :disabled="loginPending"
          class="w-full flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
          @click="googleLoginAndJoin"
        >
          <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
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
          Googleでログインして参加
        </button>

        <div class="text-center text-sm text-gray-600">
          <p>
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
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from "vue";
import { useRoute, useRouter } from "#app";
import { useAuthStore } from "~/stores/auth";
// import type { GroupParticipant } from "~/types/group";

definePageMeta({
  layout: "default",
  title: "グループへ参加",
  auth: false, // QRコードアクセスのため初期は認証不要だが、実際の参加には認証が必要
});

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const groupConversations = useGroupConversations();

const token = route.params.token as string;

// UI状態
const errorMessage = ref("");
const successMessage = ref("");
const joining = ref(false);

// 認証状態
const isAuthenticated = computed(() => authStore.isAuthenticated);

// ログインフォーム
const loginForm = reactive({
  email: "",
  password: "",
});
const loginPending = ref(false);

// グループ参加処理
const joinGroup = async () => {
  if (!isAuthenticated.value) {
    errorMessage.value = "ログインが必要です";
    return;
  }

  errorMessage.value = "";
  joining.value = true;

  try {
    await groupConversations.joinByToken(token);
    successMessage.value = "グループに参加しました";

    // 参加成功後、グループ一覧に戻る
    setTimeout(() => {
      router.push("/user/groups");
    }, 2000);
  } catch (error: unknown) {
    console.error("参加エラー:", error);
    if (
      error &&
      typeof error === "object" &&
      "data" in error &&
      error.data &&
      typeof error.data === "object" &&
      "message" in error.data
    ) {
      errorMessage.value = error.data.message as string;
    } else {
      errorMessage.value = "参加に失敗しました";
    }
  } finally {
    joining.value = false;
  }
};

// ログインして参加
const loginAndJoin = async () => {
  errorMessage.value = "";
  loginPending.value = true;

  try {
    // ログイン処理
    await authStore.login(loginForm.email, loginForm.password);

    // ログイン成功後にグループ参加処理
    await joinGroup();
  } catch (error: unknown) {
    console.error("ログインエラー:", error);
    errorMessage.value = "ログイン処理中にエラーが発生しました";
  } finally {
    loginPending.value = false;
  }
};

// Googleログインして参加
const googleLoginAndJoin = () => {
  // グループトークンをセッションストレージに保存
  sessionStorage.setItem("pendingGroupToken", token);

  // Google認証ページに直接リダイレクト
  const config = useRuntimeConfig();
  const apiBase = config.public.apiBase || "http://localhost/api";
  window.location.href = `${apiBase}/auth/google/redirect`;
};

// 認証済みユーザーが復帰した場合の自動参加処理
onMounted(() => {
  if (import.meta.client) {
    const pendingToken = sessionStorage.getItem("pendingGroupToken");
    if (pendingToken === token && isAuthenticated.value) {
      sessionStorage.removeItem("pendingGroupToken");
      joinGroup();
    }
  }
});
</script>
