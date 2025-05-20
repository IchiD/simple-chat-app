<template>
  <div class="min-h-screen flex flex-col justify-center">
    <UCard class="mx-auto w-full max-w-md text-center">
      <template #header>
        <h1 class="text-xl font-semibold">メール確認</h1>
      </template>

      <div class="py-4">
        <UIcon name="i-heroicons-envelope" class="text-5xl mb-4 text-primary" />
        <p class="mb-4">
          登録したメールアドレスに確認メールを送信しました。<br />
          メール内のリンクをクリックして登録を完了してください。
        </p>

        <UButton
          :loading="loading"
          color="primary"
          variant="outline"
          @click="resendVerification"
        >
          確認メールを再送信
        </UButton>
      </div>

      <template #footer>
        <NuxtLink to="/auth/login" class="text-primary hover:underline text-sm">
          ログインページへ戻る
        </NuxtLink>
      </template>
    </UCard>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
// definePageMetaとuseToastはNuxtが自動でインポートするので明示的インポート不要

definePageMeta({
  layout: "default",
  title: "メール確認",
});

const toast = useToast();
const loading = ref(false);

// 確認メール再送信（実際の実装ではAPIとの連携が必要）
const resendVerification = async () => {
  loading.value = true;

  try {
    // APIリクエストをここに実装
    // 仮の実装として1秒待機
    await new Promise((resolve) => setTimeout(resolve, 1000));

    toast.add({
      title: "送信完了",
      description: "確認メールを再送信しました",
      color: "success",
    });
  } catch (err: unknown) {
    console.error(err);
    toast.add({
      title: "エラー",
      description: "確認メールの送信に失敗しました",
      color: "error",
    });
  } finally {
    loading.value = false;
  }
};
</script>
