<template>
  <div v-if="isCheckingAccess" class="p-4 text-center">
    <div
      class="h-10 w-10 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
    />
    <p class="mt-4 text-gray-600">アクセス権限を確認中...</p>
  </div>
  <div v-else class="p-4">
    <div class="max-w-4xl mx-auto">
      <!-- 戻るボタン -->
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

      <div class="flex justify-between items-start mb-4">
        <h1 class="text-xl font-bold">{{ group?.name }}</h1>
        <button
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex-shrink-0"
          @click="() => router.push(`/user/groups/${id}/edit`)"
        >
          編集・管理
        </button>
      </div>

      <div v-if="pending" class="text-gray-500">読み込み中...</div>
      <div v-else-if="error" class="text-red-500">{{ error.message }}</div>
      <div v-else-if="group" class="space-y-4">
        <p>{{ group.description }}</p>

        <!-- チャットスタイル表示セクション -->
        <div
          v-if="group.chat_styles && group.chat_styles.length > 0"
          class="flex flex-wrap gap-2"
        >
          <span
            v-for="style in group.chat_styles"
            :key="style"
            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
            :class="{
              'bg-blue-50 text-blue-700 border border-blue-200':
                style === 'group',
              'bg-green-50 text-green-700 border border-green-200':
                style === 'group_member',
            }"
          >
            <span v-if="style === 'group'">グループチャット</span>
            <span v-else-if="style === 'group_member'">メンバー間チャット</span>
          </span>
        </div>
        <div v-else class="text-gray-500 text-xs">
          チャットスタイルが設定されていません
        </div>
        <!-- QRコード招待セクション -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
          <h2 class="font-semibold text-lg mb-4 flex items-center">
            <svg
              class="w-5 h-5 mr-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 16h4.01M12 8h4.01"
              />
            </svg>
            グループ参加用QRコード
          </h2>

          <div v-if="qrLoading" class="text-center py-8">
            <div
              class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"
            />
            <p class="mt-2 text-gray-600">QRコードを生成中...</p>
          </div>

          <div v-else-if="qrError" class="text-center py-8">
            <div class="text-red-600 mb-4">
              <svg
                class="w-12 h-12 mx-auto"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.962-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                />
              </svg>
            </div>
            <p class="text-red-600 mb-4">{{ qrError }}</p>
            <button
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
              @click="loadQRCode"
            >
              再試行
            </button>
          </div>

          <div v-else-if="qrCodeImage" class="text-center">
            <div class="bg-white p-4 rounded-lg shadow-sm inline-block mb-4">
              <img
                :src="qrCodeImage"
                alt="QRコード"
                class="w-48 h-48 mx-auto"
              />
            </div>
            <p class="text-sm text-gray-600 mb-4">
              このQRコードをスキャンしてもらうことで、グループに加入できます。（登録が必要です）
            </p>
            <div class="flex flex-col sm:flex-row gap-2 justify-center">
              <button
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center justify-center"
                @click="downloadQRCode"
              >
                <svg
                  class="w-4 h-4 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  />
                </svg>
                ダウンロード
              </button>
              <button
                v-if="canShare"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center justify-center"
                @click="shareQRCode"
              >
                <svg
                  class="w-4 h-4 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"
                  />
                </svg>
                共有
              </button>
              <button
                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 flex items-center justify-center"
                @click="copyJoinUrl"
              >
                <svg
                  class="w-4 h-4 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                  />
                </svg>
                URLをコピー
              </button>
              <button
                :disabled="regenerating"
                class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 disabled:opacity-50 flex items-center justify-center"
                @click="regenerateQRCode"
              >
                <svg
                  class="w-4 h-4 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                  />
                </svg>
                {{ regenerating ? "再生成中..." : "再生成" }}
              </button>
            </div>
          </div>
        </div>

        <!-- メンバー一覧セクション -->
        <div>
          <div class="flex items-center gap-3 mb-4">
            <h2 class="font-semibold">メンバー一覧</h2>
            <button
              class="flex items-center gap-1 px-2 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded transition-colors"
              @click="isMembersExpanded = !isMembersExpanded"
            >
              <span>{{ isMembersExpanded ? "閉じる" : "全て表示" }}</span>
              <svg
                class="w-3 h-3 transition-transform duration-200"
                :class="{ 'rotate-180': isMembersExpanded }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"
                />
              </svg>
            </button>
            <!-- 人数表示 -->
            <div
              class="flex items-center gap-1 px-3 py-1 bg-emerald-50 border border-emerald-200 rounded-lg text-sm"
            >
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
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
              <span class="font-medium text-emerald-700">
                {{ currentMemberCount }} / {{ group?.max_members || 0 }}
              </span>
            </div>
          </div>

          <!-- 折りたたみ可能なメンバー一覧コンテンツ -->
          <div
            v-show="isMembersExpanded"
            class="transition-all duration-300 ease-in-out"
          >
            <!-- 検索 & ソート UI -->
            <div class="flex flex-col sm:flex-row gap-2 mb-4">
              <input
                v-model="keyword"
                type="text"
                placeholder="検索 (名前・ID)"
                class="border rounded px-2 py-1 w-full sm:w-60"
              />
              <select
                v-model="sortKey"
                class="border rounded px-2 py-1 w-full sm:w-32"
              >
                <option value="name">名前</option>
                <option value="friend_id">フレンドID</option>
                <option value="joined_at">加入順</option>
              </select>
              <select
                v-model="sortOrder"
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
            <div v-else-if="paginatedItems.length === 0" class="text-gray-500">
              メンバーはいません
            </div>
            <div v-else class="grid gap-3">
              <div
                v-for="member in paginatedItems"
                :key="member.id || `deleted-${member.friend_id || 'unknown'}`"
                :class="[
                  'border rounded-lg p-3',
                  member.is_deleted_user
                    ? 'bg-red-50 border-red-200'
                    : 'bg-gray-50 border-gray-200',
                ]"
              >
                <div class="flex justify-between items-center">
                  <div>
                    <div class="font-medium flex items-center gap-2">
                      {{ member.owner_nickname || member.name }}
                      <span
                        v-if="member.is_deleted_user"
                        class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full"
                      >
                        {{
                          member.removal_type === "user_leave"
                            ? "ユーザー自身によるアカウント削除"
                            : "削除済み"
                        }}
                      </span>
                    </div>
                    <div class="text-sm text-gray-600">
                      <span
                        v-if="member.owner_nickname && !member.is_deleted_user"
                      >
                        {{ member.name }} •
                      </span>
                      フレンドID: {{ member.friend_id }}
                    </div>
                    <div v-if="member.joined_at" class="text-xs text-gray-500">
                      加入日: {{ formatJoinedDate(member.joined_at) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- ページネーション -->
            <div
              v-if="totalPages > 1"
              class="flex justify-center items-center gap-4 mt-4"
            >
              <button
                class="px-3 py-1 border rounded disabled:opacity-40"
                :disabled="page === 1"
                @click="prev"
              >
                前へ
              </button>
              <span class="text-sm text-gray-600">
                {{ page }} / {{ totalPages }}
              </span>
              <button
                class="px-3 py-1 border rounded disabled:opacity-40"
                :disabled="page === totalPages"
                @click="next"
              >
                次へ
              </button>
            </div>
          </div>
        </div>

        <button
          class="mt-4 px-4 py-2 bg-emerald-600 text-white rounded"
          @click="openChat"
        >
          チャットを開く
        </button>
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
import { useQRCode } from "~/composables/useQRCode";
import { useSortableMembers } from "~/composables/useSortableMembers";

// GroupMember型の定義
interface GroupMember {
  id: number | null; // 削除済みユーザーの場合nullの可能性がある
  name: string;
  friend_id: string | null; // 削除済みユーザーの場合nullの可能性がある
  group_member_label: string;
  joined_at?: string;
  owner_nickname?: string | null; // オーナー専用ニックネーム
  is_deleted_user?: boolean; // 削除済みユーザーフラグ
  removal_type?: string | null; // 削除タイプ
}

// ページメタデータでプレミアム認証をミドルウェアで制御
definePageMeta({
  middleware: ["premium-required"],
});

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const groupConversations = useGroupConversations();
const toast = useToast();

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

const id = Number(route.params.id as string);

// データ取得
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

const _refresh = loadGroup;

// 初回読み込み
await loadGroup();

// メンバー一覧データ
const groupMembers = ref<GroupMember[]>([]);
const membersPending = ref(false);
const membersError = ref<Error | null>(null);
const isMembersExpanded = ref(false); // メンバー一覧の展開状態（デフォルトは折りたたみ）

// 現在のメンバー数を取得するcomputed（自分自身も含む）
const currentMemberCount = computed(() => {
  // 削除済みメンバーを除外してカウント
  const activeMemberCount = groupMembers.value.filter(
    (member) => !member.is_deleted_user
  ).length;
  const currentUserId = authStore.user?.id;
  const isOwner = group.value?.owner_user_id === currentUserId;

  // 自分がオーナーで、アクティブメンバー一覧に自分が含まれていない場合は+1
  if (isOwner) {
    const isOwnerInActiveMemberList = groupMembers.value.some(
      (member) => member.id === currentUserId && !member.is_deleted_user
    );
    return isOwnerInActiveMemberList
      ? activeMemberCount
      : activeMemberCount + 1;
  }

  return activeMemberCount;
});

// useSortableMembers用に型を変換したデータ
const sortableMembers = computed(() => {
  return groupMembers.value.map((member) => ({
    name: member.name,
    friend_id: member.friend_id || "不明", // nullの場合は'不明'に変換
    owner_nickname: member.owner_nickname,
    joined_at: member.joined_at,
    // 元のメンバー情報も含める（テンプレートで使用するため）
    _original: member,
  }));
});

// 検索・ソート・ページネーション composable
const {
  keyword,
  sortKey,
  sortOrder,
  page,
  totalPages,
  paginatedItems: sortedPaginatedItems,
  next,
  prev,
} = useSortableMembers(sortableMembers, 50);

// テンプレート用のページネーションアイテム（元のGroupMember型）
const paginatedItems = computed(() => {
  return sortedPaginatedItems.value.map((item) => item._original);
});

// グループオーナーかどうかを判定
const isGroupOwner = computed(() => {
  return (
    group.value &&
    authStore.user &&
    group.value.owner_user_id === authStore.user.id
  );
});

// メンバー一覧を取得
const loadMembers = async () => {
  if (!group.value?.id) return;

  try {
    membersPending.value = true;
    membersError.value = null;

    if (isGroupOwner.value) {
      // オーナーの場合はニックネーム情報を含む全メンバー情報を取得
      const allMembers = await groupConversations.getAllGroupMembers(
        group.value.id
      );
      // 全メンバー（削除済み含む）を表示
      groupMembers.value = allMembers.map((member) => ({
        id: member.id || 0, // 削除済みユーザーのidがnullの場合は0
        name: member.name,
        friend_id: member.friend_id || "不明", // 削除済みユーザーのfriend_idがnullの場合は'不明'
        group_member_label: member.group_member_label,
        joined_at: member.joined_at,
        owner_nickname: member.owner_nickname,
        is_deleted_user: !!member.is_deleted_user,
        removal_type: member.removal_type,
      }));
    } else {
      // 一般メンバーの場合は通常のメンバー情報を取得
      groupMembers.value = await groupConversations.getGroupMembers(
        group.value.id
      );
    }
  } catch (e) {
    membersError.value = e as Error;
    groupMembers.value = [];
  } finally {
    membersPending.value = false;
  }
};

// QRコード関連の状態
const qrLoading = ref(false);
const qrError = ref("");
const qrCodeImage = ref("");
const regenerating = ref(false);

// Web Share API対応チェック
const canShare = computed(() => {
  return import.meta.client && "share" in navigator;
});

const { generateQRImage } = useQRCode();

// QRコード読み込み
const loadQRCode = async () => {
  if (!group.value?.id) return;

  qrLoading.value = true;
  qrError.value = "";

  try {
    const { qr_code_token } = await groupConversations.getQrCode(
      group.value.id
    );
    qrCodeImage.value = await generateQRImage(qr_code_token);
  } catch (error: unknown) {
    console.error("QRコード取得エラー:", error);
    qrError.value = "QRコードの取得に失敗しました";
  } finally {
    qrLoading.value = false;
  }
};

// QRコード再生成
const regenerateQRCode = async () => {
  if (!group.value?.id) return;

  // 確認ダイアログを表示
  const isConfirmed = window.confirm(
    "QRコードを再生成しますか？\n\n注意: 以前のQRコードやURLは無効になります。"
  );

  if (!isConfirmed) {
    return;
  }

  regenerating.value = true;
  qrError.value = "";

  try {
    const { qr_code_token } = await groupConversations.regenerateQrCode(
      group.value.id
    );
    qrCodeImage.value = await generateQRImage(qr_code_token);
    toast.add({
      title: "成功",
      description: "QRコードを再生成しました。",
      color: "success",
    });
  } catch (error: unknown) {
    console.error("QRコード再生成エラー:", error);
    toast.add({
      title: "エラー",
      description: "QRコードの再生成に失敗しました",
      color: "error",
    });
  } finally {
    regenerating.value = false;
  }
};

// QRコードダウンロード
const downloadQRCode = async () => {
  if (!qrCodeImage.value || !group.value) return;

  try {
    // Canvas要素を作成
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");
    if (!ctx) return;

    // キャンバスサイズを設定
    canvas.width = 600;
    canvas.height = 800;

    // 背景色を設定
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // フォント設定
    ctx.fillStyle = "#000000";
    ctx.textAlign = "center";

    let currentY = 60;

    // グループ名 + 参加QRコードを描画
    ctx.font = "bold 24px Arial, sans-serif";
    ctx.fillText(
      `${group.value.name} 参加QRコード`,
      canvas.width / 2,
      currentY
    );
    currentY += 50;

    // 紹介文を描画（長い場合は折り返し）
    if (group.value.description) {
      ctx.font = "16px Arial, sans-serif";
      const description = group.value.description;
      const maxWidth = canvas.width - 60;
      const lineHeight = 24;

      // テキストを折り返し（日本語対応）
      let line = "";

      for (let i = 0; i < description.length; i++) {
        const testLine = line + description[i];
        const metrics = ctx.measureText(testLine);
        const testWidth = metrics.width;

        if (testWidth > maxWidth && line.length > 0) {
          ctx.fillText(line, canvas.width / 2, currentY);
          line = description[i];
          currentY += lineHeight;
        } else {
          line = testLine;
        }
      }
      if (line.length > 0) {
        ctx.fillText(line, canvas.width / 2, currentY);
      }
      currentY += 40;
    }

    // QRコードの画像を読み込んで描画
    const qrImg = new Image();
    qrImg.onload = () => {
      if (!group.value) return;

      // QRコードを中央に配置
      const qrSize = 300;
      const qrX = (canvas.width - qrSize) / 2;
      const qrY = currentY + 20;

      ctx.drawImage(qrImg, qrX, qrY, qrSize, qrSize);

      // 参加URLを描画
      const joinUrl = `${window.location.origin}/join/${group.value.qr_code_token}`;
      ctx.font = "14px Arial, sans-serif";
      ctx.fillStyle = "#000000";
      ctx.fillText(joinUrl, canvas.width / 2, qrY + qrSize + 40);

      // 説明文を描画
      ctx.font = "16px Arial, sans-serif";
      ctx.fillStyle = "#000000";
      ctx.fillText(
        "QRコードをスキャンまたは上記URLにアクセスして",
        canvas.width / 2,
        qrY + qrSize + 80
      );
      ctx.fillText("グループに参加", canvas.width / 2, qrY + qrSize + 110);

      // 注意書きを描画
      ctx.font = "bold 16px Arial, sans-serif";
      ctx.fillStyle = "#dc2626";
      ctx.fillText(
        "※ 会員登録が必要です",
        canvas.width / 2,
        qrY + qrSize + 150
      );

      // 枠線を描画
      ctx.strokeStyle = "#e5e7eb";
      ctx.lineWidth = 2;
      ctx.strokeRect(10, 10, canvas.width - 20, canvas.height - 20);

      // ダウンロード
      canvas.toBlob((blob) => {
        if (blob) {
          const url = URL.createObjectURL(blob);
          const link = document.createElement("a");
          link.download = `group-${group.value?.id}-invite.png`;
          link.href = url;
          link.click();
          URL.revokeObjectURL(url);
        }
      }, "image/png");
    };

    qrImg.src = qrCodeImage.value;
  } catch (error) {
    console.error("ダウンロードエラー:", error);
    toast.add({
      title: "エラー",
      description: "画像のダウンロードに失敗しました",
      color: "error",
    });
  }
};

// QRコード共有（Web Share API）
const shareQRCode = async () => {
  if (!qrCodeImage.value || !group.value) return;

  try {
    // Data URLをBlobに変換
    const response = await fetch(qrCodeImage.value);
    const blob = await response.blob();
    const file = new File([blob], `group-${group.value.id}-qr.png`, {
      type: "image/png",
    });

    await navigator.share({
      title: `${group.value.name}に参加`,
      text: `グループ「${group.value.name}」に参加しませんか？`,
      files: [file],
    });
  } catch (error) {
    console.error("共有エラー:", error);
    toast.add({
      title: "エラー",
      description: "共有に失敗しました",
      color: "error",
    });
  }
};

// 参加URLコピー
const copyJoinUrl = async () => {
  if (!group.value?.qr_code_token) return;

  const joinUrl = `${window.location.origin}/join/${group.value.qr_code_token}`;

  try {
    await navigator.clipboard.writeText(joinUrl);
    toast.add({
      title: "成功",
      description: "参加URLをコピーしました",
      color: "success",
    });
  } catch (error) {
    console.error("コピーエラー:", error);
    toast.add({
      title: "エラー",
      description: "URLのコピーに失敗しました",
      color: "error",
    });
  }
};

// グループ読み込み後にQRコードとメンバー一覧も読み込む
watch(
  group,
  (newGroup) => {
    if (newGroup) {
      if (!qrCodeImage.value) {
        loadQRCode();
      }
      loadMembers();
    }
  },
  { immediate: true }
);

function openChat() {
  router.push(`/user/groups/${id}/chat`);
}

function goBack() {
  if (window.history.length > 1) {
    window.history.back();
  } else {
    router.push("/user/groups");
  }
}

// 加入日時をフォーマット
function formatJoinedDate(joinedAt: string): string {
  const date = new Date(joinedAt);
  return date.toLocaleDateString("ja-JP", {
    year: "numeric",
    month: "numeric",
    day: "numeric",
  });
}
</script>
