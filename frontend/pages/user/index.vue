<template>
  <div class="bg-gradient-to-br min-h-full">
    <div class="relative flex antialiased text-gray-800 min-h-full">
      <div class="flex min-h-full w-full">
        <!-- メインコンテンツ (認証済みユーザーのみ) -->
        <div class="w-full min-h-full overflow-y-auto">
          <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div v-if="isLoading" class="py-16 text-center">
              <!-- ローディングスピナー -->
              <div
                class="h-10 w-10 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
              />
              <p class="mt-4 text-gray-600 font-medium">
                ユーザー情報を読み込み中...
              </p>
            </div>

            <template v-else>
              <div class="max-w-5xl mx-auto">
                <!-- ヘッダーエリア -->
                <div class="mb-6">
                  <div>
                    <p v-if="authStore.user" class="text-lg text-gray-600 mt-1">
                      ようこそ、<span style="color: var(--primary)">{{
                        authStore.user.name
                      }}</span
                      >さん
                    </p>
                  </div>
                </div>

                <div v-if="authStore.user" class="space-y-6">
                  <!-- メニューカード -->
                  <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
                  >
                    <NuxtLink to="/friends" class="block relative">
                      <div
                        class="flex flex-row bg-white h-full shadow-md rounded-lg p-4 transition-all duration-300 ease-in-out hover:shadow-lg hover:scale-[1.02] hover:bg-gray-50/50"
                        style="border-color: var(--primary-light)"
                      >
                        <!-- 友達申請バッジ -->
                        <div
                          v-if="shouldShowFriendBadge"
                          class="badge-dot absolute -top-1 -right-1 z-10"
                        />

                        <div class="flex items-center">
                          <div
                            style="
                              background-color: white;
                              color: var(--primary);
                              border: 1px solid var(--primary-light);
                            "
                            class="w-12 h-12 flex items-center justify-center rounded-full mr-4 flex-shrink-0"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-6 w-6"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd"
                              />
                            </svg>
                          </div>
                          <div>
                            <h3 class="font-semibold">友達を管理</h3>
                            <!-- <p class="text-sm text-gray-600">
                              友達の追加・確認ができます
                            </p> -->
                          </div>
                        </div>
                      </div>
                    </NuxtLink>

                    <NuxtLink to="/chat" class="block relative">
                      <div
                        class="flex flex-row bg-white rounded-lg h-full shadow-md p-4 transition-all duration-300 ease-in-out hover:shadow-lg hover:scale-[1.02] hover:bg-gray-50/50"
                        style="border-color: var(--primary-light)"
                      >
                        <!-- 未読メッセージバッジ -->
                        <div
                          v-if="shouldShowBadge"
                          class="badge-dot absolute -top-1 -right-1 z-10"
                        />

                        <div class="flex items-center">
                          <div
                            style="
                              background-color: white;
                              color: var(--primary);
                              border: 1px solid var(--primary-light);
                            "
                            class="w-12 h-12 flex items-center justify-center rounded-full mr-4 flex-shrink-0"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-6 w-6"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"
                              />
                              <path
                                d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"
                              />
                            </svg>
                          </div>
                          <div>
                            <h3 class="font-semibold">チャット</h3>
                            <!-- <p class="text-sm text-gray-600">
                              友達とメッセージを交換できます
                            </p> -->
                          </div>
                        </div>
                      </div>
                    </NuxtLink>

                    <!-- グループ機能カード（課金者専用） -->
                    <NuxtLink
                      v-if="
                        authStore.user.plan && authStore.user.plan !== 'free'
                      "
                      to="/user/groups"
                      class="block relative"
                    >
                      <!-- グループ未読メッセージバッジ -->
                      <div
                        v-if="shouldShowGroupBadge"
                        class="badge-dot absolute -top-1 -right-1 z-10"
                      />
                      <div
                        class="flex flex-row bg-white rounded-lg h-full shadow-md p-4 transition-all duration-300 ease-in-out hover:shadow-lg hover:scale-[1.02] hover:bg-gray-50/50"
                        style="border-color: var(--primary-light)"
                      >
                        <div class="flex items-center">
                          <div
                            style="
                              background-color: white;
                              color: var(--primary);
                              border: 1px solid var(--primary-light);
                            "
                            class="w-12 h-12 flex items-center justify-center rounded-full mr-4 flex-shrink-0"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-6 w-6"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                              />
                            </svg>
                          </div>
                          <div>
                            <h3 class="font-semibold">グループ管理</h3>
                            <!-- <p class="text-sm text-gray-600">
                              グループチャットを管理
                            </p> -->
                            <span
                              class="inline-block text-xs px-2 py-1 mt-1"
                              :class="getPlanLabelClass(authStore.user?.plan)"
                            >
                              {{ getPlanLabelText(authStore.user?.plan) }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </NuxtLink>

                    <!-- フリープランユーザー向けアップグレード案内 -->
                    <NuxtLink v-else to="/pricing" class="block">
                      <div
                        class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-dashed border-yellow-300 rounded-lg h-full shadow-md p-4 transition-all duration-300 ease-in-out hover:shadow-lg hover:scale-[1.02] hover:from-yellow-100 hover:to-orange-100 hover:border-yellow-400"
                      >
                        <div class="flex items-center">
                          <div
                            class="w-12 h-12 flex items-center justify-center rounded-full mr-4 bg-yellow-100 text-yellow-600 flex-shrink-0"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-6 w-6"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732L14.146 12.8l-1.179 4.456a1 1 0 01-1.934 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732L9.854 7.2l1.179-4.456A1 1 0 0112 2z"
                                clip-rule="evenodd"
                              />
                            </svg>
                          </div>
                          <div>
                            <h3 class="font-semibold text-yellow-800">
                              グループチャット
                            </h3>
                            <p class="text-sm text-yellow-700">
                              グループチャットを利用するにはアップグレードが必要です
                            </p>
                          </div>
                        </div>
                      </div>
                    </NuxtLink>
                  </div>

                  <!-- サブスクリプション管理エリア（テスト用：常に表示） -->
                  <div
                    class="bg-white rounded-lg shadow-md transition-all duration-300 ease-in-out hover:shadow-lg hover:scale-[1.01] hover:bg-gray-50/50"
                  >
                    <NuxtLink to="/user/subscription" class="block p-6">
                      <div class="flex items-center">
                        <div class="flex-shrink-0">
                          <div
                            class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center"
                          >
                            <svg
                              class="w-6 h-6 text-purple-600"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                              />
                            </svg>
                          </div>
                        </div>
                        <div class="ml-4 flex-1">
                          <h3 class="text-lg font-semibold text-gray-900">
                            サブスクリプション管理
                          </h3>
                          <p class="text-sm text-gray-600">
                            現在のプラン:
                            {{ getPlanDisplayName(authStore.user.plan) }}
                          </p>
                          <p class="text-xs text-gray-500 mt-1">
                            プラン詳細・履歴・キャンセル
                          </p>
                        </div>
                        <div class="flex-shrink-0">
                          <svg
                            class="w-5 h-5 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M9 5l7 7-7 7"
                            />
                          </svg>
                        </div>
                      </div>
                    </NuxtLink>
                  </div>

                  <!-- フレンドID表示エリア（カラフルでモダンな表示） -->
                  <div
                    class="bg-white border-l-4 rounded-lg overflow-hidden shadow-md"
                    style="border-left-color: var(--primary)"
                  >
                    <div class="relative">
                      <!-- 装飾用背景要素 -->
                      <div
                        class="absolute -top-10 -right-10 w-40 h-40 rounded-full opacity-20"
                        style="background-color: var(--primary-light)"
                      />
                      <div
                        class="absolute -bottom-12 -left-12 w-32 h-32 rounded-full opacity-20"
                        style="background-color: var(--primary)"
                      />

                      <div
                        class="flex items-center justify-between relative z-10 p-6"
                      >
                        <div class="flex-1">
                          <h3
                            class="text-lg font-semibold mb-2 flex items-center"
                            style="color: var(--primary)"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-5 w-5 mr-2"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4h3a3 3 0 006 0h3a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.45 4a2.5 2.5 0 10-4.9 0h4.9zM12 9a1 1 0 100 2h3a1 1 0 100-2h-3zm-1 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z"
                                clip-rule="evenodd"
                              />
                            </svg>
                            あなたのフレンドID
                          </h3>
                          <div
                            class="bg-white border rounded-lg p-3 inline-block"
                            style="border-color: var(--primary-light)"
                          >
                            <p
                              class="text-2xl font-bold tracking-wider"
                              style="color: var(--primary-dark)"
                            >
                              {{ authStore.user.friend_id || "未設定" }}
                            </p>
                          </div>
                          <p class="text-sm text-gray-700 mt-3">
                            このIDを友達に教えると、あなたを友達に追加できます
                          </p>
                        </div>
                        <button
                          v-if="authStore.user.friend_id"
                          class="font-medium py-2 px-4 rounded-lg transition transform hover:scale-105 active:scale-95 flex items-center cursor-pointer"
                          style="
                            background-color: white;
                            color: var(--primary);
                            border: 1px solid var(--primary-light);
                          "
                          @click="copyFriendId"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 mr-2"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"
                            />
                            <path
                              d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"
                            />
                          </svg>
                          コピー
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- ユーザー情報エリア -->
                  <div class="bg-white rounded-lg shadow-md p-6">
                    <h3
                      class="text-md font-semibold text-gray-800 mb-4 flex items-center"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2"
                        style="color: var(--primary)"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      アカウント情報
                    </h3>
                    <div class="space-y-4">
                      <!-- メールアドレス表示 -->
                      <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                          <p class="text-sm text-gray-500">メールアドレス</p>
                          <!-- Google認証ユーザーの場合は変更ボタンを非表示 -->
                          <button
                            v-if="
                              !isChangingEmail &&
                              !pendingEmailChange &&
                              authStore.user.social_type !== 'google'
                            "
                            class="text-sm hover:text-opacity-80 transition-colors cursor-pointer"
                            style="color: var(--primary)"
                            @click="isChangingEmail = true"
                          >
                            変更
                          </button>
                          <!-- Google認証ユーザーには説明バッジを表示 -->
                          <span
                            v-if="authStore.user.social_type === 'google'"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-blue-800"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-3 w-3 mr-1"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                                clip-rule="evenodd"
                              />
                            </svg>
                            Google認証
                          </span>
                        </div>
                        <p class="font-medium">{{ authStore.user.email }}</p>

                        <!-- Google認証ユーザー向けの説明 -->
                        <div
                          v-if="authStore.user.social_type === 'google'"
                          class="mt-2 p-3 bg-blue-50 rounded-md border border-blue-200"
                        >
                          <div class="flex items-start">
                            <div class="flex-shrink-0">
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="text-blue-400 h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                            </div>
                            <div class="ml-3">
                              <h3 class="text-sm font-medium text-blue-800">
                                Google認証でログイン中
                              </h3>
                              <div class="mt-1 text-sm text-blue-700">
                                <p>
                                  メールアドレスはGoogleアカウントと同期されています。<br />
                                  変更はGoogleアカウント設定から行ってください。
                                </p>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- メールアドレス変更中の状態表示 (通常ユーザーのみ) -->
                        <div
                          v-if="
                            pendingEmailChange &&
                            authStore.user.social_type !== 'google'
                          "
                          class="mt-2 p-3 bg-blue-50 rounded-md border border-blue-200"
                        >
                          <div class="flex items-start">
                            <div class="flex-shrink-0">
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="text-blue-400 h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                            </div>
                            <div class="ml-3">
                              <h3 class="text-sm font-medium text-blue-800">
                                メールアドレス変更中
                              </h3>
                              <div class="mt-1 text-sm text-blue-700">
                                <p>
                                  {{
                                    newEmail
                                  }}に確認メールを送信しました。<br />メール内のリンクをクリックすると変更が完了します。
                                </p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- メールアドレス変更フォーム (通常ユーザーのみ) -->
                      <div
                        v-if="
                          isChangingEmail &&
                          authStore.user.social_type !== 'google'
                        "
                        class="mt-4 p-4 bg-white rounded-lg border border-gray-200"
                      >
                        <h3 class="text-lg font-medium text-gray-900 mb-3">
                          メールアドレス変更
                        </h3>
                        <form @submit.prevent="changeEmail">
                          <div class="mb-4">
                            <label
                              for="new_email"
                              class="block text-sm font-medium text-gray-700 mb-1"
                              >新しいメールアドレス</label
                            >
                            <input
                              id="new_email"
                              v-model="newEmail"
                              type="email"
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              placeholder="新しいメールアドレスを入力"
                            />
                            <p class="mt-1 text-sm text-gray-500">
                              確認のため新しいメールアドレスに認証メールが送信されます。
                            </p>
                          </div>
                          <div class="flex justify-end space-x-3">
                            <button
                              type="button"
                              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-md"
                              @click="isChangingEmail = false"
                            >
                              キャンセル
                            </button>
                            <button
                              type="submit"
                              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-transparent rounded-md"
                              :disabled="emailChangeLoading"
                            >
                              <span v-if="emailChangeLoading">
                                <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  class="animate-spin h-4 w-4 mr-1 inline"
                                  viewBox="0 0 20 20"
                                  fill="currentColor"
                                >
                                  <path
                                    fill-rule="evenodd"
                                    d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                    clip-rule="evenodd"
                                  />
                                </svg>
                                処理中...
                              </span>
                              <span v-else>確認メールを送信</span>
                            </button>
                          </div>
                        </form>
                      </div>

                      <!-- ユーザーネーム表示・編集 -->
                      <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-1">
                          <p class="text-sm text-gray-500">ユーザーネーム</p>
                          <button
                            v-if="!isEditingName"
                            class="text-sm hover:text-opacity-80 transition-colors cursor-pointer font-medium"
                            style="color: var(--primary)"
                            @click="editName"
                          >
                            変更
                          </button>
                        </div>
                        <div v-if="!isEditingName">
                          <p class="font-medium">{{ authStore.user.name }}</p>
                        </div>
                        <div v-else class="space-y-2">
                          <input
                            v-model="editingName"
                            type="text"
                            :class="[
                              'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none sm:text-sm',
                              nameEditError
                                ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                                : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500',
                            ]"
                            placeholder="10文字以内"
                          />
                          <p
                            v-if="nameEditError"
                            class="mt-1 text-sm text-red-600"
                          >
                            {{ nameEditError }}
                          </p>
                          <div class="flex space-x-2 justify-end">
                            <button
                              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-md"
                              @click="cancelEditName"
                            >
                              キャンセル
                            </button>
                            <button
                              :disabled="
                                !editingName.trim() ||
                                editingName.trim().length > 10 ||
                                editingName.trim() === authStore.user.name
                              "
                              class="px-3 py-1 text-sm font-medium text-white rounded-md disabled:opacity-50"
                              style="background-color: var(--primary)"
                              @click="saveName"
                            >
                              保存
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- パスワード変更フォーム -->
                      <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                          <p class="text-sm text-gray-500">パスワード</p>
                          <!-- Google認証ユーザーの場合は変更ボタンを非表示 -->
                          <button
                            v-if="
                              !isChangingPassword &&
                              authStore.user.social_type !== 'google'
                            "
                            class="text-sm hover:text-opacity-80 transition-colors cursor-pointer font-medium"
                            style="color: var(--primary)"
                            @click="isChangingPassword = true"
                          >
                            変更
                          </button>
                          <!-- Google認証ユーザーには説明バッジを表示 -->
                          <span
                            v-if="authStore.user.social_type === 'google'"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-blue-800"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-3 w-3 mr-1"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                                clip-rule="evenodd"
                              />
                            </svg>
                            Google認証
                          </span>
                        </div>

                        <!-- Google認証ユーザー向けの説明 -->
                        <div
                          v-if="authStore.user.social_type === 'google'"
                          class="mt-2 p-3 bg-blue-50 rounded-md border border-blue-200"
                        >
                          <div class="flex items-start">
                            <div class="flex-shrink-0">
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="text-blue-400 h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                            </div>
                            <div class="ml-3">
                              <h3 class="text-sm font-medium text-blue-800">
                                Google認証でログイン中
                              </h3>
                              <div class="mt-1 text-sm text-blue-700">
                                <p>
                                  パスワードは不要です。Googleアカウントで安全に認証されています。<br />
                                  セキュリティ設定はGoogleアカウント設定から管理してください。
                                </p>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- パスワード変更フォーム (通常ユーザーのみ) -->
                        <div
                          v-if="
                            isChangingPassword &&
                            authStore.user.social_type !== 'google'
                          "
                          class="mt-2 space-y-4"
                        >
                          <form @submit.prevent="changePassword">
                            <div>
                              <label
                                for="current_password"
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >現在のパスワード</label
                              >
                              <input
                                id="current_password"
                                v-model="currentPassword"
                                type="password"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm mb-2"
                              />
                            </div>
                            <div>
                              <label
                                for="new_password"
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >新しいパスワード</label
                              >
                              <input
                                id="new_password"
                                v-model="newPassword"
                                type="password"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm mb-2"
                              />
                            </div>
                            <div>
                              <label
                                for="new_password_confirmation"
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >新しいパスワード（確認）</label
                              >
                              <input
                                id="new_password_confirmation"
                                v-model="newPasswordConfirmation"
                                type="password"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              />
                            </div>
                            <div class="flex justify-end space-x-3 mt-4">
                              <button
                                type="button"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-md"
                                @click="cancelChangePassword"
                              >
                                キャンセル
                              </button>
                              <button
                                type="submit"
                                class="px-4 py-2 text-sm font-medium text-white border-transparent rounded-md"
                                style="background-color: var(--primary)"
                                :disabled="passwordChangeLoading"
                              >
                                <span v-if="passwordChangeLoading">
                                  <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="animate-spin h-4 w-4 mr-1 inline"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                  >
                                    <path
                                      fill-rule="evenodd"
                                      d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                      clip-rule="evenodd"
                                    />
                                  </svg>
                                  処理中...
                                </span>
                                <span v-else>パスワードを更新</span>
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- 通知設定エリア -->
                  <div class="bg-white rounded-lg shadow-md p-6">
                    <h3
                      class="text-md font-semibold text-gray-800 mb-4 flex items-center"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2"
                        style="color: var(--primary)"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
                        />
                      </svg>
                      通知設定
                    </h3>
                    <NotificationSettings :is-dev="isDevelopment" />
                  </div>

                  <!-- ログアウトボタン -->
                  <div class="text-center pt-6 pb-8">
                    <button
                      class="bg-red-500 hover:bg-red-600 text-white rounded-md px-8 py-3 flex items-center mx-auto transition transform cursor-pointer shadow-md"
                      @click="handleLogout"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M3 3a1 1 0 011 1v12a1 1 0 01-1 1h12a1 1 0 01-1-1V4a1 1 0 011-1H3zm7 8a2 2 0 100-4 2 2 0 000 4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      ログアウト
                    </button>
                  </div>

                  <!-- アカウント削除セクション -->
                  <div
                    v-if="showDangerousSettings"
                    class="bg-red-50 border border-red-200 rounded-lg shadow-md p-6"
                  >
                    <h3
                      class="text-md font-semibold text-red-800 mb-4 flex items-center"
                    >
                      アカウント削除
                    </h3>
                    <button
                      class="bg-red-600 hover:bg-red-700 text-white rounded-md px-6 py-2 flex items-center transition transform cursor-pointer shadow-md"
                      @click="openDeleteAccountModal"
                    >
                      アカウント削除へ進む
                    </button>
                  </div>

                  <!-- 危険な設定表示ボタン -->
                  <div v-if="!showDangerousSettings" class="text-center py-4">
                    <button
                      class="text-sm text-gray-500 hover:text-gray-700 underline transition-colors"
                      @click="showDangerousSettings = true"
                    >
                      その他設定を表示
                    </button>
                  </div>

                  <!-- 危険な設定を隠すボタン -->
                  <div v-if="showDangerousSettings" class="text-center py-4">
                    <button
                      class="text-sm text-gray-500 hover:text-gray-700 underline transition-colors"
                      @click="showDangerousSettings = false"
                    >
                      その他設定を隠す
                    </button>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- アカウント削除確認モーダル -->
    <div
      v-if="showDeleteAccountModal"
      class="fixed inset-0 z-50 overflow-y-auto"
    >
      <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75" />
        </div>

        <!-- モーダルコンテンツ -->
        <div
          class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all max-w-lg w-full"
        >
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"
              >
                <svg
                  class="h-6 w-6 text-red-600"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.734-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"
                  />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3
                  id="modal-title"
                  class="text-lg leading-6 font-medium text-gray-900"
                >
                  アカウント削除の確認
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500 mb-4">
                    アカウントを削除すると：
                  </p>
                  <ul
                    class="text-sm text-gray-500 space-y-1 list-disc list-inside mb-4"
                  >
                    <li>他のユーザーから見えなくなります</li>
                    <li>
                      グループチャットでは名前の横に「（退室済み）」と表示されます
                    </li>
                    <li>
                      同じメールアドレスで再登録すればデータは復元できますが、所属していたグループへは再参加する必要があります。
                    </li>
                  </ul>

                  <!-- サブスクリプション関連の警告 -->
                  <div
                    v-if="
                      authStore.user?.subscription_status === 'active' ||
                      authStore.user?.subscription_status === 'trialing'
                    "
                    class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md"
                  >
                    <div class="flex items-start">
                      <svg
                        class="h-5 w-5 text-yellow-400 mt-0.5 mr-3 flex-shrink-0"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <div>
                        <h4 class="text-sm font-medium text-yellow-800 mb-2">
                          サブスクリプションについて
                        </h4>
                        <p class="text-sm text-yellow-700 mb-2">
                          現在アクティブなサブスクリプションがあります。アカウント削除により：
                        </p>
                        <ul
                          class="text-sm text-yellow-700 space-y-1 list-disc list-inside"
                        >
                          <li>
                            サブスクリプションは現在の請求期間終了時に自動的にキャンセルされます
                          </li>
                          <li>
                            期間終了まではサービスを継続してご利用いただけます
                          </li>
                          <li>次回以降の課金は発生しません</li>
                        </ul>
                      </div>
                    </div>
                  </div>

                  <form @submit.prevent="handleDeleteAccount">
                    <!-- Google認証ユーザー以外はパスワード入力が必要 -->
                    <div
                      v-if="authStore.user?.social_type !== 'google'"
                      class="mb-4"
                    >
                      <label
                        for="deletePassword"
                        class="block text-sm font-medium text-gray-700 mb-2"
                      >
                        ログインパスワードを入力してください
                      </label>
                      <input
                        id="deletePassword"
                        v-model="deleteAccountPassword"
                        type="password"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                        placeholder="パスワード"
                      />
                    </div>
                    <!-- Google認証ユーザー向けの説明 -->
                    <div v-else class="mb-4">
                      <div
                        class="bg-blue-50 border border-blue-200 rounded-md p-3"
                      >
                        <div class="flex items-center">
                          <svg
                            class="h-5 w-5 text-blue-400 mr-2"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          <span class="text-sm text-blue-800">
                            Google認証のため、パスワード入力は不要です
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="mb-4">
                      <label
                        for="deleteReason"
                        class="block text-sm font-medium text-gray-700 mb-2"
                      >
                        削除理由（任意）
                      </label>
                      <textarea
                        id="deleteReason"
                        v-model="deleteAccountReason"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                        placeholder="削除理由があれば入力してください（任意）"
                      />
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
              :disabled="deleteAccountLoading"
              @click="handleDeleteAccount"
            >
              <span v-if="deleteAccountLoading">
                <svg
                  class="animate-spin h-4 w-4 mr-2 inline"
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
                削除中...
              </span>
              <span v-else>アカウントを削除</span>
            </button>
            <button
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              @click="closeDeleteAccountModal"
            >
              キャンセル
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 名前変更提案モーダル -->
    <div
      v-if="showNameChangeSuggestionModal"
      class="fixed inset-0 z-50 overflow-y-auto"
    >
      <div
        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
      >
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75" />
        </div>

        <!-- モーダルコンテンツ -->
        <div
          class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10"
              >
                <svg
                  class="h-6 w-6 text-blue-600"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                  />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3
                  id="modal-title"
                  class="text-lg leading-6 font-medium text-gray-900"
                >
                  名前の変更提案
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500 mb-4">
                    以前の名前：<span class="font-medium text-gray-900">{{
                      authStore.user?.previous_name
                    }}</span>
                  </p>
                  <p class="text-sm text-gray-500 mb-4">
                    で使われていましたが「{{
                      authStore.user?.previous_name
                    }}」に名前を変更しますか？
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
              style="background-color: var(--primary)"
              :disabled="nameChangeSuggestionLoading"
              @click="handleNameChangeSuggestion('accept')"
            >
              <span v-if="nameChangeSuggestionLoading">
                <svg
                  class="animate-spin h-4 w-4 mr-2 inline"
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
                変更中...
              </span>
              <span v-else>変更する</span>
            </button>
            <button
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              @click="handleNameChangeSuggestion('decline')"
            >
              現在の名前を維持
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from "vue";
import { useAuthStore } from "../../stores/auth";
import { useRouter } from "vue-router";
import { useToast } from "../../composables/useToast";
import { useApi } from "../../composables/useApi";
import { useUnreadMessages } from "../../composables/useUnreadMessages";
import { useFriendRequests } from "../../composables/useFriendRequests";
import { useGroupUnreadMessages } from "../../composables/useGroupUnreadMessages";
import { FetchError } from "ofetch";

definePageMeta({
  layout: "default",
  title: "ユーザーページ",
});

const authStore = useAuthStore();
const toast = useToast();
const router = useRouter();
const isLoading = ref(true);
const { shouldShowBadge, checkUnreadMessages } = useUnreadMessages();
const { shouldShowBadge: shouldShowFriendBadge, checkPendingRequests } =
  useFriendRequests();
const { shouldShowGroupBadge, checkGroupUnreadMessages } =
  useGroupUnreadMessages();
const isEditingName = ref(false);
const editingName = ref("");
const nameEditError = ref("");
const { api } = useApi();
const isChangingEmail = ref(false);
const newEmail = ref("");
const emailChangeLoading = ref(false);
const pendingEmailChange = ref(false);
const isChangingPassword = ref(false);
const currentPassword = ref("");
const newPassword = ref("");
const newPasswordConfirmation = ref("");
const passwordChangeLoading = ref(false);
const isDevelopment = ref(process.env.NODE_ENV === "development");
const showDeleteAccountModal = ref(false);
const deleteAccountPassword = ref("");
const deleteAccountReason = ref("");
const deleteAccountLoading = ref(false);
const showNameChangeSuggestionModal = ref(false);
const nameChangeSuggestionLoading = ref(false);
const showDangerousSettings = ref(false);

// editingName を監視してリアルタイムバリデーション
watch(editingName, (newName) => {
  if (newName.trim().length > 10) {
    nameEditError.value = "ユーザー名は10文字以内で入力してください。";
  } else {
    nameEditError.value = ""; // 10文字以内ならエラーをクリア
  }
});

onMounted(async () => {
  try {
    // 認証状態をチェック
    await authStore.checkAuth();

    if (!authStore.isAuthenticated) {
      // 認証されていない場合はログインページにリダイレクト
      toast.add({
        title: "認証エラー",
        description: "ログインが必要です。ログインページに移動します。",
        color: "error",
      });
      return router.push("/auth/login");
    }

    // 最新のユーザー情報を強制的に取得（退会後の再登録に対応）
    try {
      const { api } = useApi();
      const userData = await api("/users/me");
      authStore.user = userData;
    } catch (error) {
      console.error("Failed to fetch fresh user data:", error);
    }

    // 名前変更提案が必要かチェック
    if (authStore.user?.should_suggest_name_change) {
      showNameChangeSuggestionModal.value = true;
    }

    // 未読メッセージのチェック
    await checkUnreadMessages();

    // 友達申請のチェック
    await checkPendingRequests();

    // グループ未読メッセージのチェック
    await checkGroupUnreadMessages();
  } catch (error) {
    console.error("Auth check error:", error);
    toast.add({
      title: "エラー",
      description: "認証情報の取得に失敗しました",
      color: "error",
    });
    // エラー時も認証ページへリダイレクト
    return router.push("/auth/login");
  } finally {
    isLoading.value = false;
  }
});

// 名前編集関連の関数
const editName = () => {
  editingName.value = authStore.user?.name || "";
  isEditingName.value = true;
};

const cancelEditName = () => {
  isEditingName.value = false;
};

const saveName = async () => {
  nameEditError.value = "";

  if (!editingName.value.trim()) {
    nameEditError.value = "ユーザー名を入力してください。";
    toast.add({
      title: "入力エラー",
      description: nameEditError.value,
      color: "error",
    });
    return;
  }

  if (editingName.value.trim().length > 10) {
    nameEditError.value = "ユーザー名は10文字以内で入力してください。";
    toast.add({
      title: "入力エラー",
      description: nameEditError.value,
      color: "error",
    });
    return;
  }

  if (editingName.value.trim() === authStore.user?.name) {
    toast.add({
      title: "変更なし",
      description: "ユーザー名が変更されていません。",
      color: "info",
    });
    isEditingName.value = false;
    return;
  }

  try {
    // APIを呼び出してユーザー名を更新 (実際のAPIエンドポイントとリクエスト形式に合わせてください)
    const _response = await api("/user/update-name", {
      // 実際のAPIパスに置き換えてください
      method: "PUT",
      body: { name: editingName.value },
    });

    // ストアのユーザー名を更新
    // authStore.user.name = editingName.value; // Piniaストアのアクション経由が望ましい
    // 例: authStore.updateUserName(editingName.value);
    // このためには、authStoreにupdateUserNameアクションを定義する必要があります。
    // 直接ストアのプロパティを更新するのは避けるべきです。
    // ここでは仮に直接更新していますが、後ほどストアの修正が必要です。
    if (authStore.user) {
      authStore.user.name = editingName.value;
    }

    toast.add({
      title: "成功",
      description: "ユーザー名を更新しました。",
      color: "success",
    });
    isEditingName.value = false;
  } catch (error) {
    console.error("ユーザー名の更新に失敗しました:", error);
    const errorMessage = "ユーザー名の更新に失敗しました。";
    // エラーレスポンスから詳細なメッセージを取得しようと試みる (任意)
    // if (error.response && error.response.data && error.response.data.message) {
    //   errorMessage = error.response.data.message;
    // }
    toast.add({
      title: "エラー",
      description: errorMessage,
      color: "error",
    });
  }
};

// フレンドIDをクリップボードにコピーする機能
const copyFriendId = () => {
  if (authStore.user?.friend_id) {
    navigator.clipboard
      .writeText(authStore.user.friend_id)
      .then(() => {
        toast.add({
          title: "コピー完了",
          description: "フレンドIDをクリップボードにコピーしました",
          color: "success",
        });
      })
      .catch((error) => {
        console.error("クリップボードへのコピーに失敗しました:", error);
        toast.add({
          title: "エラー",
          description: "フレンドIDのコピーに失敗しました",
          color: "error",
        });
      });
  }
};

// ログアウト処理
const handleLogout = async () => {
  await authStore.logout();
  toast.add({
    title: "ログアウト",
    description: "ログアウトしました",
    color: "success",
  });
  router.push("/auth/login");
};

const changeEmail = async () => {
  if (!newEmail.value.trim()) {
    toast.add({
      title: "エラー",
      description: "新しいメールアドレスを入力してください",
      color: "error",
    });
    return;
  }

  try {
    emailChangeLoading.value = true;
    await api("/user/update-email", {
      method: "PUT",
      body: { email: newEmail.value },
    });

    toast.add({
      title: "成功",
      description:
        "確認メールを送信しました。メール内のリンクをクリックして変更を完了してください。",
      color: "success",
    });
    isChangingEmail.value = false;
    pendingEmailChange.value = true;
  } catch (error) {
    console.error("メールアドレスの更新に失敗しました:", error);
    const errorMessage = "メールアドレスの更新に失敗しました。";
    toast.add({
      title: "エラー",
      description: errorMessage,
      color: "error",
    });
  } finally {
    emailChangeLoading.value = false;
  }
};

const changePassword = async () => {
  // バリデーション
  if (
    !currentPassword.value ||
    !newPassword.value ||
    !newPasswordConfirmation.value
  ) {
    toast.add({
      title: "入力エラー",
      description: "すべてのパスワードフィールドを入力してください。",
      color: "error",
    });
    return;
  }

  if (newPassword.value.length < 8) {
    // 例: 最小8文字
    toast.add({
      title: "入力エラー",
      description: "新しいパスワードは8文字以上で入力してください。",
      color: "error",
    });
    return;
  }

  if (newPassword.value !== newPasswordConfirmation.value) {
    toast.add({
      title: "入力エラー",
      description: "新しいパスワードと確認用パスワードが一致しません。",
      color: "error",
    });
    return;
  }

  try {
    passwordChangeLoading.value = true;
    await api("/user/update-password", {
      method: "PUT",
      body: {
        current_password: currentPassword.value,
        password: newPassword.value,
        password_confirmation: newPasswordConfirmation.value, // バックエンドのバリデーションで利用
      },
    });

    toast.add({
      title: "成功",
      description: "パスワードを更新しました。",
      color: "success",
    });
    isChangingPassword.value = false;
    // 成功したらフォームをクリア
    currentPassword.value = "";
    newPassword.value = "";
    newPasswordConfirmation.value = "";
  } catch (error) {
    console.error("パスワードの更新に失敗しました:", error);
    let errorMessage = "パスワードの更新に失敗しました。";
    if (error instanceof FetchError && error.data) {
      const errorData = error.data as {
        message?: string;
        errors?: Record<string, string[]>;
      };
      if (errorData.message) {
        errorMessage = errorData.message;
      } else if (errorData.errors) {
        const firstErrorKey = Object.keys(errorData.errors)[0];
        if (firstErrorKey && errorData.errors[firstErrorKey][0]) {
          errorMessage = errorData.errors[firstErrorKey][0];
        }
      }
    } else if (error instanceof Error) {
      errorMessage = error.message;
    }
    toast.add({
      title: "エラー",
      description: errorMessage,
      color: "error",
    });
  } finally {
    passwordChangeLoading.value = false;
  }
};

const cancelChangePassword = () => {
  isChangingPassword.value = false;
  // キャンセル時もフォームをクリア
  currentPassword.value = "";
  newPassword.value = "";
  newPasswordConfirmation.value = "";
};

const getPlanLabelClass = (plan: string | undefined) => {
  if (plan === "standard") {
    return "bg-green-100 text-green-800";
  } else if (plan === "premium") {
    return "bg-yellow-100 text-yellow-800";
  } else {
    return "bg-gray-100 text-gray-800";
  }
};

const getPlanLabelText = (plan: string | undefined) => {
  if (plan === "standard") {
    return "Standard";
  } else if (plan === "premium") {
    return "Premium";
  } else {
    return "Free";
  }
};

const getPlanDisplayName = (plan: string | undefined) => {
  if (plan === "standard") {
    return "STANDARD";
  } else if (plan === "premium") {
    return "PREMIUM";
  } else {
    return "FREE";
  }
};

const handleDeleteAccount = async () => {
  // Google認証ユーザー以外はパスワード確認が必要
  if (
    authStore.user?.social_type !== "google" &&
    !deleteAccountPassword.value
  ) {
    toast.add({
      title: "エラー",
      description: "パスワードを入力してください",
      color: "error",
    });
    return;
  }

  try {
    deleteAccountLoading.value = true;

    // リクエストボディを動的に構築
    const requestBody: { password?: string; reason?: string } = {};

    // Google認証ユーザー以外はパスワードを送信
    if (authStore.user?.social_type !== "google") {
      requestBody.password = deleteAccountPassword.value;
    }

    // 削除理由があれば追加
    if (deleteAccountReason.value) {
      requestBody.reason = deleteAccountReason.value;
    }

    await api("/user/delete-account", {
      method: "DELETE",
      body: requestBody,
    });

    toast.add({
      title: "成功",
      description: "アカウントを削除しました",
      color: "success",
    });
    showDeleteAccountModal.value = false;
    // アカウント削除後はサーバー側で既にトークンが削除されているため、
    // ローカルの認証状態のみをクリア
    authStore.clearAuthState();
    router.push("/auth/login");
  } catch (error) {
    console.error("アカウント削除に失敗しました:", error);
    const errorMessage = "アカウント削除に失敗しました。";
    toast.add({
      title: "エラー",
      description: errorMessage,
      color: "error",
    });
  } finally {
    deleteAccountLoading.value = false;
  }
};

const openDeleteAccountModal = () => {
  // フォームをリセット
  deleteAccountPassword.value = "";
  deleteAccountReason.value = "";
  showDeleteAccountModal.value = true;
};

const closeDeleteAccountModal = () => {
  showDeleteAccountModal.value = false;
  // フォームをリセット
  deleteAccountPassword.value = "";
  deleteAccountReason.value = "";
};

// 名前変更提案のハンドラー
const handleNameChangeSuggestion = async (action: "accept" | "decline") => {
  try {
    nameChangeSuggestionLoading.value = true;

    const response = await api("/user/handle-name-suggestion", {
      method: "POST",
      body: { action },
    });

    // ユーザー情報を更新
    if (authStore.user && response.user) {
      authStore.user.name = response.user.name;
      authStore.user.should_suggest_name_change = false;
      authStore.user.previous_name = undefined;
    }

    toast.add({
      title: "成功",
      description: response.message,
      color: "success",
    });

    showNameChangeSuggestionModal.value = false;
  } catch (error) {
    console.error("名前変更提案処理に失敗しました:", error);
    toast.add({
      title: "エラー",
      description: "名前変更提案処理に失敗しました",
      color: "error",
    });
  } finally {
    nameChangeSuggestionLoading.value = false;
  }
};
</script>
