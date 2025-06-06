<template>
  <div class="max-w-3xl mx-auto p-4 space-y-6">
    <h1 class="text-2xl font-bold text-center">プランを選択</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="border rounded-lg p-4 flex flex-col items-center">
        <h2 class="text-xl font-semibold mb-2">Standard</h2>
        <p class="mb-4">月額料金 - 最大50名</p>
        <button
          class="px-4 py-2 bg-emerald-600 text-white rounded"
          @click="checkout('standard')"
        >このプランを選択</button>
      </div>
      <div class="border rounded-lg p-4 flex flex-col items-center">
        <h2 class="text-xl font-semibold mb-2">Premium</h2>
        <p class="mb-4">月額料金 - 最大200名</p>
        <button
          class="px-4 py-2 bg-emerald-600 text-white rounded"
          @click="checkout('premium')"
        >このプランを選択</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useToast } from '@/composables/useToast';
import { useApi } from '@/composables/useApi';

const toast = useToast();
const { api } = useApi();

const checkout = async (plan: 'standard' | 'premium') => {
  try {
    const res = await api<{ url: string }>('/stripe/create-checkout-session', {
      method: 'POST',
      body: { plan },
    });
    if (res.url) {
      window.location.href = res.url;
    }
  } catch (error) {
    console.error('checkout error', error);
    toast.add({
      title: 'エラー',
      description: '決済処理でエラーが発生しました',
      color: 'error',
    });
  }
};
</script>
