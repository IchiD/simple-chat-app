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
          編集
        </button>
      </div>
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
            QRコード招待（認証必須）
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
              このQRコードをスキャンして、メンバーをグループに招待できます（要ログイン）
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
              <span>{{ isMembersExpanded ? "折りたたむ" : "展開" }}</span>
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
            他のメンバーはいません
          </div>
          <div v-else class="grid gap-3">
            <div
              v-for="member in paginatedItems"
              :key="member.id"
              class="bg-gray-50 border rounded-lg p-3"
            >
              <div class="flex justify-between items-center">
                <div>
                  <div class="font-medium">{{ member.name }}</div>
                  <div class="text-sm text-gray-600">
                    フレンドID: {{ member.friend_id }}
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
import type { GroupConversation } from "~/types/group";
import { useQRCode } from "~/composables/useQRCode";
import { useSortableMembers } from "~/composables/useSortableMembers";

// GroupMember型の定義
interface GroupMember {
  id: number;
  name: string;
  friend_id: string;
  group_member_label: string;
}

// ページメタデータでプレミアム認証をミドルウェアで制御
definePageMeta({
  middleware: ["premium-required"],
});

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const groupConversations = useGroupConversations();

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

const refresh = loadGroup;

// 初回読み込み
await loadGroup();

const successMessage = ref("");
const errorMessage = ref("");

// メンバー一覧データ
const groupMembers = ref<GroupMember[]>([]);
const membersPending = ref(false);
const membersError = ref<Error | null>(null);
const isMembersExpanded = ref(false); // メンバー一覧の展開状態（デフォルトは折りたたみ）

// 検索・ソート・ページネーション composable
const {
  keyword,
  sortKey,
  sortOrder,
  page,
  totalPages,
  paginatedItems,
  next,
  prev,
} = useSortableMembers(groupMembers, 50);

// メンバー一覧を取得
const loadMembers = async () => {
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

  regenerating.value = true;
  qrError.value = "";

  try {
    const { qr_code_token } = await groupConversations.regenerateQrCode(
      group.value.id
    );
    qrCodeImage.value = await generateQRImage(qr_code_token);
    successMessage.value = "QRコードを再生成しました";
  } catch (error: unknown) {
    console.error("QRコード再生成エラー:", error);
    errorMessage.value = "QRコードの再生成に失敗しました";
  } finally {
    regenerating.value = false;
  }
};

// QRコードダウンロード
const downloadQRCode = () => {
  if (!qrCodeImage.value) return;

  const link = document.createElement("a");
  link.download = `group-${group.value?.id}-qr.png`;
  link.href = qrCodeImage.value;
  link.click();
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
    errorMessage.value = "共有に失敗しました";
  }
};

// 参加URLコピー
const copyJoinUrl = async () => {
  if (!group.value?.qr_code_token) return;

  const joinUrl = `${window.location.origin}/join/${group.value.qr_code_token}`;

  try {
    await navigator.clipboard.writeText(joinUrl);
    successMessage.value = "参加URLをコピーしました";
  } catch (error) {
    console.error("コピーエラー:", error);
    errorMessage.value = "URLのコピーに失敗しました";
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
</script>
