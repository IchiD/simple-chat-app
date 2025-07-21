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
            利用規約
          </h1>

          <div class="space-y-8">
            <!-- 第1条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第1条（適用）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  本規約は、当チャットアプリケーションサービス（以下「本サービス」）の利用条件を定めるものです。
                </p>
                <p>
                  2.
                  登録ユーザーは、本サービスを利用するにあたり、本規約に同意したものとみなします。
                </p>
                <p>
                  3.
                  本規約に同意しない場合は、本サービスをご利用いただけません。
                </p>
              </div>
            </section>

            <!-- 第2条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第2条（利用登録）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  本サービスの利用を希望する者は、本規約に同意の上、運営者が定める方法により利用登録を申請するものとします。
                </p>
                <p>
                  2.
                  運営者は、利用登録の申請者に対して、運営者の基準に従って、登録の可否を決定し、これを申請者に通知するものとします。
                </p>
                <p>
                  3.
                  利用登録の申請者が、以下の事由に該当する場合、運営者は利用登録を承認しないことがあります。
                </p>
                <ul class="list-disc ml-8 space-y-1">
                  <li>利用登録の申請に際して虚偽の事項を届け出た場合</li>
                  <li>本規約に違反したことがある者からの申請である場合</li>
                  <li>
                    未成年者、成年被後見人、被保佐人又は被補助人からの申請であり、法定代理人、後見人、保佐人又は補助人の同意等を得ていない場合
                  </li>
                  <li>その他、運営者が利用登録を適当でないと判断した場合</li>
                </ul>
              </div>
            </section>

            <!-- 第3条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第3条（ユーザーIDおよびパスワードの管理）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  ユーザーは、自己の責任において、本サービスのユーザーIDおよびパスワードを管理するものとします。
                </p>
                <p>
                  2.
                  ユーザーは、いかなる場合にも、ユーザーIDおよびパスワードを第三者に譲渡または貸与することはできません。
                </p>
                <p>
                  3.
                  ユーザーIDおよびパスワードが第三者によって使用されたことによって生じた損害は、ユーザーが負担するものとします。
                </p>
              </div>
            </section>

            <!-- 第4条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第4条（料金および支払方法）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  本サービスには、無料プランと有料プラン（STANDARDプラン、PREMIUMプラン）があります。
                </p>
                <p>
                  2.
                  有料プランの料金は、本サービスのWebサイトに表示される料金表に従うものとします。
                </p>
                <p>
                  3.
                  有料プランの料金は、月額制とし、毎月の契約更新日に自動的に決済されます。
                </p>
                <p>
                  4.
                  料金の支払いは、クレジットカード決済（Stripe決済システム）により行うものとします。
                </p>
                <p>
                  5.
                  料金は、支払い時期に関わらず、返金されないものとします。ただし、運営者の責に帰すべき事由による場合は除きます。
                </p>
                <p>
                  6.
                  運営者は、料金を予告なく変更する場合があります。変更後の料金は、変更後の請求サイクルから適用されます。
                </p>
              </div>
            </section>

            <!-- 第5条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第5条（禁止事項）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  ユーザーは、本サービスの利用にあたり、以下の行為をしてはなりません。
                </p>
                <ul class="list-disc ml-8 space-y-1">
                  <li>法令または公序良俗に違反する行為</li>
                  <li>犯罪行為に関連する行為</li>
                  <li>
                    サーバーまたはネットワークの機能を破壊したり、妨害したりする行為
                  </li>
                  <li>本サービスの運営を妨害する行為</li>
                  <li>
                    他のユーザーに関する個人情報等を収集または蓄積する行為
                  </li>
                  <li>他のユーザーに成りすます行為</li>
                  <li>
                    本サービスに関連して、反社会的勢力に対して直接または間接に利益を供与する行為
                  </li>
                  <li>公序良俗に反する内容の情報を送信する行為</li>
                  <li>
                    わいせつ、児童ポルノ、児童虐待にあたる内容の情報を送信する行為
                  </li>
                  <li>
                    無限連鎖講の勧誘その他の迷惑行為にあたる内容の情報を送信する行為
                  </li>
                  <li>
                    コンピューターウイルス等の有害なコンピュータープログラムを送信する行為
                  </li>
                  <li>
                    営利、非営利を問わず、物やサービスの売買、交換（それらの宣伝、告知、勧誘を含む）を目的とする情報を送信する行為
                  </li>
                  <li>
                    他のユーザーや第三者の著作権、商標権、肖像権、その他の権利を侵害する行為
                  </li>
                  <li>他のユーザーや第三者を誹謗中傷し、名誉を傷つける行為</li>
                  <li>その他、運営者が不適切と判断する行為</li>
                </ul>
              </div>
            </section>

            <!-- 第6条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第6条（本サービスの提供の停止等）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  運営者は、以下のいずれかの事由があると判断した場合、ユーザーに事前に通知することなく本サービスの全部または一部の提供を停止または中断することができるものとします。
                </p>
                <ul class="list-disc ml-8 space-y-1">
                  <li>
                    本サービスにかかるコンピューター・システムの保守点検または更新を行う場合
                  </li>
                  <li>
                    地震、落雷、火災、停電または天災などの不可抗力により、本サービスの提供が困難となった場合
                  </li>
                  <li>
                    コンピューター・システムまたは通信回線等が事故により停止した場合
                  </li>
                  <li>その他、運営者が本サービスの提供が困難と判断した場合</li>
                </ul>
                <p>
                  2.
                  運営者は、本サービスの提供の停止または中断により、ユーザーまたは第三者が被ったいかなる不利益または損害について、理由を問わず一切の責任を負わないものとします。
                </p>
              </div>
            </section>

            <!-- 第7条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第7条（利用制限および登録抹消）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  運営者は、以下の場合には、事前の通知なく、ユーザーに対して、本サービスの全部もしくは一部の利用を制限し、またはユーザーとしての登録を抹消することができるものとします。
                </p>
                <ul class="list-disc ml-8 space-y-1">
                  <li>本規約のいずれかの条項に違反した場合</li>
                  <li>登録事項に虚偽の事実があることが判明した場合</li>
                  <li>
                    決済手段として当初登録されたクレジットカード等の有効性が確認できない場合
                  </li>
                  <li>料金等の支払債務の不履行があった場合</li>
                  <li>
                    運営者からの連絡に対し、相当の期間が経過しても返答がない場合
                  </li>
                  <li>最後の利用日から相当期間、本サービスの利用がない場合</li>
                  <li>
                    その他、運営者が本サービスの利用を適当でないと判断した場合
                  </li>
                </ul>
                <p>
                  2.
                  前項各号のいずれかに該当した場合、ユーザーは、当然に運営者に対する一切の債務について期限の利益を失い、その時点において負担する一切の債務を直ちに一括して弁済しなければなりません。
                </p>
                <p>
                  3.
                  運営者は、本条に基づき運営者が行った行為によりユーザーに生じた損害について、一切の責任を負いません。
                </p>
              </div>
            </section>

            <!-- 第8条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第8条（退会）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  ユーザーは、運営者の定める退会手続により、本サービスから退会できるものとします。
                </p>
                <p>
                  2.
                  退会にあたり、運営者に対する債務がある場合は、ユーザーは、当該債務を退会後も引き続き負担するものとします。
                </p>
                <p>
                  3.
                  退会後のユーザーの投稿データ等の取り扱いについては、運営者が別途定める方法によるものとします。
                </p>
              </div>
            </section>

            <!-- 第9条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第9条（保証の否認および免責事項）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  運営者は、本サービスに事実上または法律上の瑕疵（安全性、信頼性、正確性、完全性、有効性、特定の目的への適合性、セキュリティなどに関する欠陥、エラーやバグ、権利侵害などを含みます。）がないことを明示的にも黙示的にも保証しておりません。
                </p>
                <p>
                  2.
                  運営者は、本サービスに起因してユーザーに生じたあらゆる損害について一切の責任を負いません。
                </p>
                <p>
                  3.
                  運営者は、本サービスがユーザーの特定の目的に適合すること、期待する機能・商品的価値・正確性・有用性を有すること、継続的に利用できることを保証するものではありません。
                </p>
                <p>
                  4.
                  運営者は、本サービスの利用により、ユーザーと他のユーザーまたは第三者との間において生じた取引、連絡または紛争等について一切責任を負いません。
                </p>
              </div>
            </section>

            <!-- 第10条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第10条（サービス内容の変更等）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  運営者は、ユーザーに通知することなく、本サービスの内容を変更しまたは本サービスの提供を中止することができるものとし、これによってユーザーに生じた損害について一切の責任を負いません。
                </p>
                <p>
                  2.
                  運営者は、本サービスの提供を終了する場合、ユーザーに事前に通知するものとします。
                </p>
              </div>
            </section>

            <!-- 第11条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第11条（利用規約の変更）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  運営者は、必要と判断した場合には、ユーザーに通知することなくいつでも本規約を変更することができるものとします。
                </p>
                <p>
                  2.
                  本規約の変更後、本サービスの利用を開始した場合には、当該ユーザーは変更後の規約に同意したものとみなします。
                </p>
                <p>
                  3.
                  本規約の変更は、本サービスのウェブサイトに掲示された時点から効力を生じるものとします。
                </p>
              </div>
            </section>

            <!-- 第12条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第12条（個人情報の取扱い）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  運営者は、本サービスの利用によって取得する個人情報については、運営者「プライバシーポリシー」に従い適切に取り扱うものとします。
                </p>
                <p>
                  2.
                  運営者は、ユーザーが本サービスを利用するために運営者が取得した個人情報を、個人を特定しない形で、本サービスの改善、新サービスの開発等に利用することができるものとします。
                </p>
              </div>
            </section>

            <!-- 第13条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第13条（通知または連絡）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  1.
                  ユーザーと運営者との間の通知または連絡は、運営者の定める方法によって行うものとします。
                </p>
                <p>
                  2.
                  運営者は、ユーザーから、運営者が別途定める方式に従った変更届け出がない限り、現在登録されている連絡先が有効なものとみなして当該連絡先へ通知または連絡を行い、これらは、発信時にユーザーへ到達したものとみなします。
                </p>
              </div>
            </section>

            <!-- 第14条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第14条（権利義務の譲渡の禁止）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>
                  ユーザーは、運営者の書面による事前の承諾なく、利用契約上の地位または本規約に基づく権利もしくは義務を第三者に譲渡し、または担保に供することはできません。
                </p>
              </div>
            </section>

            <!-- 第15条 -->
            <section>
              <h2
                class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2"
              >
                第15条（準拠法・管轄裁判所）
              </h2>
              <div class="space-y-3 text-gray-700">
                <p>1. 本規約の解釈にあたっては、日本法を準拠法とします。</p>
                <p>
                  2.
                  本サービスに関して紛争が生じた場合には、札幌地方裁判所を管轄裁判所とします。
                  ただし、消費者契約法その他の法令により消費者に保障された権利を制限するものではありません。
                </p>
              </div>
            </section>

            <!-- 最終更新日 -->
            <section class="text-center pt-8 border-t border-gray-200">
              <p class="text-sm text-gray-500">制定日：2025年7月1日</p>
              <p class="text-sm text-gray-500">最終更新日：2025年7月1日</p>
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
  title: "利用規約",
  meta: [
    {
      name: "description",
      content:
        "チャットアプリケーションサービスの利用規約です。サービスの利用条件、禁止事項、料金、その他重要な事項について記載しています。",
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
