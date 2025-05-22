<template>
  <div class="relative flex h-screen antialiased text-gray-800">
    <div class="flex h-full w-full">
      <ChatSidebar
        ref="chatSidebarRef"
        :conversations="sidebarConversations"
        :pending="sidebarPending"
        :error="sidebarError"
        :selected-conversation-room-token="currentRoomToken"
        @conversation-selected="handleSidebarConversationSelected"
        @close-sidebar="closeMobileSidebar"
      />

      <!-- Overlay for mobile when sidebar is open -->
      <div
        v-if="isMobileSidebarOpen"
        class="fixed inset-0 z-20 bg-black bg-opacity-50 md:hidden"
        aria-hidden="true"
        @click="closeMobileSidebar"
      />

      <!-- Main Chat Area -->
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
          <h2 class="ml-4 text-xl font-semibold">
            {{ currentConversation?.participants[0]?.name || "チャット" }}
          </h2>
        </div>

        <div
          class="flex h-full flex-auto flex-shrink-0 flex-col rounded-2xl bg-gray-100 p-4"
        >
          <!-- Messages Display Area -->
          <div
            ref="messageContainerRef"
            class="flex flex-col h-full overflow-x-auto mb-4"
          >
            <div
              v-if="
                isLoadingInitialData ||
                (!currentConversation && !conversationError && !messagesError)
              "
              class="flex items-center justify-center h-full"
            >
              <p>メッセージを読み込み中...</p>
            </div>
            <div
              v-else-if="conversationError"
              class="flex items-center justify-center h-full"
            >
              <p class="text-red-500">会話情報の読み込みに失敗しました。</p>
            </div>
            <div
              v-else-if="!currentConversation"
              class="flex items-center justify-center h-full"
            >
              <p>会話が見つかりません。</p>
            </div>
            <div v-else>
              <div
                v-if="hasNextPage && !messagesPending && !loadingMoreMessages"
                class="text-center my-2"
              >
                <button
                  :disabled="loadingMoreMessages"
                  class="px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-100 rounded-md hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                  @click="loadMoreMessages"
                >
                  <span v-if="loadingMoreMessages">読み込み中...</span>
                  <span v-else>さらに読み込む</span>
                </button>
              </div>
              <div class="grid grid-cols-12 gap-y-2">
                <template
                  v-for="(message, index) in messages"
                  :key="message.id"
                >
                  <div
                    v-if="shouldShowDateSeparator(message, index, messages)"
                    class="col-span-12 text-center my-2"
                  >
                    <span
                      class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded-full"
                    >
                      -- {{ formatDateSeparatorText(message.sent_at) }} --
                    </span>
                  </div>
                  <div
                    :class="
                      isMyMessage(message.sender_id)
                        ? 'col-start-6 col-end-13'
                        : 'col-start-1 col-end-8'
                    "
                    class="p-3 rounded-lg"
                  >
                    <div
                      :class="
                        isMyMessage(message.sender_id)
                          ? 'flex items-center justify-start flex-row-reverse'
                          : 'flex flex-row items-center'
                      "
                    >
                      <div
                        class="flex items-center justify-center h-10 w-10 rounded-full flex-shrink-0"
                        :class="
                          isMyMessage(message.sender_id)
                            ? 'bg-indigo-500 text-white'
                            : 'bg-gray-300'
                        "
                      >
                        {{ getAvatarInitials(message.sender?.name) }}
                      </div>
                      <div
                        class="relative ml-3 mr-3 text-sm py-2 px-4 shadow rounded-xl"
                        :class="
                          isMyMessage(message.sender_id)
                            ? 'bg-indigo-100'
                            : 'bg-white'
                        "
                      >
                        <div class="whitespace-pre-line">
                          {{ message.text_content }}
                        </div>
                        <div
                          class="absolute text-xs bottom-0 right-0 -mb-4 mr-2 text-gray-500 min-w-[3.5rem]"
                        >
                          {{ formatMessageTime(message.sent_at) }}
                        </div>
                      </div>
                    </div>
                  </div>
                </template>
              </div>
            </div>
          </div>

          <!-- Message Input Area -->
          <div class="mt-auto border-t border-gray-200 pt-4">
            <div class="flex items-center space-x-2">
              <textarea
                v-model="newMessageText"
                :disabled="
                  !currentConversation || sendingMessage || isLoadingInitialData
                "
                class="flex-grow p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
                rows="1"
                placeholder="メッセージを入力..."
                @keydown="handleKeydown"
              />
              <button
                :disabled="
                  !currentConversation ||
                  !newMessageText.trim() ||
                  sendingMessage ||
                  isLoadingInitialData
                "
                class="bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline disabled:opacity-50 disabled:cursor-not-allowed"
                @click="sendMessage"
              >
                <svg
                  v-if="sendingMessage"
                  class="animate-spin h-5 w-5 text-white"
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
                <span v-else>送信</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick, watchEffect, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "~/stores/auth";
import { storeToRefs } from "pinia";
import ChatSidebar from "~/components/ChatSidebar.vue";
import { useToast } from "~/composables/useToast";

// Type definitions (Consider moving to a shared file if not already)
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
  room_token: string;
  participants: Participant[];
  latest_message: LatestMessage | null;
  unread_messages_count: number;
  type?: string;
  created_at?: string;
  updated_at?: string;
};

type Message = {
  id: number;
  conversation_id: number;
  sender_id: number;
  content_type: string;
  text_content: string | null;
  sent_at: string;
  sender: MessageSender;
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

type PaginatedMessagesResponse = {
  current_page: number;
  data: Message[];
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
const { user: authUser } = storeToRefs(authStore);
const route = useRoute();
const router = useRouter();
const config = useRuntimeConfig();
const toast = useToast();

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

const currentRoomToken = computed(() => route.params.room_token as string);

// Renamed and refactored pending/error states for clarity
const conversationPending = ref(true); // Indicates conversation specific loading
const conversationError = ref<Error | null>(null);
const messagesPending = ref(false); // For loading more messages or initial message load for a conversation
const messagesError = ref<Error | null>(null); // Specific to message fetching errors

const currentConversation = ref<Conversation | null>(null);
const messages = ref<Message[]>([]);
const newMessageText = ref("");
const sendingMessage = ref(false);
const messageContainerRef = ref<HTMLDivElement | null>(null);
const currentPage = ref(1);
const hasNextPage = ref(false);
const loadingMoreMessages = ref(false);

// Sidebar related state
const chatSidebarRef = ref<InstanceType<typeof ChatSidebar> | null>(null);
const isMobileSidebarOpen = ref(false);
const {
  data: sidebarApiResponse,
  pending: sidebarPending,
  error: sidebarError,
} = await useFetch<PaginatedConversationsResponse>(
  `${config.public.apiBase}/conversations`,
  {
    method: "GET",
    headers: computed(() => ({
      // Make sidebar headers reactive to token too
      Accept: "application/json",
      ...(authStore.token
        ? { Authorization: `Bearer ${authStore.token}` }
        : {}),
    })),
    server: false,
  }
);
const sidebarConversations = computed(
  () => sidebarApiResponse.value?.data || []
);

// Fetch headers for conversation details, reactive to authStore.token
const conversationDetailHeaders = computed(() => {
  console.log(
    "[ChatRoom] Computing conversationDetailHeaders. Auth token present:",
    !!authStore.token
  );
  return {
    Accept: "application/json",
    ...(authStore.token ? { Authorization: `Bearer ${authStore.token}` } : {}),
  };
});

// useFetch for conversation details - defined once, non-immediate
const {
  data: fetchedConvData,
  error: fetchedConvError,
  pending: fetchConvPending,
  execute: executeFetchConversationDetails,
} = useFetch<Conversation>(
  () => {
    const roomToken = currentRoomToken.value;
    if (!roomToken) return ""; // Prevent fetch if no room token by providing invalid URL effectively
    return `${config.public.apiBase}/conversations/token/${roomToken}`;
  },
  {
    headers: conversationDetailHeaders,
    immediate: false, // Crucial: Do not run on setup, we trigger manually
    server: false,
    watch: false, // We control refresh via watchEffect
  }
);

// Function to fetch messages (to be called after conversation is loaded)
const fetchMessagesForCurrentConversation = async () => {
  if (!currentConversation.value) {
    console.warn(
      "[ChatRoom] fetchMessages: No currentConversation to fetch messages for."
    );
    messages.value = [];
    return;
  }
  console.log(
    `[ChatRoom] fetchMessages: Fetching messages for conv ID: ${currentConversation.value.id}, room_token: ${currentConversation.value.room_token}`
  );
  messagesPending.value = true;
  messagesError.value = null;
  currentPage.value = 1; // Reset pagination for new conversation messages
  hasNextPage.value = false;

  try {
    const msgData = await $fetch<PaginatedMessagesResponse>(
      `${config.public.apiBase}/conversations/room/${currentConversation.value.room_token}/messages?page=${currentPage.value}`,
      {
        headers: conversationDetailHeaders.value,
      }
    );

    messages.value = msgData.data.sort(
      (a: Message, b: Message) =>
        new Date(a.sent_at).getTime() - new Date(b.sent_at).getTime()
    );
    hasNextPage.value = msgData.next_page_url !== null;
    await markConversationAsRead(currentConversation.value.id); // Mark as read after messages load
    console.log(
      "[ChatRoom] Messages processed. Message count:",
      messages.value.length,
      "Has next page:",
      hasNextPage.value
    );
  } catch (e: unknown) {
    console.error(
      `[ChatRoom] Error fetching messages for conversation ${currentConversation.value.id}:`,
      e
    );
    if (
      typeof e === "object" &&
      e !== null &&
      "data" in e &&
      typeof (e as { data?: { message?: string } }).data === "object" &&
      (e as { data?: { message?: string } }).data !== null &&
      "message" in (e as { data: { message?: string } }).data
    ) {
      messagesError.value = new Error(
        String((e as { data: { message: string } }).data.message)
      );
    } else if (e instanceof Error) {
      messagesError.value = e;
    } else {
      messagesError.value = new Error(
        "An unknown error occurred while fetching messages."
      );
    }
    messages.value = [];
  } finally {
    messagesPending.value = false;
    await scrollToBottom();
  }
};

// Main data fetching orchestrator
watchEffect(async () => {
  const roomTokenVal = currentRoomToken.value;
  const authTokenVal = authStore.token;

  console.log(
    `[ChatRoom] Main watchEffect. RoomToken: ${roomTokenVal}, AuthToken: ${
      authTokenVal ? "present" : "null"
    }`
  );

  if (roomTokenVal && authTokenVal) {
    conversationPending.value = true;
    conversationError.value = null;
    currentConversation.value = null; // Reset before fetching new one
    messages.value = []; // Clear messages when conversation changes

    console.log(
      `[ChatRoom] watchEffect: Fetching conversation details for roomToken: ${roomTokenVal}`
    );
    await executeFetchConversationDetails(); // Execute the fetch

    conversationPending.value = fetchConvPending.value; // Reflect pending state
    currentConversation.value = fetchedConvData.value;
    conversationError.value = fetchedConvError.value;

    console.log(
      "[ChatRoom] watchEffect: Conversation fetch completed.",
      "Data:",
      JSON.parse(JSON.stringify(currentConversation.value)),
      "Error:",
      JSON.parse(JSON.stringify(conversationError.value)),
      "Pending was:",
      fetchConvPending.value
    );

    if (currentConversation.value && !conversationError.value) {
      await fetchMessagesForCurrentConversation();
    } else if (conversationError.value) {
      console.error(
        `[ChatRoom] watchEffect: Error occurred fetching conversation ${roomTokenVal}:`,
        JSON.parse(JSON.stringify(conversationError.value))
      );
      // Ensure messagesPending is false if conversation fetch fails before message fetch starts
      messagesPending.value = false;
    } else {
      console.warn(
        `[ChatRoom] watchEffect: Conversation data is null for ${roomTokenVal} even after fetch attempt.`
      );
      messagesPending.value = false;
    }
  } else {
    console.log(
      "[ChatRoom] watchEffect: Conditions not met for fetching (no roomToken or no authToken)."
    );
    currentConversation.value = null;
    messages.value = [];
    conversationPending.value = !roomTokenVal; // If no room token, not pending. If room token but no auth, still pending auth.
    messagesPending.value = false;
  }
});

// The old fetchConversationAndMessages is no longer needed.
// Replace initialMessagesPending in template with a computed property if needed, or use conversationPending && messagesPending
const isLoadingInitialData = computed(
  () => conversationPending.value || messagesPending.value
);

const openMobileSidebar = () => {
  isMobileSidebarOpen.value = true;
  chatSidebarRef.value?.toggleMobileSidebar(true);
};

const closeMobileSidebar = () => {
  isMobileSidebarOpen.value = false;
  chatSidebarRef.value?.toggleMobileSidebar(false);
};

const handleSidebarConversationSelected = (roomToken: string) => {
  if (roomToken && roomToken !== currentRoomToken.value) {
    router.push(`/chat/${roomToken}/`);
  }
  if (isMobileSidebarOpen.value && window.innerWidth < 768) {
    closeMobileSidebar();
  }
};

const currentUserId = computed<number | undefined>(() => authUser.value?.id);

const isMyMessage = (messageSenderId: number): boolean => {
  return (
    currentUserId.value !== undefined && messageSenderId === currentUserId.value
  );
};

const scrollToBottom = async (behavior: "auto" | "smooth" = "auto") => {
  await nextTick();
  if (messageContainerRef.value) {
    messageContainerRef.value.scrollTo({
      top: messageContainerRef.value.scrollHeight,
      behavior: behavior,
    });
  }
};

const markConversationAsRead = async (conversationId: number) => {
  if (!conversationId) return;
  try {
    const fetchPostHeaders = {
      ...(authStore.token
        ? { Authorization: `Bearer ${authStore.token}` }
        : {}),
    };
    await $fetch(
      `${config.public.apiBase}/conversations/${conversationId}/read`,
      {
        method: "POST",
        headers: fetchPostHeaders,
      }
    );
    const convInSidebar = sidebarConversations.value.find(
      (c) => c.id === conversationId
    );
    if (convInSidebar) convInSidebar.unread_messages_count = 0;
  } catch (readError: unknown) {
    console.error(
      `Error calling $fetch for marking conversation ${conversationId} as read:`,
      readError
    );
  }
};

watch(
  currentRoomToken,
  (newToken, oldToken) => {
    console.log(
      `[ChatRoom] currentRoomToken watcher (navigation). New: ${newToken}, Old: ${oldToken}`
    );
    // The watchEffect should handle fetching new data when currentRoomToken changes.
    // We might not need to do anything explicit here anymore unless it's for
    // resetting states not covered by watchEffect's re-run.
    if (newToken && newToken !== oldToken) {
      // Ensure message container scrolls to top or resets if that's desired on navigating between rooms
      if (messageContainerRef.value) messageContainerRef.value.scrollTop = 0;
    }
  }
  // { immediate: true } // Removed immediate as watchEffect handles initial load
);

watch(
  messages,
  async (newMessages, oldMessages) => {
    if (newMessages.length > (oldMessages?.length || 0)) {
      if (!loadingMoreMessages.value) {
        scrollToBottom("smooth");
      }
    }
  },
  { deep: true }
);

const loadMoreMessages = async () => {
  if (
    !currentConversation.value ||
    !hasNextPage.value ||
    loadingMoreMessages.value ||
    messagesPending.value
  )
    return;

  loadingMoreMessages.value = true;
  messagesError.value = null;
  currentPage.value++;

  const messageContainer = messageContainerRef.value;
  const previousScrollHeight = messageContainer?.scrollHeight || 0;
  const previousScrollTop = messageContainer?.scrollTop || 0;

  try {
    const fetchMoreMessagesHeaders = {
      Accept: "application/json",
      ...(authStore.token
        ? { Authorization: `Bearer ${authStore.token}` }
        : {}),
    };
    const data = await $fetch<PaginatedMessagesResponse>(
      `${config.public.apiBase}/conversations/room/${currentConversation.value.room_token}/messages?page=${currentPage.value}`,
      {
        headers: fetchMoreMessagesHeaders,
      }
    );

    const newMsgs = data.data.sort(
      (a: Message, b: Message) =>
        new Date(a.sent_at).getTime() - new Date(b.sent_at).getTime()
    );
    messages.value = [...newMsgs, ...messages.value];
    hasNextPage.value = data.next_page_url !== null;
    await nextTick();
    if (messageContainer) {
      const newScrollHeight = messageContainer.scrollHeight;
      messageContainer.scrollTop =
        previousScrollTop + (newScrollHeight - previousScrollHeight);
    }
  } catch (e: unknown) {
    console.error("[ChatRoom] Error loading more messages:", e);
    if (
      typeof e === "object" &&
      e !== null &&
      "data" in e &&
      typeof (e as { data?: { message?: string } }).data === "object" &&
      (e as { data?: { message?: string } }).data !== null &&
      "message" in (e as { data: { message?: string } }).data
    ) {
      messagesError.value = new Error(
        String((e as { data: { message: string } }).data.message)
      );
    } else if (e instanceof Error) {
      messagesError.value = e;
    } else {
      messagesError.value = new Error(
        "An unknown error occurred while loading more messages."
      );
    }
    currentPage.value--;
  } finally {
    loadingMoreMessages.value = false;
  }
};

const sendMessage = async () => {
  if (
    !currentConversation.value ||
    !newMessageText.value.trim() ||
    sendingMessage.value
  )
    return;

  sendingMessage.value = true;
  const conversationId = currentConversation.value.id;
  const textContent = newMessageText.value;

  try {
    const sendMessageHeaders = {
      Accept: "application/json",
      "Content-Type": "application/json",
      ...(authStore.token
        ? { Authorization: `Bearer ${authStore.token}` }
        : {}),
    };
    const sentMessageData = await $fetch<Message>(
      `${config.public.apiBase}/conversations/room/${currentConversation.value.room_token}/messages`,
      {
        method: "POST",
        headers: sendMessageHeaders,
        body: { text_content: textContent },
      }
    );

    messages.value.push(sentMessageData);
    newMessageText.value = "";
    // Update latest message in sidebar
    const convInSidebar = sidebarConversations.value.find(
      (c) => c.id === conversationId
    );
    if (convInSidebar) {
      convInSidebar.latest_message = {
        id: sentMessageData.id,
        text_content: sentMessageData.text_content,
        sent_at: sentMessageData.sent_at,
        sender: sentMessageData.sender,
      };
    }
    await scrollToBottom("smooth");
  } catch (e: unknown) {
    console.error("Error sending message:", e);
    alert("メッセージの送信に失敗しました。");
  } finally {
    sendingMessage.value = false;
  }
};

const getAvatarInitials = (name?: string): string => {
  if (!name) return "?";
  const nameParts = name.split(" ");
  if (nameParts.length > 1 && nameParts[0] && nameParts[nameParts.length - 1]) {
    return (nameParts[0][0] + nameParts[nameParts.length - 1][0]).toUpperCase();
  }
  return name?.[0]?.toUpperCase() || "?";
};

const formatMessageTime = (sentAt?: string | null): string => {
  if (!sentAt) return "";
  return new Date(sentAt).toLocaleTimeString("ja-JP", {
    hour: "numeric",
    minute: "2-digit",
    hour12: true,
  });
};

const formatDateSeparatorText = (sentAt?: string | null): string => {
  if (!sentAt) return "";
  const date = new Date(sentAt);
  return `${date.getFullYear()}.${String(date.getMonth() + 1).padStart(
    2,
    "0"
  )}.${String(date.getDate()).padStart(2, "0")}`;
};

const shouldShowDateSeparator = (
  message: Message,
  index: number,
  allMessages: Message[]
): boolean => {
  if (index === 0) return true;
  const prevMessage = allMessages[index - 1];
  if (!prevMessage?.sent_at || !message.sent_at) return false;
  return (
    new Date(prevMessage.sent_at).toDateString() !==
    new Date(message.sent_at).toDateString()
  );
};

const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === "Enter" && !event.shiftKey) {
    event.preventDefault();
    sendMessage();
  }
};
</script>

<style scoped>
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
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
