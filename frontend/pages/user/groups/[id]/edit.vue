<template>
  <div v-if="isCheckingAccess" class="p-4 text-center">
    <div
      class="h-10 w-10 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
    />
    <p class="mt-4 text-gray-600">アクセス権限を確認中...</p>
  </div>
  <div v-else class="p-4">
    <div class="max-w-4xl mx-auto">
      <div class="mb-6">
        <button
          class="group flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 active:bg-gray-300 transition-all duration-200 hover:shadow-md active:scale-95"
          @click="goBack"
        >
          <svg
            class="w-5 h-5 text-gray-600 group-hover:text-gray-800 transition-colors duration-200"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 19l-7-7 7-7"
            />
          </svg>
        </button>
      </div>

      <h1 class="text-xl font-bold mb-4">{{ group?.name }} 編集</h1>
      <div
        v-if="successMessage"
        class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded"
      >
        {{ successMessage }}
      </div>
      <div
        v-if="errorMessage"
        class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded"
      >
        {{ errorMessage }}
      </div>
      <div v-if="pending" class="text-gray-500">読み込み中...</div>
      <div v-else-if="error" class="text-red-500">{{ error.message }}</div>
      <div v-else-if="group">
        <section class="mb-8 space-y-4">
          <h2 class="font-semibold mb-4">基本情報</h2>
          <div>
            <label for="edit-name" class="block text-sm font-medium"
              >グループ名</label
            >
            <input
              id="edit-name"
              v-model="editForm.name"
              type="text"
              class="border rounded px-2 py-1 w-full"
            />
          </div>
          <div>
            <label for="edit-desc" class="block text-sm font-medium"
              >説明</label
            >
            <textarea
              id="edit-desc"
              v-model="editForm.description"
              class="border rounded px-2 py-1 w-full"
            />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2"
              >チャットルームスタイル</label
            >
            <div class="space-y-2">
              <div class="flex items-start space-x-2">
                <input
                  id="style-group"
                  v-model="editForm.chat_styles"
                  type="checkbox"
                  value="group"
                  class="mt-0.5"
                />
                <div>
                  <label
                    for="style-group"
                    class="text-sm font-medium cursor-pointer"
                    >グループ全体チャット</label
                  >
                  <p class="text-xs text-gray-600">
                    グループメンバー全員が参加する共通のチャットルーム
                  </p>
                </div>
              </div>
              <div class="flex items-start space-x-2">
                <input
                  id="style-group-member"
                  v-model="editForm.chat_styles"
                  type="checkbox"
                  value="group_member"
                  class="mt-0.5"
                />
                <div>
                  <label
                    for="style-group-member"
                    class="text-sm font-medium cursor-pointer"
                    >作成者とメンバー間個別チャット</label
                  >
                  <p class="text-xs text-gray-600">
                    グループ作成者と各メンバーとの1対1チャットルーム
                  </p>
                </div>
              </div>
            </div>
          </div>
          <button
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
            :disabled="saving"
            @click="save"
          >
            {{ saving ? "保存中..." : "保存" }}
          </button>
        </section>

        <section>
          <h2 class="font-semibold mb-4">メンバー管理</h2>
          <div class="mb-6">
            <h3 class="font-semibold mb-2">メンバー追加</h3>
            <div class="mb-2 flex space-x-2 items-end">
              <div class="flex-1">
                <label for="member-user" class="block text-sm font-medium"
                  >フレンドID</label
                >
                <input
                  id="member-user"
                  v-model="newMemberFriendId"
                  type="text"
                  placeholder="フレンドID"
                  class="border rounded px-2 py-1 w-full"
                />
              </div>
              <button
                class="px-3 py-1 bg-blue-600 text-white rounded disabled:opacity-50"
                :disabled="adding"
                @click="addMember"
              >
                {{ adding ? "追加中..." : "追加" }}
              </button>
            </div>
          </div>

          <div>
            <div class="flex flex-col sm:flex-row gap-2 mb-4">
              <input
                v-model="_extendedKeyword"
                type="text"
                placeholder="検索 (名前・ID)"
                class="border rounded px-2 py-1 w-full sm:w-60"
              />
              <select
                v-model="_extendedSortKey"
                class="border rounded px-2 py-1 w-full sm:w-32"
              >
                <option value="name">名前</option>
                <option value="friend_id">フレンドID</option>
              </select>
              <select
                v-model="_extendedSortOrder"
                class="border rounded px-2 py-1 w-full sm:w-28"
              >
                <option value="asc">昇順</option>
                <option value="desc">降順</option>
              </select>
            </div>

            <div v-if="membersPending" class="text-gray-500">
              メンバー一覧を読み込み中...
            </div>
            <div v-else-if="membersError" class="text-red-500">
              メンバー一覧の取得に失敗しました
            </div>
            <div
              v-else-if="paginatedExtendedItems.length === 0"
              class="text-gray-500"
            >
              他のメンバーはいません
            </div>
            <div v-else class="grid gap-3">
              <div
                v-for="member in paginatedExtendedItems"
                :key="member.member_id"
                :class="[
                  'border rounded-lg p-3',
                  member.is_active ? 'bg-gray-50' : 'bg-red-50 border-red-200',
                ]"
              >
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <div class="flex items-center gap-2">
                      <div class="font-medium">{{ member.name }}</div>
                      <span
                        v-if="!member.is_active"
                        class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded"
                      >
                        削除済み
                      </span>
                      <span
                        v-if="!member.is_active && !member.can_rejoin"
                        class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded"
                      >
                        再参加禁止
                      </span>
                    </div>
                    <div class="text-sm text-gray-600">
                      フレンドID: {{ member.friend_id }}
                    </div>
                    <div
                      v-if="!member.is_active"
                      class="text-xs text-gray-500 mt-1"
                    >
                      削除日時: {{ new Date(member.left_at!).toLocaleString() }}
                      <span v-if="member.removed_by_user">
                        (削除者: {{ member.removed_by_user.name }})
                      </span>
                    </div>
                  </div>
                  <div class="flex flex-col gap-1">
                    <template v-if="member.is_active">
                      <button
                        class="text-red-600 hover:text-red-800 text-sm px-2 py-1 border border-red-300 rounded"
                        @click="removeMember(member.member_id)"
                      >
                        削除
                      </button>
                    </template>
                    <template v-else>
                      <button
                        class="text-green-600 hover:text-green-800 text-sm px-2 py-1 border border-green-300 rounded"
                        @click="restoreMember(member)"
                      >
                        復活
                      </button>
                      <button
                        :class="[
                          'text-sm px-2 py-1 border rounded',
                          member.can_rejoin
                            ? 'text-orange-600 hover:text-orange-800 border-orange-300'
                            : 'text-blue-600 hover:text-blue-800 border-blue-300',
                        ]"
                        @click="toggleRejoin(member)"
                      >
                        {{ member.can_rejoin ? "再参加禁止" : "再参加許可" }}
                      </button>
                    </template>
                  </div>
                </div>
              </div>
            </div>

            <div
              v-if="_extendedTotalPages > 1"
              class="flex justify-center items-center gap-4 mt-4"
            >
              <button
                class="px-3 py-1 border rounded disabled:opacity-40"
                :disabled="_extendedPage === 1"
                @click="_extendedPrev"
              >
                前へ
              </button>
              <span class="text-sm text-gray-600"
                >{{ _extendedPage }} / {{ _extendedTotalPages }}</span
              >
              <button
                class="px-3 py-1 border rounded disabled:opacity-40"
                :disabled="_extendedPage === _extendedTotalPages"
                @click="_extendedNext"
              >
                次へ
              </button>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick } from "vue";
import { useRoute, useRouter } from "#app";
import { useAuthStore } from "~/stores/auth";
import type { GroupConversation } from "~/types/group";
import { useSortableMembers } from "~/composables/useSortableMembers";

interface GroupMember {
  id: number;
  name: string;
  friend_id: string;
  group_member_label: string;
}

interface ExtendedGroupMember extends GroupMember {
  member_id: number; // GroupMemberのID
  role: string;
  joined_at: string;
  left_at: string | null;
  can_rejoin: boolean;
  removal_type: string | null;
  removed_by_user: {
    id: number;
    name: string;
  } | null;
  is_active: boolean;
}

interface GroupEditForm {
  name: string;
  description: string;
  chat_styles: string[];
}

definePageMeta({
  middleware: ["premium-required"],
});

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const groupConversations = useGroupConversations();

const isCheckingAccess = ref(true);
const hasPremiumAccess = computed(() => {
  const userPlan = authStore.user?.plan;
  return userPlan && userPlan !== "free";
});

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

const id = Number(route.params.id as string);

const group = ref<GroupConversation | null>(null);
const pending = ref(true);
const error = ref<Error | null>(null);

const loadGroup = async () => {
  try {
    pending.value = true;
    error.value = null;
    group.value = await groupConversations.getGroup(id);
  } catch (e) {
    error.value = e as Error;
  } finally {
    pending.value = false;
  }
};

const refresh = loadGroup;
await loadGroup();

const editForm = ref<GroupEditForm>({
  name: "",
  description: "",
  chat_styles: [],
});

watch(
  group,
  (g) => {
    if (g) {
      editForm.value = {
        name: g.name,
        description: g.description || "",
        chat_styles: g.chat_styles || [],
      };
    }
  },
  { immediate: true }
);

const successMessage = ref("");
const errorMessage = ref("");
const saving = ref(false);

const save = async () => {
  successMessage.value = "";
  errorMessage.value = "";
  if (!editForm.value.name.trim()) {
    errorMessage.value = "グループ名を入力してください";
    return;
  }
  if (editForm.value.name.length > 100) {
    errorMessage.value = "グループ名は100文字以内で入力してください";
    return;
  }
  if (editForm.value.chat_styles.length === 0) {
    errorMessage.value = "チャットスタイルを少なくとも1つ選択してください";
    return;
  }
  try {
    saving.value = true;
    await groupConversations.updateGroup(id, {
      name: editForm.value.name,
      description: editForm.value.description,
      chatStyles: editForm.value.chat_styles,
    });
    await refresh();
    successMessage.value = "グループ情報を更新しました";
  } catch (e) {
    console.error(e);
    errorMessage.value = "更新に失敗しました";
  } finally {
    saving.value = false;
  }
};

const newMemberFriendId = ref("");
const adding = ref(false);
const groupMembers = ref<GroupMember[]>([]);
const extendedMembers = ref<ExtendedGroupMember[]>([]);
const membersPending = ref(false);
const membersError = ref<Error | null>(null);

const {
  keyword: _keyword,
  sortKey: _sortKey,
  sortOrder: _sortOrder,
  page: _page,
  totalPages: _totalPages,
  paginatedItems: _paginatedItems,
  next: _next,
  prev: _prev,
} = useSortableMembers(groupMembers, 50);

// 拡張メンバー用のソート機能
const {
  keyword: _extendedKeyword,
  sortKey: _extendedSortKey,
  sortOrder: _extendedSortOrder,
  page: _extendedPage,
  totalPages: _extendedTotalPages,
  paginatedItems: paginatedExtendedItems,
  next: _extendedNext,
  prev: _extendedPrev,
} = useSortableMembers(extendedMembers, 50);

const _loadMembers = async () => {
  if (!group.value?.id) return;
  try {
    membersPending.value = true;
    membersError.value = null;
    groupMembers.value = await groupConversations.getGroupMembers(
      group.value.id
    );
  } catch (e) {
    membersError.value = e as Error;
    groupMembers.value = [];
  } finally {
    membersPending.value = false;
  }
};

const loadExtendedMembers = async () => {
  if (!group.value?.id) return;
  try {
    membersPending.value = true;
    membersError.value = null;
    extendedMembers.value = await groupConversations.getAllGroupMembers(
      group.value.id
    );
  } catch (e) {
    membersError.value = e as Error;
    extendedMembers.value = [];
  } finally {
    membersPending.value = false;
  }
};

watch(
  group,
  (g) => {
    if (g) {
      loadExtendedMembers();
    }
  },
  { immediate: true }
);

const addMember = async () => {
  errorMessage.value = "";
  successMessage.value = "";
  if (!newMemberFriendId.value.trim()) {
    errorMessage.value = "フレンドIDを入力してください";
    return;
  }
  adding.value = true;
  try {
    await groupConversations.addMember(id, {
      friend_id: newMemberFriendId.value.trim(),
    });
    newMemberFriendId.value = "";
    await loadExtendedMembers();
    successMessage.value = "メンバーを追加しました";
  } catch (e) {
    console.error(e);
    errorMessage.value = "メンバー追加に失敗しました";
  } finally {
    adding.value = false;
  }
};

const removeMember = async (participantId: number) => {
  if (
    !confirm("本当に削除しますか？\n\n削除されたメンバーは再参加可能です。")
  ) {
    return;
  }

  errorMessage.value = "";
  successMessage.value = "";
  try {
    // デフォルトで再参加許可
    await groupConversations.removeMember(id, participantId, true);
    await loadExtendedMembers();
    successMessage.value = "メンバーを削除しました（再参加可能）";
  } catch (e) {
    console.error(e);
    errorMessage.value = "メンバー削除に失敗しました";
  }
};

const toggleRejoin = async (member: ExtendedGroupMember) => {
  const newStatus = !member.can_rejoin;
  const confirmMessage = newStatus
    ? "このメンバーの再参加を許可しますか？"
    : "このメンバーの再参加を禁止しますか？";

  if (!confirm(confirmMessage)) {
    return;
  }

  errorMessage.value = "";
  successMessage.value = "";
  try {
    await groupConversations.toggleMemberRejoin(
      id,
      member.member_id,
      newStatus
    );
    await loadExtendedMembers();
    successMessage.value = newStatus
      ? "再参加を許可しました"
      : "再参加を禁止しました";
  } catch (e) {
    console.error(e);
    errorMessage.value = "設定変更に失敗しました";
  }
};

const restoreMember = async (member: ExtendedGroupMember) => {
  if (!confirm("このメンバーを復活させますか？")) {
    return;
  }

  errorMessage.value = "";
  successMessage.value = "";
  try {
    await groupConversations.restoreMember(id, member.member_id);
    await loadExtendedMembers();
    successMessage.value = "メンバーを復活しました";
  } catch (e) {
    console.error(e);
    errorMessage.value = "メンバー復活に失敗しました";
  }
};

function goBack() {
  if (window.history.length > 1) {
    window.history.back();
  } else {
    router.push(`/user/groups/${id}`);
  }
}
</script>
