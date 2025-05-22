<template>
  <div class="relative flex h-screen antialiased text-gray-800">
    <div class="flex h-full w-full">
      <ChatSidebar
        ref="chatSidebarRef"
        :conversations="conversations"
        :pending="pending"
        :error="error"
        :selected-conversation-room-token="null"
        @conversation-selected="handleConversationSelected"
        @close-sidebar="closeMobileSidebar"
      />

      <!-- Overlay for mobile when sidebar is open -->
      <div
        v-if="isMobileSidebarOpen"
        class="fixed inset-0 z-20 bg-black bg-opacity-50 md:hidden"
        aria-hidden="true"
        @click="closeMobileSidebar"
      />

      <!-- Main Content Area for chat list page -->
      <div class="flex h-full flex-auto flex-col p-6">
        <!-- Header for Chat Area (with toggle button for mobile) -->
        <div class="mb-4 flex items-center md:hidden">
          <button
            class="rounded-md p-2 text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
            @click="openMobileSidebar"
          >
            <span class="sr-only">Open sidebar</span>
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
                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
              />
            </svg>
          </button>
          <h2 class="ml-4 text-xl font-semibold">LumoChat</h2>
        </div>

        <div
          class="flex h-full flex-auto flex-shrink-0 flex-col rounded-2xl bg-gray-100 p-4 items-center justify-center"
        >
          <div class="text-center">
            <svg
              class="w-16 h-16 mx-auto text-gray-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
              />
            </svg>
            <p class="mt-2 text-lg font-semibold text-gray-700">
              会話を選択してください
            </p>
            <p class="text-sm text-gray-500">
              左側のリストから会話を選択してチャットを開始します。
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { useAuthStore } from "~/stores/auth";
import { useRouter } from "vue-router";
import ChatSidebar from "~/components/ChatSidebar.vue";

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

const chatSidebarRef = ref<InstanceType<typeof ChatSidebar> | null>(null);
const isMobileSidebarOpen = ref(false);

const router = useRouter();

const config = useRuntimeConfig();

const {
  data: apiResponse,
  pending,
  error,
} = await useFetch<PaginatedConversationsResponse>(
  `${config.public.apiBase}/conversations`,
  {
    method: "GET",
    headers: {
      Accept: "application/json",
      ...(authStore.token
        ? { Authorization: `Bearer ${authStore.token}` }
        : {}),
    },
    server: false,
    onResponseError({ response }) {
      console.error(
        `Error fetching conversations: ${response.status} ${response.statusText}`,
        response._data
      );
    },
  }
);

if (error.value) {
  console.error(
    "Detailed error fetching conversations:",
    JSON.stringify(error.value, null, 2)
  );
}

const conversations = computed(() => apiResponse.value?.data || []);

const openMobileSidebar = () => {
  isMobileSidebarOpen.value = true;
  chatSidebarRef.value?.toggleMobileSidebar(true);
};

const closeMobileSidebar = () => {
  isMobileSidebarOpen.value = false;
  chatSidebarRef.value?.toggleMobileSidebar(false);
};

const handleConversationSelected = (roomToken: string) => {
  if (roomToken) {
    router.push(`/chat/${roomToken}/`);
  }
  if (isMobileSidebarOpen.value && window.innerWidth < 768) {
    closeMobileSidebar();
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
