<template>
  <div>
    <form class="flex flex-col sm:flex-row" @submit.prevent="searchFriend">
      <div class="flex-1">
        <div class="relative">
          <input
            v-model="searchTerm"
            type="text"
            placeholder="6桁のフレンドIDを入力"
            class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition duration-200 pl-12"
            maxlength="6"
            pattern="[A-Za-z0-9]{6}"
            title="6桁の英数字を入力してください"
            required
          />
          <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-400"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              />
            </svg>
          </div>
        </div>
      </div>
      <button
        type="submit"
        class="inline-flex items-center justify-center px-4 sm:px-6 py-3 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
        :disabled="isLoading || searchTerm.length !== 6"
      >
        <svg
          v-if="!isLoading"
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4 mr-2"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          />
        </svg>
        <div
          v-else
          class="h-4 w-4 mr-2 border-2 border-white border-t-transparent rounded-full animate-spin"
        />
        {{ isLoading ? "検索中..." : "検索" }}
      </button>
    </form>

    <!-- 検索結果 -->
    <div v-if="searchResults.length > 0" class="mt-4 sm:mt-6">
      <div
        v-for="result in searchResults"
        :key="result.id"
        class="bg-gradient-to-r from-emerald-50 to-blue-50 rounded-xl p-3 sm:p-6 border border-emerald-100 mb-4"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div>
              <h3 class="font-semibold text-gray-900 text-lg">
                {{ result.name }}
              </h3>
              <p class="text-sm text-gray-500">
                フレンドID: {{ result.friend_id }}
              </p>
            </div>
          </div>
          <button
            class="inline-flex items-center p-2 sm:px-6 sm:py-3 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition duration-200 shadow-md hover:shadow-lg"
            @click="selectFriend(result)"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"
              />
            </svg>
            友達申請
          </button>
        </div>
      </div>
    </div>

    <!-- 検索結果がない場合 -->
    <div
      v-else-if="hasSearched && !isLoading && searchResults.length === 0"
      class="mt-4"
    >
      <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <div class="text-center">
          <svg
            class="h-8 w-8 text-gray-400 mx-auto mb-2"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            />
          </svg>
          <p class="text-sm text-gray-500">
            該当するユーザーが見つかりませんでした
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";

// 型定義
interface User {
  id: number;
  name: string;
  friend_id: string;
}

// emit定義
const emit = defineEmits<{
  (e: "friendSelected", user: User): void;
}>();

// ステート管理
const searchTerm = ref("");
const searchResults = ref<User[]>([]);
const isLoading = ref(false);
const hasSearched = ref(false);

// API設定
const { api } = useApi();
const toast = useToast();

// 友達検索処理
const searchFriend = async () => {
  if (!searchTerm.value.trim()) return;

  isLoading.value = true;
  try {
    // レスポンスの型を修正
    type SearchResponse = {
      status?: string;
      message?: string;
      user?: User;
      users?: User[];
      friendship_status?: number;
    };

    const response = await api<SearchResponse>("/friends/search", {
      method: "POST",
      body: { friend_id: searchTerm.value.trim() },
    });

    // エラーメッセージがあれば表示
    if (response.status === "error" && response.message) {
      toast.add({
        title: "エラー",
        description: response.message,
        color: "error",
      });
      searchResults.value = [];
      hasSearched.value = true;
      return;
    }

    // レスポンスの形式に合わせて処理
    if (response.user) {
      // 単一ユーザーの場合（古いAPIの形式）
      searchResults.value = [response.user];
    } else if (response.users) {
      // 複数ユーザーの場合（新しいAPIの形式）
      searchResults.value = response.users;
    } else {
      // レスポンス形式が不明な場合、空の配列を設定
      searchResults.value = [];
    }

    hasSearched.value = true;
  } catch (error: unknown) {
    console.error("Error searching for friends:", error);

    // APIからのエラーメッセージを取得
    let errorMsg = "友達の検索中にエラーが発生しました";

    // @ts-expect-error - エラーオブジェクトの構造を確認
    if (error && error.data && error.data.message) {
      // @ts-expect-error - APIエラーレスポンスからメッセージを取得するため、型安全性を一時的に無視します
      errorMsg = error.data.message;
    } else if (error instanceof Error) {
      errorMsg = error.message;
    }

    toast.add({
      title: "エラー",
      description: errorMsg,
      color: "error",
    });
  } finally {
    isLoading.value = false;
  }
};

// 友達選択処理
const selectFriend = (user: User) => {
  emit("friendSelected", user);
  // 選択後は検索結果をクリア
  searchResults.value = [];
  searchTerm.value = "";
};
</script>

<script lang="ts">
// Nuxt自動インポートの問題に対処するために明示的なexportを追加
export default {};
</script>
