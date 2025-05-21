<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div v-if="loading" class="py-12 text-center">
        <!-- ローディングスピナー -->
        <div
          class="h-10 w-10 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
        />
        <p class="mt-4 text-gray-600">友達情報を読み込み中...</p>
      </div>

      <template v-else>
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <!-- ヘッダー -->
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h1 class="text-xl font-semibold text-gray-900">友達管理</h1>
              <div class="flex space-x-2">
                <NuxtLink
                  to="/user"
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition duration-150 ease-in-out"
                >
                  <svg
                    class="w-5 h-5 mr-2"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                    />
                  </svg>
                  ホームへ
                </NuxtLink>
                <NuxtLink
                  to="/chat"
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition duration-150 ease-in-out"
                >
                  <svg
                    class="w-5 h-5 mr-2"
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
                  チャットへ
                </NuxtLink>
              </div>
            </div>
          </div>

          <div class="p-6">
            <!-- フレンドID検索フォーム -->
            <div class="mb-6">
              <h2 class="text-lg font-medium mb-3 text-gray-900">友達を追加</h2>
              <FriendSearch @friend-selected="handleFriendSelected" />
            </div>

            <!-- タブメニュー -->
            <div class="border-b border-gray-200">
              <nav class="-mb-px flex space-x-8">
                <button
                  class="py-4 px-1 border-b-2 font-medium text-sm flex items-center space-x-2"
                  :class="[
                    activeTab === 'friends'
                      ? 'border-blue-500 text-blue-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  ]"
                  @click="activeTab = 'friends'"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"
                    />
                  </svg>
                  <span>友達一覧</span>
                </button>

                <button
                  class="py-4 px-1 border-b-2 font-medium text-sm flex items-center space-x-2"
                  :class="[
                    activeTab === 'requests'
                      ? 'border-blue-500 text-blue-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  ]"
                  @click="activeTab = 'requests'"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                    />
                  </svg>
                  <span>友達申請</span>
                </button>

                <button
                  class="py-4 px-1 border-b-2 font-medium text-sm flex items-center space-x-2"
                  :class="[
                    activeTab === 'sent'
                      ? 'border-blue-500 text-blue-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  ]"
                  @click="activeTab = 'sent'"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"
                    />
                  </svg>
                  <span>送信済み</span>
                </button>
              </nav>
            </div>

            <!-- 友達リスト -->
            <div v-if="activeTab === 'friends'" class="mt-6">
              <div v-if="friends.length === 0" class="text-center py-8">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-12 w-12 mx-auto text-gray-400 mb-4"
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
                <p class="text-gray-500 font-medium">まだ友達がいません</p>
                <p class="text-sm text-gray-400 mt-1">
                  フレンドIDで友達を検索してみましょう
                </p>
              </div>
              <ul v-else class="divide-y divide-gray-200">
                <li
                  v-for="friend in friends"
                  :key="friend.id"
                  class="py-4 flex justify-between items-center"
                >
                  <div>
                    <span class="font-medium text-gray-900">{{
                      friend.name
                    }}</span>
                    <p class="text-sm text-gray-500">
                      ID: {{ friend.friend_id }}
                    </p>
                  </div>
                  <div class="flex space-x-2">
                    <button
                      class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                      @click="startChat(friend.id)"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1"
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
                      class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                      @click="unfriend(friend.id)"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1"
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
                </li>
              </ul>
            </div>

            <!-- 受け取った友達申請 -->
            <div v-if="activeTab === 'requests'" class="mt-6">
              <div v-if="friendRequests.length === 0" class="text-center py-8">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-12 w-12 mx-auto text-gray-400 mb-4"
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
                <p class="text-gray-500 font-medium">友達申請はありません</p>
              </div>
              <ul v-else class="divide-y divide-gray-200">
                <li
                  v-for="request in friendRequests"
                  :key="request.id"
                  class="py-4"
                >
                  <div class="flex justify-between items-center mb-2">
                    <div>
                      <span class="font-medium text-gray-900">{{
                        request.user.name
                      }}</span>
                      <p class="text-sm text-gray-500">
                        ID: {{ request.user.friend_id }}
                      </p>
                    </div>
                    <div class="flex space-x-2">
                      <button
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        @click="acceptRequest(request.user.id)"
                      >
                        承認
                      </button>
                      <button
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        @click="rejectRequest(request.user.id)"
                      >
                        拒否
                      </button>
                    </div>
                  </div>
                  <p
                    v-if="request.message"
                    class="text-sm text-gray-600 bg-gray-50 p-3 rounded-md"
                  >
                    {{ request.message }}
                  </p>
                </li>
              </ul>
            </div>

            <!-- 送信した友達申請 -->
            <div v-if="activeTab === 'sent'" class="mt-6">
              <div v-if="sentRequests.length === 0" class="text-center py-8">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-12 w-12 mx-auto text-gray-400 mb-4"
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
                <p class="text-gray-500 font-medium">
                  送信した友達申請はありません
                </p>
              </div>
              <ul v-else class="divide-y divide-gray-200">
                <li
                  v-for="request in sentRequests"
                  :key="request.id"
                  class="py-4 flex justify-between items-center"
                >
                  <div>
                    <span class="font-medium text-gray-900">{{
                      request.friend.name
                    }}</span>
                    <p class="text-sm text-gray-500">
                      ID: {{ request.friend.friend_id }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                      {{
                        new Date(request.created_at).toLocaleDateString()
                      }}に申請
                    </p>
                  </div>
                  <button
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    @click="cancelRequest(request.id)"
                  >
                    取消
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useApi } from "../../composables/useApi";
import { useToast } from "../../composables/useToast";
import { useAuthStore } from "~/stores/auth";
import { useRouter } from "vue-router";

// 型定義
interface User {
  id: number;
  name: string;
  friend_id: string;
  status: number;
}

interface FriendRequest {
  id: number;
  user: User;
  friend: User;
  message?: string;
  created_at: string;
  status: number;
}

// Conversation 型定義 (チャットページから持ってくるか、共通化が必要)
type Conversation = {
  id: number;
  room_token: string;
  // 他のプロパティも必要に応じて追加
};

// ページメタデータ
definePageMeta({
  layout: "default",
  title: "友達管理",
});

// API関連の設定
const { api } = useApi();
const toast = useToast();
const authStore = useAuthStore();
const router = useRouter();

// 状態管理
const loading = ref(true);
const friends = ref<User[]>([]);
const friendRequests = ref<FriendRequest[]>([]);
const sentRequests = ref<FriendRequest[]>([]);
const activeTab = ref("friends");

// データ読み込み
const fetchData = async () => {
  loading.value = true;
  try {
    // 友達一覧を取得
    const friendsResponse = await api<{ friends: User[] }>("/friends");
    friends.value = friendsResponse.friends;

    // 受け取った友達申請を取得
    const receivedRequestsResponse = await api<{
      received_requests: FriendRequest[];
    }>("/friends/requests/received");
    friendRequests.value = receivedRequestsResponse.received_requests;
    console.log("受け取った友達申請:", friendRequests.value);

    // 送信した友達申請を取得
    const sentRequestsResponse = await api<{ sent_requests: FriendRequest[] }>(
      "/friends/requests/sent"
    );
    sentRequests.value = sentRequestsResponse.sent_requests;
  } catch (error) {
    console.error("Error fetching friend data:", error);
    toast.add({
      title: "エラー",
      description: "友達情報の取得に失敗しました",
      color: "error",
    });
  } finally {
    loading.value = false;
  }
};

// 友達申請の送信
const sendFriendRequest = async (userId: number, message: string = "") => {
  try {
    await api("/friends/request", {
      method: "POST",
      body: { user_id: userId, message },
    });

    toast.add({
      title: "成功",
      description: "友達申請を送信しました",
      color: "success",
    });

    await fetchData(); // データを再取得
  } catch (error) {
    console.error("Error sending friend request:", error);
    toast.add({
      title: "エラー",
      description: "友達申請の送信に失敗しました",
      color: "error",
    });
  }
};

// 友達申請を承認
const acceptRequest = async (userId: number) => {
  try {
    await api("/friends/accept", {
      method: "POST",
      body: { user_id: userId },
    });

    toast.add({
      title: "成功",
      description: "友達申請を承認しました",
      color: "success",
    });

    await fetchData(); // データを再取得
  } catch (error) {
    console.error("Error accepting friend request:", error);
    toast.add({
      title: "エラー",
      description: "友達申請の承認に失敗しました",
      color: "error",
    });
  }
};

// 友達申請を拒否
const rejectRequest = async (userId: number) => {
  try {
    await api("/friends/reject", {
      method: "POST",
      body: { user_id: userId },
    });

    toast.add({
      title: "成功",
      description: "友達申請を拒否しました",
      color: "info",
    });

    await fetchData(); // データを再取得
  } catch (error) {
    console.error("Error rejecting friend request:", error);
    toast.add({
      title: "エラー",
      description: "友達申請の拒否に失敗しました",
      color: "error",
    });
  }
};

// 友達を削除
const unfriend = async (userId: number) => {
  try {
    await api("/friends/unfriend", {
      method: "DELETE",
      body: { user_id: userId },
    });

    toast.add({
      title: "成功",
      description: "友達を削除しました",
      color: "info",
    });

    await fetchData(); // データを再取得
  } catch (error) {
    console.error("Error unfriending user:", error);
    toast.add({
      title: "エラー",
      description: "友達の削除に失敗しました",
      color: "error",
    });
  }
};

// 送信した友達申請の取り消し
const cancelRequest = async (requestId: number) => {
  try {
    await api(`/friends/cancel/${requestId}`, {
      method: "DELETE",
    });

    toast.add({
      title: "成功",
      description: "友達申請を取り消しました",
      color: "info",
    });

    await fetchData(); // データを再取得
  } catch (error) {
    console.error("Error canceling friend request:", error);
    toast.add({
      title: "エラー",
      description: "友達申請の取り消しに失敗しました",
      color: "error",
    });
  }
};

// 検索結果の友達を選択した時の処理
const handleFriendSelected = (user: User) => {
  if (user) {
    // 確認ダイアログを表示
    if (confirm(`${user.name}さんに友達申請を送りますか？`)) {
      sendFriendRequest(user.id);
    }
  }
};

// チャット開始処理
const startChat = async (friendId: number) => {
  if (!authStore.user?.id) {
    toast.add({
      title: "エラー",
      description: "ログインしていません。",
      color: "error",
    });
    return;
  }

  try {
    const response = await api<Conversation>("/conversations", {
      method: "POST",
      body: {
        recipient_id: friendId,
      },
    });

    if (response && response.room_token) {
      router.push(`/chat/${response.room_token}/`);
    } else {
      throw new Error("チャットルームの取得に失敗しました。");
    }
  } catch (error) {
    console.error("Error starting chat:", error);
    let errorMessage = "チャットの開始に失敗しました。";
    if (error instanceof Error && error.message) {
      errorMessage = error.message;
    }
    // APIからのエラーメッセージを優先して表示 (もしあれば)
    const apiError = error as { data?: { message?: string } };
    if (apiError?.data?.message) {
      errorMessage = apiError.data.message;
    }

    toast.add({
      title: "エラー",
      description: errorMessage,
      color: "error",
    });
  }
};

// 初期データ読み込み
onMounted(fetchData);
</script>
