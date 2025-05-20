<template>
  <div>
    <form @submit.prevent="searchFriend" class="flex items-center space-x-2">
      <div class="flex-1">
        <input
          type="text"
          v-model="searchTerm"
          placeholder="6桁のフレンドIDを入力"
          class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
          maxlength="6"
          pattern="[A-Za-z0-9]{6}"
          title="6桁の英数字を入力してください"
          required
        />
      </div>
      <button
        type="submit"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        :disabled="isLoading"
      >
        <svg
          v-if="isLoading"
          class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
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
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        <svg
          v-else
          class="h-4 w-4 mr-1"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
            clip-rule="evenodd"
          />
        </svg>
        検索
      </button>
    </form>

    <div
      v-if="searchResults.length > 0"
      class="mt-4 border rounded-md overflow-hidden"
    >
      <ul class="divide-y divide-gray-200">
        <li
          v-for="result in searchResults"
          :key="result.id"
          class="p-4 hover:bg-gray-50 transition flex justify-between items-center"
        >
          <div>
            <div class="font-medium text-gray-900">{{ result.name }}</div>
            <div class="text-sm text-gray-500">ID: {{ result.friend_id }}</div>
          </div>
          <button
            @click="selectFriend(result)"
            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            選択
          </button>
        </li>
      </ul>
    </div>

    <div
      v-else-if="hasSearched && !isLoading"
      class="mt-4 p-4 bg-gray-50 rounded-md text-center text-gray-500"
    >
      該当するユーザーが見つかりませんでした
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useApi } from "../composables/useApi";
import { useToast } from "../composables/useToast";

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
      user?: User;
      users?: User[];
      friendship_status?: number;
    };

    const response = await api<SearchResponse>("/friends/search", {
      method: "POST",
      body: { friend_id: searchTerm.value.trim() },
    });

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
  } catch (error) {
    console.error("Error searching for friends:", error);
    toast.add({
      title: "エラー",
      description: "友達の検索中にエラーが発生しました",
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
