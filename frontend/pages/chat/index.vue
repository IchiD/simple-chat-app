<template>
  <div class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div
      class="relative flex antialiased text-gray-800"
      style="height: calc(100vh - 7.5rem)"
    >
      <div class="flex h-full w-full">
        <!-- ゲストユーザー向けメッセージ -->
        <div v-if="!authStore.isAuthenticated" class="max-w-4xl mx-auto w-full">
          <div class="h-full flex items-center justify-center p-8">
            <div class="bg-white rounded-xl shadow-sm p-8 text-center max-w-md">
              <div
                class="h-16 w-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-8 w-8 text-green-600"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"
                  />
                  <path
                    d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"
                  />
                </svg>
              </div>
              <h2 class="text-xl font-bold text-gray-900 mb-4">
                ゲストユーザー
              </h2>
              <p class="text-gray-600 mb-6">
                ゲストユーザーとしてチャット機能をご利用いただけます。<br />
                参加済みのグループチャットがある場合は、直接チャットルームにアクセスしてください。
              </p>
              <div
                class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800"
              >
                <p class="font-medium mb-1">制限事項</p>
                <ul class="text-left space-y-1">
                  <li>• 友達とのプライベートチャットは利用できません</li>
                  <li>• チャット一覧機能は利用できません</li>
                  <li>• 参加済みのグループチャットのみ利用可能です</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- 認証済みユーザー向け通常のチャット一覧 -->
        <div v-else class="max-w-4xl mx-auto w-full">
          <ChatSidebar
            :conversations="conversations"
            :pending="pending"
            :error="error"
            :selected-conversation-room-token="null"
            class="w-full"
            @conversation-selected="handleConversationSelected"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Nuxtの自動インポート機能を活用し、手動インポートを最小限に
import { computed, onMounted, ref, watch } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "~/stores/auth";
import { useApi } from "~/composables/useApi";

type Participant = {
  id: number;
  name: string;
  friend_id?: number | null;
};

type MessageSender = {
  id: number;
  name: string;
};

type AdminSender = {
  id: number;
  name: string;
};

type LatestMessage = {
  id: number;
  text_content: string | null;
  sent_at: string | null;
  sender: MessageSender | null;
  admin_sender_id?: number | null;
  adminSender?: AdminSender | null;
};

type Conversation = {
  id: number;
  participants: Participant[];
  latest_message: LatestMessage | null;
  unread_messages_count: number;
  room_token: string;
  type?: string;
  name?: string; // グループ名
  created_at?: string;
  updated_at?: string;
};

type PaginatedConversationsResponse = {
  current_page: number;
  data: Conversation[];
  first_page_url: string;
  from: number | null;
  last_page: number;
  last_page_url: string;
  links: { url: string | null; label: string; active: boolean }[];
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number | null;
  total: number;
};

const authStore = useAuthStore();
const router = useRouter();

// API応答の状態管理
const apiResponse = ref<PaginatedConversationsResponse | null>(null);
const pending = ref(false);
const error = ref<Error | null>(null);

// 会話データを取得する関数
const fetchConversations = async () => {
  if (!authStore.isAuthenticated || !authStore.token) {
    return;
  }

  try {
    pending.value = true;
    error.value = null;

    const { api } = useApi();
    const data = await api<PaginatedConversationsResponse>("/conversations");

    apiResponse.value = data;
  } catch (err) {
    error.value = err instanceof Error ? err : new Error("Unknown error");
    console.error(
      "Detailed error fetching conversations:",
      JSON.stringify(err, null, 2)
    );
  } finally {
    pending.value = false;
  }
};

// 認証チェック（ゲストユーザーも許可）
onMounted(async () => {
  try {
    // 認証状態をチェック（失敗してもエラーを投げない）
    await authStore.checkAuth();

    // 認証済みの場合は会話データを取得
    if (authStore.isAuthenticated) {
      await fetchConversations();
    }
  } catch (error) {
    console.log("認証チェック失敗 - ゲストユーザーとして続行:", error);
    // ゲストユーザーとして継続
  }
});

// 認証状態の変化を監視
watch(
  () => authStore.isAuthenticated,
  async (isAuthenticated) => {
    if (isAuthenticated) {
      await fetchConversations();
    } else {
      // 認証されていない場合はデータをクリア
      apiResponse.value = null;
      error.value = null;
    }
  }
);

const conversations = computed(() => {
  if (!authStore.isAuthenticated) {
    return [];
  }

  const conversationList = apiResponse.value?.data || [];

  // サポート会話を識別して表示名を調整
  return conversationList.map((conversation: Conversation) => {
    if (conversation.type === "support") {
      return {
        ...conversation,
        participants: [{ id: 0, name: "サポート", friend_id: null }],
      };
    }
    return conversation;
  });
});

const handleConversationSelected = (roomToken: string) => {
  if (roomToken) {
    router.push(`/chat/${roomToken}/`);
  }
};
</script>

<style scoped>
/* Add any page-specific styles here if needed */
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* カスタムアニメーション */
@keyframes float {
  0%,
  100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}

/* ホバーエフェクト */
.group:hover .animate-float {
  animation: float 2s ease-in-out infinite;
}

/* グラデーションアニメーション */
.bg-gradient-to-r {
  background-size: 200% 200%;
  animation: gradient 3s ease infinite;
}

@keyframes gradient {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

/* Sidebar scrollbar styling (optional) */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: #cbd5e0; /* Tailwind gray-400 */
  border-radius: 0;
}
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background-color: #a0aec0; /* Tailwind gray-500 */
}
</style>
