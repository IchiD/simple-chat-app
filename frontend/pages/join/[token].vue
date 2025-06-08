<template>
  <div class="p-4 max-w-md mx-auto">
    <h1 class="text-xl font-bold mb-4">グループ参加</h1>
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
    <div class="mb-4">
      <label for="nickname" class="block text-sm font-medium mb-1"
        >ニックネーム</label
      >
      <input
        id="nickname"
        v-model="nickname"
        type="text"
        class="border rounded px-3 py-2 w-full"
        placeholder="ニックネーム"
      />
    </div>
    <button
      class="px-4 py-2 bg-emerald-600 text-white rounded disabled:opacity-50"
      :disabled="pending"
      @click="joinGroup"
    >
      {{ pending ? "参加中..." : "参加する" }}
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useApi } from "~/composables/useApi";
import type { GroupMember } from "~/types/group";

const route = useRoute();
const router = useRouter();
const { api } = useApi();

const token = route.params.token as string;

const nickname = ref("");
const pending = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

const joinGroup = async () => {
  errorMessage.value = "";
  successMessage.value = "";
  if (!nickname.value.trim()) {
    errorMessage.value = "ニックネームを入力してください";
    return;
  }
  if (nickname.value.length > 50) {
    errorMessage.value = "ニックネームは50文字以内で入力してください";
    return;
  }
  pending.value = true;
  try {
    const member = await api<GroupMember>(`/groups/join/${token}`, {
      method: "POST",
      body: { nickname: nickname.value },
    });
    successMessage.value = "グループに参加しました";
    router.push(`/user/groups/${member.group_id}/chat`);
  } catch (e) {
    console.error(e);
    errorMessage.value = "参加に失敗しました";
  } finally {
    pending.value = false;
  }
};
</script>
