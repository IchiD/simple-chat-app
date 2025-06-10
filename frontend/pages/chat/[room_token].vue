<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div
      class="relative flex antialiased text-gray-800"
      style="height: calc(100vh - 4rem)"
    >
      <div class="flex h-full w-full">
        <!-- Main Chat Area -->
        <div class="max-w-4xl mx-auto w-full">
          <div class="flex h-full w-full flex-col pt-3 md:p-6">
            <!-- Header for Chat Area -->
            <div
              class="mb-2 flex items-center justify-between bg-white rounded-lg shadow-sm p-3 border border-gray-200"
            >
              <div class="flex items-center">
                <NuxtLink
                  to="/chat"
                  class="rounded-md p-2 text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500 mr-3"
                >
                  <span class="sr-only">チャット一覧へ戻る</span>
                  <svg
                    class="h-5 w-5"
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
                </NuxtLink>
                <div>
                  <h2 class="text-base font-semibold text-gray-900">
                    {{ conversationDisplayName }}
                  </h2>
                </div>
              </div>
            </div>

            <div
              class="flex h-full flex-auto flex-shrink-0 flex-col rounded-2xl bg-white shadow-sm border border-gray-200 overflow-hidden"
            >
              <!-- Messages Display Area -->
              <div
                ref="messageContainerRef"
                class="flex flex-col h-full overflow-x-auto p-6 bg-gradient-to-b from-gray-50/50 to-gray-100/50"
              >
                <div
                  v-if="
                    isLoadingInitialData ||
                    (!currentConversation &&
                      !conversationError &&
                      !messagesError)
                  "
                  class="flex items-center justify-center h-full"
                >
                  <div class="text-center">
                    <div
                      class="h-12 w-12 mx-auto border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"
                    />
                    <p class="text-gray-600 font-medium">
                      メッセージを読み込み中...
                    </p>
                  </div>
                </div>
                <div
                  v-else-if="conversationError"
                  class="flex items-center justify-center h-full"
                >
                  <div class="text-center">
                    <div
                      class="h-16 w-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-8 w-8 text-red-600"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                    <p class="text-red-600 font-medium mb-2">
                      {{ getConversationErrorMessage() }}
                    </p>
                    <p class="text-gray-500 text-sm mt-1 mb-4">
                      {{ getConversationErrorDescription() }}
                    </p>
                    <button
                      class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition duration-200"
                      @click="handleConversationError"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-2"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      チャット一覧に戻る
                    </button>
                  </div>
                </div>
                <div
                  v-else-if="!currentConversation"
                  class="flex items-center justify-center h-full"
                >
                  <div class="text-center">
                    <div
                      class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-8 w-8 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
                    </div>
                    <p class="text-gray-600 font-medium">
                      会話が見つかりません
                    </p>
                    <p class="text-gray-500 text-sm mt-1">
                      会話一覧から選択してください
                    </p>
                  </div>
                </div>
                <div v-else>
                  <div
                    v-if="
                      hasNextPage && !messagesPending && !loadingMoreMessages
                    "
                    class="text-center my-4"
                  >
                    <button
                      :disabled="loadingMoreMessages"
                      class="inline-flex items-center px-4 py-2 text-sm font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition duration-200 shadow-sm disabled:opacity-50"
                      @click="loadMoreMessages"
                    >
                      <svg
                        v-if="!loadingMoreMessages"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-2"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <div
                        v-else
                        class="h-4 w-4 mr-2 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin"
                      />
                      <span v-if="loadingMoreMessages">読み込み中...</span>
                      <span v-else>さらに読み込む</span>
                    </button>
                  </div>
                  <div class="grid grid-cols-12 gap-y-1">
                    <template
                      v-for="(message, index) in messages"
                      :key="message.id"
                    >
                      <div
                        v-if="shouldShowDateSeparator(message, index, messages)"
                        class="col-span-12 text-center my-4"
                      >
                        <div class="relative">
                          <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300" />
                          </div>
                          <div class="relative flex justify-center">
                            <span
                              class="text-xs text-gray-500 bg-white px-3 py-1 border border-gray-300 shadow-sm"
                            >
                              {{ formatDateSeparatorText(message.sent_at) }}
                            </span>
                          </div>
                        </div>
                      </div>
                      <div
                        :class="
                          isMyMessage(message.sender_id)
                            ? 'col-start-4 col-end-13'
                            : 'col-start-1 col-end-10'
                        "
                        class="p-1 rounded-lg"
                      >
                        <div
                          :class="
                            isMyMessage(message.sender_id)
                              ? 'flex justify-start flex-row-reverse'
                              : 'flex flex-row'
                          "
                        >
                          <div
                            class="relative text-sm py-2 px-4 shadow-md rounded-2xl"
                            :class="[
                              isMyMessage(message.sender_id)
                                ? 'bg-emerald-500 text-white max-w-sm lg:max-w-lg'
                                : 'bg-white border border-gray-200 max-w-md lg:max-w-xl',
                            ]"
                          >
                            <div
                              v-if="
                                !isMyMessage(message.sender_id) &&
                                shouldShowSenderName()
                              "
                              class="text-xs mb-1"
                              :class="
                                isMyMessage(message.sender_id)
                                  ? 'text-emerald-200'
                                  : 'text-gray-500'
                              "
                            >
                              {{ getMessageSenderName(message) }}
                            </div>
                            <div class="whitespace-pre-line leading-relaxed">
                              {{ message.text_content }}
                            </div>
                          </div>
                          <div
                            class="text-xs min-w-[3.5rem] flex items-end self-end mb-1"
                            :class="[
                              isMyMessage(message.sender_id)
                                ? 'text-emerald-600 mr-2 justify-end'
                                : 'text-gray-500 ml-2 justify-end',
                            ]"
                          >
                            {{ formatMessageTime(message.sent_at) }}
                          </div>
                        </div>
                      </div>
                    </template>
                  </div>
                </div>
              </div>

              <!-- Message Input Area -->
              <div class="border-t border-gray-200 bg-white p-4">
                <div class="flex items-center space-x-3">
                  <div class="flex-grow">
                    <textarea
                      v-model="newMessageText"
                      :disabled="
                        !currentConversation ||
                        sendingMessage ||
                        isLoadingInitialData
                      "
                      class="w-full p-3 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none bg-gray-50 transition duration-200"
                      rows="1"
                      placeholder="メッセージを入力..."
                      @keydown="handleKeydown"
                    />
                  </div>
                  <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-full w-12 h-12 transition duration-200 ease-in-out text-white font-bold focus:outline-none shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    :class="
                      sendingMessage || !newMessageText.trim()
                        ? 'bg-gray-400'
                        : 'bg-emerald-600 hover:bg-emerald-700'
                    "
                    :disabled="sendingMessage || !newMessageText.trim()"
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
                    <svg
                      v-else
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"
                      />
                    </svg>
                  </button>
                </div>
              </div>
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
import { useToast } from "~/composables/useToast";
import { useApi } from "~/composables/useApi";

// Type definitions (Consider moving to a shared file if not already)
type Participant = {
  id: number;
  name: string;
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
  room_token: string;
  participants: Participant[];
  other_participant?: Participant; // member_chat/friend_chatの相手
  latest_message: LatestMessage | null;
  unread_messages_count: number;
  type?: string;
  name?: string; // グループ名
  group_name?: string; // グループ名
  group_owner?: GroupOwner; // グループオーナー情報（member_chatの場合）
  participant_count?: number; // 参加者数（group_chatの場合）
  created_at?: string;
  updated_at?: string;
};

type Message = {
  id: number;
  conversation_id: number;
  sender_id: number | null;
  admin_sender_id?: number | null;
  content_type: string;
  text_content: string | null;
  sent_at: string;
  sender: MessageSender | null;
  adminSender?: AdminSender | null;
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

type ApiErrorData = {
  message?: string;
  errors?: Record<string, string[]>;
};

type ApiError = Error & {
  data?: ApiErrorData;
  statusCode?: number;
};

const authStore = useAuthStore();
const { user: authUser } = storeToRefs(authStore);
const route = useRoute();
const router = useRouter();
const config = useRuntimeConfig();
const toast = useToast();
const { api } = useApi();

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

// Fetch headers for conversation details, reactive to authStore.token
const conversationDetailHeaders = computed(() => {
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
    messages.value = [];
    return;
  }

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

  if (roomTokenVal && authTokenVal) {
    conversationPending.value = true;
    conversationError.value = null;
    currentConversation.value = null; // Reset before fetching new one
    messages.value = []; // Clear messages when conversation changes

    await executeFetchConversationDetails(); // Execute the fetch

    conversationPending.value = fetchConvPending.value; // Reflect pending state
    currentConversation.value = fetchedConvData.value;
    conversationError.value = fetchedConvError.value;

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
      messagesPending.value = false;
    }
  } else {
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

const currentUserId = computed<number | undefined>(() => authUser.value?.id);

// サポート会話かどうかを判定（将来の利用のため）
const _isSupportConversation = computed(() => {
  const result = currentConversation.value?.type === "support";
  return result;
});

// チャットタイプに応じた表示名を取得
const conversationDisplayName = computed(() => {
  const conversation = currentConversation.value;
  if (!conversation) return "チャット";

  // サポートチャットの場合
  if (conversation.type === "support_chat") {
    return "サポート（順次対応いたします）";
  }

  // グループチャットの場合：「グループ名（6人）」
  if (conversation.type === "group_chat") {
    const groupName =
      conversation.group_name || conversation.name || "グループ";
    const count = conversation.participant_count || 0;
    return `${groupName}（${count}人）`;
  }

  // メンバーチャットの場合：「グループ名 グループオーナー名」
  if (conversation.type === "member_chat") {
    const groupName =
      conversation.group_name || conversation.name || "グループ";
    const ownerName = conversation.group_owner?.name || "オーナー";
    return `${groupName} ${ownerName}`;
  }

  // フレンドチャットの場合：相手の名前
  if (conversation.type === "friend_chat" && conversation.other_participant) {
    return conversation.other_participant.name;
  }

  // 旧構造との互換性: participants 配列がある場合
  if (conversation.participants && conversation.participants.length > 0) {
    const otherParticipant = conversation.participants.find(
      (p) => p.id !== currentUserId.value
    );
    return otherParticipant?.name || "チャット";
  }

  return "チャット";
});

// メッセージが管理者からかどうかを判定
const isAdminMessage = (message: Message): boolean => {
  return (
    message.admin_sender_id !== null && message.admin_sender_id !== undefined
  );
};

// メッセージの送信者名を取得
const _getMessageSenderName = (message: Message): string => {
  if (isAdminMessage(message)) {
    return "サポート";
  }
  if (message.sender) {
    return message.sender.name;
  }
  return "不明";
};

// グループチャットで発言者名を表示するかどうかを判定
const shouldShowSenderName = (): boolean => {
  return currentConversation.value?.type === "group_chat";
};

// メッセージの発言者名を取得
const getMessageSenderName = (message: Message): string => {
  if (isAdminMessage(message)) {
    return "サポート";
  }
  if (message.sender) {
    return message.sender.name;
  }
  return "不明なユーザー";
};

const isMyMessage = (messageSenderId: number | null): boolean => {
  return (
    currentUserId.value !== undefined &&
    messageSenderId !== null &&
    messageSenderId === currentUserId.value
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
      `${config.public.apiBase}/conversations/room/${conversationId}/read`,
      {
        method: "POST",
        headers: fetchPostHeaders,
      }
    );
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
  const textContent = newMessageText.value;

  try {
    const sentMessageData = await api<Message>(
      `/conversations/room/${currentConversation.value.room_token}/messages`,
      {
        method: "POST",
        body: { text_content: textContent },
      }
    );

    messages.value.push(sentMessageData);
    newMessageText.value = "";
    await scrollToBottom("smooth");
  } catch (e: unknown) {
    console.error("Error sending message:", e);

    // エラーハンドリングを詳細化
    let errorMessage = "メッセージの送信に失敗しました。";
    let shouldRedirect = false;

    if (typeof e === "object" && e !== null) {
      const error = e as ApiError;
      const errorData = error.data;
      const statusCode = error.statusCode;

      if (errorData?.message) {
        if (
          errorData.message.includes("削除されています") ||
          errorData.message.includes("deleted")
        ) {
          errorMessage = "この会話は削除されています。チャット一覧に戻ります。";
          shouldRedirect = true;
        } else if (
          errorData.message.includes("アカウントが削除") ||
          errorData.message.includes("account_deleted")
        ) {
          errorMessage = "アカウントが削除されています。ログアウトします。";
          toast.add({
            title: "アカウント削除",
            description: errorMessage,
            color: "error",
          });
          authStore.logout();
          router.push("/auth/login");
          return;
        } else if (
          errorData.message.includes("友達関係") ||
          errorData.message.includes("unfriended")
        ) {
          errorMessage =
            "友達関係が解除されたため、メッセージを送信できません。";
          shouldRedirect = true;
        } else if (errorData.message.includes("権限") || statusCode === 403) {
          errorMessage = "メッセージを送信する権限がありません。";
          shouldRedirect = true;
        } else {
          errorMessage = errorData.message;
        }
      }
    }

    toast.add({
      title: "送信エラー",
      description: errorMessage,
      color: "error",
    });

    if (shouldRedirect) {
      setTimeout(() => {
        router.push("/chat");
      }, 2000);
    }
  } finally {
    sendingMessage.value = false;
  }
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
  if (event.key === "Enter" && event.shiftKey) {
    event.preventDefault();
    sendMessage();
  }
};

const getConversationErrorMessage = () => {
  if (!conversationError.value) return "会話情報の読み込みに失敗しました。";

  const errorMessage = conversationError.value.message || "";
  const error = conversationError.value as ApiError;
  const errorData = error.data;

  // バックエンドからのエラーメッセージをチェック
  if (errorData?.message) {
    if (
      errorData.message.includes("削除されています") ||
      errorData.message.includes("deleted")
    ) {
      return "この会話は削除されています";
    }
    if (
      errorData.message.includes("アクセス権") ||
      errorData.message.includes("権限")
    ) {
      return "この会話にアクセスする権限がありません";
    }
    if (
      errorData.message.includes("友達関係") ||
      errorData.message.includes("unfriended")
    ) {
      return "友達関係が解除されています";
    }
    if (
      errorData.message.includes("アカウントが削除") ||
      errorData.message.includes("user_deleted")
    ) {
      return "相手のアカウントが削除されています";
    }
    return errorData.message;
  }

  // HTTPステータスコードに基づく判定
  if (errorMessage.includes("404") || error.statusCode === 404) {
    return "会話が見つかりません";
  }
  if (errorMessage.includes("403") || error.statusCode === 403) {
    return "この会話にアクセスする権限がありません";
  }

  return "会話情報の読み込みに失敗しました";
};

const getConversationErrorDescription = () => {
  if (!conversationError.value) return "ページを再読み込みしてください。";

  const error = conversationError.value as ApiError;
  const errorData = error.data;

  // バックエンドからのエラーメッセージをチェック
  if (errorData?.message) {
    if (
      errorData.message.includes("削除されています") ||
      errorData.message.includes("deleted")
    ) {
      return "管理者によって削除された可能性があります。チャット一覧に戻ってください。";
    }
    if (
      errorData.message.includes("友達関係") ||
      errorData.message.includes("unfriended")
    ) {
      return "友達関係を再度確認してください。";
    }
    if (
      errorData.message.includes("アカウントが削除") ||
      errorData.message.includes("user_deleted")
    ) {
      return "チャット一覧に戻って他の会話を確認してください。";
    }
    if (
      errorData.message.includes("アクセス権") ||
      errorData.message.includes("権限")
    ) {
      return "チャット一覧に戻ってアクセス可能な会話を確認してください。";
    }
  }

  // HTTPステータスコードに基づく判定
  const errorMessage = conversationError.value.message || "";
  if (errorMessage.includes("404")) {
    return "会話が存在しないか、削除された可能性があります。";
  }
  if (errorMessage.includes("403")) {
    return "この会話にアクセスする権限がありません。";
  }

  return "ネットワーク接続を確認するか、ページを再読み込みしてください。";
};

const handleConversationError = () => {
  // エラーの種類に応じて適切な処理を行う
  const error = conversationError.value as ApiError;
  const errorData = error?.data;

  if (errorData?.message) {
    if (
      errorData.message.includes("アカウントが削除されています") ||
      errorData.message.includes("account_deleted")
    ) {
      // ユーザーアカウントが削除されている場合はログアウト
      toast.add({
        title: "アカウント削除",
        description: "アカウントが削除されています。ログアウトします。",
        color: "error",
      });
      authStore.logout();
      router.push("/auth/login");
      return;
    }
  }

  // その他のエラーの場合はチャット一覧に戻る
  router.push("/chat");
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
  border-radius: 0;
}
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background-color: #a0aec0; /* Tailwind gray-500 */
}
</style>
