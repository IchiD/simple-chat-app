<template>
  <div
    class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50"
  >
    <!-- ヘッダーセクション -->
    <div class="relative overflow-hidden">
      <!-- 背景装飾 -->
      <div class="absolute inset-0">
        <div
          class="absolute top-10 left-10 w-20 h-20 bg-emerald-200 rounded-full opacity-20 animate-pulse"
        />
        <div
          class="absolute top-40 right-20 w-16 h-16 bg-teal-200 rounded-full opacity-30 animate-bounce"
        />
        <div
          class="absolute bottom-20 left-20 w-12 h-12 bg-cyan-200 rounded-full opacity-25"
        />
      </div>

      <UContainer class="relative">
        <!-- メインヒーローセクション -->
        <div class="py-20 text-center">
          <div class="max-w-4xl mx-auto">
            <!-- ロゴとタイトル -->
            <div class="mb-8">
              <div
                class="inline-flex items-center justify-center w-20 h-20 bg-emerald-600 rounded-2xl mb-6 shadow-lg"
              >
                <svg
                  class="w-10 h-10 text-white"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"
                  />
                  <circle cx="9" cy="10" r="1" />
                  <circle cx="12" cy="10" r="1" />
                  <circle cx="15" cy="10" r="1" />
                </svg>
              </div>
              <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-4">
                <span class="text-emerald-600">Lumo</span>Chat
              </h1>
              <p class="text-xl md:text-2xl text-gray-600 mb-8">
                つながりを、もっと楽しく、簡単に
              </p>
            </div>

            <!-- 主要な説明 -->
            <div class="mb-12">
              <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                LumoChatは、完全無料のチャットアプリです。
              </p>

              <!-- CTAボタン -->
              <div
                class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16"
              >
                <NuxtLink
                  to="/auth/register"
                  class="inline-flex items-center px-8 py-4 text-lg font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105"
                >
                  <Icon name="i-heroicons-user-plus" class="mr-2" />
                  今すぐ始める
                </NuxtLink>
                <NuxtLink
                  to="/auth/login"
                  class="inline-flex items-center px-8 py-4 text-lg font-semibold text-emerald-600 bg-white border-2 border-emerald-600 hover:bg-emerald-50 rounded-lg transition-all duration-300"
                >
                  <Icon
                    name="i-heroicons-arrow-right-on-rectangle"
                    class="mr-2"
                  />
                  ログイン
                </NuxtLink>
              </div>
            </div>
          </div>
        </div>
      </UContainer>
    </div>

    <!-- 特徴セクション -->
    <div class="py-20 bg-white">
      <UContainer>
        <div class="max-w-6xl mx-auto">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              なぜLumoChatが選ばれるのか
            </h2>
            <p class="text-lg text-gray-600">
              多くの人に愛される理由があります
            </p>
          </div>

          <div class="grid md:grid-cols-3 gap-8">
            <!-- 特徴1 -->
            <div class="text-center group">
              <div
                class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-2xl mb-6 group-hover:bg-emerald-200 transition-colors duration-300"
              >
                <Icon
                  name="i-heroicons-cog-6-tooth"
                  class="w-8 h-8 text-emerald-600"
                />
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3">
                シンプルな機能
              </h3>
              <p class="text-gray-600 leading-relaxed">
                複雑な機能は一切なし。必要な機能だけでストレスフリーなチャット体験。
              </p>
            </div>

            <!-- 特徴2 -->
            <div class="text-center group">
              <div
                class="inline-flex items-center justify-center w-16 h-16 bg-teal-100 rounded-2xl mb-6 group-hover:bg-teal-200 transition-colors duration-300"
              >
                <Icon name="i-heroicons-gift" class="w-8 h-8 text-teal-600" />
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3">
                完全無料のチャットアプリ
              </h3>
              <p class="text-gray-600 leading-relaxed">
                登録から利用まで完全無料。隠れた費用は一切ありません。
              </p>
            </div>

            <!-- 特徴3 -->
            <div class="text-center group">
              <div
                class="inline-flex items-center justify-center w-16 h-16 bg-cyan-100 rounded-2xl mb-6 group-hover:bg-cyan-200 transition-colors duration-300"
              >
                <Icon
                  name="i-heroicons-sparkles"
                  class="w-8 h-8 text-cyan-600"
                />
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3">
                シンプル機能
              </h3>
              <p class="text-gray-600 leading-relaxed">
                わかりやすい操作で、誰でも迷わず使える設計です。
              </p>
            </div>
          </div>
        </div>
      </UContainer>
    </div>

    <!-- 機能紹介セクション -->
    <div class="py-20 bg-gray-50">
      <UContainer>
        <div class="max-w-6xl mx-auto">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              充実の機能
            </h2>
            <p class="text-lg text-gray-600">
              コミュニケーションをより豊かにする機能が満載
            </p>
          </div>

          <div class="grid md:grid-cols-2 gap-12 items-center">
            <!-- 機能リスト -->
            <div class="space-y-8">
              <div class="flex items-start space-x-4">
                <div
                  class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center"
                >
                  <Icon
                    name="i-heroicons-user-group"
                    class="w-5 h-5 text-emerald-600"
                  />
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    友達管理
                  </h3>
                  <p class="text-gray-600">
                    簡単な友達追加システムで、大切な人とつながろう
                  </p>
                </div>
              </div>

              <div class="flex items-start space-x-4">
                <div
                  class="flex-shrink-0 w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center"
                >
                  <Icon
                    name="i-heroicons-chat-bubble-oval-left-ellipsis"
                    class="w-5 h-5 text-teal-600"
                  />
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    1対1チャット
                  </h3>
                  <p class="text-gray-600">
                    プライベートな会話を楽しめる個人チャット機能
                  </p>
                </div>
              </div>

              <div class="flex items-start space-x-4">
                <div
                  class="flex-shrink-0 w-8 h-8 bg-cyan-100 rounded-lg flex items-center justify-center"
                >
                  <Icon
                    name="i-heroicons-question-mark-circle"
                    class="w-5 h-5 text-cyan-600"
                  />
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    サポート機能
                  </h3>
                  <p class="text-gray-600">
                    困ったときはいつでもサポートチームがお手伝い
                  </p>
                </div>
              </div>
            </div>

            <!-- イメージプレースホルダー -->
            <div class="relative">
              <div
                class="bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl p-8 text-white shadow-xl"
              >
                <div class="space-y-4">
                  <div class="flex items-center space-x-3">
                    <div
                      class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center"
                    >
                      <Icon name="i-heroicons-user" class="w-6 h-6" />
                    </div>
                    <div class="flex-1">
                      <div class="h-4 bg-white bg-opacity-20 rounded mb-2" />
                      <div class="h-3 bg-white bg-opacity-10 rounded w-3/4" />
                    </div>
                  </div>
                  <div class="ml-13 space-y-2">
                    <div class="bg-white bg-opacity-20 rounded-lg p-3">
                      <div class="h-3 bg-white bg-opacity-30 rounded mb-1" />
                      <div class="h-3 bg-white bg-opacity-20 rounded w-2/3" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </UContainer>
    </div>

    <!-- CTA最終セクション -->
    <div class="py-20 bg-gradient-to-r from-emerald-600 to-teal-600">
      <UContainer>
        <div class="max-w-4xl mx-auto text-center">
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            今すぐLumoChatを始めましょう
          </h2>
          <p class="text-xl text-emerald-100 mb-8">
            無料で始められます。大切な人とのつながりを深めませんか？
          </p>
          <div
            class="flex flex-col sm:flex-row gap-4 justify-center items-center"
          >
            <NuxtLink
              to="/auth/register"
              class="inline-flex items-center px-8 py-4 text-lg font-semibold text-emerald-600 bg-white hover:bg-gray-50 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105"
            >
              <Icon name="i-heroicons-sparkles" class="mr-2" />
              無料で始める
            </NuxtLink>
            <NuxtLink
              to="/auth/login"
              class="inline-flex items-center px-8 py-4 text-lg font-semibold text-white border-2 border-white hover:bg-white hover:text-emerald-600 rounded-lg transition-all duration-300"
            >
              既にアカウントをお持ちの方
            </NuxtLink>
          </div>
        </div>
      </UContainer>
    </div>

    <!-- フッター -->
    <div class="py-8 bg-gray-900 text-center">
      <UContainer>
        <p class="text-gray-400">© 2025 LumoChat. All Rights Reserved.</p>
      </UContainer>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "~/stores/auth";

definePageMeta({
  layout: false,
  title: "LumoChat - 友達とのつながりを、もっと楽しく",
  // middleware: ["redirect-if-authenticated"],
});

const _authStore = useAuthStore();
</script>

<style scoped>
/* カスタムアニメーション */
@keyframes float {
  0%,
  100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}
</style>
