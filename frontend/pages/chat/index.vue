<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- ナビゲーションバー -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <div class="flex items-center space-x-3">
              <div
                class="h-8 w-8 bg-emerald-600 rounded-lg flex items-center justify-center"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-white"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"
                  />
                </svg>
              </div>
              <h1 class="text-xl font-bold text-gray-900">LumoChat</h1>
            </div>
          </div>
          <div class="flex items-center space-x-3">
            <NuxtLink
              to="/user"
              class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-150 ease-in-out"
            >
              <svg
                class="w-4 h-4 mr-2"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                />
              </svg>
              ホーム
            </NuxtLink>
            <NuxtLink
              to="/friends"
              class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-150 ease-in-out"
            >
              <svg
                class="w-4 h-4 mr-2"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"
                />
              </svg>
              友達
            </NuxtLink>
          </div>
        </div>
      </div>
    </nav>

    <div
      class="relative flex antialiased text-gray-800"
      style="height: calc(100vh - 4rem)"
    >
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
              class="rounded-md p-2 text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500"
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
            class="flex h-full flex-auto flex-shrink-0 flex-col rounded-2xl bg-white shadow-sm border border-gray-200 p-8 items-center justify-center"
          >
            <div class="text-center max-w-md">
              <div
                class="h-20 w-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6"
              >
                <svg
                  class="w-10 h-10 text-emerald-600"
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
              </div>
              <h2 class="text-2xl font-bold text-gray-900 mb-3">
                会話を選択してください
              </h2>
              <p class="text-gray-500 mb-6">
                左側のリストから会話を選択してチャットを開始するか、友達ページで新しい友達を追加してチャットを始めましょう。
              </p>
              <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <NuxtLink
                  to="/friends"
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition duration-200 shadow-md hover:shadow-lg"
                >
                  <svg
                    class="w-4 h-4 mr-2"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"
                    />
                  </svg>
                  友達を追加
                </NuxtLink>
                <button
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-200 md:hidden"
                  @click="openMobileSidebar"
                >
                  <svg
                    class="w-4 h-4 mr-2"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  会話一覧を表示
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useAuthStore } from "~/stores/auth";
import { useRouter } from "vue-router";
import ChatSidebar from "~/components/ChatSidebar.vue";
import { useToast } from "~/composables/useToast";

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
const router = useRouter();
const toast = useToast();

const chatSidebarRef = ref<InstanceType<typeof ChatSidebar> | null>(null);
const isMobileSidebarOpen = ref(false);

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
      router.push("/auth/login");
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
    router.push("/auth/login");
  }
});

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
