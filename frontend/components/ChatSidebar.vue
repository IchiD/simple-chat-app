<template>
  <div
    class="fixed inset-y-0 left-0 z-30 flex w-full flex-col transform border-r border-gray-200 bg-white pb-8 pl-6 pr-6 transition-transform duration-300 ease-in-out md:static md:w-80 md:flex-shrink-0 md:transform-none"
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
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
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
        class="px-2 py-4 text-center text-gray-500"
      >
        <p>会話はありません。</p>
      </div>
      <button
        v-for="convo in conversations"
        :key="convo.id"
        class="flex flex-row items-start hover:bg-gray-100 p-2 focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
        :class="{
          'bg-[var(--primary-light)]/20':
            selectedConversationRoomToken === convo.room_token,
        }"
        @click="onConversationClick(convo)"
      >
        <div
          class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center text-white font-semibold"
          style="background-color: var(--primary)"
        >
          <img
            v-if="convo.participants[0]?.avatar"
            :src="convo.participants[0]?.avatar"
            alt="Avatar"
            class="h-full w-full rounded-full object-cover"
          />
          <span v-else>{{
            getAvatarInitials(convo.participants[0]?.name)
          }}</span>
        </div>
        <div class="ml-2 text-sm flex-grow text-left max-w-[50%]">
          <div class="font-semibold">
            {{ convo.participants[0]?.name || "不明なユーザー" }}
          </div>
          <p class="text-xs text-gray-500 truncate w-48">
            <span
              v-if="
                convo.latest_message &&
                currentUserId !== undefined &&
                convo.latest_message.sender?.id === currentUserId
              "
              >自分:
            </span>
            {{ convo.latest_message?.text_content || "メッセージはありません" }}
          </p>
        </div>
        <!-- Time and Unread Count -->
        <div
          class="flex flex-row items-center ml-auto text-xs space-x-1 flex-shrink-0"
        >
          <span
            v-if="convo.unread_messages_count > 0"
            class="flex items-center justify-center bg-red-500 text-white text-xs rounded-full h-5 w-5"
          >
            {{ convo.unread_messages_count }}
          </span>
          <span class="text-gray-500 whitespace-nowrap">{{
            formatSentAt(convo.latest_message?.sent_at)
          }}</span>
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
