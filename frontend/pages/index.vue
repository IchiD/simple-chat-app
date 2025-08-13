<template>
  <div class="min-h-screen bg-surface text-gray-900">
    <!-- Header -->
    <header
      class="landing-header sticky top-0 w-full bg-white/60 backdrop-blur-xl z-50 border-b border-white/40"
    >
      <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between"
      >
        <NuxtLink to="/" class="inline-flex items-center">
          <img src="/images/rogo.png" alt="LumoChat" class="h-12 w-auto" />
        </NuxtLink>
        <nav class="hidden md:flex items-center gap-6">
          <a
            href="#features"
            class="text-gray-700 hover:text-primary transition-colors"
            >機能</a
          >
          <a
            href="#pricing"
            class="text-gray-700 hover:text-primary transition-colors"
            >料金</a
          >
          <a
            href="#how"
            class="text-gray-700 hover:text-primary transition-colors"
            >はじめ方</a
          >
          <NuxtLink
            to="/auth/login"
            class="text-gray-700 hover:text-primary transition-colors"
            >ログイン</NuxtLink
          >
          <NuxtLink
            to="/auth/register"
            class="text-gray-700 hover:text-primary transition-colors"
            >無料で始める</NuxtLink
          >
        </nav>
        <button
          class="md:hidden w-12 h-12 flex flex-col items-center justify-center relative gap-1.5"
          @click="toggleMobileMenu"
        >
          <span class="sr-only">メニューを開閉</span>
          <div
            class="w-6 h-0.5 bg-gray-900 transition-all duration-300"
            :class="{ 'rotate-45 translate-y-2': isMobileMenuOpen }"
          />
          <div
            class="w-6 h-0.5 bg-gray-900 transition-opacity duration-300"
            :class="{ 'opacity-0': isMobileMenuOpen }"
          />
          <div
            class="w-6 h-0.5 bg-gray-900 transition-all duration-300"
            :class="{ '-rotate-45 -translate-y-2': isMobileMenuOpen }"
          />
        </button>
      </div>
      <!-- Mobile Menu -->
      <nav
        v-if="isMobileMenuOpen"
        class="md:hidden bg-white shadow-md p-4 space-y-3"
      >
        <a
          href="#features"
          class="block hover:text-primary"
          @click="toggleMobileMenu"
          >機能</a
        >
        <a
          href="#pricing"
          class="block hover:text-primary"
          @click="toggleMobileMenu"
          >料金</a
        >
        <a
          href="#how"
          class="block hover:text-primary"
          @click="toggleMobileMenu"
          >はじめ方</a
        >
        <NuxtLink
          to="/auth/login"
          class="block hover:text-primary"
          @click="toggleMobileMenu"
          >ログイン</NuxtLink
        >
        <NuxtLink
          to="/auth/register"
          class="block hover:text-primary"
          @click="toggleMobileMenu"
          >無料で始める</NuxtLink
        >
      </nav>
    </header>

    <main>
      <!-- Hero -->
      <section
        class="relative overflow-hidden min-h-[calc(100vh-4rem)] flex items-center"
      >
        <div class="absolute inset-0 z-0 pointer-events-none">
          <div class="hero-color" />
          <div class="gradient-blob blob-a" />
          <div class="gradient-blob blob-b" />
        </div>
        <div
          class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16"
        >
          <div class="mx-auto max-w-3xl text-center">
            <h1
              class="text-4xl md:text-7xl font-bold tracking-[0.02em] md:tracking-[0.03em] leading-[1.2] md:leading-[1.3]"
            >
              手軽につながる。<br />チャットで伝わる。
            </h1>
            <p class="mt-8 md:mt-10 text-lg md:text-xl text-gray-700">
              必要な機能だけをシンプルに。チャット、メンバー管理、一括配信を迷わずに。
            </p>
            <div
              class="mt-12 md:mt-16 flex flex-col sm:flex-row items-center justify-center gap-5 md:gap-6"
            >
              <NuxtLink
                to="/auth/register"
                class="px-6 py-3 rounded-button border border-gray-300 bg-white font-medium hover:bg-gray-50 transition-colors"
                >無料ではじめる</NuxtLink
              >
            </div>
          </div>
        </div>
      </section>

      <!-- Features -->
      <section id="features" class="py-16 md:py-24 scroll-mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-semibold tracking-tight">
              シンプルな機能
            </h2>
            <!-- <p class="mt-3 text-gray-600">
              わかりやすいUIと操作で、迷わずに使えます。
            </p> -->
          </div>
          <div class="grid md:grid-cols-3 gap-6 md:gap-8">
            <div class="feature-card">
              <h3 class="text-xl font-semibold mb-2">チャット</h3>
              <p class="text-gray-600">
                1対1/グループ対応。既読や通知機能で重要な連絡も確実に届けます。
              </p>
            </div>
            <div class="feature-card">
              <h3 class="text-xl font-semibold mb-2">メンバー管理</h3>
              <p class="text-gray-600">
                QRコードやリンクで招待。メンバー管理も快適に行えます。
              </p>
            </div>
            <div class="feature-card">
              <h3 class="text-xl font-semibold mb-2">一括送信</h3>
              <p class="text-gray-600">
                お知らせやメッセージをグループ内の選択した人に一括送信できます。
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- How it works -->
      <section id="how" class="py-16 md:py-24 bg-gray-50 scroll-mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-semibold tracking-tight">
              はじめ方
            </h2>
            <p class="mt-3 text-gray-600">
              登録はGoogleアカウントでOK。30秒で始められます。
            </p>
          </div>
          <div class="grid md:grid-cols-2 gap-6 md:gap-8 max-w-5xl mx-auto">
            <!-- 1対1チャット（無料） -->
            <div
              class="bg-white rounded-lg shadow-sm border border-gray-100 p-6"
            >
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold">1対1チャット</h3>
                <span
                  class="inline-flex items-center text-xs font-semibold text-emerald-700 bg-emerald-100 px-2 py-1 rounded-full"
                  >無料</span
                >
              </div>
              <div class="space-y-4">
                <div class="flex items-start">
                  <span class="step-index">1</span>
                  <div class="text-gray-700 ml-3">
                    <p class="font-bold">フレンド申請を送る</p>
                    <p class="text-gray-600 text-sm">
                      フレンドIDで友達を検索し、フレンド申請。
                    </p>
                  </div>
                </div>
                <div class="flex items-start">
                  <span class="step-index">2</span>
                  <div class="text-gray-700 ml-3">
                    <p class="font-bold">送られた申請を承認する。</p>
                    <p class="text-gray-600 text-sm">
                      フレンド申請を確認し、承認。
                    </p>
                  </div>
                </div>
                <div class="flex items-start">
                  <span class="step-index">3</span>
                  <div class="text-gray-700 ml-3">
                    <p class="font-bold">チャット開始</p>
                    <p class="text-gray-600 text-sm">
                      すぐにやり取りできます。
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- グループ加入（無料） -->
            <div
              class="bg-white rounded-lg shadow-sm border border-gray-100 p-6"
            >
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold">グループ加入</h3>
                <span
                  class="inline-flex items-center text-xs font-semibold text-emerald-700 bg-emerald-100 px-2 py-1 rounded-full"
                  >無料</span
                >
              </div>
              <div class="space-y-4">
                <div class="flex items-start">
                  <span class="step-index">1</span>
                  <div class="text-gray-700 ml-3">
                    <p class="font-bold">招待を受け取る</p>
                    <p class="text-gray-600 text-sm">
                      リンクまたはQRコードから。
                    </p>
                  </div>
                </div>
                <div class="flex items-start">
                  <span class="step-index">2</span>
                  <div class="text-gray-700 ml-3">
                    <p class="font-bold">参加する</p>
                    <p class="text-gray-600 text-sm">ワンタップで参加。</p>
                  </div>
                </div>
                <div class="flex items-start">
                  <span class="step-index">3</span>
                  <div class="text-gray-700 ml-3">
                    <p class="font-bold">チャットに参加</p>
                    <p class="text-gray-600 text-sm">
                      全体チャットとオーナーとの1対1、両方でやり取りできます。
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Pricing Plans -->
      <section id="pricing" class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-semibold tracking-tight">
              料金プラン
            </h2>
            <p class="mt-3 text-gray-600">用途に合わせて選べる3つのプラン</p>
          </div>
          <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <!-- Free Plan -->
            <div
              class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
            >
              <h3 class="text-xl font-semibold mb-2">Free</h3>
              <p class="text-3xl font-bold mb-4">
                ¥0<span class="text-base font-normal text-gray-600">/月</span>
              </p>
              <ul class="space-y-2 mb-6">
                <li class="flex items-start">
                  <svg
                    class="w-5 h-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    ></path>
                  </svg>
                  <span class="text-gray-700">1対1チャット無制限</span>
                </li>
                <li class="flex items-start">
                  <svg
                    class="w-5 h-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    ></path>
                  </svg>
                  <span class="text-gray-700">グループ参加無制限</span>
                </li>
                <li class="flex items-start">
                  <svg
                    class="w-5 h-5 text-gray-400 mr-2 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    ></path>
                  </svg>
                  <span class="text-gray-500">グループ作成不可</span>
                </li>
              </ul>
              <NuxtLink
                to="/auth/register"
                class="block w-full text-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
              >
                無料ではじめる
              </NuxtLink>
            </div>

            <!-- Standard Plan -->
            <div
              class="bg-white rounded-lg shadow-md border-2 border-primary p-6 relative"
            >
              <h3 class="text-xl font-semibold mb-2">Standard</h3>
              <p class="text-3xl font-bold mb-4">
                ¥500<span class="text-base font-normal text-gray-600">/月</span>
              </p>
              <ul class="space-y-2 mb-6">
                <li class="flex items-start">
                  <svg
                    class="w-5 h-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    ></path>
                  </svg>
                  <span class="text-gray-700">Freeプランの全機能</span>
                </li>
                <li class="flex items-start">
                  <svg
                    class="w-5 h-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    ></path>
                  </svg>
                  <span class="text-gray-700">グループ作成無制限</span>
                </li>
                <li class="flex items-start">
                  <svg
                    class="w-5 h-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    ></path>
                  </svg>
                  <span class="text-gray-700">グループ人数50人まで</span>
                </li>
              </ul>
              <NuxtLink
                to="/auth/register"
                class="block w-full text-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors"
              >
                今すぐ始める
              </NuxtLink>
            </div>

            <!-- Premium Plan -->
            <div
              class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
            >
              <h3 class="text-xl font-semibold mb-2">Premium</h3>
              <p class="text-3xl font-bold mb-4">
                ¥900<span class="text-base font-normal text-gray-600">/月</span>
              </p>
              <ul class="space-y-2 mb-6">
                <li class="flex items-start">
                  <svg
                    class="w-5 h-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    ></path>
                  </svg>
                  <span class="text-gray-700">Standardプランの全機能</span>
                </li>
                <li class="flex items-start">
                  <svg
                    class="w-5 h-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    ></path>
                  </svg>
                  <span class="text-gray-700">グループ人数200人まで</span>
                </li>
                <li class="flex items-start">
                  <svg
                    class="w-5 h-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    ></path>
                  </svg>
                  <span class="text-gray-700">優先サポート</span>
                </li>
              </ul>
            </div>
          </div>
          <div class="text-center mt-8">
            <p class="text-sm text-gray-600">
              ※ 価格は全て税込表示です<br />
              ※ いつでもプラン変更・解約が可能です
            </p>
          </div>
        </div>
      </section>

      <!-- About Service -->
      <section class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-semibold tracking-tight">
              LumoChatについて
            </h2>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-8">
            <div class="space-y-6">
              <div>
                <h3 class="text-lg font-semibold mb-2">サービス概要</h3>
                <p class="text-gray-700">
                  LumoChatは、シンプルで使いやすいオンラインチャットサービスです。
                  個人間のコミュニケーションから、グループでの情報共有まで、
                  様々なシーンでご利用いただけます。
                </p>
              </div>
              <div>
                <h3 class="text-lg font-semibold mb-2">主な機能</h3>
                <ul class="list-disc list-inside text-gray-700 space-y-1">
                  <li>チャット機能</li>
                  <li>グループ作成・管理機能（有料プラン）</li>
                  <li>メンバー招待機能（QRコード・リンク）</li>
                  <li>既読確認機能</li>
                  <li>プッシュ通知機能</li>
                  <li>メール通知機能</li>
                  <li>一括メッセージ送信機能</li>
                </ul>
              </div>
              <div>
                <h3 class="text-lg font-semibold mb-2">運営会社</h3>
                <p class="text-gray-700">
                  運営者: 市川 大詩<br />
                  お問い合わせ: lumochatapp@gmail.com
                </p>
              </div>
              <div>
                <h3 class="text-lg font-semibold mb-2">セキュリティ</h3>
                <p class="text-gray-700">
                  お客様のデータ通信はSSL/TLSで暗号化されています。（Webブラウザ
                  ↔ サーバー間、外部サービス連携、メール送信を含む）<br />
                  お客様の個人情報は適切に管理し、第三者への提供は行いません。
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA: gradient card -->
      <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="cta-card text-center">
            <h2 class="text-3xl md:text-4xl font-semibold mb-4 text-white">
              今すぐ始めよう
            </h2>
            <p class="text-white/90 mb-8">
              シンプルなチャットアプリをまずは使ってみてください。
            </p>
            <NuxtLink
              to="/auth/register"
              class="inline-block bg-white text-primary px-8 py-3 rounded-button font-medium hover:bg-gray-100 transition-colors"
              >無料ではじめる</NuxtLink
            >
          </div>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="landing-footer bg-gray-950 text-gray-300 py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col items-center space-y-6 text-sm">
          <NuxtLink to="/" class="inline-block">
            <img src="/images/rogo.png" alt="LumoChat" class="h-10 w-auto" />
          </NuxtLink>
          <div class="flex justify-center items-center space-x-6">
            <NuxtLink
              to="/legal/terms"
              class="hover:text-white transition-colors"
              >利用規約</NuxtLink
            >
            <NuxtLink
              to="/legal/privacy"
              class="hover:text-white transition-colors"
              >プライバシーポリシー</NuxtLink
            >
            <NuxtLink
              to="/legal/tokushoho"
              class="hover:text-white transition-colors"
              >特定商取引法に基づく表記</NuxtLink
            >
          </div>
          <p class="text-xs text-gray-500">
            © 2025 LumoChat. All rights reserved.
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
useHead({
  title: "コミュニティ運営を、最短距離で。",
  meta: [
    {
      name: "description",
      content:
        "チャット・メンバー管理・一括配信をシンプルに。画像や動画なしでもわかるモダンな体験。",
    },
    { name: "viewport", content: "width=device-width, initial-scale=1.0" },
  ],
});

const isMobileMenuOpen = ref(false);
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};
</script>

<style scoped>
.bg-surface {
  background: radial-gradient(
      1200px 600px at 50% -10%,
      #f0fdfa 0%,
      transparent 60%
    ),
    radial-gradient(1000px 500px at 110% 10%, #ccfbf1 0%, transparent 60%),
    #ffffff;
}

.landing-header {
  background-color: transparent !important;
  border-bottom-color: transparent !important;
}
.landing-footer {
  background-color: #0b1220 !important;
}

.rounded-button {
  border-radius: 12px !important;
}

.gradient-blob {
  position: absolute;
  filter: blur(28px);
  opacity: 0.85;
}
.blob-a {
  top: -80px;
  left: -80px;
  width: 520px;
  height: 520px;
  background: radial-gradient(
    closest-side,
    rgba(45, 212, 191, 0.55),
    transparent
  );
}
.blob-b {
  bottom: -100px;
  right: -60px;
  width: 520px;
  height: 520px;
  background: radial-gradient(
    closest-side,
    rgba(20, 184, 166, 0.5),
    transparent
  );
}

.feature-card {
  background: linear-gradient(180deg, #ffffff, #ffffff) padding-box,
    linear-gradient(180deg, rgba(16, 185, 129, 0.15), rgba(59, 130, 246, 0.15))
      border-box;
  border: 1px solid transparent;
  border-radius: 14px;
  padding: 1.25rem;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
}

.step {
  text-align: center;
}
.step-index {
  display: inline-grid;
  place-items: center;
  width: 40px;
  height: 40px;
  min-width: 40px;
  min-height: 40px;
  border-radius: 9999px;
  background: linear-gradient(135deg, #10b981 0%, #14b8a6 60%, #06b6d4 100%);
  color: #fff;
  font-weight: 700;
  margin-bottom: 0.5rem;
  flex-shrink: 0;
}

.cta-card {
  border-radius: 18px;
  padding: 2.5rem 1.5rem;
  background: linear-gradient(135deg, #14b8a6 0%, #06b6d4 100%);
  box-shadow: 0 10px 30px rgba(20, 184, 166, 0.25);
}

.hero-color {
  position: absolute;
  inset: 0;
  background: radial-gradient(
      1000px 520px at 10% 10%,
      rgba(20, 184, 166, 0.75),
      transparent 60%
    ),
    radial-gradient(
      1000px 520px at 90% 0%,
      rgba(6, 182, 212, 0.6),
      transparent 60%
    ),
    linear-gradient(
      180deg,
      rgba(236, 253, 245, 0.95),
      rgba(240, 253, 250, 0.95)
    );
}
</style>
