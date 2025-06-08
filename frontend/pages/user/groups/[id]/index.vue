<template>
  <div v-if="isCheckingAccess" class="p-4 text-center">
    <div
      class="h-10 w-10 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
    />
    <p class="mt-4 text-gray-600">アクセス権限を確認中...</p>
  </div>
  <div v-else class="p-4">
    <h1 class="text-xl font-bold mb-4">{{ group?.name }} 詳細</h1>
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
      <div>
        <h2 class="font-semibold mb-2">メンバー</h2>
        <div class="mb-2 flex space-x-2 items-end">
          <div>
            <label for="member-user" class="block text-sm font-medium"
              >ユーザーID</label
            >
            <input
              id="member-user"
              v-model="newMemberUserId"
              placeholder="ユーザーID"
              class="border rounded px-2 py-1 w-32"
            />
          </div>
          <div class="flex-1">
            <label for="member-nick" class="block text-sm font-medium"
              >ニックネーム</label
            >
            <input
              id="member-nick"
              v-model="newMemberNickname"
              placeholder="ニックネーム"
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
        <ul class="space-y-1">
          <li
            v-for="member in groupMembers"
            :key="member.id"
            class="border p-2 rounded flex justify-between"
          >
            <span>{{ member.nickname }}</span>
            <button
              class="px-2 py-1 bg-red-600 text-white rounded text-sm"
              @click="removeMember(member.id)"
            >
              削除
            </button>
          </li>
        </ul>
      </div>
      <button
        class="mt-4 px-4 py-2 bg-emerald-600 text-white rounded"
        @click="openChat"
      >
        チャットを開く
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "~/stores/auth";
import type { Group, GroupMember } from "~/types/group";

// ページメタデータでプレミアム認証をミドルウェアで制御
definePageMeta({
  middleware: ["premium-required"],
});

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const config = useRuntimeConfig();

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

const id = route.params.id as string;
const headers: Record<string, string> = { Accept: "application/json" };
if (authStore.token) headers.Authorization = `Bearer ${authStore.token}`;

const { data, pending, error, refresh } = await useFetch<Group>(
  `${config.public.apiBase}/groups/${id}`,
  { headers, server: false }
);

const group = computed(() => data.value);
const groupMembers = computed<GroupMember[]>(
  () => (data.value as any)?.members || []
);

const successMessage = ref("");
const errorMessage = ref("");
const adding = ref(false);

function openChat() {
  router.push(`/user/groups/${id}/chat`);
}

const newMemberUserId = ref("");
const newMemberNickname = ref("");

async function addMember() {
  errorMessage.value = "";
  successMessage.value = "";
  if (!newMemberNickname.value.trim()) {
    errorMessage.value = "ニックネームを入力してください";
    return;
  }
  if (newMemberNickname.value.length > 50) {
    errorMessage.value = "ニックネームは50文字以内で入力してください";
    return;
  }
  adding.value = true;
  try {
    await $fetch(`${config.public.apiBase}/groups/${id}/members`, {
      method: "POST",
      headers,
      body: {
        user_id: newMemberUserId.value ? Number(newMemberUserId.value) : null,
        nickname: newMemberNickname.value,
      },
    });
    newMemberUserId.value = "";
    newMemberNickname.value = "";
    await refresh();
    successMessage.value = "メンバーを追加しました";
  } catch (e) {
    console.error(e);
    errorMessage.value = "メンバー追加に失敗しました";
  } finally {
    adding.value = false;
  }
}

async function removeMember(memberId: number) {
  if (!confirm("このメンバーを削除しますか？")) return;
  errorMessage.value = "";
  successMessage.value = "";
  try {
    await $fetch(`${config.public.apiBase}/groups/${id}/members/${memberId}`, {
      method: "DELETE",
      headers,
    });
    await refresh();
    successMessage.value = "メンバーを削除しました";
  } catch (e) {
    console.error(e);
    errorMessage.value = "メンバー削除に失敗しました";
  }
}
</script>
