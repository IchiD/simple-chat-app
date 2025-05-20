<template>
  <div
    v-if="toasts.length > 0"
    aria-live="assertive"
    class="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6 sm:items-start z-50"
  >
    <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
      <TransitionGroup
        name="toast"
        tag="div"
        class="max-w-sm w-full"
        @enter="onEnter"
        @leave="onLeave"
      >
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
          :class="{
            'ring-green-500 ring-2': toast.color === 'success',
            'ring-red-500 ring-2': toast.color === 'error',
            'ring-blue-500 ring-2': toast.color === 'info',
            'ring-yellow-500 ring-2': toast.color === 'warning',
          }"
        >
          <div class="p-4">
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <!-- Success Icon -->
                <svg
                  v-if="toast.color === 'success'"
                  class="h-6 w-6 text-green-400"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>

                <!-- Error Icon -->
                <svg
                  v-else-if="toast.color === 'error'"
                  class="h-6 w-6 text-red-400"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>

                <!-- Info Icon -->
                <svg
                  v-else-if="toast.color === 'info'"
                  class="h-6 w-6 text-blue-400"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>

                <!-- Warning Icon -->
                <svg
                  v-else-if="toast.color === 'warning'"
                  class="h-6 w-6 text-yellow-400"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                  />
                </svg>
              </div>
              <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-medium text-gray-900">
                  {{ toast.title }}
                </p>
                <p v-if="toast.description" class="mt-1 text-sm text-gray-500">
                  {{ toast.description }}
                </p>
              </div>
              <div class="ml-4 flex-shrink-0 flex">
                <button
                  @click="removeToast(toast.id)"
                  class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <span class="sr-only">閉じる</span>
                  <svg
                    class="h-5 w-5"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </div>
</template>

<script setup lang="ts">
import { watchEffect } from "vue";
import { useToastStore } from "../composables/useToast";

// トーストストアを取得
const toastStore = useToastStore();
const { toasts, removeToast } = toastStore;

// トランジション関数
const onEnter = (el: HTMLElement) => {
  const height = el.scrollHeight;
  el.style.opacity = "0";
  el.style.transform = "translateX(100%)";

  // アニメーション用のスタイルリセット
  setTimeout(() => {
    el.style.transition = "all 0.3s ease-out";
    el.style.opacity = "1";
    el.style.transform = "translateX(0)";
  }, 50);
};

const onLeave = (el: HTMLElement) => {
  el.style.transition = "all 0.3s ease-in";
  el.style.opacity = "0";
  el.style.transform = "translateX(100%)";
};

// 自動削除の設定
watchEffect(() => {
  toasts.value.forEach((toast) => {
    if (!toast.persistent) {
      setTimeout(() => {
        removeToast(toast.id);
      }, toast.duration || 5000);
    }
  });
});
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>
