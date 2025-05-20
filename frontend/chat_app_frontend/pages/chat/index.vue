<template>
  <div class="relative flex h-screen antialiased text-gray-800">
    <div class="flex h-full w-full">
      <!-- Sidebar -->
      <div
        class="fixed inset-y-0 left-0 z-30 flex w-full flex-col transform border-r border-gray-200 bg-white py-8 pl-6 pr-6 transition-transform duration-300 ease-in-out md:static md:w-80 md:flex-shrink-0 md:transform-none"
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
                    ? 'text-indigo-600 border-indigo-600 active'
                    : 'text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300',
                ]"
              >
                {{ tab.name }}
              </NuxtLink>
            </li>
          </ul>
        </nav>
        <div class="flex h-12 w-full flex-row items-center justify-center">
          <div
            class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700"
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

          <div class="ml-2 text-2xl font-bold">チャット</div>
          <!-- Close button for sidebar on mobile -->
          <button
            class="ml-auto mr-2 md:hidden rounded-md p-1 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
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

        <!-- Search Bar (Optional UI) -->
        <div class="mt-4">
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
              <svg
                class="h-5 w-5 text-gray-400"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                  clip-rule="evenodd"
                />
              </svg>
            </span>
            <input
              type="text"
              class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-md placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="検索..."
            />
          </div>
        </div>

        <!-- Conversation List -->
        <div class="flex flex-col mt-4 overflow-y-auto">
          <div v-if="pending" class="flex justify-center items-center h-48">
            <svg
              class="animate-spin h-8 w-8 text-indigo-600"
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
            <p>エラーが発生しました。<br />{{ error.message }}</p>
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
            class="flex flex-row items-start hover:bg-gray-100 p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            :class="{ 'bg-indigo-100': selectedConversationId === convo.id }"
            @click="selectConversation(convo.id)"
          >
            <div
              class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-200 flex items-center justify-center text-indigo-700 font-semibold"
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
                    currentUserId.value !== undefined &&
                    convo.latest_message.sender?.id === currentUserId.value
                  "
                  >自分:
                </span>
                {{
                  convo.latest_message?.text_content || "メッセージはありません"
                }}
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

        <!-- New Chat Button (Optional UI) -->
        <div class="mt-auto mb-4">
          <button
            class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline"
          >
            新しいチャット
          </button>
        </div>
      </div>

      <!-- Overlay for mobile when sidebar is open -->
      <div
        v-if="isSidebarOpen"
        class="fixed inset-0 z-20 bg-black bg-opacity-50 md:hidden"
        aria-hidden="true"
        @click="isSidebarOpen = false"
      />

      <!-- Main Chat Area -->
      <div class="flex h-full flex-auto flex-col p-6">
        <!-- Header for Chat Area (with toggle button for mobile) -->
        <div class="mb-4 flex items-center md:hidden">
          <button
            class="rounded-md p-2 text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
            @click="isSidebarOpen = true"
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
            {{
              selectedConversationId
                ? conversations.find((c) => c.id === selectedConversationId)
                    ?.participants[0]?.name || "チャット"
                : "チャット"
            }}
          </h2>
        </div>

        <div
          class="flex h-full flex-auto flex-shrink-0 flex-col rounded-2xl bg-gray-100 p-4"
        >
          <!-- ★ メッセージ表示エリア -->
          <div
            v-if="selectedConversationId"
            ref="messageContainerRef"
            class="flex flex-col h-full overflow-x-auto mb-4"
          >
            <div
              v-if="messagesPending"
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
              v-else-if="
                !selectedConversationId ||
                (messages.length === 0 && !hasNextPage)
              "
              class="flex items-center justify-center h-full"
            >
              <p>
                {{
                  selectedConversationId
                    ? "メッセージはありません。"
                    : "会話を選択してください。"
                }}
              </p>
            </div>
            <div v-else>
              <!-- ★ 「さらに読み込む」ボタン -->
              <div
                v-if="hasNextPage && !messagesPending"
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
              <!-- メッセージのループ表示 -->
              <div class="grid grid-cols-12 gap-y-2">
                <template
                  v-for="(message, index) in messages"
                  :key="message.id"
                >
                  <!-- 日付区切り線 -->
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

                  <!-- メッセージ本体 -->
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
          <!-- メッセージがない、または会話が選択されていない場合の表示 -->
          <div v-else class="flex items-center justify-center h-full">
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
                チャットを選択してください
              </p>
              <p class="text-sm text-gray-500">
                左側のリストから会話を選択して開始します。
              </p>
            </div>
          </div>
          <!-- ★ メッセージ入力エリア -->
          <div class="mt-auto border-t border-gray-200 pt-4">
            <div class="flex items-center space-x-2">
              <textarea
                v-model="newMessageText"
                :disabled="!selectedConversationId || sendingMessage"
                class="flex-grow p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
                rows="1"
                placeholder="メッセージを入力..."
                @keydown="handleKeydown"
              />
              <button
                :disabled="
                  !selectedConversationId ||
                  !newMessageText.trim() ||
                  sendingMessage
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
import { ref, computed, watch, nextTick } from "vue";
import { useAuthStore } from "~/stores/auth";
import { storeToRefs } from "pinia";
import { useRoute } from "vue-router";

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
const selectedConversationId = ref<number | null>(null);

const messages = ref<Message[]>([]);
const messagesPending = ref(false);
const messagesError = ref<Error | null>(null);
const newMessageText = ref("");
const sendingMessage = ref(false);

const messageContainerRef = ref<HTMLDivElement | null>(null);

// 追加読み込み機能のための状態
const currentPage = ref(1);
const hasNextPage = ref(false);
const loadingMoreMessages = ref(false);

// スマホ表示時のサイドバー開閉状態
const isSidebarOpen = ref(false);

const route = useRoute();

const navigationTabs = [
  { name: "ホーム", path: "/" },
  { name: "友達", path: "/friends" },
];

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

const currentUserId = computed<number | undefined>(() => authUser.value?.id);

const isMyMessage = (messageSenderId: number): boolean => {
  return (
    currentUserId.value !== undefined && messageSenderId === currentUserId.value
  );
};

const scrollToBottom = async () => {
  await nextTick();
  if (messageContainerRef.value) {
    messageContainerRef.value.scrollTop =
      messageContainerRef.value.scrollHeight;
  }
};

watch(
  messages,
  async (newMessages, oldMessages) => {
    // メッセージが追加された場合（特に初回ロードや追加ロード時）
    if (newMessages.length > (oldMessages?.length || 0)) {
      // 新しいメッセージが現在のビューポートの下に追加された場合のみ自動スクロール
      // 追加読み込みで上にメッセージが追加された場合はスクロールしない
      if (!loadingMoreMessages.value) {
        // loadingMoreMessages が false の時だけ（つまり、新規メッセージ受信・送信時）
        scrollToBottom();
      }
    }
  },
  { deep: true }
);

watch(
  selectedConversationId,
  async (newConversationId, oldConversationId) => {
    if (newConversationId === null) {
      messages.value = [];
      currentPage.value = 1;
      hasNextPage.value = false;
      return;
    }

    if (newConversationId !== oldConversationId) {
      messagesPending.value = true;
      messagesError.value = null;
      messages.value = [];
      currentPage.value = 1; // ページ番号をリセット
      hasNextPage.value = false;

      try {
        const { data, error: fetchError } =
          await useFetch<PaginatedMessagesResponse>(
            `${config.public.apiBase}/conversations/${newConversationId}/messages?page=${currentPage.value}`,
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

        if (fetchError.value) {
          messagesError.value = fetchError.value;
          console.error(
            `Error fetching messages for conversation ${newConversationId}:`,
            fetchError.value
          );
        } else if (data.value) {
          messages.value = data.value.data.sort(
            (a, b) =>
              new Date(a.sent_at).getTime() - new Date(b.sent_at).getTime()
          );
          hasNextPage.value = data.value.next_page_url !== null;

          if (newConversationId) {
            try {
              await useFetch(
                `${config.public.apiBase}/conversations/${newConversationId}/read`,
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
                      const conversationInList = conversations.value.find(
                        (c) => c.id === newConversationId
                      );
                      if (conversationInList) {
                        conversationInList.unread_messages_count = 0;
                      }
                    } else {
                      console.error(
                        `Failed to mark conversation ${newConversationId} as read. Status: ${response.status}`
                      );
                    }
                  },
                  onRequestError({ error: requestError }) {
                    console.error(
                      `Request error marking conversation ${newConversationId} as read:`,
                      requestError
                    );
                  },
                }
              );
            } catch (readError) {
              console.error(
                `Error calling useFetch for marking conversation ${newConversationId} as read:`,
                readError
              );
            }
          }
        }
      } catch (e) {
        if (e instanceof Error) {
          messagesError.value = e;
        } else {
          messagesError.value = new Error(String(e));
        }
        console.error(
          `Critical error fetching messages for conversation ${newConversationId}:`,
          e
        );
      } finally {
        messagesPending.value = false;
        await scrollToBottom(); // 初回ロード時は一番下にスクロール
      }
    }
  },
  { immediate: false }
);

const loadMoreMessages = async () => {
  if (
    !selectedConversationId.value ||
    !hasNextPage.value ||
    loadingMoreMessages.value
  ) {
    return;
  }

  loadingMoreMessages.value = true;
  messagesError.value = null; // エラー状態をリセット
  currentPage.value++;

  const messageContainer = messageContainerRef.value;
  const previousScrollHeight = messageContainer?.scrollHeight || 0;
  const previousScrollTop = messageContainer?.scrollTop || 0;

  try {
    const { data, error: fetchError } =
      await useFetch<PaginatedMessagesResponse>(
        `${config.public.apiBase}/conversations/${selectedConversationId.value}/messages?page=${currentPage.value}`,
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

    if (fetchError.value) {
      messagesError.value = fetchError.value;
      console.error(
        `Error fetching more messages for conversation ${selectedConversationId.value}:`,
        fetchError.value
      );
      currentPage.value--; // エラー時はページ番号を戻す
    } else if (data.value) {
      const newMessages = data.value.data.sort(
        (a, b) => new Date(a.sent_at).getTime() - new Date(b.sent_at).getTime()
      );
      messages.value = [...newMessages, ...messages.value]; // 新しいメッセージを配列の先頭に追加
      hasNextPage.value = data.value.next_page_url !== null;

      // スクロール位置の調整
      await nextTick();
      if (messageContainer) {
        const newScrollHeight = messageContainer.scrollHeight;
        messageContainer.scrollTop =
          previousScrollTop + (newScrollHeight - previousScrollHeight);
      }
    }
  } catch (e) {
    if (e instanceof Error) {
      messagesError.value = e;
    } else {
      messagesError.value = new Error(String(e));
    }
    console.error(
      `Critical error fetching more messages for conversation ${selectedConversationId.value}:`,
      e
    );
    currentPage.value--; // エラー時はページ番号を戻す
  } finally {
    loadingMoreMessages.value = false;
  }
};

const sendMessage = async () => {
  if (!selectedConversationId.value || !newMessageText.value.trim()) {
    return;
  }

  sendingMessage.value = true;
  const conversationId = selectedConversationId.value;
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
        body: {
          text_content: textContent,
        },
        server: false, // クライアントサイドでのみ実行
      }
    );

    if (error.value) {
      console.error("Error sending message:", error.value);
      if (error.value.statusCode === 404)
        alert("エラー: 会話が見つかりません。");
      else if (error.value.statusCode === 403)
        alert("エラー: この会話にメッセージを送信する権限がありません。");
      else if (error.value.statusCode === 422)
        alert("エラー: メッセージ内容が不正です。");
      else alert("メッセージの送信に失敗しました。");
    } else if (sentMessage.value) {
      messages.value.push(sentMessage.value);
      newMessageText.value = "";
      // scrollToBottom(); // messages の watch で処理される

      // サイドバーの最新メッセージを更新
      if (apiResponse.value && apiResponse.value.data) {
        const conversationIndex = apiResponse.value.data.findIndex(
          (c) => c.id === conversationId
        );
        if (conversationIndex !== -1) {
          // sentMessage.value (Message型) から LatestMessage型に必要な情報を抽出
          const newLatestMessage: LatestMessage = {
            id: sentMessage.value.id,
            text_content: sentMessage.value.text_content,
            sent_at: sentMessage.value.sent_at,
            sender: sentMessage.value.sender, // Message型にはsenderオブジェクトが直接含まれる
          };
          apiResponse.value.data[conversationIndex].latest_message =
            newLatestMessage;

          // オプション: Vueに配列要素の変更をより確実に通知するために配列を再割り当てする
          // apiResponse.value.data = [...apiResponse.value.data];
        }
      }
      await scrollToBottom(); // ★ 送信成功後にも明示的にスクロール
    }
  } catch (e) {
    console.error("Critical error sending message:", e);
    alert("メッセージの送信中に予期せぬエラーが発生しました。");
    // エラーハンドリングを拡充する場合
    // if (e instanceof Error) {
    //   messagesError.value = e;
    // } else {
    //   messagesError.value = new Error(String(e));
    // }
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
  if (name && name.length > 0) {
    return name[0].toUpperCase();
  }
  return "?";
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
  if (index === 0) return true; // 最初のメッセージの前には常に表示
  const prevMessage = allMessages[index - 1];
  if (!prevMessage || !prevMessage.sent_at || !message.sent_at) return false;
  const prevDate = new Date(prevMessage.sent_at).toDateString();
  const currentDate = new Date(message.sent_at).toDateString();
  return prevDate !== currentDate;
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

const selectConversation = (id: number) => {
  selectedConversationId.value = id;
  // console.log(`Conversation ${id} selected.`); // デバッグ用

  // スマホ表示でサイドバーが開いていれば、会話選択時に閉じる
  if (isSidebarOpen.value && window.innerWidth < 768) {
    // mdブレークポイント (768px) 未満
    isSidebarOpen.value = false;
  }
};

const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === "Enter" && event.shiftKey) {
    event.preventDefault(); // デフォルトの改行を防ぐ
    sendMessage();
  }
  // Enterのみの場合はデフォルトの改行動作（何もしない）
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
