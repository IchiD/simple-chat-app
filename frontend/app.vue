<template>
  <div class="bg-gray-50">
    <ToastContainer />
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>

<script setup lang="ts">
import { onMounted, provide, ref } from "vue";
import { useAuthStore } from "~/stores/auth";
import ToastContainer from "~/components/ToastContainer.vue";

// アプリ初期化時に認証状態を確認
onMounted(async () => {
  console.log("アプリ初期化時の認証状態チェック開始 (app.vue)");
  const authStore = useAuthStore();
  await authStore.checkAuth();
  console.log("認証状態チェック完了 (app.vue):", {
    isAuthenticated: authStore.isAuthenticated,
  });
});

// Nuxt UIのロケールコンテキストを提供
provide(Symbol.for("nuxt-ui.locale-context"), {
  locale: ref("ja"),
  locales: {
    ja: {
      // 日本語のローカライズ設定をここに追加できます
    },
  },
});
</script>
