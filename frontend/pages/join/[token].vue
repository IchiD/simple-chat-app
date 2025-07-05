<template>
  <div class="flex-grow flex items-center justify-center px-4 py-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
      <!-- ローディング中 -->
      <div v-if="loading" class="text-center">
        <div
          class="w-16 h-16 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-4"
        />
        <p class="text-gray-600">グループ情報を読み込み中...</p>
      </div>

      <!-- グループ情報表示 -->
      <div v-else-if="groupInfo" class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
          {{ groupInfo.name }}へ参加
        </h1>
        <div class="text-gray-600 space-y-1">
          <p v-if="groupInfo.description">{{ groupInfo.description }}</p>
          <p class="text-sm">
            現在のメンバー: {{ groupInfo.member_count }} /
            {{ groupInfo.max_members }}人
          </p>
          <p class="text-sm">グループオーナー: {{ groupInfo.owner_name }}</p>
        </div>

        <!-- グループが満員の場合の警告 -->
        <div
          v-if="!groupInfo.can_join"
          class="mt-4 p-3 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg text-sm"
        >
          このグループは現在満員です
        </div>
      </div>

      <!-- グループが見つからない場合 -->
      <div v-else-if="!loading" class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
          グループが見つかりません
        </h1>
        <p class="text-gray-600">無効なQRコードまたはリンクです</p>
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

      <!-- グループ情報が取得できた場合のみ表示 -->
      <div v-if="groupInfo">
        <!-- 認証済みの場合の自動参加 -->
        <div v-if="isAuthenticated && !joining" class="text-center">
          <div class="mb-6">
            <p class="text-gray-700 mb-4">
              ログイン済みです。<strong>{{ groupInfo.name }}</strong
              >に参加しますか？
            </p>
          </div>
          <button
            :disabled="!groupInfo.can_join"
            :class="{
              'w-full py-2 px-4 rounded-md transition-colors': true,
              'bg-blue-600 text-white hover:bg-blue-700': groupInfo.can_join,
              'bg-gray-400 text-white cursor-not-allowed': !groupInfo.can_join,
            }"
            @click="joinGroup"
          >
            {{
              groupInfo.can_join
                ? `${groupInfo.name}に参加`
                : "グループが満員です"
            }}
          </button>
        </div>

        <!-- 参加処理中 -->
        <div v-else-if="joining" class="text-center">
          <div class="mb-6">
            <div
              class="w-16 h-16 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
            />
            <p class="mt-4 text-gray-600">{{ groupInfo.name }}に参加中...</p>
          </div>
        </div>

        <!-- 未認証の場合のログインフォーム -->
        <div v-else class="space-y-6">
          <h2 class="text-lg font-semibold text-gray-900 text-center">
            ログインして{{ groupInfo.name }}に参加
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
              :disabled="loginPending || !groupInfo.can_join"
              :class="{
                'w-full py-2 px-4 rounded-md transition-colors': true,
                'bg-blue-600 text-white hover:bg-blue-700':
                  !loginPending && groupInfo.can_join,
                'bg-gray-400 text-white cursor-not-allowed':
                  loginPending || !groupInfo.can_join,
              }"
            >
              {{
                loginPending
                  ? "ログイン中..."
                  : groupInfo.can_join
                  ? `ログインして${groupInfo.name}に参加`
                  : "グループが満員です"
              }}
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
            :disabled="loginPending || !groupInfo.can_join"
            :class="{
              'w-full flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium transition-colors': true,
              'text-gray-700 bg-white hover:bg-gray-50':
                !loginPending && groupInfo.can_join,
              'text-gray-400 bg-gray-100 cursor-not-allowed':
                loginPending || !groupInfo.can_join,
            }"
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
            {{
              groupInfo.can_join
                ? `Googleでログインして${groupInfo.name}に参加`
                : "グループが満員です"
            }}
          </button>

          <!-- 新規登録リンク -->
          <div class="text-center">
            <p class="text-sm text-gray-600">
              アカウントをお持ちでない方は
              <NuxtLink
                :to="`/auth/register-and-join/${token}`"
                class="font-medium text-blue-600 hover:text-blue-500"
              >
                新規登録
              </NuxtLink>
            </p>
          </div>
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
const loading = ref(true);

// 認証状態
const isAuthenticated = computed(() => authStore.isAuthenticated);

// ログインフォーム
const loginForm = reactive({
  email: "",
  password: "",
});
const loginPending = ref(false);

// グループ情報
const groupInfo = ref<{
  id: number;
  name: string;
  description?: string;
  member_count: number;
  max_members: number;
  owner_name: string;
  can_join: boolean;
} | null>(null);

// グループ参加処理
const joinGroup = async () => {
  if (!isAuthenticated.value) {
    errorMessage.value = "ログインが必要です";
    return;
  }

  if (!groupInfo.value?.can_join) {
    errorMessage.value = "このグループは現在満員です";
    return;
  }

  errorMessage.value = "";
  joining.value = true;

  try {
    await groupConversations.joinByToken(token);
    successMessage.value = `${groupInfo.value.name}に参加しました`;

    // 参加成功後、チャット画面に遷移
    setTimeout(() => {
      router.push("/chat");
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
  if (!groupInfo.value?.can_join) {
    errorMessage.value = "このグループは現在満員です";
    return;
  }

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
  if (!groupInfo.value?.can_join) {
    errorMessage.value = "このグループは現在満員です";
    return;
  }

  // グループトークンをセッションストレージに保存
  sessionStorage.setItem("pendingGroupToken", token);

  // Google認証ページに直接リダイレクト
  const config = useRuntimeConfig();
  const apiBase = config.public.apiBase || "http://localhost/api";
  window.location.href = `${apiBase}/auth/google/redirect`;
};

// グループ情報を取得
const loadGroupInfo = async () => {
  loading.value = true;
  errorMessage.value = "";

  try {
    groupInfo.value = await groupConversations.getGroupInfoByToken(token);
  } catch (error: unknown) {
    console.error("グループ情報取得エラー:", error);
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
      errorMessage.value = "グループ情報の取得に失敗しました";
    }
    groupInfo.value = null;
  } finally {
    loading.value = false;
  }
};

// 認証済みユーザーが復帰した場合の自動参加処理
onMounted(async () => {
  // まずグループ情報を取得
  await loadGroupInfo();

  if (import.meta.client) {
    const pendingToken = sessionStorage.getItem("pendingGroupToken");
    if (
      pendingToken === token &&
      isAuthenticated.value &&
      groupInfo.value?.can_join
    ) {
      sessionStorage.removeItem("pendingGroupToken");
      await joinGroup();
    }
  }
});
</script>
