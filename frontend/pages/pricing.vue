<template>
  <div class="max-w-6xl mx-auto p-4 space-y-8">
    <!-- テスト環境バナー -->
    <div
      class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg"
    >
      <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path
            fill-rule="evenodd"
            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
            clip-rule="evenodd"
          />
        </svg>
        <span class="font-medium">テスト環境</span>
      </div>
      <p class="mt-1 text-sm">
        現在はStripeテストモードです。実際の決済は行われず、テストカードでプランの変更をテストできます。
      </p>
    </div>

    <!-- ヘッダーセクション -->
    <div class="text-center space-y-4">
      <h1 class="text-3xl md:text-4xl font-bold text-gray-900">料金プラン</h1>
    </div>

    <!-- 認証状態の表示 -->
    <div
      v-if="!authStore.isAuthenticated"
      class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6"
    >
      <div class="flex items-center">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-yellow-600 mr-2"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
            clip-rule="evenodd"
          />
        </svg>
        <p class="text-yellow-800">
          プランを選択するには、まずログインが必要です。
          <NuxtLink
            to="/auth/login"
            class="text-yellow-900 underline font-medium ml-1"
          >
            こちらからログイン
          </NuxtLink>
          してください。
        </p>
      </div>
    </div>

    <!-- 現在のプラン表示 -->
    <div
      v-if="authStore.isAuthenticated && authStore.user"
      class="bg-blue-50 border border-blue-200 rounded-lg p-4"
    >
      <div class="flex items-center">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-blue-600 mr-2"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
            clip-rule="evenodd"
          />
        </svg>
        <p class="text-blue-800">
          現在のプラン:
          <span class="font-semibold">{{ getCurrentPlanDisplay() }}</span>
          <span v-if="authStore.user.subscription_status" class="ml-2 text-sm">
            ({{ getStatusDisplay(authStore.user.subscription_status) }})
          </span>
        </p>
      </div>
    </div>

    <!-- プランカード -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
      <!-- FREE プラン -->
      <div class="border-2 rounded-xl p-6 relative bg-white shadow-sm">
        <div class="text-center space-y-4">
          <h2 class="text-xl font-semibold text-gray-900">FREE</h2>
          <div class="space-y-1">
            <div class="text-3xl font-bold text-gray-900">¥0</div>
            <div class="text-sm text-gray-500">月額</div>
          </div>
          <ul class="space-y-2 text-sm text-gray-600">
            <li class="flex items-center">
              <svg
                class="w-4 h-4 text-green-500 mr-2"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
              基本チャット機能
            </li>
            <li class="flex items-center">
              <svg
                class="w-4 h-4 text-green-500 mr-2"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
              1対1チャット
            </li>
            <li class="flex items-center">
              <svg
                class="w-4 h-4 text-gray-400 mr-2"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
              グループチャット
            </li>
          </ul>
          <button
            disabled
            class="w-full py-2 px-4 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed font-medium"
          >
            現在のプラン
          </button>
        </div>
      </div>

      <!-- STANDARD プラン -->
      <div
        class="border-2 border-blue-500 rounded-xl p-6 relative bg-white shadow-lg"
      >
        <div
          class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium"
        >
          おすすめ
        </div>
        <div class="text-center space-y-4">
          <h2 class="text-xl font-semibold text-gray-900">STANDARD</h2>
          <div class="space-y-1">
            <div class="text-3xl font-bold text-gray-900">¥2,980</div>
            <div class="text-sm text-gray-500">月額（税込）</div>
          </div>
          <ul class="space-y-2 text-sm text-gray-600">
            <li class="flex items-center">
              <svg
                class="w-4 h-4 text-green-500 mr-2"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
              FREEプランの全機能
            </li>
            <li class="flex items-center">
              <svg
                class="w-4 h-4 text-green-500 mr-2"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
              グループチャット（最大50名）
            </li>
            <li class="flex items-center">
              <svg
                class="w-4 h-4 text-green-500 mr-2"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
              一括配信（月10回）
            </li>
            <li class="flex items-center">
              <svg
                class="w-4 h-4 text-green-500 mr-2"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
              QRコード参加機能
            </li>
          </ul>
          <button
            class="w-full py-2 px-4 rounded-lg font-medium transition-all duration-200"
            :class="{
              'bg-blue-500 hover:bg-blue-600 text-white':
                !isLoading && !isCurrentPlan('standard'),
              'bg-blue-300 text-white cursor-not-allowed':
                isLoading && selectedPlan !== 'standard',
              'bg-blue-600 text-white cursor-wait':
                isLoading && selectedPlan === 'standard',
              'bg-gray-400 text-white cursor-not-allowed':
                isCurrentPlan('standard'),
              'opacity-50 cursor-not-allowed': !authStore.isAuthenticated,
            }"
            :disabled="
              isLoading ||
              !authStore.isAuthenticated ||
              isCurrentPlan('standard')
            "
            @click="checkout('standard')"
          >
            <template v-if="isLoading && selectedPlan === 'standard'">
              <div class="flex items-center justify-center">
                <svg
                  class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  />
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  />
                </svg>
                <span class="text-sm">{{
                  loadingState.message || "処理中..."
                }}</span>
              </div>
            </template>
            <template v-else-if="isLoading">
              <span class="opacity-70 text-sm">他のプラン処理中...</span>
            </template>
            <template v-else-if="isCurrentPlan('standard')">
              現在のプラン
            </template>
            <template v-else-if="!authStore.isAuthenticated">
              ログインが必要です
            </template>
            <template v-else>
              <span v-if="authStore.user?.plan === 'free'">
                このプランを選択
              </span>
              <span v-else-if="authStore.user?.plan === 'premium'">
                ダウングレード
              </span>
              <span v-else> このプランを選択 </span>
            </template>
          </button>
        </div>
      </div>

      <!-- PREMIUM プラン -->
      <div
        class="border-2 border-purple-500 rounded-xl p-6 relative bg-white shadow-lg"
      >
        <div
          class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-1 rounded-full text-sm font-medium"
        >
          最高機能
        </div>
        <div class="text-center space-y-4">
          <h2 class="text-xl font-semibold text-gray-900">PREMIUM</h2>
          <div class="space-y-1">
            <div class="text-3xl font-bold text-gray-900">¥5,980</div>
            <div class="text-sm text-gray-500">月額（税込）</div>
          </div>
          <ul class="space-y-2 text-sm text-gray-600">
            <li class="flex items-center">
              <svg
                class="w-4 h-4 text-green-500 mr-2"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
              STANDARDプランの全機能
            </li>
            <li class="flex items-center">
              <svg
                class="w-4 h-4 text-green-500 mr-2"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
              グループチャット（最大200名）
            </li>
            <li class="flex items-center">
              <svg
                class="w-4 h-4 text-green-500 mr-2"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
              一括配信（無制限）
            </li>
            <li class="flex items-center">
              <svg
                class="w-4 h-4 text-green-500 mr-2"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
              優先サポート
            </li>
          </ul>
          <button
            class="w-full py-2 px-4 rounded-lg font-medium transition-all duration-200"
            :class="{
              'bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white':
                !isLoading && !isCurrentPlan('premium'),
              'bg-gradient-to-r from-purple-300 to-pink-300 text-white cursor-not-allowed':
                isLoading && selectedPlan !== 'premium',
              'bg-gradient-to-r from-purple-600 to-pink-600 text-white cursor-wait':
                isLoading && selectedPlan === 'premium',
              'bg-gray-400 text-white cursor-not-allowed':
                isCurrentPlan('premium'),
              'opacity-50 cursor-not-allowed': !authStore.isAuthenticated,
            }"
            :disabled="
              isLoading ||
              !authStore.isAuthenticated ||
              isCurrentPlan('premium')
            "
            @click="checkout('premium')"
          >
            <template v-if="isLoading && selectedPlan === 'premium'">
              <div class="flex items-center justify-center">
                <svg
                  class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  />
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  />
                </svg>
                <span class="text-sm">{{
                  loadingState.message || "処理中..."
                }}</span>
              </div>
            </template>
            <template v-else-if="isLoading">
              <span class="opacity-70 text-sm">他のプラン処理中...</span>
            </template>
            <template v-else-if="isCurrentPlan('premium')">
              現在のプラン
            </template>
            <template v-else-if="!authStore.isAuthenticated">
              ログインが必要です
            </template>
            <template v-else>
              <span v-if="authStore.user?.plan === 'free'">
                このプランを選択
              </span>
              <span v-else-if="authStore.user?.plan === 'standard'">
                アップグレード
              </span>
              <span v-else> このプランを選択 </span>
            </template>
          </button>
        </div>
      </div>
    </div>

    <!-- プラン比較テーブル -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="px-6 py-4 bg-gray-50 border-b">
        <h2 class="text-xl font-semibold text-gray-900">詳細機能比較</h2>
        <p class="text-sm text-gray-600 mt-1">
          各プランの機能を詳しく比較できます
        </p>
      </div>

      <!-- デスクトップ版テーブル -->
      <div class="hidden md:block">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th
                class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider"
              >
                機能
              </th>
              <th
                class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider"
              >
                FREE
              </th>
              <th
                class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider"
              >
                STANDARD
              </th>
              <th
                class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider"
              >
                PREMIUM
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr
              v-for="feature in comparisonFeatures"
              :key="feature.name"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 text-sm font-medium text-gray-900">
                {{ feature.name }}
                <div
                  v-if="feature.description"
                  class="text-xs text-gray-500 mt-1"
                >
                  {{ feature.description }}
                </div>
              </td>
              <td class="px-6 py-4 text-center">
                <div class="flex justify-center">
                  <component
                    :is="getFeatureIcon(feature.free)"
                    :class="getFeatureClass(feature.free)"
                    :value="feature.free"
                  />
                </div>
              </td>
              <td class="px-6 py-4 text-center">
                <div class="flex justify-center">
                  <component
                    :is="getFeatureIcon(feature.standard)"
                    :class="getFeatureClass(feature.standard)"
                    :value="feature.standard"
                  />
                </div>
              </td>
              <td class="px-6 py-4 text-center">
                <div class="flex justify-center">
                  <component
                    :is="getFeatureIcon(feature.premium)"
                    :class="getFeatureClass(feature.premium)"
                    :value="feature.premium"
                  />
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- モバイル版カード -->
      <div class="md:hidden space-y-4 p-4">
        <div
          v-for="feature in comparisonFeatures"
          :key="feature.name"
          class="border rounded-lg p-4"
        >
          <h3 class="font-medium text-gray-900 mb-2">{{ feature.name }}</h3>
          <p v-if="feature.description" class="text-sm text-gray-600 mb-3">
            {{ feature.description }}
          </p>
          <div class="grid grid-cols-3 gap-4 text-center">
            <div>
              <div class="text-xs text-gray-500 mb-1">FREE</div>
              <div class="flex justify-center">
                <component
                  :is="getFeatureIcon(feature.free)"
                  :class="getFeatureClass(feature.free)"
                  :value="feature.free"
                />
              </div>
            </div>
            <div>
              <div class="text-xs text-gray-500 mb-1">STANDARD</div>
              <div class="flex justify-center">
                <component
                  :is="getFeatureIcon(feature.standard)"
                  :class="getFeatureClass(feature.standard)"
                  :value="feature.standard"
                />
              </div>
            </div>
            <div>
              <div class="text-xs text-gray-500 mb-1">PREMIUM</div>
              <div class="flex justify-center">
                <component
                  :is="getFeatureIcon(feature.premium)"
                  :class="getFeatureClass(feature.premium)"
                  :value="feature.premium"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 決済前確認モーダル -->
    <div
      v-if="confirmationState.isVisible"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-xl shadow-2xl p-6 max-w-lg w-full mx-4">
        <div class="text-center mb-6">
          <div
            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4"
          >
            <svg
              class="h-6 w-6 text-blue-600"
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
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">
            プラン選択の確認
          </h3>
          <p class="text-sm text-gray-600">以下の内容で決済を進めますか？</p>
        </div>

        <!-- プラン詳細 -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
          <div class="flex justify-between items-center mb-3">
            <span class="font-medium text-gray-900">選択プラン</span>
            <span class="text-lg font-bold text-blue-600 uppercase">
              {{ confirmationState.selectedPlan }}
            </span>
          </div>

          <div class="flex justify-between items-center mb-3">
            <span class="text-gray-600">月額料金</span>
            <span class="font-semibold text-gray-900">
              {{ getPlanPrice(confirmationState.selectedPlan) }}
            </span>
          </div>

          <!-- 現在のプランからの変更の場合 -->
          <div
            v-if="authStore.user?.plan && authStore.user.plan !== 'free'"
            class="border-t pt-3 mt-3"
          >
            <div class="flex justify-between items-center mb-2">
              <span class="text-gray-600">現在のプラン</span>
              <span class="text-gray-900 uppercase">{{
                authStore.user.plan
              }}</span>
            </div>
            <div class="flex justify-between items-center mb-2">
              <span class="text-gray-600">現在の料金</span>
              <span class="text-gray-900">{{
                getPlanPrice(authStore.user.plan)
              }}</span>
            </div>
            <div class="flex justify-between items-center font-semibold">
              <span class="text-gray-900">差額</span>
              <span class="text-green-600">
                +{{
                  calculatePriceDifference(
                    authStore.user.plan,
                    confirmationState.selectedPlan
                  )
                }}
              </span>
            </div>
          </div>

          <!-- 初回決済の場合 -->
          <div v-else class="border-t pt-3 mt-3">
            <div class="flex justify-between items-center font-semibold">
              <span class="text-gray-900">初回決済額</span>
              <span class="text-blue-600">
                {{ getPlanPrice(confirmationState.selectedPlan) }}
              </span>
            </div>
          </div>
        </div>

        <!-- 重要事項 -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-6">
          <div class="flex items-start">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 text-yellow-600 mr-2 mt-0.5 flex-shrink-0"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                clip-rule="evenodd"
              />
            </svg>
            <div class="text-xs text-yellow-800">
              <p class="font-medium mb-1">決済に関する重要事項</p>
              <ul class="space-y-1">
                <li>• 決済はStripeの安全なシステムで処理されます</li>
                <li>• 月額料金は毎月自動で請求されます</li>
                <li>• プランはいつでも変更・キャンセル可能です</li>
                <li>• 決済完了後、即座にプラン機能が利用可能になります</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- ボタン -->
        <div class="flex space-x-3">
          <button
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
            @click="cancelConfirmation"
          >
            キャンセル
          </button>
          <button
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
            @click="proceedWithPayment"
          >
            決済を進める
          </button>
        </div>
      </div>
    </div>

    <!-- ローディング状態表示 -->
    <div
      v-if="loadingState.isLoading"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4">
        <div class="text-center">
          <!-- ローディングアイコン -->
          <div class="mb-6">
            <div class="relative">
              <svg
                class="animate-spin h-16 w-16 text-blue-600 mx-auto"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                />
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                />
              </svg>
              <!-- プラン表示 -->
              <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-xs font-bold text-blue-600 uppercase">
                  {{ loadingState.selectedPlan }}
                </span>
              </div>
            </div>
          </div>

          <!-- プログレスバー -->
          <div class="mb-6">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
              <span>進行状況</span>
              <span>{{ Math.round(loadingState.progress) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
              <div
                class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-300 ease-out"
                :style="{ width: `${loadingState.progress}%` }"
              />
            </div>
          </div>

          <!-- ローディングメッセージ -->
          <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
              決済処理中...
            </h3>
            <p class="text-sm text-gray-600 mb-3">
              {{ loadingState.message }}
            </p>

            <!-- ステージ表示 -->
            <div class="text-xs text-gray-500">
              <span v-if="loadingState.stage">
                ステージ: {{ loadingState.stage }}
              </span>
              <span v-if="elapsedTime > 0" class="ml-2">
                経過時間: {{ elapsedTime }}秒
              </span>
            </div>
          </div>

          <!-- 注意事項 -->
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
            <div class="flex items-start">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 text-yellow-600 mr-2 mt-0.5 flex-shrink-0"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
              <div class="text-xs text-yellow-800">
                <p class="font-medium">重要な注意事項</p>
                <p class="mt-1">
                  このページを閉じたり、ブラウザの戻るボタンを押さないでください。
                  決済処理が中断される可能性があります。
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- エラー状態表示 -->
    <div
      v-if="errorState.hasError"
      class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6"
    >
      <div class="flex items-start">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-red-600 mr-2 mt-0.5 flex-shrink-0"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
            clip-rule="evenodd"
          />
        </svg>
        <div class="flex-1">
          <h3 class="text-red-800 font-medium">
            {{
              errorState.errorType
                ? ERROR_MESSAGES[errorState.errorType as ErrorType]?.title
                : "エラーが発生しました"
            }}
          </h3>
          <p class="text-red-700 mt-1 text-sm">
            {{
              errorState.errorType
                ? ERROR_MESSAGES[errorState.errorType as ErrorType]?.description
                : "予期しないエラーが発生しました。"
            }}
          </p>
          <div class="mt-3 flex items-center space-x-3">
            <button
              v-if="errorState.canRetry"
              class="text-sm bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded-md transition-colors"
              @click="_retryCheckout(selectedPlan as 'standard' | 'premium')"
            >
              再試行 ({{ 3 - errorState.retryCount }}/3)
            </button>
            <button
              class="text-sm text-red-600 hover:text-red-800 underline"
              @click="resetErrorState"
            >
              エラーを閉じる
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- よくある質問 -->
    <div class="mt-16">
      <h2 class="text-2xl font-bold text-center mb-8">よくある質問</h2>
      <div class="max-w-3xl mx-auto space-y-4">
        <details
          v-for="faq in faqs"
          :key="faq.question"
          class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50"
        >
          <summary class="font-semibold text-gray-900 select-none">
            {{ faq.question }}
          </summary>
          <p class="mt-2 text-gray-600">{{ faq.answer }}</p>
        </details>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, h, computed } from "vue";
import { useToast } from "@/composables/useToast";
import { useApi } from "@/composables/useApi";
import { useAuthStore } from "~/stores/auth";

const toast = useToast();
const { api } = useApi();
const authStore = useAuthStore();

// ローディング状態を詳細管理
const loadingState = ref<{
  isLoading: boolean;
  selectedPlan: string | null;
  stage: string | null;
  progress: number;
  message: string;
  startTime: number | null;
}>({
  isLoading: false,
  selectedPlan: null,
  stage: null,
  progress: 0,
  message: "",
  startTime: null,
});

// ローディングステージの定義
const LOADING_STAGES = {
  INIT: "init",
  AUTH_CHECK: "auth_check",
  USER_VALIDATION: "user_validation",
  PLAN_VALIDATION: "plan_validation",
  PAYMENT_PREPARATION: "payment_preparation",
  STRIPE_SESSION: "stripe_session",
  REDIRECT_PREPARATION: "redirect_preparation",
  REDIRECTING: "redirecting",
} as const;

type LoadingStage = (typeof LOADING_STAGES)[keyof typeof LOADING_STAGES];

// ローディングステージ別の設定
const LOADING_STAGE_CONFIG: Record<
  LoadingStage,
  {
    message: string;
    progress: number;
    duration: number; // 推定時間（ミリ秒）
  }
> = {
  [LOADING_STAGES.INIT]: {
    message: "決済処理を開始しています...",
    progress: 10,
    duration: 500,
  },
  [LOADING_STAGES.AUTH_CHECK]: {
    message: "認証状態を確認しています...",
    progress: 20,
    duration: 800,
  },
  [LOADING_STAGES.USER_VALIDATION]: {
    message: "ユーザー情報を検証しています...",
    progress: 35,
    duration: 1000,
  },
  [LOADING_STAGES.PLAN_VALIDATION]: {
    message: "プラン情報を確認しています...",
    progress: 50,
    duration: 700,
  },
  [LOADING_STAGES.PAYMENT_PREPARATION]: {
    message: "決済情報を準備しています...",
    progress: 65,
    duration: 1200,
  },
  [LOADING_STAGES.STRIPE_SESSION]: {
    message: "Stripe決済セッションを作成しています...",
    progress: 80,
    duration: 2000,
  },
  [LOADING_STAGES.REDIRECT_PREPARATION]: {
    message: "決済ページを準備しています...",
    progress: 90,
    duration: 800,
  },
  [LOADING_STAGES.REDIRECTING]: {
    message: "決済ページに移動しています...",
    progress: 100,
    duration: 2000,
  },
};

// 従来の状態管理（後方互換性のため）
const isLoading = computed(() => loadingState.value.isLoading);
const selectedPlan = computed(() => loadingState.value.selectedPlan);

// エラー状態管理
const errorState = ref<{
  hasError: boolean;
  errorType: string | null;
  retryCount: number;
  canRetry: boolean;
}>({
  hasError: false,
  errorType: null,
  retryCount: 0,
  canRetry: false,
});

// エラータイプの定義
const ERROR_TYPES = {
  NETWORK: "network",
  AUTH: "auth",
  SUBSCRIPTION: "subscription",
  STRIPE: "stripe",
  SERVER: "server",
  VALIDATION: "validation",
  TIMEOUT: "timeout",
  UNKNOWN: "unknown",
} as const;

type ErrorType = (typeof ERROR_TYPES)[keyof typeof ERROR_TYPES];

// エラーメッセージの定義
const ERROR_MESSAGES: Record<
  ErrorType,
  {
    title: string;
    description: string;
    action?: string;
    canRetry: boolean;
  }
> = {
  [ERROR_TYPES.NETWORK]: {
    title: "ネットワークエラー",
    description:
      "インターネット接続を確認してください。接続が安定してから再度お試しください。",
    action: "再試行",
    canRetry: true,
  },
  [ERROR_TYPES.AUTH]: {
    title: "認証エラー",
    description:
      "認証の有効期限が切れている可能性があります。再度ログインしてください。",
    action: "ログインページへ",
    canRetry: false,
  },
  [ERROR_TYPES.SUBSCRIPTION]: {
    title: "サブスクリプションエラー",
    description:
      "既にアクティブなサブスクリプションがあります。プラン変更については、サポートまでお問い合わせください。",
    action: "サポートに連絡",
    canRetry: false,
  },
  [ERROR_TYPES.STRIPE]: {
    title: "決済システム未設定",
    description:
      "現在、決済システムが設定されていません。開発環境では決済機能をご利用いただけません。",
    action: "了解",
    canRetry: false,
  },
  [ERROR_TYPES.SERVER]: {
    title: "サーバーエラー",
    description:
      "サーバーで一時的な問題が発生しています。しばらく時間をおいてから再度お試しください。",
    action: "再試行",
    canRetry: true,
  },
  [ERROR_TYPES.VALIDATION]: {
    title: "入力エラー",
    description:
      "入力内容に問題があります。内容を確認してから再度お試しください。",
    action: "内容を確認",
    canRetry: false,
  },
  [ERROR_TYPES.TIMEOUT]: {
    title: "タイムアウトエラー",
    description:
      "処理に時間がかかりすぎています。ネットワーク接続を確認してから再度お試しください。",
    action: "再試行",
    canRetry: true,
  },
  [ERROR_TYPES.UNKNOWN]: {
    title: "予期しないエラー",
    description:
      "予期しないエラーが発生しました。問題が続く場合は、サポートまでお問い合わせください。",
    action: "再試行",
    canRetry: true,
  },
};

// ローディング状態管理関数
const setLoadingStage = async (stage: LoadingStage) => {
  const config = LOADING_STAGE_CONFIG[stage];

  loadingState.value.stage = stage;
  loadingState.value.message = config.message;
  loadingState.value.progress = config.progress;

  // プログレスバーのアニメーション
  await new Promise((resolve) => {
    const startProgress = loadingState.value.progress;
    const targetProgress = config.progress;
    const duration = 300; // アニメーション時間
    const steps = 20;
    const increment = (targetProgress - startProgress) / steps;

    let currentStep = 0;
    const interval = setInterval(() => {
      currentStep++;
      loadingState.value.progress = Math.min(
        startProgress + increment * currentStep,
        targetProgress
      );

      if (currentStep >= steps) {
        clearInterval(interval);
        resolve(void 0);
      }
    }, duration / steps);
  });

  // ステージ固有の最小待機時間
  const minWaitTime = Math.max(config.duration - 300, 200);
  await new Promise((resolve) => setTimeout(resolve, minWaitTime));
};

const startLoading = (plan: string) => {
  loadingState.value = {
    isLoading: true,
    selectedPlan: plan,
    stage: LOADING_STAGES.INIT,
    progress: 0,
    message: "処理を開始しています...",
    startTime: Date.now(),
  };
};

const stopLoading = () => {
  loadingState.value = {
    isLoading: false,
    selectedPlan: null,
    stage: null,
    progress: 0,
    message: "",
    startTime: null,
  };
};

// 経過時間の計算
const elapsedTime = computed(() => {
  if (!loadingState.value.startTime) return 0;
  return Math.floor((Date.now() - loadingState.value.startTime) / 1000);
});

// 決済前確認状態管理
const confirmationState = ref<{
  isVisible: boolean;
  selectedPlan: string | null;
}>({
  isVisible: false,
  selectedPlan: null,
});

// プラン料金の定義
const PLAN_PRICES: Record<string, { price: number; display: string }> = {
  free: { price: 0, display: "¥0" },
  standard: { price: 2980, display: "¥2,980" },
  premium: { price: 5980, display: "¥5,980" },
};

// プラン料金取得関数
const getPlanPrice = (plan: string | null): string => {
  if (!plan) return "¥0";
  return PLAN_PRICES[plan]?.display || "¥0";
};

// 差額計算関数
const calculatePriceDifference = (
  currentPlan: string | null,
  newPlan: string | null
): string => {
  if (!currentPlan || !newPlan) return "¥0";

  const currentPrice = PLAN_PRICES[currentPlan]?.price || 0;
  const newPrice = PLAN_PRICES[newPlan]?.price || 0;
  const difference = newPrice - currentPrice;

  if (difference > 0) {
    return `¥${difference.toLocaleString()}`;
  }
  return "¥0";
};

// 確認モーダルキャンセル関数
const cancelConfirmation = () => {
  confirmationState.value = {
    isVisible: false,
    selectedPlan: null,
  };
};

// 決済実行関数
const proceedWithPayment = () => {
  if (confirmationState.value.selectedPlan) {
    const plan = confirmationState.value.selectedPlan as "standard" | "premium";
    confirmationState.value.isVisible = false;
    executeCheckout(plan);
  }
};

// プラン比較データ
const comparisonFeatures = ref([
  {
    name: "基本チャット機能",
    description: "友達との1対1チャット",
    free: true,
    standard: true,
    premium: true,
  },
  {
    name: "グループチャット",
    description: "複数人でのチャット機能",
    free: false,
    standard: "最大50名",
    premium: "最大200名",
  },
  {
    name: "一括配信",
    description: "グループメンバーへの一斉メッセージ送信",
    free: false,
    standard: "月10回",
    premium: "無制限",
  },
  {
    name: "QRコード参加",
    description: "QRコードでグループに簡単参加",
    free: false,
    standard: true,
    premium: true,
  },
  {
    name: "メッセージ履歴",
    description: "過去のメッセージの保存期間",
    free: "30日間",
    standard: "1年間",
    premium: "無制限",
  },
  {
    name: "ファイル共有",
    description: "画像・ファイルの共有機能",
    free: "5MB",
    standard: "50MB",
    premium: "200MB",
  },
  {
    name: "サポート",
    description: "カスタマーサポートの対応",
    free: "コミュニティ",
    standard: "メール",
    premium: "優先対応",
  },
  {
    name: "API アクセス",
    description: "外部システムとの連携",
    free: false,
    standard: false,
    premium: true,
  },
]);

// FAQ データ
const faqs = ref([
  {
    question: "プランの変更はいつでも可能ですか？",
    answer:
      "はい、いつでもプランの変更・キャンセルが可能です。アップグレードは即座に反映され、ダウングレードは次の請求サイクルから適用されます。",
  },
  {
    question: "支払い方法は何が利用できますか？",
    answer:
      "クレジットカード（Visa、Mastercard、American Express、JCB）をご利用いただけます。安全なStripe決済システムを使用しています。",
  },
  {
    question: "無料トライアルはありますか？",
    answer:
      "現在、無料トライアルは提供しておりませんが、FREEプランで基本機能を無料でお試しいただけます。",
  },
  {
    question: "法人での利用は可能ですか？",
    answer:
      "はい、法人でのご利用も可能です。請求書発行などの法人向けサービスについては、サポートまでお問い合わせください。",
  },
  {
    question: "データの安全性について教えてください",
    answer:
      "すべてのデータは暗号化されて保存され、定期的なバックアップを行っています。また、厳格なアクセス制御により、お客様のプライバシーを保護しています。",
  },
]);

// エラー分析関数
const analyzeError = (error: unknown): ErrorType => {
  if (!error) return ERROR_TYPES.UNKNOWN;

  // ネットワークエラーの検出
  if (error instanceof TypeError && error.message.includes("fetch")) {
    return ERROR_TYPES.NETWORK;
  }

  // APIエラーレスポンスの場合
  if (error && typeof error === "object") {
    // FetchErrorのdataプロパティをチェック
    if ("data" in error) {
      const errorData = (error as { data?: { error_type?: string } }).data;
      if (errorData?.error_type === "stripe_not_configured") {
        return ERROR_TYPES.STRIPE;
      }
      if (errorData?.error_type === "invalid_plan") {
        return ERROR_TYPES.VALIDATION;
      }
    }

    // 直接のエラーオブジェクトをチェック
    if ("error_type" in error) {
      const errorObj = error as { error_type?: string };
      if (errorObj.error_type === "stripe_not_configured") {
        return ERROR_TYPES.STRIPE;
      }
      if (errorObj.error_type === "invalid_plan") {
        return ERROR_TYPES.VALIDATION;
      }
    }
  }

  if (error instanceof Error) {
    const message = error.message.toLowerCase();

    // 認証エラー
    if (
      message.includes("auth") ||
      message.includes("unauthorized") ||
      message.includes("401")
    ) {
      return ERROR_TYPES.AUTH;
    }

    // サブスクリプションエラー
    if (
      message.includes("subscription") ||
      message.includes("already active") ||
      message.includes("duplicate")
    ) {
      return ERROR_TYPES.SUBSCRIPTION;
    }

    // Stripeエラー
    if (
      message.includes("stripe") ||
      message.includes("payment") ||
      message.includes("checkout") ||
      message.includes("stripe設定が必要です") ||
      message.includes("本番環境でのみ利用可能です")
    ) {
      return ERROR_TYPES.STRIPE;
    }

    // サーバーエラー
    if (
      message.includes("500") ||
      message.includes("server") ||
      message.includes("internal")
    ) {
      return ERROR_TYPES.SERVER;
    }

    // バリデーションエラー
    if (
      message.includes("validation") ||
      message.includes("invalid") ||
      message.includes("422")
    ) {
      return ERROR_TYPES.VALIDATION;
    }

    // タイムアウトエラー
    if (message.includes("timeout") || message.includes("aborted")) {
      return ERROR_TYPES.TIMEOUT;
    }

    // ネットワークエラー
    if (
      message.includes("network") ||
      message.includes("fetch") ||
      message.includes("connection")
    ) {
      return ERROR_TYPES.NETWORK;
    }
  }

  return ERROR_TYPES.UNKNOWN;
};

// エラーハンドリング関数
const handleCheckoutError = (error: unknown, plan: string) => {
  const errorType = analyzeError(error);
  const errorConfig = ERROR_MESSAGES[errorType];

  errorState.value = {
    hasError: true,
    errorType,
    retryCount: errorState.value.retryCount + 1,
    canRetry: errorConfig.canRetry && errorState.value.retryCount < 3,
  };

  console.error(`Checkout error (${errorType}):`, error);

  // エラーログの詳細記録
  const errorDetails = {
    type: errorType,
    plan,
    retryCount: errorState.value.retryCount,
    timestamp: new Date().toISOString(),
    userAgent: navigator.userAgent,
    error: error instanceof Error ? error.message : String(error),
    loadingStage: loadingState.value.stage,
    elapsedTime: elapsedTime.value,
  };

  console.error("Detailed error info:", errorDetails);

  // ユーザーへのエラー表示
  toast.add({
    title: errorConfig.title,
    description: errorConfig.description,
    color: "error",
    timeout: errorType === ERROR_TYPES.NETWORK ? 8000 : 5000,
  });

  // 特定のエラータイプに対する追加アクション
  switch (errorType) {
    case ERROR_TYPES.AUTH:
      setTimeout(() => {
        navigateTo("/auth/login");
      }, 3000);
      break;

    case ERROR_TYPES.NETWORK:
      if (errorState.value.canRetry) {
        setTimeout(() => {
          toast.add({
            title: "自動再試行",
            description: "ネットワーク接続が回復したら自動的に再試行します",
            color: "info",
          });
        }, 2000);
      }
      break;
  }
};

// リトライ機能
const _retryCheckout = async (plan: "standard" | "premium") => {
  if (!errorState.value.canRetry) {
    toast.add({
      title: "再試行できません",
      description:
        "このエラーは再試行できません。サポートまでお問い合わせください。",
      color: "warning",
    });
    return;
  }

  toast.add({
    title: "再試行中",
    description: "決済処理を再試行しています...",
    color: "info",
  });

  // エラー状態をリセット
  errorState.value.hasError = false;

  // 少し待ってから再試行
  setTimeout(() => {
    checkout(plan);
  }, 1000);
};

// エラー状態のリセット
const resetErrorState = () => {
  errorState.value = {
    hasError: false,
    errorType: null,
    retryCount: 0,
    canRetry: false,
  };
};

// メソッド
const getCurrentPlanDisplay = () => {
  if (!authStore.user?.plan || authStore.user.plan === "free") {
    return "FREE";
  }
  return authStore.user.plan.toUpperCase();
};

const getStatusDisplay = (status: string) => {
  const statusMap: Record<string, string> = {
    active: "アクティブ",
    canceled: "キャンセル済み",
    past_due: "支払い期限超過",
    incomplete: "不完全",
    trialing: "トライアル中",
  };
  return statusMap[status] || status;
};

const isCurrentPlan = (plan: string) => {
  if (plan === "free") {
    return !authStore.user?.plan || authStore.user.plan === "free";
  }
  return authStore.user?.plan === plan;
};

const getFeatureIcon = (value: boolean | string) => {
  if (typeof value === "boolean") {
    return value ? CheckIcon : XIcon;
  } else if (typeof value === "string") {
    return () => TextIcon({ value });
  }
  return () => TextIcon({ value: value as string });
};

const getFeatureClass = (value: boolean | string) => {
  if (typeof value === "boolean") {
    return value ? "w-5 h-5 text-green-500" : "w-5 h-5 text-gray-400";
  }
  return "text-sm font-medium text-gray-900";
};

// アイコンコンポーネント
const CheckIcon = () =>
  h(
    "svg",
    {
      class: "w-5 h-5 text-green-500",
      fill: "currentColor",
      viewBox: "0 0 20 20",
    },
    [
      h("path", {
        "fill-rule": "evenodd",
        d: "M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z",
        "clip-rule": "evenodd",
      }),
    ]
  );

const XIcon = () =>
  h(
    "svg",
    {
      class: "w-5 h-5 text-gray-400",
      fill: "currentColor",
      viewBox: "0 0 20 20",
    },
    [
      h("path", {
        "fill-rule": "evenodd",
        d: "M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z",
        "clip-rule": "evenodd",
      }),
    ]
  );

const TextIcon = ({ value }: { value: string }) =>
  h(
    "span",
    {
      class: "text-sm font-medium text-gray-900",
    },
    value
  );

// 確認モーダル表示関数
const checkout = (plan: "standard" | "premium") => {
  // まず確認モーダルを表示
  confirmationState.value = {
    isVisible: true,
    selectedPlan: plan,
  };
};

// 実際の決済処理関数
const executeCheckout = async (plan: "standard" | "premium") => {
  startLoading(plan);
  resetErrorState();

  try {
    // 1. 初期化
    await setLoadingStage(LOADING_STAGES.INIT);

    // 2. 認証チェック
    await setLoadingStage(LOADING_STAGES.AUTH_CHECK);
    if (!authStore.isAuthenticated) {
      stopLoading();
      toast.add({
        title: "認証が必要です",
        description: "プランを選択するには、まずログインしてください",
        color: "warning",
      });

      await navigateTo({
        path: "/auth/login",
        query: { return_url: "/pricing" },
      });
      return;
    }

    // 3. ユーザー情報検証
    await setLoadingStage(LOADING_STAGES.USER_VALIDATION);
    if (!authStore.user) {
      try {
        await authStore.checkAuth();
        if (!authStore.user) {
          throw new Error("ユーザー情報の取得に失敗しました");
        }
      } catch (error) {
        stopLoading();
        handleCheckoutError(error, plan);
        await navigateTo("/auth/login");
        return;
      }
    }

    // 4. プラン検証
    await setLoadingStage(LOADING_STAGES.PLAN_VALIDATION);
    if (authStore.user.plan && authStore.user.plan !== "free") {
      const currentPlan = authStore.user.plan;
      const isSamePlan = currentPlan === plan;

      if (isSamePlan) {
        stopLoading();
        toast.add({
          title: "既に同じプラン",
          description: `既に${plan.toUpperCase()}プランをご利用中です`,
          color: "info",
        });
        return;
      }

      // 開発環境ではアップグレード・ダウングレード両方を許可
      const isUpgrade = currentPlan === "standard" && plan === "premium";
      const isDowngrade = currentPlan === "premium" && plan === "standard";

      if (isUpgrade) {
        toast.add({
          title: "プランアップグレード",
          description: `${currentPlan.toUpperCase()}から${plan.toUpperCase()}プランにアップグレードします`,
          color: "info",
        });
      } else if (isDowngrade) {
        toast.add({
          title: "プランダウングレード",
          description: `${currentPlan.toUpperCase()}から${plan.toUpperCase()}プランにダウングレードします（開発環境）`,
          color: "warning",
        });
      }
    }

    // 5. 決済準備
    await setLoadingStage(LOADING_STAGES.PAYMENT_PREPARATION);

    // 6. Stripeセッション作成
    await setLoadingStage(LOADING_STAGES.STRIPE_SESSION);

    // タイムアウト設定付きでAPI呼び出し
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 30000);

    const res = await api<{
      url?: string;
      status?: string;
      error_type?: string;
      message?: string;
    }>("/stripe/create-checkout-session", {
      method: "POST",
      body: { plan },
      signal: controller.signal,
    });

    clearTimeout(timeoutId);

    // エラーレスポンスの場合
    if (res.status === "error") {
      throw new Error(res.message || "決済セッションの作成に失敗しました");
    }

    if (res.url) {
      // 7. リダイレクト準備
      await setLoadingStage(LOADING_STAGES.REDIRECT_PREPARATION);

      toast.add({
        title: "決済ページに移動します",
        description: "数秒後に決済ページに移動します",
        color: "success",
      });

      // 8. リダイレクト実行
      await setLoadingStage(LOADING_STAGES.REDIRECTING);

      // プラン情報をURLパラメータとして追加
      const urlWithParams = new URL(res.url);
      urlWithParams.searchParams.set("plan", plan);

      setTimeout(() => {
        window.location.href = urlWithParams.toString();
      }, 2000);
    } else {
      throw new Error("決済URLが取得できませんでした");
    }
  } catch (error) {
    stopLoading();
    handleCheckoutError(error, plan);
  }
};

// ページ読み込み時に認証状態をチェック
onMounted(async () => {
  if (!authStore.user && authStore.isAuthenticated) {
    try {
      await authStore.checkAuth();
    } catch (error) {
      console.error("認証チェックエラー:", error);
    }
  }
});
</script>
