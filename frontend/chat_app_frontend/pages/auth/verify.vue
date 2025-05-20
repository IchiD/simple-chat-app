<template>
  <div class="min-h-screen flex flex-col justify-center">
    <UCard class="mx-auto w-full max-w-md text-center">
      <template #header>
        <h1 class="text-xl font-semibold">メール認証</h1>
      </template>

      <div class="py-4">
        <template v-if="loading">
          <UIcon
            name="i-heroicons-arrow-path"
            class="text-5xl mb-4 text-primary animate-spin"
          />
          <p class="mb-4">認証中です。しばらくお待ちください...</p>
        </template>

        <template v-else-if="error">
          <UIcon
            name="i-heroicons-exclamation-triangle"
            class="text-5xl mb-4 text-error"
          />
          <p class="mb-4 text-error">{{ error }}</p>
          <UButton to="/auth/login" color="primary"> ログインページへ </UButton>
        </template>

        <template v-else-if="success">
          <UIcon
            name="i-heroicons-check-circle"
            class="text-5xl mb-4 text-success"
          />
          <p class="mb-4">{{ message }}</p>
          <p class="text-sm text-gray-500">ホーム画面に自動的に移動します...</p>
        </template>
      </div>
    </UCard>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useAuthStore } from "~/stores/auth";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const toast = useToast();

const loading = ref(true);
const error = ref<string | null>(null);
const success = ref(false);
const message = ref("");

const verifyToken = async (token: string) => {
  try {
    const result = await authStore.verifyEmail(token);

    if (result.success) {
      success.value = true;
      message.value = result.message || "メールアドレスの認証が完了しました";

      // 成功したら3秒後にホームページへリダイレクト
      setTimeout(() => {
        router.push("/");
      }, 3000);
    } else {
      error.value = result.message || "認証に失敗しました";
    }
  } catch (err: any) {
    console.error("Verification error:", err);
    error.value = err.message || "認証処理中にエラーが発生しました";
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  const token = route.query.token as string;

  if (!token) {
    error.value = "認証トークンが見つかりません";
    loading.value = false;
    return;
  }

  verifyToken(token);
});
</script>
