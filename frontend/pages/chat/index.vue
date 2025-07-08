<template>
  <div class="bg-gradient-to-br min-h-full">
    <div class="relative flex antialiased text-gray-800 min-h-full">
      <div class="flex min-h-full w-full">
        <!-- 認証済みユーザー向け通常のチャット一覧 -->
        <div class="max-w-5xl mx-auto w-full min-h-full">
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

type GroupOwner = {
  id: number;
  name: string;
  friend_id: string;
};

type Conversation = {
  id: number;
  participants: Participant[];
  latest_message: LatestMessage | null;
  unread_messages_count: number;
  room_token: string;
  type?: string;
  name?: string; // グループ名
  group_name?: string; // グループ名
  group_owner?: GroupOwner; // グループオーナー情報（member_chatの場合）
  participant_count?: number; // 参加者数（group_chatの場合）
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

// チャットデータを取得する関数
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

// 認証チェック（認証必須）
onMounted(async () => {
  try {
    // 認証状態をチェック
    await authStore.checkAuth();

    // 認証済みの場合のみチャットデータを取得
    if (authStore.isAuthenticated) {
      await fetchConversations();
    } else {
      // 未認証の場合はログインページにリダイレクト
      router.push("/auth/login");
    }
  } catch (error) {
    console.error("認証チェック失敗:", error);
    router.push("/auth/login");
  }
});

// 認証状態の変化を監視
watch(
  () => authStore.isAuthenticated,
  async (isAuthenticated) => {
    if (isAuthenticated) {
      await fetchConversations();
    } else {
      // 認証されていない場合はログインページにリダイレクト
      router.push("/auth/login");
    }
  }
);

const conversations = computed(() => {
  const conversationList = apiResponse.value?.data || [];

  // お問い合わせチャットを識別して表示名を調整
  return conversationList.map((conversation: Conversation) => {
    if (conversation.type === "support") {
      return {
        ...conversation,
        participants: [{ id: 0, name: "お問い合わせ", friend_id: null }],
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
