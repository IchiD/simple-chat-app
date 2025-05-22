<template>
  <div>
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <NuxtLink to="/" class="text-xl font-bold text-blue-600"
                >LumoChat</NuxtLink
              >
            </div>
          </div>
        </div>
      </div>
    </header>

    <main>
      <slot />
    </main>

    <!-- <Toast /> -->
  </div>
</template>

<script setup lang="ts">
// デフォルトレイアウト
</script>
