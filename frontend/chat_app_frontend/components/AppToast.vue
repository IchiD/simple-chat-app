<template>
  <Transition
    enter-active-class="transition-opacity ease-out duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition-opacity ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="show"
      class="fixed top-4 right-4 z-50 w-full max-w-sm shadow-lg rounded-lg pointer-events-auto"
      :class="[
        color === 'success' ? 'bg-green-50 border-l-4 border-green-500' : '',
        color === 'error' ? 'bg-red-50 border-l-4 border-red-500' : '',
        color === 'warning' ? 'bg-yellow-50 border-l-4 border-yellow-500' : '',
        color === 'info' ? 'bg-blue-50 border-l-4 border-blue-500' : '',
      ]"
    >
      <div class="p-4">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <svg
              v-if="color === 'success'"
              class="h-6 w-6 text-green-500"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 13l4 4L19 7"
              />
            </svg>
            <svg
              v-if="color === 'error'"
              class="h-6 w-6 text-red-500"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
            <svg
              v-if="color === 'warning'"
              class="h-6 w-6 text-yellow-500"
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
            <svg
              v-if="color === 'info'"
              class="h-6 w-6 text-blue-500"
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
          </div>
          <div class="ml-3 w-0 flex-1 pt-0.5">
            <p
              class="text-sm font-medium"
              :class="[
                color === 'success' ? 'text-green-800' : '',
                color === 'error' ? 'text-red-800' : '',
                color === 'warning' ? 'text-yellow-800' : '',
                color === 'info' ? 'text-blue-800' : '',
              ]"
            >
              {{ title }}
            </p>
            <p
              v-if="description"
              class="mt-1 text-sm"
              :class="[
                color === 'success' ? 'text-green-700' : '',
                color === 'error' ? 'text-red-700' : '',
                color === 'warning' ? 'text-yellow-700' : '',
                color === 'info' ? 'text-blue-700' : '',
              ]"
            >
              {{ description }}
            </p>
          </div>
          <div class="ml-4 flex-shrink-0 flex">
            <button
              class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none"
              @click="close"
            >
              <span class="sr-only">閉じる</span>
              <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
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
  </Transition>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";

interface Props {
  title: string;
  description?: string;
  color: "success" | "error" | "warning" | "info";
  timeout?: number;
  onClose?: () => void;
}

const props = withDefaults(defineProps<Props>(), {
  description: "",
  color: "info",
  timeout: 5000,
});

const emit = defineEmits(["close"]);

const show = ref(true);

onMounted(() => {
  if (props.timeout > 0) {
    setTimeout(() => {
      close();
    }, props.timeout);
  }
});

function close() {
  show.value = false;
  emit("close");
  if (props.onClose) {
    props.onClose();
  }
}
</script>
