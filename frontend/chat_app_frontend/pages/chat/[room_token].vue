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
              v-if="initialMessagesPending || !currentConversation"
              class="flex items-center justify-center h-full"
            >
              <p>メッセージを読み込み中...</p>
            </div>
            <div
              v-else-if="messagesError"
              class="flex items-center justify-center h-full"
            >
              <p class="text-red-500">メッセージの読み込みに失敗しました。</p>
            </div>
            <div
              v-else-if="messages.length === 0 && !hasNextPage"
              class="flex items-center justify-center h-full"
            >
              <p>メッセージはありません。</p>
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
                  !currentConversation ||
                  sendingMessage ||
                  initialMessagesPending
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
                  initialMessagesPending
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
import { ref, computed, watch, nextTick, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "~/stores/auth";
import { storeToRefs } from "pinia";
import ChatSidebar from "~/components/ChatSidebar.vue";

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

const currentRoomToken = computed(() => route.params.room_token as string);
const currentConversation = ref<Conversation | null>(null);

const messages = ref<Message[]>([]);
const messagesPending = ref(false); // For loading more messages
const initialMessagesPending = ref(true); // For initial load of conversation and messages
const messagesError = ref<Error | null>(null);
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
    headers: {
      Accept: "application/json",
      ...(authStore.token
        ? { Authorization: `Bearer ${authStore.token}` }
        : {}),
    },
    server: false,
  }
);
const sidebarConversations = computed(
  () => sidebarApiResponse.value?.data || []
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

const fetchConversationAndMessages = async (roomToken: string) => {
  if (!roomToken) return;
  initialMessagesPending.value = true;
  messagesError.value = null;
  messages.value = [];
  currentPage.value = 1;
  hasNextPage.value = false;
  currentConversation.value = null;

  try {
    // Fetch conversation details by room_token
    const { data: convData, error: convError } = await useFetch<Conversation>(
      `${config.public.apiBase}/conversations/token/${roomToken}`,
      {
        headers: {
          Accept: "application/json",
          ...(authStore.token
            ? { Authorization: `Bearer ${authStore.token}` }
            : {}),
        },
        server: false,
      }
    );

    if (convError.value) {
      messagesError.value = convError.value;
      console.error(
        `Error fetching conversation ${roomToken}:`,
        convError.value
      );
      initialMessagesPending.value = false;
      return;
    }
    currentConversation.value = convData.value;

    if (currentConversation.value) {
      // Fetch messages for this conversation
      const { data: msgData, error: msgFetchError } =
        await useFetch<PaginatedMessagesResponse>(
          `${config.public.apiBase}/conversations/${currentConversation.value.id}/messages?page=${currentPage.value}`,
          {
            headers: {
              Accept: "application/json",
              ...(authStore.token
                ? { Authorization: `Bearer ${authStore.token}` }
                : {}),
            },
            server: false,
          }
        );

      if (msgFetchError.value) {
        messagesError.value = msgFetchError.value;
        console.error(
          `Error fetching messages for conversation ${currentConversation.value.id}:`,
          msgFetchError.value
        );
      } else if (msgData.value) {
        messages.value = msgData.value.data.sort(
          (a, b) =>
            new Date(a.sent_at).getTime() - new Date(b.sent_at).getTime()
        );
        hasNextPage.value = msgData.value.next_page_url !== null;
        await markConversationAsRead(currentConversation.value.id);
      }
    }
  } catch (e) {
    if (e instanceof Error) messagesError.value = e;
    else messagesError.value = new Error(String(e));
    console.error(
      `Critical error fetching conversation/messages for ${roomToken}:`,
      e
    );
  } finally {
    initialMessagesPending.value = false;
    await scrollToBottom();
  }
};

const markConversationAsRead = async (conversationId: number) => {
  if (!conversationId) return;
  try {
    await useFetch(
      `${config.public.apiBase}/conversations/${conversationId}/read`,
      {
        method: "POST",
        headers: {
          ...(authStore.token
            ? { Authorization: `Bearer ${authStore.token}` }
            : {}),
        },
        server: false,
        onResponse({ response }) {
          if (response.ok) {
            const convInSidebar = sidebarConversations.value.find(
              (c) => c.id === conversationId
            );
            if (convInSidebar) convInSidebar.unread_messages_count = 0;
          } else {
            console.error(
              `Failed to mark conversation ${conversationId} as read. Status: ${response.status}`
            );
          }
        },
      }
    );
  } catch (readError) {
    console.error(
      `Error calling useFetch for marking conversation ${conversationId} as read:`,
      readError
    );
  }
};

watch(
  currentRoomToken,
  (newToken) => {
    if (newToken) {
      fetchConversationAndMessages(newToken);
    }
  },
  { immediate: true }
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
  messagesPending.value = true; // Indicate general message loading activity
  messagesError.value = null;
  currentPage.value++;

  const messageContainer = messageContainerRef.value;
  const previousScrollHeight = messageContainer?.scrollHeight || 0;
  const previousScrollTop = messageContainer?.scrollTop || 0;

  try {
    const { data, error: fetchError } =
      await useFetch<PaginatedMessagesResponse>(
        `${config.public.apiBase}/conversations/${currentConversation.value.id}/messages?page=${currentPage.value}`,
        {
          headers: {
            Accept: "application/json",
            ...(authStore.token
              ? { Authorization: `Bearer ${authStore.token}` }
              : {}),
          },
          server: false,
        }
      );

    if (fetchError.value) {
      messagesError.value = fetchError.value;
      currentPage.value--;
    } else if (data.value) {
      const newMsgs = data.value.data.sort(
        (a, b) => new Date(a.sent_at).getTime() - new Date(b.sent_at).getTime()
      );
      messages.value = [...newMsgs, ...messages.value];
      hasNextPage.value = data.value.next_page_url !== null;
      await nextTick();
      if (messageContainer) {
        const newScrollHeight = messageContainer.scrollHeight;
        messageContainer.scrollTop =
          previousScrollTop + (newScrollHeight - previousScrollHeight);
      }
    }
  } catch (e) {
    if (e instanceof Error) messagesError.value = e;
    else messagesError.value = new Error(String(e));
    currentPage.value--;
  } finally {
    loadingMoreMessages.value = false;
    messagesPending.value = false;
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
    const { data: sentMessage, error } = await useFetch<Message>(
      `${config.public.apiBase}/conversations/${conversationId}/messages`,
      {
        method: "POST",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          ...(authStore.token
            ? { Authorization: `Bearer ${authStore.token}` }
            : {}),
        },
        body: { text_content: textContent },
        server: false,
      }
    );

    if (error.value) {
      console.error("Error sending message:", error.value);
      // Basic error alert, can be replaced with toast notifications
      alert("メッセージの送信に失敗しました。");
    } else if (sentMessage.value) {
      messages.value.push(sentMessage.value);
      newMessageText.value = "";
      // Update latest message in sidebar
      const convInSidebar = sidebarConversations.value.find(
        (c) => c.id === conversationId
      );
      if (convInSidebar) {
        convInSidebar.latest_message = {
          id: sentMessage.value.id,
          text_content: sentMessage.value.text_content,
          sent_at: sentMessage.value.sent_at,
          sender: sentMessage.value.sender,
        };
      }
      await scrollToBottom("smooth");
    }
  } catch (e) {
    console.error("Critical error sending message:", e);
    alert("メッセージの送信中に予期せぬエラーが発生しました。");
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
