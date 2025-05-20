<template>
  <div>
    <UContainer>
      <UCard class="max-w-lg mx-auto">
        <template #header>
          <h1 class="text-2xl font-bold">チャットアプリへようこそ</h1>
        </template>

        <div class="py-6 space-y-4">
          <p>友達との会話を楽しみましょう！</p>

          <div class="flex gap-4">
            <UButton to="/auth/login" color="primary"> ログイン </UButton>
            <UButton to="/auth/register" color="gray" variant="soft">
              アカウント登録
            </UButton>
          </div>
        </div>
      </UCard>
    </UContainer>
  </div>
</template>

<script setup lang="ts">
// import { onMounted } from "vue"; // onMounted は不要になるためコメントアウトまたは削除
import { useAuthStore } from "~/stores/auth";

definePageMeta({
  layout: "default",
  title: "ホーム",
  middleware: ["redirect-if-authenticated"], // 作成したミドルウェアを適用
});

const authStore = useAuthStore();
// const router = useRouter(); // router も不要になるためコメントアウトまたは削除

/*
onMounted(async () => {
  // await authStore.checkAuth(); // ミドルウェアで処理されるため不要

  // 認証済みならユーザーホームにリダイレクト
  // if (authStore.isAuthenticated) { // ミドルウェアで処理されるため不要
  //   router.push("/user");
  // }
});
*/
</script>
