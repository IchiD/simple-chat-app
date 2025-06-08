<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">グループへ参加</h1>
        <p class="text-gray-600">参加方法を選択してください</p>
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

      <!-- 選択状態 -->
      <div v-if="!selectedOption" class="space-y-4">
        <!-- アカウントをお持ちの方 -->
        <button
          @click="selectAccountLogin"
          class="w-full p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors"
        >
          <div class="flex items-center justify-between">
            <div class="text-left">
              <h3 class="font-semibold text-gray-900">アカウントをお持ちの方</h3>
              <p class="text-sm text-gray-600">ログインしてチャットリストに追加</p>
            </div>
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </div>
        </button>

        <!-- ゲストとして続ける方 -->
        <button
          @click="selectGuestLogin"
          class="w-full p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-colors"
        >
          <div class="flex items-center justify-between">
            <div class="text-left">
              <h3 class="font-semibold text-gray-900">ゲストとして続ける方</h3>
              <p class="text-sm text-gray-600">すぐにチャットを開始</p>
            </div>
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </div>
        </button>
      </div>

      <!-- ログインフォーム -->
      <div v-if="selectedOption === 'account'" class="space-y-6">
        <div class="flex items-center mb-4">
          <button
            @click="goBack"
            class="mr-3 p-1 text-gray-500 hover:text-gray-700"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
          <h2 class="text-lg font-semibold text-gray-900">ログイン</h2>
        </div>

        <!-- 通常ログインフォーム -->
        <form @submit.prevent="loginAndJoin" class="space-y-4">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
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
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
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
          @click="googleLoginAndJoin"
          :disabled="loginPending"
          class="w-full flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
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

      <!-- ゲストフォーム -->
      <div v-if="selectedOption === 'guest'" class="space-y-6">
        <div class="flex items-center mb-4">
          <button
            @click="goBack"
            class="mr-3 p-1 text-gray-500 hover:text-gray-700"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
          <h2 class="text-lg font-semibold text-gray-900">ゲストとして参加</h2>
        </div>

        <div>
          <label for="nickname" class="block text-sm font-medium text-gray-700 mb-1">
            ニックネーム
          </label>
          <input
            id="nickname"
            v-model="nickname"
            type="text"
            required
            maxlength="50"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
            placeholder="ニックネームを入力"
          />
        </div>

        <button
          @click="joinAsGuest"
          :disabled="guestPending || !nickname.trim()"
          class="w-full py-2 px-4 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ guestPending ? "参加中..." : "ゲストとして参加" }}
        </button>

        <div class="text-sm text-yellow-600 bg-yellow-50 p-3 rounded-md">
          <p class="font-medium mb-1">ゲスト参加の制限</p>
          <ul class="text-xs space-y-1">
            <li>• 友達機能は利用できません</li>
            <li>• ユーザー設定が一部制限されます</li>
            <li>• このチャットのみ利用可能です</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useApi } from "~/composables/useApi";
import { useAuthStore } from "~/stores/auth";
import type { GroupMember } from "~/types/group";

const route = useRoute();
const router = useRouter();
const { api } = useApi();
const authStore = useAuthStore();

const token = route.params.token as string;

// UI状態
const selectedOption = ref<'account' | 'guest' | null>(null);
const errorMessage = ref("");
const successMessage = ref("");

// ログインフォーム
const loginForm = reactive({
  email: "",
  password: "",
});
const loginPending = ref(false);

// ゲストフォーム
const nickname = ref("");
const guestPending = ref(false);

// 選択肢の処理
const selectAccountLogin = () => {
  selectedOption.value = 'account';
  errorMessage.value = "";
};

const selectGuestLogin = () => {
  selectedOption.value = 'guest';
  errorMessage.value = "";
};

const goBack = () => {
  selectedOption.value = null;
  errorMessage.value = "";
  successMessage.value = "";
};

// グループ参加処理
const joinGroup = async (isLoggedIn: boolean) => {
  errorMessage.value = "";
  
  if (isLoggedIn) {
    loginPending.value = true;
  } else {
    guestPending.value = true;
  }

  try {
    const requestBody = {
      nickname: isLoggedIn ? authStore.user?.name || "ユーザー" : nickname.value,
    };

    const member = await api<GroupMember>(`/groups/join/${token}`, {
      method: "POST",
      body: requestBody,
    });

    successMessage.value = "グループに参加しました";
    
    // 参加成功後のリダイレクト
    if (isLoggedIn) {
      // ログインユーザーはチャット一覧に追加される
      router.push("/chat");
    } else {
      // ゲストは直接チャットルームへ
      router.push(`/chat/${member.group_id}`);
    }
  } catch (error: any) {
    console.error("参加エラー:", error);
    if (error.data?.message) {
      errorMessage.value = error.data.message;
    } else {
      errorMessage.value = "参加に失敗しました";
    }
  } finally {
    loginPending.value = false;
    guestPending.value = false;
  }
};

// ログインして参加
const loginAndJoin = async () => {
  errorMessage.value = "";
  loginPending.value = true;

  try {
    // ログイン処理
    const loginResult = await authStore.login(loginForm.email, loginForm.password);
    
    if (!loginResult.success) {
      errorMessage.value = loginResult.message || "ログインに失敗しました";
      return;
    }

    // グループ参加処理
    await joinGroup(true);
  } catch (error) {
    console.error("ログインエラー:", error);
    errorMessage.value = "ログイン処理中にエラーが発生しました";
  } finally {
    loginPending.value = false;
  }
};

// Googleログインして参加
const googleLoginAndJoin = () => {
  // グループトークンをセッションストレージに保存
  sessionStorage.setItem('pendingGroupToken', token);
  // Googleログインを開始
  authStore.startGoogleLogin();
};

// ゲストとして参加
const joinAsGuest = async () => {
  if (!nickname.value.trim()) {
    errorMessage.value = "ニックネームを入力してください";
    return;
  }
  
  await joinGroup(false);
};

// Googleログインからの復帰処理
if (process.client) {
  const pendingToken = sessionStorage.getItem('pendingGroupToken');
  if (pendingToken === token && authStore.isAuthenticated) {
    sessionStorage.removeItem('pendingGroupToken');
    joinGroup(true);
  }
}
</script>
