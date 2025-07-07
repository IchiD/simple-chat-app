<template>
  <div class="min-h-screen text-gray-800">
    <!-- Header -->
    <header class="fixed w-full bg-white/80 backdrop-blur-md z-50 shadow-sm">
      <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between"
      >
        <NuxtLink to="/" class="text-3xl font-['Pacifico'] text-primary"
          >logo</NuxtLink
        >
        <nav
          class="hidden md:flex items-center space-x-8"
          :class="{ hidden: !isMobileMenuOpen }"
        >
          <a
            href="#features"
            class="text-gray-700 hover:text-primary transition-colors"
            >機能</a
          >
          <a
            href="#pricing"
            class="text-gray-700 hover:text-primary transition-colors"
            >料金プラン</a
          >
          <a
            href="#faq"
            class="text-gray-700 hover:text-primary transition-colors"
            >よくある質問</a
          >
          <NuxtLink
            to="/login"
            class="text-gray-700 hover:text-primary transition-colors"
            >ログイン</NuxtLink
          >
          <NuxtLink
            to="/register"
            class="bg-primary text-white px-5 py-2 rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap"
            >無料登録</NuxtLink
          >
        </nav>
        <button
          class="md:hidden w-10 h-10 flex items-center justify-center"
          @click="toggleMobileMenu"
        >
          <i
            :class="isMobileMenuOpen ? 'ri-close-line' : 'ri-menu-line'"
            class="text-xl"
          />
        </button>
      </div>
      <!-- Mobile Menu -->
      <nav
        v-if="isMobileMenuOpen"
        class="md:hidden bg-white shadow-md p-4 space-y-4"
      >
        <a
          href="#features"
          class="block text-gray-700 hover:text-primary transition-colors"
          @click="toggleMobileMenu"
          >機能</a
        >
        <a
          href="#pricing"
          class="block text-gray-700 hover:text-primary transition-colors"
          @click="toggleMobileMenu"
          >料金プラン</a
        >
        <a
          href="#faq"
          class="block text-gray-700 hover:text-primary transition-colors"
          @click="toggleMobileMenu"
          >よくある質問</a
        >
        <NuxtLink
          to="/login"
          class="block text-gray-700 hover:text-primary transition-colors"
          @click="toggleMobileMenu"
          >ログイン</NuxtLink
        >
        <NuxtLink
          to="/register"
          class="block bg-primary text-white px-5 py-2 rounded-button hover:bg-primary/90 transition-colors text-center"
          @click="toggleMobileMenu"
          >無料登録</NuxtLink
        >
      </nav>
    </header>

    <!-- Hero Section -->
    <section class="pt-24 pb-16 md:pt-32 md:pb-24 relative overflow-hidden">
      <div class="absolute inset-0 gradient-bg" />
      <div class="absolute top-0 right-0 w-full h-full hero-bg" />
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="max-w-2xl hero-gradient rounded-xl p-8 md:p-12">
          <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
            コミュニティ運営、ぜんぶこのチャットで。
          </h1>
          <p class="text-lg md:text-xl mb-8">
            面倒な管理作業をシンプルに。ITに詳しくなくても始められます。
          </p>
          <div class="flex flex-col sm:flex-row gap-4">
            <NuxtLink
              to="/register"
              class="bg-primary text-white px-8 py-3 rounded-button text-center font-medium hover:bg-primary/90 transition-colors whitespace-nowrap"
            >
              30秒で無料登録
            </NuxtLink>
            <a
              href="#features"
              class="border border-gray-300 bg-white/80 backdrop-blur-sm px-8 py-3 rounded-button text-center font-medium hover:bg-white transition-colors whitespace-nowrap"
            >
              機能を見る
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Problem & Solution Section -->
    <section class="py-16 md:py-24 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold mb-4">
            現状の課題、解決します
          </h2>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            既存のツールでは解決できなかった、コミュニティ運営の悩みをスマートに解消します。
          </p>
        </div>
        <div class="grid md:grid-cols-2 gap-12 items-center">
          <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-xl font-bold mb-4 flex items-center">
              <span
                class="w-8 h-8 flex items-center justify-center bg-red-100 text-red-500 rounded-full mr-2"
              >
                <i class="ri-close-line" />
              </span>
              現状の課題
            </h3>
            <ul class="space-y-4">
              <li class="flex items-start">
                <span
                  class="w-6 h-6 flex items-center justify-center text-red-500 mt-0.5 mr-2"
                >
                  <i class="ri-error-warning-line" />
                </span>
                <p>
                  <strong>LINE</strong
                  >：グループが増えると管理が煩雑になり、メンバー情報の一元管理ができない
                </p>
              </li>
              <li class="flex items-start">
                <span
                  class="w-6 h-6 flex items-center justify-center text-red-500 mt-0.5 mr-2"
                >
                  <i class="ri-error-warning-line" />
                </span>
                <p>
                  <strong>Discord</strong
                  >：設定が複雑で、ITに詳しくない方には敷居が高い
                </p>
              </li>
              <li class="flex items-start">
                <span
                  class="w-6 h-6 flex items-center justify-center text-red-500 mt-0.5 mr-2"
                >
                  <i class="ri-error-warning-line" />
                </span>
                <p>
                  <strong>Slack</strong
                  >：料金が高く、コミュニティ向けの機能が不足している
                </p>
              </li>
            </ul>
          </div>
          <div class="bg-gradient-to-br from-teal-50 to-blue-50 p-6 rounded-lg">
            <h3 class="text-xl font-bold mb-4 flex items-center">
              <span
                class="w-8 h-8 flex items-center justify-center bg-green-100 text-green-500 rounded-full mr-2"
              >
                <i class="ri-check-line" />
              </span>
              私たちの解決策
            </h3>
            <ul class="space-y-4">
              <li class="flex items-start">
                <span
                  class="w-6 h-6 flex items-center justify-center text-green-500 mt-0.5 mr-2"
                >
                  <i class="ri-check-line" />
                </span>
                <p>
                  <strong>シンプルな操作</strong
                  >：ITに詳しくなくても直感的に使えるインターフェース
                </p>
              </li>
              <li class="flex items-start">
                <span
                  class="w-6 h-6 flex items-center justify-center text-green-500 mt-0.5 mr-2"
                >
                  <i class="ri-check-line" />
                </span>
                <p>
                  <strong>一元管理</strong
                  >：メンバー情報、チャット、配信がすべて一つのツールで完結
                </p>
              </li>
              <li class="flex items-start">
                <span
                  class="w-6 h-6 flex items-center justify-center text-green-500 mt-0.5 mr-2"
                >
                  <i class="ri-check-line" />
                </span>
                <p>
                  <strong>リーズナブル</strong
                  >：無料プランから始められ、成長に合わせて柔軟にアップグレード
                </p>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-16 md:py-24 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold mb-4">主な機能</h2>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            コミュニティ運営に必要な機能を、シンプルかつパワフルに提供します。
          </p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div
            class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow"
          >
            <div
              class="w-14 h-14 flex items-center justify-center bg-teal-100 text-primary rounded-full mb-4"
            >
              <i class="ri-chat-3-line text-2xl" />
            </div>
            <h3 class="text-xl font-bold mb-2">フレンド／グループチャット</h3>
            <p class="text-gray-600">
              運営者とメンバー間の双方向コミュニケーションを実現。個別対応もグループ配信も簡単に。
            </p>
          </div>
          <div
            class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow"
          >
            <div
              class="w-14 h-14 flex items-center justify-center bg-blue-100 text-blue-500 rounded-full mb-4"
            >
              <i class="ri-user-search-line text-2xl" />
            </div>
            <h3 class="text-xl font-bold mb-2">メンバー管理</h3>
            <p class="text-gray-600">
              検索・ソート・ページネーション機能で、大人数のコミュニティでも迷わず管理できます。
            </p>
          </div>
          <div
            class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow"
          >
            <div
              class="w-14 h-14 flex items-center justify-center bg-teal-100 text-primary rounded-full mb-4"
            >
              <i class="ri-broadcast-line text-2xl" />
            </div>
            <h3 class="text-xl font-bold mb-2">一括配信</h3>
            <p class="text-gray-600">
              重要なお知らせをワンクリックで全メンバーに配信。既読確認も簡単です。
            </p>
          </div>
          <div
            class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow"
          >
            <div
              class="w-14 h-14 flex items-center justify-center bg-blue-100 text-blue-500 rounded-full mb-4"
            >
              <i class="ri-shield-user-line text-2xl" />
            </div>
            <h3 class="text-xl font-bold mb-2">権限管理</h3>
            <p class="text-gray-600">
              オーナー／モデレーター権限で、安心・安全なコミュニティ運営を実現します。
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Friend Chat Section -->
    <section class="py-16 md:py-24 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold mb-4">
            無料で使える 1対1 チャット
          </h2>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            友達登録したユーザーとのコミュニケーションを、もっとスムーズに。
          </p>
        </div>
        <div class="grid md:grid-cols-2 gap-12 items-center max-w-5xl mx-auto">
          <div class="order-2 md:order-1">
            <div class="space-y-6">
              <div class="flex items-start">
                <div
                  class="w-12 h-12 flex items-center justify-center bg-teal-100 text-primary rounded-full shrink-0 mt-1"
                >
                  <i class="ri-chat-smile-2-line text-xl" />
                </div>
                <div class="ml-4">
                  <h3 class="text-xl font-bold mb-2">
                    シンプルな 1対1 チャット
                  </h3>
                  <p class="text-gray-600">
                    テキスト、画像、ファイルの送信が可能。既読確認もできるので、重要なメッセージも確実に届きます。
                  </p>
                </div>
              </div>
              <div class="flex items-start">
                <div
                  class="w-12 h-12 flex items-center justify-center bg-blue-100 text-blue-500 rounded-full shrink-0 mt-1"
                >
                  <i class="ri-notification-2-line text-xl" />
                </div>
                <div class="ml-4">
                  <h3 class="text-xl font-bold mb-2">プッシュ通知対応</h3>
                  <p class="text-gray-600">
                    新着メッセージをリアルタイムでお知らせ。大切な連絡を見逃しません。
                  </p>
                </div>
              </div>
              <div class="flex items-start">
                <div
                  class="w-12 h-12 flex items-center justify-center bg-teal-100 text-primary rounded-full shrink-0 mt-1"
                >
                  <i class="ri-history-line text-xl" />
                </div>
                <div class="ml-4">
                  <h3 class="text-xl font-bold mb-2">メッセージ履歴</h3>
                  <p class="text-gray-600">
                    過去のやり取りをいつでも確認できます。検索機能で必要な情報もすぐに見つかります。
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="order-1 md:order-2">
            <div
              class="w-full h-80 bg-gray-200 rounded-lg shadow-lg flex items-center justify-center"
            >
              <p class="text-gray-500">チャット画面プレビュー</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- How to Use Section -->
    <section class="py-16 md:py-24 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold mb-4">
            使い方はこんなにカンタン
          </h2>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            30秒でアカウントを作成し、すぐにコミュニティ運営を始められます。
          </p>
        </div>
        <div class="max-w-5xl mx-auto bg-gray-100 rounded-lg overflow-hidden">
          <div
            class="w-full h-[300px] md:h-[450px] bg-gray-200 flex items-center justify-center"
          >
            <p class="text-gray-500">アプリデモ動画エリア</p>
          </div>
        </div>
        <div class="mt-12 grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
          <div class="text-center">
            <div
              class="w-12 h-12 flex items-center justify-center bg-teal-100 text-primary rounded-full mx-auto mb-4"
            >
              <span class="font-bold">1</span>
            </div>
            <h3 class="font-bold mb-2">アカウント作成</h3>
            <p class="text-gray-600">
              メールアドレスだけでサクッと登録。すぐに使い始められます。
            </p>
          </div>
          <div class="text-center">
            <div
              class="w-12 h-12 flex items-center justify-center bg-teal-100 text-primary rounded-full mx-auto mb-4"
            >
              <span class="font-bold">2</span>
            </div>
            <h3 class="font-bold mb-2">グループ作成</h3>
            <p class="text-gray-600">
              目的に合わせたグループをかんたんに作成できます。
            </p>
          </div>
          <div class="text-center">
            <div
              class="w-12 h-12 flex items-center justify-center bg-teal-100 text-primary rounded-full mx-auto mb-4"
            >
              <span class="font-bold">3</span>
            </div>
            <h3 class="font-bold mb-2">メンバー招待</h3>
            <p class="text-gray-600">
              招待リンクを共有するだけで、メンバーを簡単に招待できます。
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-16 md:py-24 gradient-bg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold mb-4">料金プラン</h2>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            コミュニティの規模に合わせて、最適なプランをお選びいただけます。
          </p>
        </div>
        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
          <!-- FREE Plan -->
          <div
            class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden"
          >
            <div class="p-6 border-b">
              <h3 class="text-xl font-bold mb-2">FREE</h3>
              <div class="text-3xl font-bold mb-4">
                ¥0 <span class="text-sm font-normal text-gray-500">/ 月</span>
              </div>
              <p class="text-gray-600">個人利用や小規模コミュニティに最適</p>
            </div>
            <div class="p-6">
              <ul class="space-y-3">
                <li class="flex items-center">
                  <span
                    class="w-6 h-6 flex items-center justify-center text-green-500 mr-2"
                  >
                    <i class="ri-check-line" />
                  </span>
                  フレンドチャット
                </li>
                <li class="flex items-center text-gray-400">
                  <span class="w-6 h-6 flex items-center justify-center mr-2">
                    <i class="ri-close-line" />
                  </span>
                  グループチャット
                </li>
                <li class="flex items-center text-gray-400">
                  <span class="w-6 h-6 flex items-center justify-center mr-2">
                    <i class="ri-close-line" />
                  </span>
                  作成可能グループ数
                </li>
                <li class="flex items-center text-gray-400">
                  <span class="w-6 h-6 flex items-center justify-center mr-2">
                    <i class="ri-close-line" />
                  </span>
                  1グループの人数上限
                </li>
                <li class="flex items-center text-gray-400">
                  <span class="w-6 h-6 flex items-center justify-center mr-2">
                    <i class="ri-close-line" />
                  </span>
                  一括配信
                </li>
              </ul>
            </div>
          </div>

          <!-- STANDARD Plan -->
          <div
            class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border-2 border-primary relative"
          >
            <div
              class="absolute top-0 right-0 bg-primary text-white text-xs font-bold px-3 py-1 rounded-bl-lg"
            >
              人気
            </div>
            <div class="p-6 border-b">
              <h3 class="text-xl font-bold mb-2">STANDARD</h3>
              <div class="text-3xl font-bold mb-4">
                ¥480 <span class="text-sm font-normal text-gray-500">/ 月</span>
              </div>
              <p class="text-gray-600">中小規模コミュニティに最適</p>
            </div>
            <div class="p-6">
              <ul class="space-y-3">
                <li class="flex items-center">
                  <span
                    class="w-6 h-6 flex items-center justify-center text-green-500 mr-2"
                  >
                    <i class="ri-check-line" />
                  </span>
                  フレンドチャット
                </li>
                <li class="flex items-center">
                  <span
                    class="w-6 h-6 flex items-center justify-center text-green-500 mr-2"
                  >
                    <i class="ri-check-line" />
                  </span>
                  グループチャット
                </li>
                <li class="flex items-center">
                  <span
                    class="w-6 h-6 flex items-center justify-center text-green-500 mr-2"
                  >
                    <i class="ri-check-line" />
                  </span>
                  作成可能グループ数：3
                </li>
                <li class="flex items-center">
                  <span
                    class="w-6 h-6 flex items-center justify-center text-green-500 mr-2"
                  >
                    <i class="ri-check-line" />
                  </span>
                  1グループの人数上限：50人
                </li>
                <li class="flex items-center">
                  <span
                    class="w-6 h-6 flex items-center justify-center text-green-500 mr-2"
                  >
                    <i class="ri-check-line" />
                  </span>
                  一括配信：月10回
                </li>
              </ul>
            </div>
          </div>

          <!-- PREMIUM Plan -->
          <div
            class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden"
          >
            <div class="p-6 border-b">
              <h3 class="text-xl font-bold mb-2">PREMIUM</h3>
              <div class="text-3xl font-bold mb-4">
                ¥980 <span class="text-sm font-normal text-gray-500">/ 月</span>
              </div>
              <p class="text-gray-600">大規模コミュニティに最適</p>
            </div>
            <div class="p-6">
              <ul class="space-y-3">
                <li class="flex items-center">
                  <span
                    class="w-6 h-6 flex items-center justify-center text-green-500 mr-2"
                  >
                    <i class="ri-check-line" />
                  </span>
                  フレンドチャット
                </li>
                <li class="flex items-center">
                  <span
                    class="w-6 h-6 flex items-center justify-center text-green-500 mr-2"
                  >
                    <i class="ri-check-line" />
                  </span>
                  グループチャット
                </li>
                <li class="flex items-center">
                  <span
                    class="w-6 h-6 flex items-center justify-center text-green-500 mr-2"
                  >
                    <i class="ri-check-line" />
                  </span>
                  作成可能グループ数：無制限
                </li>
                <li class="flex items-center">
                  <span
                    class="w-6 h-6 flex items-center justify-center text-green-500 mr-2"
                  >
                    <i class="ri-check-line" />
                  </span>
                  1グループの人数上限：200人
                </li>
                <li class="flex items-center">
                  <span
                    class="w-6 h-6 flex items-center justify-center text-green-500 mr-2"
                  >
                    <i class="ri-check-line" />
                  </span>
                  一括配信：無制限
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 md:py-24 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold mb-4">ユーザーの声</h2>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            実際に使っている方々からの評価をご紹介します。
          </p>
        </div>
        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
          <div class="bg-gray-50 p-6 rounded-lg">
            <div class="flex items-center mb-4">
              <div
                class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center mr-3"
              >
                <i class="ri-user-line text-primary text-xl" />
              </div>
              <div>
                <h4 class="font-bold">佐藤 健太</h4>
                <p class="text-sm text-gray-500">
                  オンラインフィットネスコミュニティ運営
                </p>
              </div>
            </div>
            <p class="text-gray-700">
              「以前はLINEでコミュニティを運営していましたが、メンバーが増えるにつれて管理が大変になりました。このアプリに切り替えてからは、メンバー管理が格段に楽になり、一括配信機能で効率的に情報共有ができています。」
            </p>
            <div class="mt-4 flex">
              <i class="ri-star-fill text-yellow-400" />
              <i class="ri-star-fill text-yellow-400" />
              <i class="ri-star-fill text-yellow-400" />
              <i class="ri-star-fill text-yellow-400" />
              <i class="ri-star-fill text-yellow-400" />
            </div>
          </div>
          <div class="bg-gray-50 p-6 rounded-lg">
            <div class="flex items-center mb-4">
              <div
                class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3"
              >
                <i class="ri-user-line text-blue-500 text-xl" />
              </div>
              <div>
                <h4 class="font-bold">田中 美咲</h4>
                <p class="text-sm text-gray-500">料理教室主宰</p>
              </div>
            </div>
            <p class="text-gray-700">
              「ITに詳しくない私でも簡単に使えるのが魅力です。生徒さんとのやり取りがスムーズになり、レシピの共有も一括でできるようになりました。無料プランから始めて、今はSTANDARDプランを利用しています。」
            </p>
            <div class="mt-4 flex">
              <i class="ri-star-fill text-yellow-400" />
              <i class="ri-star-fill text-yellow-400" />
              <i class="ri-star-fill text-yellow-400" />
              <i class="ri-star-fill text-yellow-400" />
              <i class="ri-star-half-fill text-yellow-400" />
            </div>
          </div>
          <div class="bg-gray-50 p-6 rounded-lg">
            <div class="flex items-center mb-4">
              <div
                class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center mr-3"
              >
                <i class="ri-user-line text-primary text-xl" />
              </div>
              <div>
                <h4 class="font-bold">山田 誠司</h4>
                <p class="text-sm text-gray-500">投資サロン運営</p>
              </div>
            </div>
            <p class="text-gray-700">
              「PREMIUMプランを利用しています。200人以上のメンバーがいるサロンですが、権限管理機能のおかげでモデレーターと分担して効率的に運営できています。一括配信の既読確認も便利で、重要な情報が確実に届いているか確認できます。」
            </p>
            <div class="mt-4 flex">
              <i class="ri-star-fill text-yellow-400" />
              <i class="ri-star-fill text-yellow-400" />
              <i class="ri-star-fill text-yellow-400" />
              <i class="ri-star-fill text-yellow-400" />
              <i class="ri-star-fill text-yellow-400" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-16 md:py-24 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold mb-4">よくある質問</h2>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            ご不明な点がございましたら、お気軽にお問い合わせください。
          </p>
        </div>
        <div class="max-w-3xl mx-auto">
          <details
            v-for="(faq, index) in faqs"
            :key="index"
            class="faq-item mb-4 bg-white rounded-lg overflow-hidden shadow-sm"
            :open="index === 0"
          >
            <summary
              class="p-5 flex justify-between items-center cursor-pointer font-bold"
            >
              {{ faq.question }}
              <span
                class="w-6 h-6 flex items-center justify-center transition-transform"
              >
                <i class="ri-arrow-down-s-line" />
              </span>
            </summary>
            <div class="p-5 pt-0 text-gray-600">
              {{ faq.answer }}
            </div>
          </details>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 md:py-24 bg-primary text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
          今すぐコミュニティ運営を始めよう
        </h2>
        <p class="text-xl mb-8 max-w-3xl mx-auto">
          30秒で無料登録。ITに詳しくなくても、すぐに使い始められます。
        </p>
        <NuxtLink
          to="/register"
          class="inline-block bg-white text-primary px-8 py-3 rounded-button font-medium hover:bg-gray-100 transition-colors"
        >
          無料で始める
        </NuxtLink>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-300 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8">
          <div>
            <NuxtLink
              to="/"
              class="text-3xl font-['Pacifico'] text-white mb-4 block"
              >logo</NuxtLink
            >
            <p class="mb-4">
              コミュニティ運営をもっとシンプルに、もっと効果的に。
            </p>
            <div class="flex space-x-4">
              <a
                href="#"
                class="w-8 h-8 flex items-center justify-center bg-gray-700 rounded-full hover:bg-primary transition-colors"
              >
                <i class="ri-twitter-x-line" />
              </a>
              <a
                href="#"
                class="w-8 h-8 flex items-center justify-center bg-gray-700 rounded-full hover:bg-primary transition-colors"
              >
                <i class="ri-facebook-fill" />
              </a>
              <a
                href="#"
                class="w-8 h-8 flex items-center justify-center bg-gray-700 rounded-full hover:bg-primary transition-colors"
              >
                <i class="ri-instagram-line" />
              </a>
            </div>
          </div>
          <div>
            <h4 class="font-bold text-white mb-4">サービス</h4>
            <ul class="space-y-2">
              <li>
                <a href="#features" class="hover:text-white transition-colors"
                  >機能</a
                >
              </li>
              <li>
                <a href="#pricing" class="hover:text-white transition-colors"
                  >料金プラン</a
                >
              </li>
              <li>
                <a href="#" class="hover:text-white transition-colors"
                  >サポート</a
                >
              </li>
              <li>
                <a href="#" class="hover:text-white transition-colors">API</a>
              </li>
            </ul>
          </div>
          <div>
            <h4 class="font-bold text-white mb-4">会社情報</h4>
            <ul class="space-y-2">
              <li>
                <a href="#" class="hover:text-white transition-colors"
                  >会社概要</a
                >
              </li>
              <li>
                <a href="#" class="hover:text-white transition-colors"
                  >ブログ</a
                >
              </li>
              <li>
                <a href="#" class="hover:text-white transition-colors"
                  >採用情報</a
                >
              </li>
              <li>
                <a href="#" class="hover:text-white transition-colors"
                  >お問い合わせ</a
                >
              </li>
            </ul>
          </div>
          <div>
            <h4 class="font-bold text-white mb-4">法的情報</h4>
            <ul class="space-y-2">
              <li>
                <a href="#" class="hover:text-white transition-colors"
                  >利用規約</a
                >
              </li>
              <li>
                <a href="#" class="hover:text-white transition-colors"
                  >プライバシーポリシー</a
                >
              </li>
              <li>
                <NuxtLink
                  to="/legal/tokushoho"
                  class="hover:text-white transition-colors"
                  >特定商取引法に基づく表記</NuxtLink
                >
              </li>
            </ul>
          </div>
        </div>
        <div class="mt-12 pt-8 border-gray-700 text-center">
          <p>© 2025 logo. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
// Head configuration
useHead({
  title: "コミュニティ運営、ぜんぶこのチャットで。",
  meta: [
    {
      name: "description",
      content: "面倒な管理作業をシンプルに。ITに詳しくなくても始められます。",
    },
    { name: "viewport", content: "width=device-width, initial-scale=1.0" },
  ],
  link: [
    { rel: "preconnect", href: "https://fonts.googleapis.com" },
    { rel: "preconnect", href: "https://fonts.gstatic.com", crossorigin: "" },
    {
      rel: "stylesheet",
      href: "https://fonts.googleapis.com/css2?family=Pacifico&display=swap",
    },
    {
      rel: "stylesheet",
      href: "https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap",
    },
    {
      rel: "stylesheet",
      href: "https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css",
    },
  ],
});

// Reactive data
const isMobileMenuOpen = ref(false);

// FAQ data
const faqs = ref([
  {
    question: "無料プランでもずっと使えますか？",
    answer:
      "はい、無料プランに期限はありません。ずっと無料でご利用いただけます。ただし、グループチャットなどの機能は有料プランでのみご利用いただけます。",
  },
  {
    question: "プランの変更はいつでもできますか？",
    answer:
      "はい、いつでもプランの変更が可能です。アップグレードは即時反映され、ダウングレードは次の請求サイクルから適用されます。",
  },
  {
    question: "支払い方法は何がありますか？",
    answer:
      "クレジットカード（Visa、Mastercard、American Express）、PayPal、銀行振込に対応しています。",
  },
  {
    question: "データのバックアップはできますか？",
    answer:
      "はい、チャット履歴やメンバー情報のエクスポート機能を提供しています。CSVまたはJSONフォーマットでダウンロード可能です。",
  },
  {
    question: "モバイルアプリはありますか？",
    answer:
      "現在はWebアプリのみのご提供ですが、レスポンシブデザインのためスマートフォンやタブレットからも快適にご利用いただけます。専用のモバイルアプリは開発中です。",
  },
]);

// Methods
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};
</script>

<style scoped>
.gradient-bg {
  background: linear-gradient(135deg, #99f6e4 0%, #dbeafe 50%, #ffffff 100%);
}

.hero-gradient {
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.9) 0%,
    rgba(255, 255, 255, 0.8) 50%,
    rgba(255, 255, 255, 0) 100%
  );
}

.hero-bg {
  background-position: right center;
  background-repeat: no-repeat;
  background-size: 60% 100%;
}

.rounded-button {
  border-radius: 8px;
}

.faq-item[open] summary .ri-arrow-down-s-line {
  transform: rotate(180deg);
}

.faq-item summary {
  list-style: none;
}

.faq-item summary::-webkit-details-marker {
  display: none;
}

:deep(.font-\[\'Pacifico\'\]) {
  font-family: "Pacifico", cursive;
}
</style>
