<template>
  <div class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div
      class="relative flex antialiased text-gray-800"
      style="height: calc(100vh - 7.5rem)"
    >
      <div class="flex h-full w-full">
        <div class="w-full overflow-y-auto">
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
              <div class="max-w-4xl mx-auto">
                <!-- ヘッダーエリア -->
                <div class="mb-6 flex justify-between items-center">
                  <div>
                    <p v-if="authStore.user" class="text-lg text-gray-600 mt-1">
                      ようこそ、<span style="color: var(--primary)">{{
                        authStore.user.name
                      }}</span
                      >さん
                    </p>
                  </div>
                  <button
                    class="bg-red-500 hover:bg-red-600 text-white rounded-md px-4 py-2 flex items-center transition transform hover:scale-105 cursor-pointer"
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
                        d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1H3zm1 2h4v10H4V5zm6 0h4v10h-4V5z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    ログアウト
                  </button>
                </div>

                <div v-if="authStore.user" class="space-y-6">
                  <!-- メニューカード -->
                  <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
                  >
                    <NuxtLink to="/friends" class="block">
                      <div
                        class="bg-white rounded-lg h-full shadow-md hover:shadow-lg hover:border border-transparent transition-all duration-300 transform hover:-translate-y-1 p-4"
                        style="border-color: var(--primary-light)"
                      >
                        <div class="flex items-center">
                          <div
                            style="
                              background-color: white;
                              color: var(--primary);
                              border: 1px solid var(--primary-light);
                            "
                            class="p-3 rounded-full mr-4"
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
                            <h3 class="font-semibold">友達を管理</h3>
                            <p class="text-sm text-gray-600">
                              友達の追加・確認ができます
                            </p>
                          </div>
                        </div>
                      </div>
                    </NuxtLink>

                    <NuxtLink to="/chat" class="block">
                      <div
                        class="bg-white rounded-lg h-full shadow-md hover:shadow-lg hover:border border-transparent transition-all duration-300 transform hover:-translate-y-1 p-4"
                        style="border-color: var(--primary-light)"
                      >
                        <div class="flex items-center">
                          <div
                            style="
                              background-color: white;
                              color: var(--primary);
                              border: 1px solid var(--primary-light);
                            "
                            class="p-3 rounded-full mr-4"
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
                            <p class="text-sm text-gray-600">
                              友達とメッセージを交換できます
                            </p>
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
                      class="block"
                    >
                      <div
                        class="bg-white rounded-lg h-full shadow-md hover:shadow-lg hover:border border-transparent transition-all duration-300 transform hover:-translate-y-1 p-4"
                        style="border-color: var(--primary-light)"
                      >
                        <div class="flex items-center">
                          <div
                            style="
                              background-color: white;
                              color: var(--primary);
                              border: 1px solid var(--primary-light);
                            "
                            class="p-3 rounded-full mr-4"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-6 w-6"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                              />
                              <path
                                fill-rule="evenodd"
                                d="M9 3a1 1 0 012 0v1.5a.5.5 0 001 0V3a2 2 0 10-4 0v1.5a.5.5 0 001 0V3z"
                                clip-rule="evenodd"
                              />
                            </svg>
                          </div>
                          <div>
                            <h3 class="font-semibold">グループ管理</h3>
                            <p class="text-sm text-gray-600">
                              1対多数のグループチャットを管理
                            </p>
                            <span
                              class="inline-block text-xs px-2 py-1 rounded-full mt-1"
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
                        class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-dashed border-yellow-300 rounded-lg h-full shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 p-4"
                      >
                        <div class="flex items-center">
                          <div
                            class="p-3 rounded-full mr-4 bg-yellow-100 text-yellow-600"
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
                              プレミアム機能
                            </h3>
                            <p class="text-sm text-yellow-700">
                              グループチャットを利用するにはアップグレードが必要です
                            </p>
                          </div>
                        </div>
                      </div>
                    </NuxtLink>
                  </div>
                  <!-- フレンドID表示エリア（カラフルでモダンな表示） -->
                  <div
                    class="bg-white border-l-4 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300"
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
                  <div
                    class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-6"
                  >
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
                  <div
                    class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-6"
                  >
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

                  <!-- アプリ使い方ガイド -->
                  <!-- <div
                    class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-6"
                  >
                    <h3
                      class="text-md font-semibold text-gray-800 mb-4 flex items-center"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2 flex-shrink-0 mt-0.5"
                        style="color: var(--primary)"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      アプリの使い方
                    </h3>
                    <ul class="space-y-3">
                      <li class="flex items-start">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 mr-2 flex-shrink-0 mt-0.5"
                          style="color: var(--primary)"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        <span
                          >フレンドIDを友達に教えて友達申請を受けることができます</span
                        >
                      </li>
                      <li class="flex items-start">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 mr-2 flex-shrink-0 mt-0.5"
                          style="color: var(--primary)"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        <span>フレンドIDを使って友達を追加できます</span>
                      </li>
                      <li class="flex items-start">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 mr-2 flex-shrink-0 mt-0.5"
                          style="color: var(--primary)"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        <span
                          >友達とトークルームでメッセージを交換できます</span
                        >
                      </li>
                    </ul>
                  </div>
                </div> -->
                </div>
              </div></template
            >
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
import { FetchError } from "ofetch";

definePageMeta({
  layout: "default",
  title: "ホーム",
});

const authStore = useAuthStore();
const toast = useToast();
const router = useRouter();
const isLoading = ref(true);
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
</script>
