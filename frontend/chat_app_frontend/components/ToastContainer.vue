<template>
  <div class="fixed top-0 right-0 z-50 p-4 space-y-4">
    <TransitionGroup
      name="toast-list"
      tag="div"
      enter-active-class="transition-opacity ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-for="toast in toasts" :key="toast.id" class="pointer-events-auto">
        <AppToast
          :title="toast.title"
          :description="toast.description"
          :color="toast.color || 'info'"
          :timeout="toast.timeout"
          @close="removeToast(toast.id)"
        />
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup lang="ts">
import { useToast } from "../composables/useToast";

const { toasts, removeToast } = useToast();
</script>
