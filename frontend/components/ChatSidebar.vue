<template>
  <div class="static flex w-full h-full flex-col pb-8 pt-4 pl-4 pr-4">
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

      <div class="ml-2 text-xl font-bold">メッセージリスト</div>
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
        <p class="text-gray-500 font-medium mb-2">チャットはありません</p>
        <p class="text-xs text-gray-400">
          友達を追加してチャットを始めましょう
        </p>
      </div>
      <button
        v-for="convo in conversations"
        :key="convo.id"
        class="group relative flex flex-row items-center p-3 m-2 rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-300 shadow-sm hover:shadow-md border cursor-pointer"
        :class="getConversationStyles(convo)"
        @click="onConversationClick(convo)"
      >
        <!-- チャットタイプアイコン -->
        <div class="flex-shrink-0 mr-3">
          <div
            class="w-10 h-10 rounded-full flex items-center justify-center"
            :class="getIconContainerStyles(convo)"
          >
            <!-- グループチャットアイコン -->
            <svg
              v-if="convo.type === 'group_chat'"
              class="w-5 h-5"
              :class="getIconStyles(convo)"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
              />
            </svg>
            <!-- メンバーチャットアイコン -->
            <svg
              v-else-if="convo.type === 'member_chat'"
              class="w-5 h-5"
              :class="getIconStyles(convo)"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
              />
            </svg>
            <!-- お問い合わせチャットアイコン -->
            <svg
              v-else-if="convo.type === 'support_chat'"
              class="w-5 h-5"
              :class="getIconStyles(convo)"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            <!-- 個人チャットアイコン -->
            <svg
              v-else
              class="w-5 h-5"
              :class="getIconStyles(convo)"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
              />
            </svg>
          </div>
        </div>

        <!-- メッセージ内容 -->
        <div class="flex-1 min-w-0">
          <div class="flex justify-between items-center mb-1">
            <div class="flex flex-col flex-1 min-w-0">
              <div class="flex items-center">
                <span class="text-sm font-semibold text-gray-900 truncate">
                  {{ getConversationDisplayName(convo) }}
                </span>
                <span
                  class="text-xs px-1.5 py-0.5 ml-2 whitespace-nowrap"
                  :class="getTypeLabel(convo).classes"
                  :title="getTypeLabel(convo).tooltip"
                >
                  {{ getTypeLabel(convo).text }}
                </span>
              </div>
              <div
                v-if="convo.type === 'group_chat'"
                class="text-xs text-left text-gray-600 mt-0.5"
              >
                メンバー {{ convo.participant_count || 0 }}人
              </div>
            </div>
            <p
              v-if="convo.latest_message?.sent_at"
              class="text-xs text-gray-500 ml-2 flex-shrink-0"
            >
              {{ formatSentAt(convo.latest_message.sent_at) }}
            </p>
          </div>
          <div class="flex justify-between items-center">
            <p
              class="text-xs text-gray-600 truncate flex-1 text-left break-all"
            >
              <span
                v-if="
                  convo.latest_message?.sender?.name ||
                  convo.latest_message?.admin_sender_id
                "
              >
                <span class="font-medium">
                  {{
                    getSenderDisplayName(
                      convo,
                      convo.latest_message.sender?.id || null
                    )
                  }}:
                </span>
                {{ convo.latest_message.text_content || "（メッセージなし）" }}
              </span>
              <span v-else class="text-gray-400">メッセージはありません</span>
            </p>
            <span
              v-if="convo.unread_messages_count > 0"
              class="badge-count ml-2 flex-shrink-0"
            >
              {{ convo.unread_messages_count }}
            </span>
          </div>
        </div>

        <!-- 右矢印アイコン -->
        <div class="ml-3 flex-shrink-0">
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
import { computed } from "vue";
import type { PropType } from "vue";
import { useAuthStore } from "../stores/auth";
import { storeToRefs } from "pinia";

// Types (Copied from pages/chat/index.vue, consider moo a shared types file)
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
  participants?: Participant[]; // 旧構造との互換性のため
  other_participant?: Participant; // 新構造（member_chat/friend_chat）
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

const props = defineProps({
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

const { user: authUser } = storeToRefs(useAuthStore());

const currentUserId = computed<number | undefined>(() => authUser.value?.id);

// チャットの表示名を取得する関数
const getConversationDisplayName = (conversation: Conversation): string => {
  // お問い合わせチャットの場合
  if (conversation.type === "support_chat") {
    return "お問い合わせ";
  }

  // グループチャットの場合：「グループ名」(人数行は別途表示)
  if (conversation.type === "group_chat") {
    return conversation.group_name || conversation.name || "グループ";
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

  // ダイレクトチャットの場合は participants を使用（旧構造との互換性）
  if (conversation.participants && conversation.participants.length > 0) {
    return (
      conversation.participants.find((p) => p.id !== currentUserId.value)
        ?.name || "チャット"
    );
  }

  return "チャット";
};

// 送信者名を取得する関数（お問い合わせチャットの場合は「お問い合わせ」を表示）
const getSenderDisplayName = (
  conversation: Conversation,
  senderId: number | null
): string => {
  // 管理者メッセージの場合
  if (conversation.latest_message?.admin_sender_id) {
    return "お問い合わせ";
  }

  if (senderId === currentUserId.value) {
    return "あなた";
  }

  // 通常のチャットの場合は送信者名を表示
  return conversation.latest_message?.sender?.name || "不明";
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
};

// チャットタイプ別のスタイルを取得する関数
const getConversationStyles = (conversation: Conversation): string => {
  const isSelected =
    props.selectedConversationRoomToken === conversation.room_token;
  const baseClasses = "border-transparent";

  if (isSelected) {
    switch (conversation.type) {
      case "group_chat":
        return `${baseClasses} bg-gradient-to-r from-blue-100 to-blue-50 border-blue-200 shadow-md`;
      case "member_chat":
        return `${baseClasses} bg-gradient-to-r from-purple-100 to-purple-50 border-purple-200 shadow-md`;
      case "support_chat":
        return `${baseClasses} bg-gradient-to-r from-green-100 to-green-50 border-green-200 shadow-md`;
      default:
        return `${baseClasses} bg-gradient-to-r from-emerald-100 to-emerald-50 border-emerald-200 shadow-md`;
    }
  }

  // ホバー状態のスタイル
  switch (conversation.type) {
    case "group_chat":
      return `${baseClasses} bg-gradient-to-r from-blue-25 to-blue-25 hover:from-blue-50 hover:to-blue-50 hover:border-blue-100`;
    case "member_chat":
      return `${baseClasses} bg-gradient-to-r from-purple-25 to-purple-25 hover:from-purple-50 hover:to-purple-50 hover:border-purple-100`;
    case "support_chat":
      return `${baseClasses} bg-gradient-to-r from-green-25 to-green-25 hover:from-green-50 hover:to-green-50 hover:border-green-100`;
    default:
      return `${baseClasses} bg-gradient-to-r from-gray-25 to-gray-25 hover:from-emerald-50 hover:to-blue-50 hover:border-emerald-100`;
  }
};

// アイコンコンテナのスタイルを取得する関数
const getIconContainerStyles = (conversation: Conversation): string => {
  switch (conversation.type) {
    case "group_chat":
      return "bg-blue-100 group-hover:bg-blue-200";
    case "member_chat":
      return "bg-purple-100 group-hover:bg-purple-200";
    case "support_chat":
      return "bg-green-100 group-hover:bg-green-200";
    default:
      return "bg-gray-100 group-hover:bg-gray-200";
  }
};

// アイコンのスタイルを取得する関数
const getIconStyles = (conversation: Conversation): string => {
  switch (conversation.type) {
    case "group_chat":
      return "text-blue-600";
    case "member_chat":
      return "text-purple-600";
    case "support_chat":
      return "text-green-600";
    default:
      return "text-gray-600";
  }
};

// チャットタイプラベルを取得する関数
const getTypeLabel = (
  conversation: Conversation
): { text: string; classes: string; tooltip: string } => {
  switch (conversation.type) {
    case "group_chat":
      return {
        text: "グループ",
        classes: "bg-blue-100 text-blue-700",
        tooltip: "このグループの全員が参加するチャット",
      };
    case "member_chat":
      return {
        text: "個別",
        classes: "bg-purple-100 text-purple-700",
        tooltip: "グループオーナーとメンバーの1対1チャット",
      };
    case "support_chat":
      return {
        text: "お問い合わせ",
        classes: "bg-green-100 text-green-700",
        tooltip: "運営お問い合わせチャット",
      };
    default:
      return {
        text: "友達",
        classes: "bg-gray-100 text-gray-700",
        tooltip: "友達とのチャット",
      };
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
  border-radius: 0;
}
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background-color: #a0aec0; /* Tailwind gray-500 */
}
</style>
