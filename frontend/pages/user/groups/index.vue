<template>
  <div v-if="isCheckingAccess" class="p-4 text-center">
    <div
      class="h-10 w-10 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
    />
    <p class="mt-4 text-gray-600">アクセス権限を確認中...</p>
  </div>
  <div v-else class="p-4">
    <div class="max-w-4xl mx-auto">
      <!-- 戻るボタン -->
      <div class="mb-6">
        <button
          class="group flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 active:bg-gray-300 transition-all duration-200 hover:shadow-md active:scale-95"
          @click="goBack"
        >
          <svg
            class="w-5 h-5 text-gray-600 group-hover:text-gray-800 transition-colors duration-200"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 19l-7-7 7-7"
            />
          </svg>
        </button>
      </div>

      <h1 class="text-xl font-bold mb-4">グループ一覧</h1>
      <div
        v-if="successMessage"
        class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded"
      >
        {{ successMessage }}
      </div>
      <div
        v-if="errorMessage"
        class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded"
      >
        {{ errorMessage }}
      </div>

      <div class="mb-4">
        <button
          class="px-4 py-2 bg-emerald-600 text-white rounded"
          @click="showCreateForm = true"
        >
          新規グループを作成
        </button>
      </div>

      <div
        v-if="showCreateForm"
        class="mb-4 p-4 border rounded bg-gray-50 space-y-4"
      >
        <div>
          <label for="group-name" class="block text-sm font-medium"
            >グループ名</label
          >
          <input
            id="group-name"
            v-model="newGroup.name"
            placeholder="グループ名"
            class="w-full border rounded px-2 py-1"
          />
        </div>

        <div>
          <label for="group-desc" class="block text-sm font-medium">説明</label>
          <textarea
            id="group-desc"
            v-model="newGroup.description"
            placeholder="説明"
            class="w-full border rounded px-2 py-1"
          />
        </div>

        <div>
          <label class="block text-sm font-semibold"
            >チャットルームスタイル</label
          >
          <p class="text-xs text-gray-600 mb-2">
            利用するチャット形式を選択してください（複数選択可）
            <br />
            後から追加のみ可能です。
          </p>
          <div class="space-y-2">
            <div class="flex items-center space-x-2">
              <input
                id="style-group"
                v-model="newGroup.chatStyles"
                type="checkbox"
                value="group"
                class="flex-shrink-0 cursor-pointer"
              />
              <div>
                <label
                  for="style-group"
                  class="text-sm font-medium cursor-pointer"
                >
                  グループ全体チャット
                </label>
                <p class="text-xs text-gray-600">
                  グループメンバー全員が参加する共通のチャットルーム
                </p>
              </div>
            </div>

            <div class="flex items-center space-x-2">
              <input
                id="style-group-member"
                v-model="newGroup.chatStyles"
                type="checkbox"
                value="group_member"
                class="flex-shrink-0 cursor-pointer"
              />
              <div>
                <label
                  for="style-group-member"
                  class="text-sm font-medium cursor-pointer"
                >
                  作成者とメンバー間個別チャット
                </label>
                <p class="text-xs text-gray-600">
                  グループ作成者と各メンバーとの1対1チャットルーム
                </p>
              </div>
            </div>
          </div>

          <div v-if="chatStyleError" class="mt-2 text-sm text-red-600">
            {{ chatStyleError }}
          </div>
        </div>

        <div class="space-x-2">
          <button
            class="px-4 py-2 bg-blue-600 text-white rounded disabled:opacity-50"
            :disabled="creating"
            @click="createGroup"
          >
            {{ creating ? "作成中..." : "作成" }}
          </button>
          <button
            class="px-4 py-2 bg-gray-400 text-white rounded"
            @click="cancelCreate"
          >
            キャンセル
          </button>
        </div>
      </div>

      <div v-if="pending" class="text-gray-500">読み込み中...</div>
      <div v-else-if="error" class="text-red-500">{{ error.message }}</div>
      <ul v-else class="space-y-2">
        <li
          v-for="group in groups"
          :key="group.id"
          class="relative p-3 bg-white border rounded cursor-pointer hover:bg-gray-50"
          @click="goToGroup(group.id)"
        >
          <!-- 未読メッセージバッジ -->
          <div
            v-if="
              group.unread_messages_count && group.unread_messages_count > 0
            "
            class="badge-dot absolute -top-1 -right-1 z-10"
          />
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <p class="font-medium">{{ group.name }}</p>
              <p v-if="group.description" class="text-sm text-gray-500">
                {{ group.description }}
              </p>
            </div>
            <div class="text-sm text-gray-600">
              {{ group.member_count || 0 }} / {{ group.max_members || 50 }}
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "~/stores/auth";
import type { GroupConversation } from "~/types/group";

// ページメタデータでプレミアム認証をミドルウェアで制御
definePageMeta({
  middleware: ["premium-required"],
});

const router = useRouter();
const authStore = useAuthStore();
const groupConversations = useGroupConversations();

const isCheckingAccess = ref(true);

// リアクティブなプラン状態チェック
const hasPremiumAccess = computed(() => {
  const userPlan = authStore.user?.plan;
  return userPlan && userPlan !== "free";
});

// プラン状態を監視してリダイレクト
watch(
  hasPremiumAccess,
  async (hasAccess) => {
    if (hasAccess === false && authStore.user) {
      // プラン情報が確定してフリープランの場合
      await router.push("/pricing");
    } else if (hasAccess === true) {
      // プレミアムアクセス確認済み
      isCheckingAccess.value = false;
    }
  },
  { immediate: true }
);

// 認証状態を監視
watch(
  () => authStore.user,
  async (user) => {
    if (user) {
      await nextTick();
      if (user.plan === "free") {
        await router.push("/pricing");
      } else {
        isCheckingAccess.value = false;
      }
    }
  },
  { immediate: true }
);

const groups = ref<GroupConversation[]>([]);
const pending = ref(true);
const error = ref<Error | null>(null);

const loadGroups = async () => {
  try {
    pending.value = true;
    error.value = null;
    const data = await groupConversations.getGroups();
    groups.value = data;
  } catch (e) {
    error.value = e as Error;
  } finally {
    pending.value = false;
  }
};

const refresh = loadGroups;

// 初回ロード
onMounted(() => {
  loadGroups();
});

const successMessage = ref("");
const errorMessage = ref("");
const chatStyleError = ref("");
const creating = ref(false);

function goToGroup(id: number) {
  router.push(`/user/groups/${id}`);
}

function goBack() {
  // ブラウザの履歴を使用して前のページに戻る
  // 履歴がない場合はダッシュボードに戻る
  if (window.history.length > 1) {
    window.history.back();
  } else {
    router.push("/user/dashboard");
  }
}

const showCreateForm = ref(false);
const newGroup = ref<{
  name: string;
  description: string;
  chatStyles: string[];
}>({
  name: "",
  description: "",
  chatStyles: [],
});

async function createGroup() {
  errorMessage.value = "";
  successMessage.value = "";
  chatStyleError.value = "";

  if (!newGroup.value.name.trim()) {
    errorMessage.value = "グループ名を入力してください";
    return;
  }
  if (newGroup.value.name.length > 50) {
    errorMessage.value = "グループ名は50文字以内で入力してください";
    return;
  }
  if (newGroup.value.chatStyles.length === 0) {
    chatStyleError.value =
      "チャットルームスタイルを少なくとも1つ選択してください";
    return;
  }

  creating.value = true;
  try {
    await groupConversations.createGroup(newGroup.value);
    showCreateForm.value = false;
    newGroup.value = { name: "", description: "", chatStyles: [] };
    await refresh();
    successMessage.value = "グループを作成しました";
  } catch (e) {
    console.error(e);
    errorMessage.value = "グループ作成に失敗しました";
  } finally {
    creating.value = false;
  }
}

function cancelCreate() {
  showCreateForm.value = false;
  newGroup.value = { name: "", description: "", chatStyles: [] };
  errorMessage.value = "";
  chatStyleError.value = "";
}
</script>
