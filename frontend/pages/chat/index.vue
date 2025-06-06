<template>
  <div class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div
      class="relative flex antialiased text-gray-800"
      style="height: calc(100vh - 7.5rem)"
    >
      <div class="flex h-full w-full">
        <div class="max-w-4xl mx-auto w-full">
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
import { computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "~/stores/auth";

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
const toast = useToast();
const config = useRuntimeConfig();

// 明示的な認証チェックを追加
onMounted(async () => {
  try {
    // 認証状態をチェック
    await authStore.checkAuth();

    if (!authStore.isAuthenticated) {
      // 認証されていない場合はログインページにリダイレクト
      toast.add({
        title: "認証エラー",
        description: "ログインが必要です。ログインページに移動します。",
        color: "error",
      });
      await router.push("/auth/login");
      return;
    }
  } catch (error) {
    console.error("Auth check error:", error);
    toast.add({
      title: "エラー",
      description: "認証情報の取得に失敗しました",
      color: "error",
    });
    // エラー時も認証ページへリダイレクト
    await router.push("/auth/login");
  }
});

const fetchHeaders: Record<string, string> = {
  Accept: "application/json",
};
if (authStore.token) {
  fetchHeaders.Authorization = `Bearer ${authStore.token}`;
}

const {
  data: apiResponse,
  pending,
  error,
} = await useFetch(`${config.public.apiBase}/conversations`, {
  headers: fetchHeaders,
  server: false,
});

if (error.value) {
  console.error(
    "Detailed error fetching conversations:",
    JSON.stringify(error.value, null, 2)
  );
}

const conversations = computed(() => {
  const conversationList =
    (apiResponse.value as PaginatedConversationsResponse)?.data || [];

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
