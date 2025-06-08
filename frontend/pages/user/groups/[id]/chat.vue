<template>
  <div v-if="isCheckingAccess" class="p-4 text-center">
    <div
      class="h-10 w-10 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
    />
    <p class="mt-4 text-gray-600">アクセス権限を確認中...</p>
  </div>
  <div v-else class="p-4">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-xl font-bold mb-4">{{ group?.name }} チャット</h1>
      <div v-if="messagesPending" class="text-gray-500">読み込み中...</div>
      <div v-else-if="messagesError" class="text-red-500">
        {{ messagesError.message }}
      </div>
      <div
        v-else
        class="space-y-2 mb-4 max-h-96 overflow-y-auto border p-2 rounded bg-white"
      >
        <div v-for="msg in messages" :key="msg.id" class="border-b pb-1">
          <span class="font-semibold">{{ msg.sender?.name || "匿名" }}</span
          >:
          {{ msg.message }}
        </div>
        <div v-if="nextPageUrl" class="text-center mt-2">
          <button
            class="px-3 py-1 text-sm bg-gray-200 rounded"
            :disabled="loadingMore"
            @click="loadMore"
          >
            さらに読み込む
          </button>
        </div>
      </div>
      <div class="flex space-x-2">
        <input
          v-model="newMessage"
          class="flex-1 border rounded px-2 py-1"
          type="text"
          placeholder="メッセージ"
        />
        <button
          class="px-4 py-1 bg-emerald-600 text-white rounded"
          :disabled="sending || !newMessage.trim()"
          @click="send"
        >
          送信
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watchEffect, watch, nextTick } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "~/stores/auth";
import type {
  Group,
  GroupMessage,
  PaginatedGroupMessages,
} from "~/types/group";

// ページメタデータでプレミアム認証をミドルウェアで制御
definePageMeta({
  middleware: ["premium-required"],
});

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const config = useRuntimeConfig();
const id = route.params.id as string;

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

const headers: Record<string, string> = { Accept: "application/json" };
if (authStore.token) headers.Authorization = `Bearer ${authStore.token}`;

const { data: groupData } = await useFetch<Group>(
  `${config.public.apiBase}/groups/${id}`,
  { headers, server: false }
);
const group = computed(() => groupData.value);

const {
  data,
  pending: messagesPending,
  error: messagesError,
  refresh,
} = await useFetch<PaginatedGroupMessages>(
  `${config.public.apiBase}/groups/${id}/messages`,
  { headers, server: false }
);
const messages = ref<GroupMessage[]>([]);
const nextPageUrl = ref<string | null>(null);
const loadingMore = ref(false);

watchEffect(() => {
  if (data.value) {
    messages.value = data.value.data;
    nextPageUrl.value = (data.value.links?.next as string) || null;
  }
});

const newMessage = ref("");
const sending = ref(false);

async function send() {
  if (!newMessage.value.trim()) return;
  sending.value = true;
  try {
    await $fetch(`${config.public.apiBase}/groups/${id}/messages`, {
      method: "POST",
      headers,
      body: { message: newMessage.value },
    });
    newMessage.value = "";
    await refresh();
  } catch (e) {
    console.error(e);
  } finally {
    sending.value = false;
  }
}

async function loadMore() {
  if (!nextPageUrl.value) return;
  loadingMore.value = true;
  try {
    const res = await $fetch<PaginatedGroupMessages>(nextPageUrl.value, {
      headers,
    });
    messages.value.push(...res.data);
    nextPageUrl.value = (res.links?.next as string) || null;
  } catch (e) {
    console.error(e);
  } finally {
    loadingMore.value = false;
  }
}
</script>
