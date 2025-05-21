<template>
  <div class="min-h-screen flex flex-col justify-center">
    <UCard class="mx-auto w-full max-w-md text-center">
      <template #header>
        <h1 class="text-xl font-semibold">メールアドレス変更確認</h1>
      </template>

      <div class="py-4">
        <template v-if="loading">
          <UIcon
            name="i-heroicons-arrow-path"
            class="text-5xl mb-4 text-primary animate-spin"
          />
          <p class="mb-4">メールアドレス変更を確認中です...</p>
        </template>

        <template v-else-if="error">
          <UIcon
            name="i-heroicons-exclamation-triangle"
            class="text-5xl mb-4 text-error"
          />
          <p class="mb-4 text-error">{{ error }}</p>
          <UButton to="/user" color="primary"> プロフィールに戻る </UButton>
        </template>

        <template v-else-if="success">
          <UIcon
            name="i-heroicons-check-circle"
            class="text-5xl mb-4 text-success"
          />
          <p class="mb-4">{{ message }}</p>
          <p v-if="email" class="text-sm mb-4">
            メールアドレスが
            <span class="font-medium">{{ email }}</span> に変更されました。
          </p>
          <p class="text-sm text-gray-500 mb-4">
            自動的にプロフィールページに移動します...
          </p>
        </template>
      </div>
    </UCard>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useApi } from "../../composables/useApi";
// import { useToast } from "../../composables/useToast"; // 未使用のためコメントアウト
import { FetchError } from "ofetch"; // FetchErrorをインポート。type指定を削除

// ページメタデータの設定
definePageMeta({
  title: "メールアドレス変更確認",
  layout: "default",
});

const route = useRoute();
const router = useRouter();
const { api } = useApi();
// const toast = useToast(); // 未使用のため削除

const loading = ref(true);
const error = ref<string | null>(null);
const success = ref(false);
const message = ref("");
const email = ref("");

const verifyEmailChange = async (token: string) => {
  try {
    const result = await api<{
      status: string;
      message: string;
      email?: string;
    }>("verify-email-change", {
      method: "GET",
      params: { token },
    });

    if (result.status === "success") {
      success.value = true;
      message.value = result.message || "メールアドレスの変更が完了しました";
      if (result.email) {
        email.value = result.email;
      }

      // 成功したら3秒後にプロフィールページへリダイレクト
      setTimeout(() => {
        router.push("/user");
      }, 3000);
    } else {
      error.value = result.message || "認証に失敗しました";
    }
  } catch (err) {
    console.error("Verification error details:", err);
    if (err instanceof FetchError) {
      // err.data はバックエンドからのレスポンスボディの型に応じて適切にキャストする
      // ここでは、バックエンドが { message: string, ... } のようなエラーオブジェクトを返すと仮定
      const errorData = err.data as {
        message?: string;
        errors?: Record<string, string[]>;
      };
      if (errorData && errorData.message) {
        error.value = errorData.message;
      } else if (errorData && errorData.errors) {
        // バリデーションエラーなどの詳細がある場合
        error.value =
          Object.values(errorData.errors).flat().join(" ") ||
          "サーバーエラーが発生しました";
      } else {
        error.value =
          err.message || "メールアドレス認証中にサーバーエラーが発生しました。";
      }
    } else if (err instanceof Error) {
      error.value = err.message || "認証処理中に不明なエラーが発生しました";
    } else {
      error.value = "認証処理中に予期せぬエラーが発生しました";
    }
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

  verifyEmailChange(token);
});
</script>
