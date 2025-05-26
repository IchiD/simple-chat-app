<template>
  <div class="min-h-screen bg-gray-50 py-4 md:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div v-if="loading" class="py-12 text-center">
        <!-- ローディングスピナー -->
        <div
          class="h-10 w-10 mx-auto border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"
        />
        <p class="mt-4 text-gray-600">友達情報を読み込み中...</p>
      </div>

      <template v-else>
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <!-- ヘッダー -->
          <div class="p-4 md:p-6 border-b border-gray-200">
            <div
              class="flex flex-col md:flex-row md:justify-between md:items-center space-y-3 md:space-y-0"
            >
              <h1 class="text-xl font-semibold text-gray-900">友達</h1>
              <div
                class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2"
              >
                <NuxtLink
                  to="/user"
                  class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition duration-150 ease-in-out"
                >
                  <svg
                    class="w-4 h-4 sm:w-5 sm:h-5 mr-2"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                    />
                  </svg>
                  <span class="hidden sm:inline">ホームへ</span>
                  <span class="sm:hidden">ホーム</span>
                </NuxtLink>
                <NuxtLink
                  to="/chat"
                  class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition duration-150 ease-in-out"
                >
                  <svg
                    class="w-4 h-4 sm:w-5 sm:h-5 mr-2"
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
                  <span class="hidden sm:inline">チャットへ</span>
                  <span class="sm:hidden">チャット</span>
                </NuxtLink>
              </div>
            </div>
          </div>

          <div class="p-4 md:p-6">
            <!-- フレンドID検索フォーム -->
            <div class="mb-6">
              <h2 class="text-lg font-medium mb-3 text-gray-900">友達を追加</h2>
              <FriendSearch @friend-selected="handleFriendSelected" />
            </div>

            <!-- タブメニュー -->
            <div class="border-b border-gray-200">
              <nav class="-mb-px flex space-x-0">
                <button
                  class="py-3 px-4 border-b-2 font-medium text-sm flex items-center justify-center flex-1 sm:flex-none space-x-2 transition-colors duration-200"
                  :class="[
                    activeTab === 'friends'
                      ? 'border-emerald-500 text-emerald-600 bg-emerald-50'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  ]"
                  @click="activeTab = 'friends'"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 sm:h-5 sm:w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"
                    />
                  </svg>
                  <span class="hidden sm:inline">友達一覧</span>
                  <span class="sm:hidden">友達</span>
                </button>

                <button
                  class="py-3 px-4 border-b-2 font-medium text-sm flex items-center justify-center flex-1 sm:flex-none space-x-2 transition-colors duration-200"
                  :class="[
                    activeTab === 'requests'
                      ? 'border-emerald-500 text-emerald-600 bg-emerald-50'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  ]"
                  @click="activeTab = 'requests'"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 sm:h-5 sm:w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                    />
                  </svg>
                  <span class="hidden sm:inline">受け取った友達申請</span>
                  <span class="sm:hidden">受信</span>
                </button>

                <button
                  class="py-3 px-4 border-b-2 font-medium text-sm flex items-center justify-center flex-1 sm:flex-none space-x-2 transition-colors duration-200"
                  :class="[
                    activeTab === 'sent'
                      ? 'border-emerald-500 text-emerald-600 bg-emerald-50'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  ]"
                  @click="activeTab = 'sent'"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 sm:h-5 sm:w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"
                    />
                  </svg>
                  <span class="hidden sm:inline">送信済みの友達申請</span>
                  <span class="sm:hidden">送信済</span>
                </button>
              </nav>
            </div>

            <!-- 友達リスト -->
            <div v-if="activeTab === 'friends'" class="mt-6">
              <div v-if="friends.length === 0" class="text-center py-12">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-16 w-16 mx-auto text-gray-300 mb-4"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                  />
                </svg>
                <p class="text-lg text-gray-500 font-medium mb-2">
                  まだ友達がいません
                </p>
                <p class="text-sm text-gray-400">
                  フレンドIDで友達を検索してみましょう
                </p>
              </div>
              <div v-else class="space-y-4">
                <div
                  v-for="friend in friends"
                  :key="friend.id"
                  class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 bg-gray-50 rounded-lg space-y-3 sm:space-y-0"
                >
                  <div class="flex-1">
                    <h3 class="font-medium text-gray-900 text-lg">
                      {{ friend.name }}
                    </h3>
                  </div>
                  <div class="flex space-x-3">
                    <button
                      class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                      @click="startChat(friend.id)"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-2"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"
                        />
                        <path
                          d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"
                        />
                      </svg>
                      チャット
                    </button>
                    <button
                      class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                      @click="unfriend(friend.id)"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-2"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M11 6a3 3 0 11-6 0 3 3 0 016 0zM14 17a6 6 0 00-12 0h12zM13 8a1 1 0 100 2h4a1 1 0 100-2h-4z"
                        />
                      </svg>
                      削除
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- 受け取った友達申請 -->
            <div v-if="activeTab === 'requests'" class="mt-6">
              <div v-if="friendRequests.length === 0" class="text-center py-12">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-16 w-16 mx-auto text-gray-300 mb-4"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                  />
                </svg>
                <p class="text-lg text-gray-500 font-medium">
                  受け取った友達申請はありません
                </p>
              </div>
              <div v-else class="space-y-4">
                <div
                  v-for="request in friendRequests"
                  :key="request.id"
                  class="p-4 bg-gray-50 rounded-lg"
                >
                  <div
                    class="flex flex-col sm:flex-row sm:justify-between sm:items-start space-y-3 sm:space-y-0"
                  >
                    <div class="flex-1">
                      <h3 class="font-medium text-gray-900 text-lg">
                        {{ request.user.name }}
                      </h3>
                      <p
                        v-if="request.message"
                        class="text-sm text-gray-600 mt-2 p-3 bg-white rounded-md border"
                      >
                        {{ request.message }}
                      </p>
                    </div>
                    <div class="flex space-x-3">
                      <button
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                        @click="acceptRequest(request.user.id)"
                      >
                        承認
                      </button>
                      <button
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                        @click="rejectRequest(request.user.id)"
                      >
                        拒否
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- 送信済みの友達申請 -->
            <div v-if="activeTab === 'sent'" class="mt-6">
              <div v-if="sentRequests.length === 0" class="text-center py-12">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-16 w-16 mx-auto text-gray-300 mb-4"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                  />
                </svg>
                <p class="text-lg text-gray-500 font-medium">
                  送信済みの友達申請はありません
                </p>
              </div>
              <div v-else class="space-y-4">
                <div
                  v-for="request in sentRequests"
                  :key="request.id"
                  class="flex flex-col sm:flex-row sm:justify-between sm:items-center p-4 bg-gray-50 rounded-lg space-y-3 sm:space-y-0"
                >
                  <div class="flex-1">
                    <h3 class="font-medium text-gray-900 text-lg">
                      {{ request.friend.name }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                      送信日: {{ formatDate(request.created_at) }}
                    </p>
                  </div>
                  <div>
                    <button
                      class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                      @click="cancelRequest(request.id)"
                    >
                      キャンセル
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- 友達追加確認モーダル -->
    <div
      v-if="showAddFriendModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white rounded-lg max-w-md w-full overflow-hidden shadow-xl transform transition-all"
      >
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">友達追加</h3>
          <p class="text-gray-600 mb-6">
            <span class="font-semibold">{{ pendingFriend?.name }}</span>
            さんに友達申請を送信しますか？
          </p>
          <div
            class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3"
          >
            <button
              type="button"
              class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200"
              @click="showAddFriendModal = false"
            >
              キャンセル
            </button>
            <button
              type="button"
              class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
              @click="addFriend"
            >
              送信する
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 友達削除確認モーダル -->
    <div
      v-if="showUnfriendModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white rounded-lg max-w-md w-full overflow-hidden shadow-xl transform transition-all"
      >
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">友達削除</h3>
          <p class="text-gray-600 mb-6">
            <span class="font-semibold">{{
              friends.find((f) => f.id === pendingUnfriendId)?.name
            }}</span>
            さんを友達から削除しますか？
          </p>
          <div
            class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3"
          >
            <button
              type="button"
              class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200"
              @click="showUnfriendModal = false"
            >
              キャンセル
            </button>
            <button
              type="button"
              class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
              @click="confirmUnfriend"
            >
              削除する
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import { useApi } from "../../composables/useApi";
import { useToast } from "../../composables/useToast";
import { useRouter } from "vue-router";
import { formatDistanceToNow } from "date-fns";
import { ja } from "date-fns/locale";

// 型定義
interface User {
  id: number;
  name: string;
  friend_id: string;
  status?: number;
}

interface FriendRequest {
  id: number;
  user: User;
  friend: User;
  message?: string;
  created_at: string;
  status: number;
}

// APIレスポンス型定義
interface ApiResponse<T> {
  status: string;
  message?: string;
  data?: T;
}

// 特定のAPIレスポンス型
interface FriendsResponse extends ApiResponse<User[]> {
  friends?: User[]; // 後方互換性のため
}

interface RequestsResponse extends ApiResponse<FriendRequest[]> {
  received_requests?: FriendRequest[]; // 後方互換性のため
}

interface SentRequestsResponse extends ApiResponse<FriendRequest[]> {
  sent_requests?: FriendRequest[]; // 後方互換性のため
}

interface ConversationResponse extends ApiResponse<{ room_token: string }> {
  room_token?: string; // 後方互換性のため
}

// ページメタデータ
definePageMeta({
  layout: "default",
  title: "友達管理",
});

// API関連の設定
const { api } = useApi();
const toast = useToast();
const router = useRouter();

// 状態管理
const loading = ref(true);
const friends = ref<User[]>([]);
const friendRequests = ref<FriendRequest[]>([]);
const sentRequests = ref<FriendRequest[]>([]);
const activeTab = ref("friends");

// モーダル関連の状態
const showAddFriendModal = ref(false);
const pendingFriend = ref<User | null>(null);
const showUnfriendModal = ref(false);
const pendingUnfriendId = ref<number | null>(null);

// 初期データ読み込み
onMounted(async () => {
  await refreshData();
});

// データのリフレッシュ
const refreshData = async () => {
  loading.value = true;
  try {
    const [friendsData, requestsData, sentData] = await Promise.all([
      api<FriendsResponse>("/friends"),
      api<RequestsResponse>("/friends/requests/received"),
      api<SentRequestsResponse>("/friends/requests/sent"),
    ]);

    console.log("Friends API response:", friendsData);
    console.log("Requests API response:", requestsData);
    console.log("Sent requests API response:", sentData);

    // API レスポンスの構造に合わせて処理
    // data プロパティがある場合はそれを使用し、ない場合は従来のプロパティを使用
    friends.value = friendsData.data || friendsData.friends || [];
    friendRequests.value =
      requestsData.data || requestsData.received_requests || [];
    sentRequests.value = sentData.data || sentData.sent_requests || [];

    console.log("Friends array after processing:", friends.value);
  } catch (error: unknown) {
    console.error("Error fetching friend data:", error);
    toast.add({
      title: "エラー",
      description: extractErrorMessage(error, "友達情報の取得に失敗しました"),
      color: "error",
    });
  } finally {
    loading.value = false;
  }
};

// 日付フォーマット
const formatDate = (dateString: string) => {
  try {
    const date = new Date(dateString);
    return formatDistanceToNow(date, {
      addSuffix: true,
      locale: ja,
    });
  } catch {
    return dateString;
  }
};

// 友達選択ハンドラー
const handleFriendSelected = (user: User) => {
  pendingFriend.value = user;
  showAddFriendModal.value = true;
};

// APIからのエラーメッセージを抽出するユーティリティ関数
const extractErrorMessage = (
  error: unknown,
  defaultMessage: string
): string => {
  if (
    typeof error === "object" &&
    error !== null &&
    "data" in error &&
    typeof error.data === "object" &&
    error.data !== null &&
    "message" in error.data
  ) {
    return error.data.message as string;
  } else if (error instanceof Error) {
    return error.message;
  }
  return defaultMessage;
};

// 友達追加
const addFriend = async () => {
  if (!pendingFriend.value) return;

  try {
    const response = await api<ApiResponse<void>>("/friends/requests", {
      method: "POST",
      body: { user_id: pendingFriend.value.id },
    });

    // レスポンスに含まれるメッセージをそのまま表示
    toast.add({
      title: "成功",
      description: response.message || "友達申請を送信しました",
      color: "success",
    });

    // 申請リストの更新
    await refreshData();
  } catch (error: unknown) {
    console.error("Error adding friend:", error);

    toast.add({
      title: "エラー",
      description: extractErrorMessage(error, "友達申請の送信に失敗しました"),
      color: "error",
    });
  } finally {
    showAddFriendModal.value = false;
    pendingFriend.value = null;
  }
};

// 友達削除モーダル表示
const unfriend = (userId: number) => {
  pendingUnfriendId.value = userId;
  showUnfriendModal.value = true;
};

// 友達削除の確認と実行
const confirmUnfriend = async () => {
  if (!pendingUnfriendId.value) return;

  try {
    const response = await api<ApiResponse<void>>(`/friends/unfriend`, {
      method: "DELETE",
      body: { user_id: pendingUnfriendId.value },
    });

    toast.add({
      title: "成功",
      description: response.message || "友達を削除しました",
      color: "success",
    });

    friends.value = friends.value.filter(
      (f) => f.id !== pendingUnfriendId.value
    );
  } catch (error: unknown) {
    console.error("Error removing friend:", error);

    toast.add({
      title: "エラー",
      description: extractErrorMessage(error, "友達の削除に失敗しました"),
      color: "error",
    });
  } finally {
    showUnfriendModal.value = false;
    pendingUnfriendId.value = null;
  }
};

// 友達申請の承認
const acceptRequest = async (userId: number) => {
  try {
    const response = await api<ApiResponse<void>>(`/friends/requests/accept`, {
      method: "POST",
      body: { user_id: userId },
    });

    toast.add({
      title: "成功",
      description: response.message || "友達申請を承認しました",
      color: "success",
    });

    await refreshData();
  } catch (error: unknown) {
    console.error("Error accepting friend request:", error);

    toast.add({
      title: "エラー",
      description: extractErrorMessage(error, "友達申請の承認に失敗しました"),
      color: "error",
    });
  }
};

// 友達申請の拒否
const rejectRequest = async (userId: number) => {
  try {
    const response = await api<ApiResponse<void>>(`/friends/requests/reject`, {
      method: "POST",
      body: { user_id: userId },
    });

    toast.add({
      title: "成功",
      description: response.message || "友達申請を拒否しました",
      color: "success",
    });

    friendRequests.value = friendRequests.value.filter(
      (req) => req.user.id !== userId
    );
  } catch (error: unknown) {
    console.error("Error rejecting friend request:", error);

    toast.add({
      title: "エラー",
      description: extractErrorMessage(error, "友達申請の拒否に失敗しました"),
      color: "error",
    });
  }
};

// 送信済み友達申請のキャンセル
const cancelRequest = async (requestId: number) => {
  try {
    const response = await api<ApiResponse<void>>(
      `/friends/requests/cancel/${requestId}`,
      {
        method: "DELETE",
      }
    );

    toast.add({
      title: "成功",
      description: response.message || "友達申請をキャンセルしました",
      color: "success",
    });

    sentRequests.value = sentRequests.value.filter(
      (req) => req.id !== requestId
    );
  } catch (error: unknown) {
    console.error("Error canceling friend request:", error);

    toast.add({
      title: "エラー",
      description: extractErrorMessage(
        error,
        "友達申請のキャンセルに失敗しました"
      ),
      color: "error",
    });
  }
};

// チャット開始
const startChat = async (friendId: number) => {
  try {
    const response = await api<ConversationResponse>("/conversations", {
      method: "POST",
      body: { recipient_id: friendId },
    });

    if (response.room_token || (response.data && response.data.room_token)) {
      const roomToken = response.room_token || response.data?.room_token;
      router.push(`/chat/${roomToken}`);
    } else {
      throw new Error(
        response.message || "チャットルームのトークンが取得できませんでした"
      );
    }
  } catch (error: unknown) {
    console.error("Error starting chat:", error);

    toast.add({
      title: "エラー",
      description: extractErrorMessage(error, "チャットの開始に失敗しました"),
      color: "error",
    });
  }
};

// タブ切り替え時にページの最上部にスクロール
watch(activeTab, () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
});
</script>
