<template>
  <div>
    <header v-if="isHomePage" class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div
          class="flex justify-between h-20 border-b"
          style="border-color: var(--border)"
        >
          <div class="flex">
            <div class="flex-shrink-0 flex items-center py-2">
              <NuxtLink to="/" class="flex items-center">
                <img
                  src="/images/rogo.png"
                  alt="LumoChat"
                  class="h-12 w-auto"
                />
                <!-- テキストロゴを非表示にします。必要に応じてコメントを外して復活させてください -->
                <!-- <span class="text-xl font-bold text-blue-600">LumoChat</span> -->
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main class="min-h-screen pb-6">
      <slot />
    </main>

    <footer v-if="isHomePage" class="bg-white py-4">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center text-sm" style="color: var(--text-secondary)">
          &copy; {{ new Date().getFullYear() }} LumoChat. All Rights Reserved.
        </div>
      </div>
    </footer>

    <!-- <Toast /> -->
  </div>
</template>

<script setup lang="ts">
// ルートを取得して、現在のページがホームページかどうかを判断
const route = useRoute();
const isHomePage = computed(() => route.path === "/user");
</script>
