<template>
  <div v-if="isCheckingAccess" class="p-4 text-center">
    <div
      class="h-10 w-10 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
    />
    <p class="mt-4 text-gray-600">アクセス権限を確認中...</p>
  </div>
  <div v-else class="min-h-screen bg-gray-50 p-4">
    <div class="max-w-5xl mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <button
          class="group flex items-center justify-center w-10 h-10 rounded-full bg-white hover:bg-gray-100 border shadow-sm transition-all duration-200 hover:shadow-md active:scale-95"
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

      <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
          {{ group?.name }} 編集
        </h1>
        <p class="text-gray-600">グループの基本情報とメンバーを管理できます</p>
      </div>

      <div v-if="pending" class="text-center py-12">
        <div
          class="h-8 w-8 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
        />
        <p class="mt-4 text-gray-600">読み込み中...</p>
      </div>
      <div v-else-if="error" class="text-center py-12">
        <div class="text-red-500 text-lg">{{ error.message }}</div>
      </div>
      <div v-else-if="group" class="space-y-8">
        <!-- Grid Layout for Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Basic Information Card -->
          <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2
              class="text-lg font-semibold text-gray-900 mb-6 flex items-center"
            >
              <svg
                class="w-5 h-5 mr-2 text-blue-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                />
              </svg>
              基本情報
            </h2>
            <div class="space-y-6">
              <div>
                <label
                  for="edit-name"
                  class="block text-sm font-medium text-gray-700 mb-2"
                >
                  グループ名 <span class="text-red-500">*</span>
                </label>
                <input
                  id="edit-name"
                  v-model="editForm.name"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  placeholder="グループ名を入力"
                  maxlength="100"
                />
              </div>
              <div>
                <label
                  for="edit-desc"
                  class="block text-sm font-medium text-gray-700 mb-2"
                >
                  説明
                </label>
                <textarea
                  id="edit-desc"
                  v-model="editForm.description"
                  rows="4"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"
                  placeholder="グループの説明を入力（任意）"
                />
              </div>
            </div>
          </div>

          <!-- Chat Room Style Card -->
          <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2
              class="text-lg font-semibold text-gray-900 mb-6 flex items-center"
            >
              <svg
                class="w-5 h-5 mr-2 text-green-600"
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
              チャットルームスタイル
            </h2>
            <div class="space-y-4">
              <p class="text-sm text-gray-600">
                利用するチャット形式を選択してください（複数選択可）
              </p>
              <div
                class="text-sm text-gray-500 bg-gray-50 rounded-md px-3 py-2 border-l-4 border-gray-300"
              >
                <span class="font-medium">注意：</span
                >一度選択するとチャットルームが作成されるため、追加のみ可能となります。
              </div>
              <div class="space-y-3">
                <div
                  :class="[
                    'flex items-start space-x-3 p-3 border rounded-lg transition-colors',
                    (group?.chat_styles || []).includes('group')
                      ? 'border-blue-200 bg-green-50'
                      : 'border-gray-200 hover:bg-gray-50',
                  ]"
                >
                  <input
                    id="style-group"
                    v-model="editForm.chat_styles"
                    type="checkbox"
                    value="group"
                    :disabled="(group?.chat_styles || []).includes('group')"
                    :class="[
                      'mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500',
                      (group?.chat_styles || []).includes('group')
                        ? 'opacity-60'
                        : '',
                    ]"
                  />
                  <div class="flex-1">
                    <div class="flex items-center gap-2">
                      <label
                        for="style-group"
                        :class="[
                          'font-medium cursor-pointer',
                          (group?.chat_styles || []).includes('group')
                            ? 'text-gray-600'
                            : 'text-gray-900',
                        ]"
                      >
                        グループ全体チャット
                      </label>
                      <span
                        v-if="(group?.chat_styles || []).includes('group')"
                        class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded"
                      >
                        設定済み
                      </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                      すべてのメンバーが参加する共通のチャットルーム
                    </p>
                  </div>
                </div>
                <div
                  :class="[
                    'flex items-start space-x-3 p-3 border rounded-lg transition-colors',
                    (group?.chat_styles || []).includes('group_member')
                      ? 'border-green-200 bg-green-50'
                      : 'border-gray-200 hover:bg-gray-50',
                  ]"
                >
                  <input
                    id="style-group-member"
                    v-model="editForm.chat_styles"
                    type="checkbox"
                    value="group_member"
                    :disabled="
                      (group?.chat_styles || []).includes('group_member')
                    "
                    :class="[
                      'mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500',
                      (group?.chat_styles || []).includes('group_member')
                        ? 'opacity-60'
                        : '',
                    ]"
                  />
                  <div class="flex-1">
                    <div class="flex items-center gap-2">
                      <label
                        for="style-group-member"
                        :class="[
                          'font-medium cursor-pointer',
                          (group?.chat_styles || []).includes('group_member')
                            ? 'text-gray-600'
                            : 'text-gray-900',
                        ]"
                      >
                        個別チャット
                      </label>
                      <span
                        v-if="
                          (group?.chat_styles || []).includes('group_member')
                        "
                        class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded"
                      >
                        設定済み
                      </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                      グループ作成者と各メンバーとの1対1チャット
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 保存ボタン -->
        <div class="flex justify-center mt-8">
          <button
            class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium shadow-sm"
            :disabled="saving"
            @click="save"
          >
            <span v-if="saving" class="flex items-center">
              <div
                class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"
              />
              保存中...
            </span>
            <span v-else>変更を保存</span>
          </button>
        </div>

        <!-- Member Management Card -->
        <div class="bg-white rounded-lg shadow-sm border">
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg
                  class="w-5 h-5 mr-2 text-purple-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
                  />
                </svg>
                メンバー管理
              </h2>
              <div class="bg-gray-100 px-3 py-1 rounded-full">
                <span class="text-sm font-medium text-gray-700">
                  {{ extendedMembers.filter((m) => m.is_active).length + 1 }} /
                  {{ group.max_members || 50 }}
                </span>
              </div>
            </div>
          </div>

          <!-- Add Member Section -->
          <div class="p-6 bg-gray-50 border-b border-gray-200">
            <h3 class="font-medium text-gray-900 mb-4">新しいメンバーを追加</h3>
            <div class="flex">
              <div class="flex-1">
                <input
                  id="member-user"
                  v-model="newMemberFriendId"
                  type="text"
                  placeholder="フレンドIDを入力"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors"
                />
              </div>
              <button
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium"
                :disabled="adding"
                @click="addMember"
              >
                <span v-if="adding" class="flex items-center">
                  <div
                    class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"
                  />
                  追加中...
                </span>
                <span v-else>追加</span>
              </button>
            </div>
          </div>

          <!-- Member List Section -->
          <div class="p-6">
            <!-- Search and Filter -->
            <div class="mb-6 flex flex-col sm:flex-row gap-3">
              <div class="flex-1">
                <input
                  v-model="_extendedKeyword"
                  type="text"
                  placeholder="名前またはフレンドIDで検索"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors"
                />
              </div>
              <div class="flex gap-2">
                <select
                  v-model="_extendedSortKey"
                  class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors"
                >
                  <option value="name">名前順</option>
                  <option value="friend_id">フレンドID順</option>
                </select>
                <select
                  v-model="_extendedSortOrder"
                  class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors"
                >
                  <option value="asc">昇順</option>
                  <option value="desc">降順</option>
                </select>
              </div>
            </div>

            <!-- Member List -->
            <div v-if="membersPending" class="text-center py-8">
              <div
                class="h-6 w-6 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
              />
              <p class="mt-2 text-gray-600">メンバー一覧を読み込み中...</p>
            </div>
            <div v-else-if="membersError" class="text-center py-8 text-red-500">
              メンバー一覧の取得に失敗しました
            </div>
            <div
              v-else-if="paginatedExtendedItems.length === 0"
              class="text-center py-8 text-gray-500"
            >
              <svg
                class="w-12 h-12 mx-auto text-gray-300 mb-3"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
                />
              </svg>
              メンバーはいません
            </div>
            <div v-else class="space-y-3">
              <div
                v-for="member in (paginatedExtendedItems as ExtendedGroupMember[])"
                :key="member.member_id"
                :class="[
                  'border rounded-lg p-4 transition-all duration-200',
                  member.is_active
                    ? 'bg-white border-gray-200 hover:shadow-md'
                    : 'bg-red-50 border-red-200',
                ]"
              >
                <div class="flex justify-between items-start">
                  <div class="flex-1 space-y-2">
                    <!-- Member Info -->
                    <div class="flex items-center gap-3">
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                          <h4 class="font-medium text-gray-900 truncate">
                            {{ member.name }}
                          </h4>
                          <span
                            v-if="!member.is_active"
                            class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full"
                          >
                            {{
                              member.removal_type === "user_leave"
                                ? "ユーザー自身によるアカウント削除"
                                : "削除済み"
                            }}
                          </span>
                          <span
                            v-if="!member.is_active && !member.can_rejoin"
                            class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded-full"
                          >
                            再参加禁止
                          </span>
                        </div>
                        <p class="text-sm text-gray-600">
                          フレンドID: {{ member.friend_id || "不明" }}
                        </p>
                      </div>
                    </div>

                    <!-- Nickname Section -->
                    <div class="ml-13">
                      <div
                        v-if="!editingNickname[member.member_id]"
                        class="flex items-center gap-2"
                      >
                        <span class="text-sm text-gray-500">ニックネーム:</span>
                        <span class="text-sm text-gray-900">
                          {{ member.owner_nickname || "未設定" }}
                        </span>
                        <button
                          class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded border border-blue-200 hover:bg-green-50 transition-colors"
                          @click="startEditNickname(member)"
                        >
                          編集
                        </button>
                      </div>
                      <div v-else class="space-y-2">
                        <div class="flex gap-2 items-center">
                          <input
                            v-model="nicknameInputs[member.member_id]"
                            type="text"
                            placeholder="ニックネームを入力"
                            class="flex-1 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                            maxlength="100"
                            @keydown="handleNicknameKeydown($event, member)"
                            @compositionstart="
                              handleCompositionStart(member.member_id)
                            "
                            @compositionend="
                              handleCompositionEnd(member.member_id)
                            "
                          />
                          <button
                            class="text-green-600 hover:text-green-800 text-xs px-2 py-1 rounded border border-green-200 hover:bg-green-50 transition-colors"
                            :disabled="savingNickname[member.member_id]"
                            @click="saveNickname(member)"
                          >
                            {{
                              savingNickname[member.member_id]
                                ? "保存中..."
                                : "保存"
                            }}
                          </button>
                          <button
                            class="text-gray-600 hover:text-gray-800 text-xs px-2 py-1 rounded border border-gray-200 hover:bg-gray-50 transition-colors"
                            @click="cancelEditNickname(member.member_id)"
                          >
                            キャンセル
                          </button>
                        </div>
                        <p class="text-xs text-gray-500">
                          ※ニックネームはあなた専用の表示名です。他のメンバーには表示されません。
                        </p>
                      </div>
                    </div>

                    <!-- Delete Info -->
                    <div
                      v-if="!member.is_active"
                      class="ml-13 text-xs text-gray-500"
                    >
                      削除日時: {{ formatDeletedDate(member.left_at!) }}
                      <span v-if="member.removed_by_user">
                        (削除者: {{ member.removed_by_user.name }})
                      </span>
                      <span
                        v-else-if="member.removal_type === 'kicked_by_admin'"
                      >
                        (削除者: システム管理者)
                      </span>
                    </div>
                  </div>

                  <!-- Action Buttons -->
                  <div class="flex flex-col gap-2 ml-4">
                    <template v-if="member.is_active">
                      <button
                        class="px-3 py-1 text-sm text-red-600 hover:text-red-800 border border-red-300 hover:bg-red-50 rounded transition-colors"
                        @click="removeMember(member.member_id)"
                      >
                        削除
                      </button>
                    </template>
                    <template v-else>
                      <button
                        class="px-3 py-1 text-sm text-green-600 hover:text-green-800 border border-green-300 hover:bg-green-50 rounded transition-colors"
                        @click="restoreMember(member)"
                      >
                        復活
                      </button>
                      <button
                        :class="[
                          'px-3 py-1 text-sm border rounded transition-colors',
                          member.can_rejoin
                            ? 'text-orange-600 hover:text-orange-800 border-orange-300 hover:bg-orange-50'
                            : 'text-blue-600 hover:text-blue-800 border-blue-300 hover:bg-green-50',
                        ]"
                        @click="toggleRejoin(member)"
                      >
                        {{
                          member.can_rejoin
                            ? "再参加禁止する"
                            : "再参加許可する"
                        }}
                      </button>
                    </template>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div
              v-if="_extendedTotalPages > 1"
              class="flex justify-center items-center gap-4 mt-6 pt-6 border-t border-gray-200"
            >
              <button
                class="px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                :disabled="_extendedPage === 1"
                @click="_extendedPrev"
              >
                前へ
              </button>
              <span
                class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full"
              >
                {{ _extendedPage }} / {{ _extendedTotalPages }}
              </span>
              <button
                class="px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                :disabled="_extendedPage === _extendedTotalPages"
                @click="_extendedNext"
              >
                次へ
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
import { useRoute, useRouter } from "#app";
import { useAuthStore } from "~/stores/auth";
import { useToast } from "~/composables/useToast";
import type { GroupConversation } from "~/types/group";
import { useSortableMembers } from "~/composables/useSortableMembers";

interface GroupMember {
  id: number | null;
  name: string;
  friend_id: string | null;
  group_member_label: string;
}

interface ExtendedGroupMember extends GroupMember {
  member_id: number; // GroupMemberのID
  role: string;
  owner_nickname: string | null; // オーナー専用ニックネーム
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
const toast = useToast();

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

const saving = ref(false);

const save = async () => {
  if (!editForm.value.name.trim()) {
    toast.add({
      title: "エラー",
      description: "グループ名を入力してください",
      color: "error",
    });
    return;
  }
  if (editForm.value.name.length > 100) {
    toast.add({
      title: "エラー",
      description: "グループ名は100文字以内で入力してください",
      color: "error",
    });
    return;
  }
  if (editForm.value.chat_styles.length === 0) {
    toast.add({
      title: "エラー",
      description: "チャットスタイルを少なくとも1つ選択してください",
      color: "error",
    });
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
    toast.add({
      title: "成功",
      description: "グループ情報を更新しました",
      color: "success",
    });
  } catch (e) {
    console.error(e);
    toast.add({
      title: "エラー",
      description: "更新に失敗しました",
      color: "error",
    });
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
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
} = useSortableMembers(groupMembers as any, 50);

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
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
} = useSortableMembers(extendedMembers as any, 50);

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
  if (!newMemberFriendId.value.trim()) {
    toast.add({
      title: "エラー",
      description: "フレンドIDを入力してください",
      color: "error",
    });
    return;
  }
  adding.value = true;
  try {
    await groupConversations.addMember(id, {
      friend_id: newMemberFriendId.value.trim(),
    });
    newMemberFriendId.value = "";
    await loadExtendedMembers();
    toast.add({
      title: "成功",
      description: "メンバーを追加しました",
      color: "success",
    });
  } catch (e) {
    console.error(e);
    toast.add({
      title: "エラー",
      description: "メンバー追加に失敗しました",
      color: "error",
    });
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

  try {
    // デフォルトで再参加許可
    await groupConversations.removeMember(id, participantId, true);
    await loadExtendedMembers();
    toast.add({
      title: "成功",
      description: "メンバーを削除しました（再参加可能）",
      color: "success",
    });
  } catch (e) {
    console.error(e);
    toast.add({
      title: "エラー",
      description: "メンバー削除に失敗しました",
      color: "error",
    });
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

  try {
    await groupConversations.toggleMemberRejoin(
      id,
      member.member_id,
      newStatus
    );
    await loadExtendedMembers();
    toast.add({
      title: "成功",
      description: newStatus ? "再参加を許可しました" : "再参加を禁止しました",
      color: "success",
    });
  } catch (e) {
    console.error(e);
    toast.add({
      title: "エラー",
      description: "設定変更に失敗しました",
      color: "error",
    });
  }
};

const restoreMember = async (member: ExtendedGroupMember) => {
  if (!confirm("このメンバーを復活させますか？")) {
    return;
  }

  try {
    await groupConversations.restoreMember(id, member.member_id);
    await loadExtendedMembers();
    toast.add({
      title: "成功",
      description: "メンバーを復活しました",
      color: "success",
    });
  } catch (e) {
    console.error(e);
    toast.add({
      title: "エラー",
      description: "メンバー復活に失敗しました",
      color: "error",
    });
  }
};

// ニックネーム編集関連
const editingNickname = ref<Record<number, boolean>>({});
const nicknameInputs = ref<Record<number, string>>({});
const savingNickname = ref<Record<number, boolean>>({});
const isComposing = ref<Record<number, boolean>>({});

const startEditNickname = (member: ExtendedGroupMember) => {
  editingNickname.value[member.member_id] = true;
  nicknameInputs.value[member.member_id] = member.owner_nickname || "";
};

const cancelEditNickname = (memberId: number) => {
  editingNickname.value[memberId] = false;
  nicknameInputs.value[memberId] = "";
};

const saveNickname = async (member: ExtendedGroupMember) => {
  const nickname = nicknameInputs.value[member.member_id]?.trim() || null;

  try {
    savingNickname.value[member.member_id] = true;
    await groupConversations.updateMemberNickname(
      id,
      member.member_id,
      nickname
    );

    // ローカルデータを更新
    const memberIndex = extendedMembers.value.findIndex(
      (m) => m.member_id === member.member_id
    );
    if (memberIndex !== -1) {
      extendedMembers.value[memberIndex].owner_nickname = nickname;
    }

    editingNickname.value[member.member_id] = false;
    nicknameInputs.value[member.member_id] = "";
    toast.add({
      title: "成功",
      description: "ニックネームを更新しました",
      color: "success",
    });
  } catch (e) {
    console.error(e);
    toast.add({
      title: "エラー",
      description: "ニックネーム更新に失敗しました",
      color: "error",
    });
  } finally {
    savingNickname.value[member.member_id] = false;
  }
};

// IME入力対応のイベントハンドラー
const handleNicknameKeydown = (
  event: KeyboardEvent,
  member: ExtendedGroupMember
) => {
  if (event.key === "Enter" && !isComposing.value[member.member_id]) {
    saveNickname(member);
  } else if (event.key === "Escape") {
    cancelEditNickname(member.member_id);
  }
};

const handleCompositionStart = (memberId: number) => {
  isComposing.value[memberId] = true;
};

const handleCompositionEnd = (memberId: number) => {
  isComposing.value[memberId] = false;
};

function goBack() {
  if (window.history.length > 1) {
    window.history.back();
  } else {
    router.push(`/user/groups/${id}`);
  }
}

// 削除日時をフォーマット
function formatDeletedDate(dateString: string): string {
  if (!dateString) return "不明";
  try {
    const date = new Date(dateString);
    // Invalid Dateチェック
    if (isNaN(date.getTime())) return "不明";
    return date.toLocaleDateString("ja-JP", {
      year: "numeric",
      month: "numeric",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  } catch {
    return "不明";
  }
}
</script>
