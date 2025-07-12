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
            プライバシーポリシー
          </h1>

          <div class="space-y-8">
            <!-- 基本方針 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                基本方針
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  運営者は、個人情報の重要性を認識し、個人情報を保護することが社会的責務であると考え、個人情報に関する法令を遵守し、運営者で取り扱う個人情報の取得、利用、管理を適正に行います。
                </p>
                <p>
                  本プライバシーポリシー（以下「本ポリシー」）は、当チャットアプリケーションサービス（以下「本サービス」）における個人情報の取り扱いについて定めたものです。
                </p>
              </div>
            </section>

            <!-- 第1条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第1条（個人情報の定義）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  本ポリシーにおいて「個人情報」とは、個人情報保護法第2条第1項に定義される、生存する個人に関する情報であって、当該情報に含まれる氏名、生年月日その他の記述等により特定の個人を識別することができるもの（他の情報と容易に照合することができ、それにより特定の個人を識別することができることとなるものを含む。）、または個人識別符号が含まれるものをいいます。
                </p>
              </div>
            </section>

            <!-- 第2条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第2条（個人情報の収集方法）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>運営者は、以下の方法により個人情報を収集します：</p>
                <ul class="list-disc ml-8 space-y-1">
                  <li>ユーザー登録時の入力フォーム</li>
                  <li>Google認証による外部サービス連携</li>
                  <li>お問い合わせフォーム</li>
                  <li>
                    サービス利用時の自動取得（アクセスログ、IPアドレス等）
                  </li>
                  <li>決済処理における情報取得（Stripe決済システム経由）</li>
                  <li>Cookie及び類似技術による情報収集</li>
                </ul>
              </div>
            </section>

            <!-- 第3条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第3条（取得する個人情報の種類）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>運営者は、以下の個人情報を取得します：</p>
                <div class="space-y-4">
                  <div>
                    <h3 class="font-medium text-gray-800 mb-2">1. 基本情報</h3>
                    <ul class="list-disc ml-8 space-y-1">
                      <li>氏名・ニックネーム</li>
                      <li>メールアドレス</li>
                      <li>パスワード（暗号化されたもの）</li>
                      <li>プロフィール画像</li>
                    </ul>
                  </div>
                  <div>
                    <h3 class="font-medium text-gray-800 mb-2">
                      2. 外部サービス連携情報
                    </h3>
                    <ul class="list-disc ml-8 space-y-1">
                      <li>Google アカウント情報（Google認証利用時）</li>
                      <li>外部サービスから提供される一意識別子</li>
                    </ul>
                  </div>
                  <div>
                    <h3 class="font-medium text-gray-800 mb-2">
                      3. サービス利用情報
                    </h3>
                    <ul class="list-disc ml-8 space-y-1">
                      <li>チャットメッセージ内容</li>
                      <li>グループ参加情報</li>
                      <li>アクセスログ・利用履歴</li>
                      <li>デバイス情報（OS、ブラウザ種類等）</li>
                      <li>IPアドレス</li>
                    </ul>
                  </div>
                  <div>
                    <h3 class="font-medium text-gray-800 mb-2">4. 決済情報</h3>
                    <ul class="list-disc ml-8 space-y-1">
                      <li>決済履歴</li>
                      <li>サブスクリプション状況</li>
                      <li>
                        ※クレジットカード情報は運営者では保存せず、Stripe社にて安全に管理されます
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </section>

            <!-- 第4条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第4条（個人情報の利用目的）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>運営者は、取得した個人情報を以下の目的で利用します：</p>
                <ul class="list-disc ml-8 space-y-1">
                  <li>本サービスの提供、運営、保守</li>
                  <li>ユーザーアカウントの管理</li>
                  <li>チャット機能、グループ機能の提供</li>
                  <li>料金の請求・決済処理</li>
                  <li>ユーザーサポート・お問い合わせ対応</li>
                  <li>本サービスの改善・新機能の開発</li>
                  <li>利用規約違反の監視・対応</li>
                  <li>システムの安全性確保・不正利用の防止</li>
                  <li>法令に基づく対応</li>
                  <li>統計データの作成（個人を特定しない形での利用）</li>
                  <li>重要なお知らせ・サービス変更の通知</li>
                </ul>
              </div>
            </section>

            <!-- 第5条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第5条（個人情報の第三者提供）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  運営者は、次に掲げる場合を除いて、あらかじめユーザーの同意を得ることなく、個人情報を第三者に提供することはありません。
                </p>
                <ul class="list-disc ml-8 space-y-1">
                  <li>
                    人の生命、身体または財産の保護のために必要がある場合であって、本人の同意を得ることが困難であるとき
                  </li>
                  <li>
                    公衆衛生の向上または児童の健全な育成の推進のために特に必要がある場合であって、本人の同意を得ることが困難であるとき
                  </li>
                  <li>
                    国の機関もしくは地方公共団体またはその委託を受けた者が法令の定める事務を遂行することに対して協力する必要がある場合であって、本人の同意を得ることにより当該事務の遂行に支障を及ぼすおそれがあるとき
                  </li>
                  <li>予め次の事項を告知あるいは公表をしている場合</li>
                </ul>
                <p>
                  2.
                  前項の定めにかかわらず、次に掲げる場合は第三者には該当しないものとします。
                </p>
                <ul class="list-disc ml-8 space-y-1">
                  <li>
                    運営者が利用目的の達成に必要な範囲内において個人情報の取扱いの全部または一部を委託する場合
                  </li>
                  <li>
                    合併その他の事由による事業の承継に伴って個人情報が提供される場合
                  </li>
                </ul>
              </div>
            </section>

            <!-- 第6条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第6条（個人情報の委託）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  運営者は、個人情報の取扱いの全部または一部を第三者に委託する場合があります。この場合、以下の委託先と委託内容があります：
                </p>
                <div class="space-y-4">
                  <div>
                    <h3 class="font-medium text-gray-800 mb-2">主な委託先</h3>
                    <ul class="list-disc ml-8 space-y-1">
                      <li>Stripe, Inc.（決済処理）</li>
                      <li>Google LLC（認証サービス、クラウドサービス）</li>
                      <li>Vercel Inc. (フロントエンドホスティング)</li>
                      <li>Railway Corporation (バックエンドホスティング)</li>
                      <li>その他、サービス提供に必要な技術的パートナー</li>
                    </ul>
                  </div>
                  <div>
                    <h3 class="font-medium text-gray-800 mb-2">委託時の管理</h3>
                    <ul class="list-disc ml-8 space-y-1">
                      <li>委託先の選定時における個人情報保護レベルの確認</li>
                      <li>委託契約書における個人情報保護の取り決め</li>
                      <li>委託先における個人情報の取扱状況の定期的な確認</li>
                    </ul>
                  </div>
                </div>
              </div>
            </section>

            <!-- 第7条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第7条（個人情報の保存期間）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  運営者は、利用目的の達成に必要な期間、個人情報を保存します。具体的な保存期間は以下の通りです：
                </p>
                <ul class="list-disc ml-8 space-y-1">
                  <li>アカウント情報：退会後最大1年間</li>
                  <li>チャットメッセージ：送信後最大5年間</li>
                  <li>決済履歴：法令に基づく保存期間（最大7年間）</li>
                  <li>アクセスログ：最大1年間</li>
                  <li>お問い合わせ履歴：最大3年間</li>
                </ul>
                <p>
                  ※法令により保存期間が定められている場合は、その期間に従います。
                </p>
              </div>
            </section>

            <!-- 第8条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第8条（個人情報の開示・訂正・削除）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  ユーザーは、運営者の保有する自己の個人情報について、開示、訂正、削除、利用停止、消去および第三者提供の停止を求めることができます。
                </p>
                <p>
                  2.
                  前項の申し出をされる場合には、運営者所定の方法により本人確認を行った上で、合理的な期間内に対応いたします。
                </p>
                <p>
                  3.
                  個人情報の開示・訂正・削除等の手続きは、以下の方法で行うことができます：
                </p>
                <ul class="list-disc ml-8 space-y-1">
                  <li>アカウント設定画面からの変更・削除</li>
                  <li>お問い合わせフォームからの申請</li>
                  <li>メールでの申請</li>
                </ul>
                <p>
                  4.
                  法令に基づく保存義務がある場合など、応じられない場合があります。その場合は理由をご説明いたします。
                </p>
              </div>
            </section>

            <!-- 第9条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第9条（個人情報の安全管理）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  運営者は、個人情報の紛失、破壊、改ざん及び漏洩などのリスクに対して、以下の安全管理措置を講じています：
                </p>
                <ul class="list-disc ml-8 space-y-1">
                  <li>SSL/TLSによる通信の暗号化</li>
                  <li>
                    データベースの保存データの暗号化（at-rest）および通信の暗号化（in-transit）
                  </li>
                  <li>パスワードのハッシュ化による安全な保存</li>
                  <li>アクセス制御・認証システムの導入</li>
                  <li>脆弱性診断の定期的な実施と速やかな対策</li>
                  <li>依存関係の脆弱性監視と速やかなアップデート</li>
                  <li>バックアップシステムの構築</li>
                </ul>
              </div>
            </section>

            <!-- 第10条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第10条（Cookie及び類似技術について）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  運営者は、ユーザーの利便性向上、サービス品質向上、利用状況の分析等を目的として、Cookie及び類似技術を使用します。
                </p>
                <p>2. 使用する主なCookieは以下の通りです：</p>
                <ul class="list-disc ml-8 space-y-1">
                  <li>認証Cookie（ログイン状態の維持）</li>
                  <li>セッションCookie（サービスの正常な動作）</li>
                  <li>設定Cookie（ユーザー設定の保存）</li>
                  <li>分析Cookie（利用状況の分析）</li>
                </ul>
                <p>
                  3.
                  ユーザーは、ブラウザの設定によりCookieを無効にすることができますが、一部機能が利用できなくなる場合があります。
                </p>
              </div>
            </section>

            <!-- 第11条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第11条（プライバシーポリシーの変更）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  運営者は、個人情報の取扱いに関する運用状況を適宜見直し、継続的な改善に努めるものとし、必要に応じて本ポリシーを変更することがあります。
                </p>
                <p>
                  2.
                  本ポリシーの変更は、運営者のウェブサイトに掲載された時点で効力を生じるものとします。
                </p>
                <p>3. 重要な変更については、ユーザーに事前に通知いたします。</p>
              </div>
            </section>

            <!-- 第12条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第12条（お問い合わせ窓口）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  個人情報の取扱いに関するお問い合わせは、以下の窓口までご連絡ください：
                </p>
                <div class="bg-gray-50 p-4 rounded-lg">
                  <p class="font-medium text-gray-800">お問い合わせ窓口</p>
                  <p class="text-gray-600">
                    メールアドレス：[メールアドレス]<br />
                    受付時間：平日 10:00〜18:00（土日祝日を除く）
                  </p>
                  <p class="text-sm text-gray-500 mt-2">
                    ※お問い合わせの際は、本人確認のため、登録されたメールアドレスからご連絡ください。
                  </p>
                </div>
              </div>
            </section>

            <!-- 第13条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第13条（適用法令）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  本ポリシーは、日本の個人情報保護法その他の関連法令に基づいて策定されています。
                </p>
              </div>
            </section>

            <!-- 最終更新日 -->
            <section class="text-center pt-8 border-t border-gray-200">
              <p class="text-sm text-gray-500">制定日：2025年7月12日</p>
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
  title: "プライバシーポリシー",
  meta: [
    {
      name: "description",
      content:
        "チャットアプリケーションサービスのプライバシーポリシーです。個人情報の取得、利用、管理、保護に関する方針について記載しています。",
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
