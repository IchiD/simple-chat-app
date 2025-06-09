<template>
  <div v-if="isCheckingAccess" class="p-4 text-center">
    <div
      class="h-10 w-10 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
    />
    <p class="mt-4 text-gray-600">アクセス権限を確認中...</p>
  </div>
  <div v-else class="p-4">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-xl font-bold mb-4">{{ group?.name }} メンバーチャット</h1>

      <!-- メンバー一覧セクション -->
      <div class="mb-6">
        <h2 class="text-lg font-semibold mb-3">メンバーを選択してください</h2>

        <div v-if="membersPending" class="text-gray-500">
          メンバー読み込み中...
        </div>
        <div v-else-if="membersError" class="text-red-500 mb-4">
          {{ membersError }}
        </div>
        <div v-else class="space-y-3">
          <!-- 全選択オプション -->
          <div class="flex items-center mb-4 p-3 bg-gray-50 rounded-lg">
            <input
              id="select-all"
              v-model="selectAll"
              type="checkbox"
              class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
              @change="toggleSelectAll"
            />
            <label
              for="select-all"
              class="ml-2 text-sm font-medium text-gray-900"
            >
              全員を選択
            </label>
          </div>

          <!-- メンバー一覧 -->
          <div
            v-for="member in members"
            :key="member.id"
            class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50"
          >
            <div class="flex items-center">
              <input
                :id="`member-${member.id}`"
                v-model="selectedMemberIds"
                :value="member.id"
                type="checkbox"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 mr-3"
              />
              <!-- <div
                class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3"
              >
                <span class="text-blue-600 font-semibold">{{
                  member.name.charAt(0)
                }}</span>
              </div> -->
              <div>
                <div class="text-sm font-medium text-gray-900">
                  {{ member.name }}
                </div>
                <!-- <div class="text-xs text-gray-500">
                  {{ member.group_member_label }}
                </div> -->
                <div class="text-xs text-gray-400">
                  ID: {{ member.friend_id }}
                </div>
              </div>
            </div>
            <button
              class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200"
              @click="startChatWithMember(member)"
            >
              個別チャット
            </button>
          </div>

          <div
            v-if="members.length === 0"
            class="text-center py-8 text-gray-500"
          >
            このグループにはまだ他のメンバーがいません
          </div>

          <!-- アクションボタン -->
          <div
            v-if="selectedMemberIds.length > 0"
            class="bg-blue-50 p-4 rounded-lg"
          >
            <div class="flex items-center justify-between mb-3">
              <span class="text-sm text-gray-700">
                {{ selectedMemberIds.length }}人のメンバーが選択されています
              </span>
            </div>
            <div class="flex space-x-3">
              <button
                v-if="selectedMemberIds.length === 1"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                @click="startChatWithSelectedMember()"
              >
                選択メンバーと個別チャット
              </button>
              <button
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                @click="showBulkMessageForm = true"
              >
                選択メンバーに一斉送信
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- 一斉送信フォーム -->
      <div
        v-if="showBulkMessageForm"
        class="mb-6 bg-green-50 p-4 rounded-lg border border-green-200"
      >
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-lg font-semibold text-green-800">
            一斉メッセージ送信
          </h3>
          <button
            class="text-green-600 hover:text-green-800"
            @click="showBulkMessageForm = false"
          >
            ✕
          </button>
        </div>

        <div class="mb-3">
          <p class="text-sm text-green-700 mb-2">
            送信先: {{ selectedMemberIds.length }}人
          </p>
          <div class="text-xs text-green-600">
            {{ selectedMembers.map((m) => m.name).join(", ") }}
          </div>
        </div>

        <div class="space-y-3">
          <textarea
            v-model="bulkMessage"
            class="w-full border rounded px-3 py-2 resize-none"
            rows="4"
            placeholder="一斉送信するメッセージを入力してください..."
          />

          <div class="flex items-center justify-between">
            <div class="text-sm text-green-600">
              選択中: {{ selectedMemberIds.length }}人のメンバー
            </div>
            <div class="space-x-2">
              <button
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
                @click="showBulkMessageForm = false"
              >
                キャンセル
              </button>
              <button
                class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
                :disabled="sending || !bulkMessage.trim()"
                @click="sendBulkMessage"
              >
                {{ sending ? "送信中..." : "一斉送信" }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- 送信結果 -->
      <div
        v-if="sendResult"
        class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg"
      >
        <h4 class="font-semibold text-green-800 mb-2">送信完了</h4>
        <p class="text-sm text-green-700">
          {{ sendResult.sent_count }}人のメンバーにメッセージを送信しました
        </p>
      </div>

      <!-- エラー表示 -->
      <div
        v-if="sendError"
        class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg"
      >
        <h4 class="font-semibold text-red-800 mb-2">送信エラー</h4>
        <p class="text-sm text-red-700">{{ sendError }}</p>
      </div>

      <!-- 現在のチャット表示 -->
      <div v-if="currentChatMember" class="border-t pt-6">
        <div class="flex items-center mb-4">
          <button
            class="mr-3 text-gray-600 hover:text-gray-800"
            @click="
              currentChatMember = null;
              currentConversation = null;
              messages = [];
            "
          >
            ← 戻る
          </button>
          <h3 class="text-lg font-semibold">
            {{ currentChatMember.name }} とのチャット
          </h3>
        </div>

        <!-- メッセージ一覧 -->
        <div v-if="messagesPending" class="text-gray-500 text-center py-4">
          メッセージを読み込み中...
        </div>
        <div v-else-if="messagesError" class="text-red-500 mb-4">
          {{ messagesError }}
        </div>
        <div
          v-else
          class="space-y-3 mb-4 max-h-96 overflow-y-auto border p-4 rounded bg-gray-50"
        >
          <div
            v-for="msg in messages"
            :key="msg.id"
            class="flex"
            :class="
              msg.sender_id === authStore.user?.id
                ? 'justify-end'
                : 'justify-start'
            "
          >
            <div
              class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg"
              :class="
                msg.sender_id === authStore.user?.id
                  ? 'bg-blue-500 text-white'
                  : 'bg-white border'
              "
            >
              <p class="text-sm">{{ msg.text_content }}</p>
              <p class="text-xs mt-1 opacity-70">
                {{ formatTime(msg.sent_at) }}
              </p>
            </div>
          </div>
          <div
            v-if="messages.length === 0"
            class="text-center py-8 text-gray-500"
          >
            まだメッセージはありません
          </div>
        </div>

        <!-- メッセージ送信 -->
        <div class="flex space-x-2">
          <input
            v-model="newMessage"
            class="flex-1 border rounded px-3 py-2"
            type="text"
            placeholder="メッセージを入力..."
            @keypress.enter="sendMessage"
          />
          <button
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-gray-400"
            :disabled="sending || !newMessage.trim()"
            @click="sendMessage"
          >
            {{ sending ? "送信中..." : "送信" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick } from "vue";
import { useRoute, useRouter } from "#app";
import { useAuthStore } from "~/stores/auth";
import type { GroupConversation, GroupMessage } from "~/types/group";

interface GroupMember {
  id: number;
  name: string;
  friend_id: string;
  group_member_label: string;
}

interface MemberConversation {
  id: number;
  type: string;
  name: string;
  room_token: string;
  group_conversation_id: number;
}

interface BulkMessageResponse {
  message: string;
  sent_count: number;
  sent_messages: Array<{
    conversation_id: number;
    target_user_id: number;
    message_id: number;
  }>;
}

// ページメタデータでプレミアム認証をミドルウェアで制御
definePageMeta({
  middleware: ["premium-required"],
});

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const groupConversations = useGroupConversations();
const id = Number(route.params.id as string);

const isCheckingAccess = ref(true);

// リアクティブなプラン状態チェック
const hasPremiumAccess = computed(() => {
  const userPlan = authStore.user?.plan;
  return userPlan && userPlan !== "free";
});

// プラン状態を監視してリダイレクト
watch(
  hasPremiumAccess,
  async (hasAccess) => {
    if (hasAccess === false && authStore.user) {
      await router.push("/pricing");
    } else if (hasAccess === true) {
      isCheckingAccess.value = false;
    }
  },
  { immediate: true }
);

// 認証状態を監視
watch(
  () => authStore.user,
  async (user) => {
    if (user) {
      await nextTick();
      if (user.plan === "free") {
        await router.push("/pricing");
      } else {
        isCheckingAccess.value = false;
      }
    }
  },
  { immediate: true }
);

// グループ情報とメンバーの状態
const group = ref<GroupConversation | null>(null);
const members = ref<GroupMember[]>([]);
const membersPending = ref(true);
const membersError = ref("");

// メンバー選択状態
const selectedMemberIds = ref<number[]>([]);
const selectAll = ref(false);

// 一斉送信状態
const showBulkMessageForm = ref(false);
const bulkMessage = ref("");
const sendResult = ref<BulkMessageResponse | null>(null);
const sendError = ref("");

// 現在のチャット状態
const currentChatMember = ref<GroupMember | null>(null);
const currentConversation = ref<MemberConversation | null>(null);
const messages = ref<GroupMessage[]>([]);
const messagesPending = ref(false);
const messagesError = ref("");

// メッセージ送信の状態
const newMessage = ref("");
const sending = ref(false);

// 選択されたメンバー情報を取得
const selectedMembers = computed(() => {
  return members.value.filter((member) =>
    selectedMemberIds.value.includes(member.id)
  );
});

// グループ情報を取得
const loadGroup = async () => {
  try {
    group.value = await groupConversations.getGroup(id);
  } catch (error) {
    console.error("グループ取得エラー:", error);
    membersError.value = "グループの取得に失敗しました";
  }
};

// メンバー一覧を取得
const loadMembers = async () => {
  membersPending.value = true;
  membersError.value = "";

  try {
    members.value = await groupConversations.getGroupMembers(id);
  } catch (error) {
    console.error("メンバー取得エラー:", error);
    membersError.value = "メンバーの取得に失敗しました";
  } finally {
    membersPending.value = false;
  }
};

// 全選択/全解除
const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedMemberIds.value = members.value.map((m) => m.id);
  } else {
    selectedMemberIds.value = [];
  }
};

// 選択状態の監視
watch(
  selectedMemberIds,
  (newSelected) => {
    selectAll.value =
      newSelected.length === members.value.length && members.value.length > 0;
  },
  { deep: true }
);

// 選択したメンバーと個別チャット開始
const startChatWithSelectedMember = () => {
  if (selectedMemberIds.value.length === 1) {
    const member = members.value.find(
      (m) => m.id === selectedMemberIds.value[0]
    );
    if (member) {
      startChatWithMember(member);
    }
  }
};

// メンバーとのチャットを開始
const startChatWithMember = async (member: GroupMember) => {
  currentChatMember.value = member;

  console.log("個別チャット開始:", { member, target_user_id: member.id });

  try {
    // メンバー間チャットルームを取得/作成
    const { api } = useApi();
    const conversation = await api<MemberConversation>(
      `/conversations/groups/${id}/member-chat`,
      {
        method: "POST",
        body: { target_user_id: member.id },
      }
    );

    currentConversation.value = conversation;
    await loadMessages();
  } catch (error: unknown) {
    console.error("チャットルーム作成エラー:", error);
    if (error && typeof error === "object" && "response" in error) {
      const httpError = error as {
        response?: { data?: { message?: string } };
        message?: string;
      };
      console.error("エラー詳細:", httpError.response?.data);
      messagesError.value = `チャットルームの作成に失敗しました: ${
        httpError.response?.data?.message || httpError.message || "不明なエラー"
      }`;
    } else {
      messagesError.value = "チャットルームの作成に失敗しました";
    }
  }
};

// メッセージを読み込み
const loadMessages = async () => {
  if (!currentConversation.value?.room_token) return;

  messagesPending.value = true;
  messagesError.value = "";

  try {
    const data = await groupConversations.getMessages(
      currentConversation.value.room_token
    );
    messages.value = data.data;
  } catch (error) {
    console.error("メッセージ取得エラー:", error);
    messagesError.value = "メッセージの取得に失敗しました";
  } finally {
    messagesPending.value = false;
  }
};

// 個別チャットメッセージ送信
const sendMessage = async () => {
  if (!newMessage.value.trim() || !currentConversation.value?.room_token)
    return;

  sending.value = true;
  try {
    await groupConversations.sendMessage(
      currentConversation.value.room_token,
      newMessage.value
    );
    newMessage.value = "";
    await loadMessages(); // メッセージ一覧を再読み込み
  } catch (error) {
    console.error("メッセージ送信エラー:", error);
    messagesError.value = "メッセージの送信に失敗しました";
  } finally {
    sending.value = false;
  }
};

// 一斉メッセージ送信
const sendBulkMessage = async () => {
  if (!bulkMessage.value.trim() || selectedMemberIds.value.length === 0) return;

  sending.value = true;
  sendError.value = "";
  sendResult.value = null;

  try {
    const result = await groupConversations.sendBulkMessage(id, {
      target_user_ids: selectedMemberIds.value,
      text_content: bulkMessage.value,
    });

    sendResult.value = result;
    bulkMessage.value = "";
    selectedMemberIds.value = [];
    selectAll.value = false;
    showBulkMessageForm.value = false;

    // 数秒後に結果メッセージを消す
    setTimeout(() => {
      sendResult.value = null;
    }, 5000);
  } catch (error: unknown) {
    console.error("一斉送信エラー:", error);
    sendError.value =
      error instanceof Error ? error.message : "メッセージの送信に失敗しました";

    // 数秒後にエラーメッセージを消す
    setTimeout(() => {
      sendError.value = "";
    }, 5000);
  } finally {
    sending.value = false;
  }
};

// 時刻フォーマット
const formatTime = (dateString: string) => {
  return new Date(dateString).toLocaleTimeString("ja-JP", {
    hour: "2-digit",
    minute: "2-digit",
  });
};

// 初期データ読み込み
const refresh = async () => {
  await loadGroup();
  await loadMembers();
};

// 初回読み込み
await refresh();
</script>
