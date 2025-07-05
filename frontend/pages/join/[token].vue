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

        <!-- 登録処理中 -->
        <div v-else-if="registering" class="text-center">
          <div class="mb-6">
            <div
              class="w-16 h-16 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
            />
            <p class="mt-4 text-gray-600">アカウント作成中...</p>
          </div>
        </div>

        <!-- メール確認画面 -->
        <div v-else-if="showVerification" class="text-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="text-5xl mb-4 mx-auto h-12 w-12 text-blue-500"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
            />
            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
          </svg>
          <h2 class="text-lg font-semibold text-gray-900 mb-4">メール確認</h2>
          <p class="text-gray-600 mb-6">
            登録したメールアドレスに確認メールを送信しました。<br />
            メール内のリンクをクリックして認証を完了してください。<br />
            認証完了後、自動的に{{ groupInfo.name }}に参加します。
          </p>
          <button
            :disabled="resendLoading"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            @click="resendVerification"
          >
            <svg
              v-if="resendLoading"
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
          <div class="mt-6 text-center">
            <button
              class="text-sm font-medium text-blue-600 hover:text-blue-500"
              @click="showVerification = false"
            >
              戻る
            </button>
          </div>
        </div>

        <!-- 未認証の場合のログイン・新規登録フォーム -->
        <div v-else class="space-y-6">
          <!-- ログインフォーム -->
          <div v-if="activeTab === 'login'" class="space-y-4">
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
                アカウントをお持ちでない場合は
                <button
                  class="font-medium text-blue-600 hover:text-blue-500 underline"
                  @click="activeTab = 'register'"
                >
                  新規登録
                </button>
              </p>
            </div>
          </div>

          <!-- 新規登録フォーム -->
          <div v-if="activeTab === 'register'" class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-900 text-center">
              アカウント作成して{{ groupInfo.name }}に参加
            </h2>

            <!-- 通常登録フォーム -->
            <form class="space-y-4" @submit.prevent="registerAndJoin">
              <div>
                <label
                  for="register-name"
                  class="block text-sm font-medium text-gray-700 mb-1"
                >
                  名前
                </label>
                <input
                  id="register-name"
                  v-model="registerForm.name"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="お名前"
                />
              </div>
              <div>
                <label
                  for="register-email"
                  class="block text-sm font-medium text-gray-700 mb-1"
                >
                  メールアドレス
                </label>
                <input
                  id="register-email"
                  v-model="registerForm.email"
                  type="email"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="メールアドレス"
                />
              </div>
              <div>
                <label
                  for="register-password"
                  class="block text-sm font-medium text-gray-700 mb-1"
                >
                  パスワード
                </label>
                <input
                  id="register-password"
                  v-model="registerForm.password"
                  type="password"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="パスワード（8文字以上）"
                />
              </div>
              <div>
                <label
                  for="register-password-confirmation"
                  class="block text-sm font-medium text-gray-700 mb-1"
                >
                  パスワード（確認）
                </label>
                <input
                  id="register-password-confirmation"
                  v-model="registerForm.password_confirmation"
                  type="password"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="パスワード（確認）"
                />
              </div>
              <button
                type="submit"
                :disabled="registering || !groupInfo.can_join"
                :class="{
                  'w-full py-2 px-4 rounded-md transition-colors': true,
                  'bg-blue-600 text-white hover:bg-blue-700':
                    !registering && groupInfo.can_join,
                  'bg-gray-400 text-white cursor-not-allowed':
                    registering || !groupInfo.can_join,
                }"
              >
                {{
                  registering
                    ? "アカウント作成中..."
                    : groupInfo.can_join
                    ? `アカウント作成して${groupInfo.name}に参加`
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
              :disabled="registering || !groupInfo.can_join"
              :class="{
                'w-full flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium transition-colors': true,
                'text-gray-700 bg-white hover:bg-gray-50':
                  !registering && groupInfo.can_join,
                'text-gray-400 bg-gray-100 cursor-not-allowed':
                  registering || !groupInfo.can_join,
              }"
              @click="googleRegisterAndJoin"
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
                  ? `Googleでアカウント作成して${groupInfo.name}に参加`
                  : "グループが満員です"
              }}
            </button>

            <!-- ログインリンク -->
            <div class="text-center">
              <p class="text-sm text-gray-600">
                既にアカウントをお持ちの場合は
                <button
                  class="font-medium text-blue-600 hover:text-blue-500 underline"
                  @click="activeTab = 'login'"
                >
                  ログイン
                </button>
              </p>
            </div>
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

definePageMeta({
  layout: "default",
  title: "グループに参加",
  auth: false,
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
const loginPending = ref(false);
const registering = ref(false);
const showVerification = ref(false);
const resendLoading = ref(false);
const registeredEmail = ref("");
const activeTab = ref<"login" | "register">("login");

// ログインフォーム
const loginForm = reactive({
  email: "",
  password: "",
});

// 新規登録フォーム
const registerForm = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

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

// 認証状態
const isAuthenticated = computed(() => authStore.isAuthenticated);

// グループ参加処理
const joinGroup = async () => {
  if (!authStore.isAuthenticated) {
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
    const result = await authStore.login(loginForm.email, loginForm.password);

    if (result.success) {
      // ログイン成功後、自動的にグループに参加
      await joinGroup();
    } else {
      throw new Error("ログインに失敗しました");
    }
  } catch (error: unknown) {
    console.error("ログインエラー:", error);
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
      errorMessage.value = "ログインに失敗しました";
    }
  } finally {
    loginPending.value = false;
  }
};

// 新規登録して参加
const registerAndJoin = async () => {
  if (!groupInfo.value?.can_join) {
    errorMessage.value = "このグループは現在満員です";
    return;
  }

  if (registerForm.password !== registerForm.password_confirmation) {
    errorMessage.value = "パスワードが一致しません";
    return;
  }

  if (registerForm.password.length < 8) {
    errorMessage.value = "パスワードは8文字以上で入力してください";
    return;
  }

  errorMessage.value = "";
  registering.value = true;

  try {
    // 新規登録処理
    const result = await authStore.register(
      registerForm.name,
      registerForm.email,
      registerForm.password,
      registerForm.password_confirmation
    );

    if (result.success) {
      // グループトークンをセッションストレージに保存（メール認証完了後の自動参加用）
      if (import.meta.client) {
        sessionStorage.setItem("pendingGroupToken", token);
      }

      // 登録したメールアドレスを保存（再送信用）
      registeredEmail.value = registerForm.email;

      // メール確認画面に切り替え
      showVerification.value = true;
      successMessage.value =
        "確認メールを送信しました。メール内のリンクをクリックして認証を完了してください。認証完了後、自動的にグループに参加します。";
      return;
    } else {
      throw new Error(result.message || "登録に失敗しました");
    }
  } catch (error: unknown) {
    console.error("登録エラー:", error);
    if (
      error &&
      typeof error === "object" &&
      "data" in error &&
      error.data &&
      typeof error.data === "object" &&
      "message" in error.data
    ) {
      errorMessage.value = error.data.message as string;
    } else if (
      error &&
      typeof error === "object" &&
      "data" in error &&
      error.data &&
      typeof error.data === "object" &&
      "errors" in error.data &&
      error.data.errors &&
      typeof error.data.errors === "object"
    ) {
      const errors = error.data.errors as Record<string, string[]>;
      const firstError = Object.values(errors)[0];
      errorMessage.value = firstError ? firstError[0] : "登録に失敗しました";
    } else {
      errorMessage.value = "登録処理中にエラーが発生しました";
    }
  } finally {
    registering.value = false;
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

  // Google認証を開始
  authStore.startGoogleLogin("login");
};

// Google新規登録して参加
const googleRegisterAndJoin = () => {
  if (!groupInfo.value?.can_join) {
    errorMessage.value = "このグループは現在満員です";
    return;
  }

  // グループトークンをセッションストレージに保存
  sessionStorage.setItem("pendingGroupToken", token);

  // Google認証を開始
  authStore.startGoogleLogin("register");
};

// メール再送信機能
const resendVerification = async () => {
  if (!registeredEmail.value) {
    errorMessage.value = "メールアドレスが取得できません。";
    return;
  }

  resendLoading.value = true;
  errorMessage.value = "";

  try {
    const { api } = useApi();
    const response = await api("/resend-verification", {
      method: "POST",
      body: { email: registeredEmail.value },
    });

    if (response.status === "success") {
      successMessage.value = "確認メールを再送信しました。";
    } else {
      errorMessage.value = response.message || "確認メールの送信に失敗しました";
    }
  } catch (error: unknown) {
    console.error("メール再送信エラー:", error);
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
      errorMessage.value = "確認メールの送信に失敗しました";
    }
  } finally {
    resendLoading.value = false;
  }
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

onMounted(async () => {
  // グループ情報を取得
  await loadGroupInfo();

  // 認証済みユーザーが復帰した場合の自動参加処理
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
