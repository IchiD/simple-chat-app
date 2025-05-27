<template>
  <div
    class="fixed inset-y-0 left-0 z-30 flex w-full flex-col transform border-r border-gray-200/50 bg-gradient-to-b from-white to-gray-50/50 backdrop-blur-sm pb-8 pl-4 pr-4 transition-transform duration-300 ease-in-out md:static md:w-80 md:flex-shrink-0 md:transform-none shadow-lg md:shadow-none"
    :class="{
      'translate-x-0': isSidebarOpen,
      '-translate-x-full': !isSidebarOpen,
    }"
  >
    <!-- Navigation Tabs -->
    <nav class="mt-6 mb-4 border-b border-gray-200">
      <ul class="flex justify-around -mb-px">
        <li v-for="tab in navigationTabs" :key="tab.name">
          <NuxtLink
            :to="tab.path"
            class="inline-block py-3 px-2 text-sm font-medium text-center border-b-2"
            :class="[
              route.path === tab.path
                ? 'text-[var(--primary)] border-[var(--primary)] active'
                : 'text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300',
            ]"
          >
            {{ tab.name }}
          </NuxtLink>
        </li>
      </ul>
    </nav>
    <div class="flex h-10 w-full flex-row items-center justify-center">
      <div
        class="flex h-9 w-9 items-center justify-center rounded-2xl text-white"
        style="background-color: var(--primary-light)"
      >
        <svg
          class="h-6 w-6"
          viewBox="0 0 20 20"
          fill="currentColor"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"
          />
          <path
            d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"
          />
        </svg>
      </div>

      <div class="ml-2 text-xl font-bold">トークリスト</div>
      <!-- Close button for sidebar on mobile -->
      <button
        class="ml-auto mr-2 md:hidden rounded-md p-1 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[var(--primary)]"
        @click="isSidebarOpen = false"
      >
        <span class="sr-only">Close sidebar</span>
        <svg
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          aria-hidden="true"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
      </button>
    </div>

    <!-- Conversation List -->
    <div class="flex flex-col mt-4 overflow-y-auto">
      <div v-if="pending" class="flex justify-center items-center h-48">
        <svg
          class="animate-spin h-8 w-8"
          style="color: var(--primary)"
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
      </div>
      <div v-else-if="error" class="px-2 py-4 text-center text-red-500">
        <p>エラーが発生しました。<br />{{ error?.message }}</p>
      </div>
      <div
        v-else-if="!conversations || conversations.length === 0"
        class="px-4 py-8 text-center"
      >
        <div
          class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4"
        >
          <svg
            class="w-8 h-8 text-gray-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
            />
          </svg>
        </div>
        <p class="text-gray-500 font-medium mb-2">会話はありません</p>
        <p class="text-xs text-gray-400">
          友達を追加してチャットを始めましょう
        </p>
      </div>
      <button
        v-for="convo in conversations"
        :key="convo.id"
        class="group relative flex flex-row items-center hover:bg-gradient-to-r hover:from-emerald-50 hover:to-blue-50 p-3 m-2 rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-300 shadow-sm hover:shadow-md border border-transparent hover:border-emerald-100"
        :class="{
          'bg-gradient-to-r from-emerald-100 to-emerald-50 border-emerald-200 shadow-md':
            selectedConversationRoomToken === convo.room_token,
        }"
        @click="onConversationClick(convo)"
      >
        <!-- アバター -->
        <div class="relative">
          <div
            class="flex-shrink-0 h-12 w-12 rounded-full flex items-center justify-center text-white font-semibold shadow-lg ring-2 ring-white transition-transform group-hover:scale-105"
            style="
              background: linear-gradient(
                135deg,
                var(--primary),
                var(--primary-dark)
              );
            "
          >
            <img
              v-if="convo.participants[0]?.avatar"
              :src="convo.participants[0]?.avatar"
              alt="Avatar"
              class="h-full w-full rounded-full object-cover"
            />
            <span v-else class="text-sm font-bold">{{
              getAvatarInitials(convo.participants[0]?.name)
            }}</span>
          </div>
          <!-- オンライン状態インジケーター -->
          <div
            class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 border-2 border-white rounded-full shadow-sm"
          />
        </div>

        <!-- メッセージ内容 -->
        <div class="ml-3 text-sm flex-grow text-left min-w-0">
          <div class="flex items-center justify-between mb-1">
            <h4 class="font-semibold text-gray-900 truncate">
              {{ convo.participants[0]?.name || "不明なユーザー" }}
            </h4>
            <div class="flex items-center space-x-2">
              <!-- 未読数バッジ -->
              <span
                v-if="convo.unread_messages_count > 0"
                class="inline-flex items-center justify-center bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold rounded-full h-6 w-6 shadow-md animate-pulse"
              >
                {{
                  convo.unread_messages_count > 9
                    ? "9+"
                    : convo.unread_messages_count
                }}
              </span>
            </div>
          </div>

          <!-- 最新メッセージ -->
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-600 truncate max-w-40">
              <span
                v-if="
                  convo.latest_message &&
                  currentUserId !== undefined &&
                  convo.latest_message.sender?.id === currentUserId
                "
                class="text-emerald-600 font-medium"
                >あなた:
              </span>
              <span class="inline">{{
                convo.latest_message?.text_content || "メッセージはありません"
              }}</span>
            </p>

            <!-- 時間表示 -->
            <span
              class="text-xs text-gray-400 whitespace-nowrap ml-2 font-medium"
            >
              {{ formatSentAt(convo.latest_message?.sent_at) }}
            </span>
          </div>
        </div>

        <!-- ホバー時のアロー -->
        <div
          class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 ml-2"
        >
          <svg
            class="w-4 h-4 text-emerald-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 5l7 7-7 7"
            />
          </svg>
        </div>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import type { PropType } from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "../stores/auth";
import { storeToRefs } from "pinia";

// Types (Copied from pages/chat/index.vue, consider moo a shared types file)
type Participant = {
  id: number;
  name: string;
  avatar?: string | null;
};

type MessageSender = {
  id: number;
  name: string;
};

type LatestMessage = {
  id: number;
  text_content: string | null;
  sent_at: string | null;
  sender: MessageSender | null;
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

defineProps({
  conversations: {
    type: Array as PropType<Conversation[] | undefined>,
    required: true,
  },
  pending: {
    type: Boolean,
    default: false,
  },
  error: {
    type: Object as PropType<Error | null>,
    default: null,
  },
  selectedConversationRoomToken: {
    type: String as PropType<string | null>,
    default: null,
  },
});

const emit = defineEmits(["conversationSelected", "closeSidebar"]);

const route = useRoute();

const isSidebarOpen = ref(true); // Default to open on larger screens, parent can control this for mobile

const navigationTabs = [
  { name: "ホーム", path: "/" },
  { name: "友達", path: "/friends" },
];

const { user: authUser } = storeToRefs(useAuthStore());

const currentUserId = computed<number | undefined>(() => authUser.value?.id);

const getAvatarInitials = (name?: string): string => {
  if (!name) return "?";
  const nameParts = name.split(" ");
  if (nameParts.length > 1 && nameParts[0] && nameParts[nameParts.length - 1]) {
    return (nameParts[0][0] + nameParts[nameParts.length - 1][0]).toUpperCase();
  }
  if (name && name.length > 0) {
    return name[0].toUpperCase();
  }
  return "?";
};

const formatSentAt = (sentAt?: string | null): string => {
  if (!sentAt) return "";
  const date = new Date(sentAt);
  const now = new Date();
  const diffSeconds = Math.round((now.getTime() - date.getTime()) / 1000);

  if (diffSeconds < 5) return "たった今";
  if (diffSeconds < 60) return `${diffSeconds}秒前`;

  const diffMinutes = Math.round(diffSeconds / 60);
  if (diffMinutes < 60) return `${diffMinutes}分前`;

  const diffHours = Math.round(diffMinutes / 60);
  if (diffHours < 24) return `${diffHours}時間前`;

  const diffDays = Math.round(diffHours / 24);
  if (diffDays === 1) return "昨日";
  if (diffDays < 7) return `${diffDays}日前`;

  return date.toLocaleDateString("ja-JP", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const onConversationClick = (conversation: Conversation) => {
  emit("conversationSelected", conversation.room_token);
  // On mobile, clicking a conversation should also close the sidebar.
  // The parent component (page) will manage the actual isSidebarOpen state for mobile toggling.
  // This component's isSidebarOpen is for the fixed/transform class logic.
  // emit('closeSidebar'); // Parent page should handle this.
};

// Method to be called by parent to toggle sidebar on mobile
const toggleMobileSidebar = (open: boolean) => {
  isSidebarOpen.value = open;
};

defineExpose({ toggleMobileSidebar });
</script>

<style scoped>
/* Add any page-specific styles here if needed */
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
/* Sidebar scrollbar styling (optional) */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: #cbd5e0; /* Tailwind gray-400 */
  border-radius: 3px;
}
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background-color: #a0aec0; /* Tailwind gray-500 */
}
</style>
