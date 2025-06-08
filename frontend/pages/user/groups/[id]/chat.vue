<template>
  <div v-if="isCheckingAccess" class="p-4 text-center">
    <div
      class="h-10 w-10 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
    />
    <p class="mt-4 text-gray-600">アクセス権限を確認中...</p>
  </div>
  <div v-else class="p-4">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-xl font-bold mb-4">{{ group?.name }} チャット</h1>
      <div v-if="messagesPending" class="text-gray-500">読み込み中...</div>
      <div v-else-if="messagesError" class="text-red-500">
        {{ messagesError }}
      </div>
      <div
        v-else
        class="space-y-2 mb-4 max-h-96 overflow-y-auto border p-2 rounded bg-white"
      >
        <div v-for="msg in messages" :key="msg.id" class="border-b pb-1">
          <span class="font-semibold">{{ msg.sender?.name || "匿名" }}</span
          >:
          {{ msg.text_content }}
          <span class="text-xs text-gray-500 ml-2">
            {{ formatTime(msg.sent_at) }}
          </span>
        </div>
        <div v-if="nextPageUrl" class="text-center mt-2">
          <button
            class="px-3 py-1 text-sm bg-gray-200 rounded"
            :disabled="loadingMore"
            @click="loadMore"
          >
            さらに読み込む
          </button>
        </div>
      </div>
      <div class="flex space-x-2">
        <input
          v-model="newMessage"
          class="flex-1 border rounded px-2 py-1"
          type="text"
          placeholder="メッセージ"
          @keypress.enter="send"
        />
        <button
          class="px-4 py-1 bg-emerald-600 text-white rounded"
          :disabled="sending || !newMessage.trim()"
          @click="send"
        >
          送信
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick } from "vue";
import { useRoute, useRouter } from "#app";
import { useAuthStore } from "~/stores/auth";
import type {
  GroupConversation,
  GroupMessage,
  PaginatedGroupMessages,
} from "~/types/group";

// ページメタデータでプレミアム認証をミドルウェアで制御
definePageMeta({
  middleware: ["premium-required"],
});

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const groupConversations = useGroupConversations();
const id = Number(route.params.id as string);

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
      await router.push("/pricing");
    } else if (hasAccess === true) {
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

// グループ情報とメッセージの状態
const group = ref<GroupConversation | null>(null);
const messages = ref<GroupMessage[]>([]);
const messagesPending = ref(true);
const messagesError = ref("");
const nextPageUrl = ref<string | null>(null);
const loadingMore = ref(false);

// グループ情報を取得
const loadGroup = async () => {
  try {
    group.value = await groupConversations.getGroup(id);
  } catch (error) {
    console.error("グループ取得エラー:", error);
    messagesError.value = "グループの取得に失敗しました";
  }
};

// メッセージを取得
const loadMessages = async () => {
  if (!group.value?.room_token) return;

  messagesPending.value = true;
  messagesError.value = "";

  try {
    const data = await groupConversations.getMessages(group.value.room_token);
    messages.value = data.data;
    nextPageUrl.value = data.links?.next || null;
  } catch (error) {
    console.error("メッセージ取得エラー:", error);
    messagesError.value = "メッセージの取得に失敗しました";
  } finally {
    messagesPending.value = false;
  }
};

// 初期データ読み込み
const refresh = async () => {
  await loadGroup();
  await loadMessages();
};

// 初回読み込み
await refresh();

const newMessage = ref("");
const sending = ref(false);

async function send() {
  if (!newMessage.value.trim() || !group.value?.room_token) return;
  sending.value = true;
  try {
    await groupConversations.sendMessage(
      group.value.room_token,
      newMessage.value
    );
    newMessage.value = "";
    await loadMessages(); // メッセージ一覧を再読み込み
  } catch (e) {
    console.error("メッセージ送信エラー:", e);
    messagesError.value = "メッセージの送信に失敗しました";
  } finally {
    sending.value = false;
  }
}

async function loadMore() {
  if (!nextPageUrl.value) return;
  loadingMore.value = true;
  try {
    // ページネーション用のAPIエンドポイントを直接呼び出し
    const config = useRuntimeConfig();
    const { api } = useApi();

    const res = await api<PaginatedGroupMessages>(
      nextPageUrl.value.replace(config.public.apiBase, "")
    );
    messages.value.push(...res.data);
    nextPageUrl.value = res.links?.next || null;
  } catch (e) {
    console.error("追加メッセージ取得エラー:", e);
  } finally {
    loadingMore.value = false;
  }
}

// 時刻フォーマット
const formatTime = (dateString: string) => {
  return new Date(dateString).toLocaleTimeString("ja-JP", {
    hour: "2-digit",
    minute: "2-digit",
  });
};
</script>
