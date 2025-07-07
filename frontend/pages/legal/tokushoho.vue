<template>
  <div class="min-h-screen">
    <!-- Main Content -->
    <main class="p-4">
      <div class="max-w-5xl mx-auto">
        <!-- 戻るボタン -->
        <div class="mb-6">
          <button
            class="group flex items-center justify-center w-10 h-10"
            @click="goBack"
          >
            <svg
              class="w-6 h-6 text-gray-600 group-hover:text-gray-800 transition-colors duration-200"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 19l-7-7 7-7"
              />
            </svg>
          </button>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-8">
          <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">
            特定商取引法に基づく表記
          </h1>

          <div class="space-y-8">
            <!-- 事業者情報 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                事業者情報
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">事業者名</h3>
                  <p class="text-gray-600">[個人事業者名]</p>
                </div>
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">運営責任者</h3>
                  <p class="text-gray-600">[代表者氏名]</p>
                </div>
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">所在地</h3>
                  <p class="text-gray-600">
                    [郵便番号]<br />
                    [都道府県市区町村番地]<br />
                    [建物名・部屋番号]
                  </p>
                  <p class="text-sm text-gray-500 mt-1">
                    ※個人事業者のため、詳細な住所についてはお問い合わせいただいた際に開示いたします。
                  </p>
                </div>
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">連絡先</h3>
                  <p class="text-gray-600">
                    電話番号：[電話番号]<br />
                    メールアドレス：[メールアドレス]<br />
                    受付時間：平日 10:00〜18:00
                  </p>
                  <p class="text-sm text-gray-500 mt-1">
                    ※個人事業者のため、電話番号についてはお問い合わせいただいた際に開示いたします。
                  </p>
                </div>
              </div>
            </section>

            <!-- サービス・料金情報 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                サービス・料金情報
              </h2>
              <div class="space-y-4">
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">サービス内容</h3>
                  <p class="text-gray-600">
                    チャットアプリケーションサービスの提供<br />
                    ・個人チャット機能<br />
                    ・グループチャット機能<br />
                    ・ファイル共有機能<br />
                    ・その他付随するサービス
                  </p>
                </div>
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">販売価格</h3>
                  <p class="text-gray-600">
                    ・無料プラン：0円/月<br />
                    ・プレミアムプラン：[価格]円/月（税込）<br />
                    ※料金は予告なく変更する場合があります。
                  </p>
                </div>
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">追加料金</h3>
                  <p class="text-gray-600">
                    上記料金以外に追加料金は発生いたしません。
                  </p>
                </div>
              </div>
            </section>

            <!-- 支払い・提供時期 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                支払い・提供時期
              </h2>
              <div class="space-y-4">
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">支払い方法</h3>
                  <p class="text-gray-600">
                    クレジットカード決済（Stripe決済システム使用）<br />
                    ・Visa<br />
                    ・Mastercard<br />
                    ・American Express<br />
                    ・JCB
                  </p>
                </div>
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">支払い時期</h3>
                  <p class="text-gray-600">
                    月額サブスクリプション：毎月の契約更新日に自動決済<br />
                    初回決済：サービス申込み時
                  </p>
                </div>
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">
                    サービス提供時期
                  </h3>
                  <p class="text-gray-600">決済完了後、即時サービス提供開始</p>
                </div>
              </div>
            </section>

            <!-- 解約・返金について -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                解約・返金について
              </h2>
              <div class="space-y-4">
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">解約について</h3>
                  <p class="text-gray-600">
                    ・解約はいつでも可能です<br />
                    ・解約手続きはマイページから行えます<br />
                    ・解約は次回更新日から適用されます<br />
                    ・解約後も契約期間満了まではサービスをご利用いただけます
                  </p>
                </div>
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">返金について</h3>
                  <p class="text-gray-600">
                    ・サービスの性質上、原則として返金は行いません<br />
                    ・システム障害等、当方の責に帰すべき事由による場合は個別対応いたします<br />
                    ・返金が必要な場合は、お問い合わせフォームよりご連絡ください
                  </p>
                </div>
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">
                    クーリングオフについて
                  </h3>
                  <p class="text-gray-600">
                    当サービスは通信販売に該当するため、クーリングオフの適用はありません。
                  </p>
                </div>
              </div>
            </section>

            <!-- その他の重要事項 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                その他の重要事項
              </h2>
              <div class="space-y-4">
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">
                    個人情報の取り扱い
                  </h3>
                  <p class="text-gray-600">
                    お客様の個人情報は、当サービスの提供および運営のためにのみ使用いたします。<br />
                    詳細については、プライバシーポリシーをご確認ください。
                  </p>
                </div>
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">免責事項</h3>
                  <p class="text-gray-600">
                    ・当サービスの利用により生じた損害について、当方は一切の責任を負いません<br />
                    ・サービスの中断・停止により生じた損害について、当方は責任を負いません<br />
                    ・システムメンテナンス等により、一時的にサービスを停止する場合があります
                  </p>
                </div>
                <div>
                  <h3 class="font-medium text-gray-700 mb-2">
                    準拠法・管轄裁判所
                  </h3>
                  <p class="text-gray-600">
                    本表記および当サービスに関する紛争については、日本法を準拠法とし、<br />
                    [所在地を管轄する地方裁判所]を専属的合意管轄裁判所とします。
                  </p>
                </div>
              </div>
            </section>

            <!-- 最終更新日 -->
            <section class="text-center pt-8 border-t border-gray-200">
              <p class="text-sm text-gray-500">最終更新日：2025年1月20日</p>
            </section>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
const router = useRouter();

// Head configuration
useHead({
  title: "特定商取引法に基づく表記",
  meta: [
    {
      name: "description",
      content:
        "特定商取引法に基づく表記ページです。事業者情報、サービス内容、料金、解約・返金について記載しています。",
    },
    { name: "robots", content: "index,follow" },
  ],
});

// 戻るボタンの処理
function goBack() {
  // ブラウザの履歴を使用して前のページに戻る
  // 履歴がない場合はトップページに戻る
  if (window.history.length > 1) {
    window.history.back();
  } else {
    router.push("/");
  }
}
</script>

<style scoped>
/* カスタムスタイルが必要な場合はここに追加 */
</style>
