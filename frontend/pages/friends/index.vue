<template>
  <div class="bg-gradient-to-br min-h-full">
    <div class="relative flex antialiased text-gray-800 min-h-full">
      <div class="flex min-h-full w-full">
        <!-- メインコンテンツ (認証済みユーザーのみ) -->
        <div
          class="w-full min-h-full overflow-y-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8"
        >
          <div v-if="loading" class="flex justify-center items-center py-20">
            <div class="text-center">
              <div
                class="h-12 w-12 mx-auto border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"
              />
              <p class="mt-4 text-gray-600 font-medium">
                友達情報を読み込み中...
              </p>
            </div>
          </div>

          <template v-else>
            <div class="max-w-5xl mx-auto">
              <!-- 友達追加セクション -->
              <div
                class="bg-white rounded-xl shadow-sm p-4 sm:p-6 mb-6 sm:mb-8"
              >
                <div class="flex items-center mb-4">
                  <div class="h-8 w-8 flex items-center justify-center mr-3">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-emerald-600"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"
                      />
                    </svg>
                  </div>
                  <h2 class="text-base sm:text-lg font-semibold text-gray-900">
                    友達を追加
                  </h2>
                </div>
                <FriendSearch @friend-selected="handleFriendSelected" />
              </div>

              <!-- タブナビゲーション -->
              <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="border-b border-gray-200">
                  <nav class="flex">
                    <button
                      class="flex-1 py-3 px-2 sm:py-4 sm:px-6 text-center font-medium text-xs sm:text-sm transition-all duration-200 relative cursor-pointer"
                      :class="[
                        activeTab === 'friends'
                          ? 'text-emerald-600 bg-emerald-50 border-b-2 border-emerald-600'
                          : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50',
                      ]"
                      @click="activeTab = 'friends'"
                    >
                      <div
                        class="flex flex-col sm:flex-row items-center justify-center space-y-1 sm:space-y-0 sm:space-x-2"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 sm:h-5 sm:w-5"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"
                          />
                        </svg>
                        <span class="text-xs sm:text-sm">友達</span>
                      </div>
                    </button>

                    <button
                      class="flex-1 py-3 px-2 sm:py-4 sm:px-6 text-center font-medium text-xs sm:text-sm transition-all duration-200 relative cursor-pointer"
                      :class="[
                        activeTab === 'requests'
                          ? 'text-emerald-600 bg-emerald-50 border-b-2 border-emerald-600'
                          : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50',
                      ]"
                      @click="activeTab = 'requests'"
                    >
                      <!-- 未読友達申請バッジ -->
                      <span
                        v-if="shouldShowFriendRequestBadge"
                        class="badge-dot absolute top-1 right-1 z-10"
                      />
                      <div
                        class="flex flex-col sm:flex-row items-center justify-center space-y-1 sm:space-y-0 sm:space-x-2"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 sm:h-5 sm:w-5"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                          />
                        </svg>
                        <span class="text-xs sm:text-sm">受け取った申請</span>
                      </div>
                    </button>

                    <button
                      class="flex-1 py-3 px-2 sm:py-4 sm:px-6 text-center font-medium text-xs sm:text-sm transition-all duration-200 relative cursor-pointer"
                      :class="[
                        activeTab === 'sent'
                          ? 'text-emerald-600 bg-emerald-50 border-b-2 border-emerald-600'
                          : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50',
                      ]"
                      @click="activeTab = 'sent'"
                    >
                      <div
                        class="flex flex-col sm:flex-row items-center justify-center space-y-1 sm:space-y-0 sm:space-x-2"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 sm:h-5 sm:w-5"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"
                          />
                        </svg>
                        <span class="text-xs sm:text-sm">送信した申請</span>
                      </div>
                    </button>
                  </nav>
                </div>
              </div>

              <!-- コンテンツエリア -->
              <div class="p-4 sm:p-6 bg-white">
                <!-- 友達一覧 -->
                <div v-if="activeTab === 'friends'">
                  <div
                    v-if="friends.length === 0"
                    class="text-center py-12 sm:py-16"
                  >
                    <div
                      class="h-16 w-16 sm:h-20 sm:w-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-8 w-8 sm:h-10 sm:w-10 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                      </svg>
                    </div>
                    <p
                      class="text-lg sm:text-xl text-gray-500 font-medium mb-2"
                    >
                      まだ友達がいません
                    </p>
                    <p class="text-sm sm:text-base text-gray-400">
                      フレンドIDで友達を検索してみましょう
                    </p>
                  </div>
                  <div v-else class="grid gap-4">
                    <div
                      v-for="friend in friends"
                      :key="friend.id"
                      class="bg-gray-50 rounded-lg p-3 sm:p-4 hover:bg-gray-100 transition duration-200"
                    >
                      <div class="flex flex-row items-center justify-between">
                        <div class="flex items-center space-x-3 sm:space-x-4">
                          <div class="min-w-0 flex-1">
                            <h3
                              class="font-semibold text-gray-900 text-base sm:text-lg truncate"
                            >
                              {{ friend.name }}
                            </h3>
                          </div>
                        </div>
                        <div class="flex flex-row space-x-2">
                          <button
                            class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 py-2 sm:px-4 text-xs sm:text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition duration-200 cursor-pointer"
                            @click="startChat(friend.id)"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-4 w-4"
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
                            <span class="hidden sm:inline">チャット</span>
                          </button>
                          <button
                            class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 py-2 sm:px-4 text-xs sm:text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition duration-200 cursor-pointer"
                            @click="unfriend(friend.id)"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-4 w-4"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                d="M11 6a3 3 0 11-6 0 3 3 0 016 0zM14 17a6 6 0 00-12 0h12zM13 8a1 1 0 100 2h4a1 1 0 100-2h-4z"
                              />
                            </svg>
                            <span class="hidden sm:inline">削除</span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- 受け取った友達申請 -->
                <div v-if="activeTab === 'requests'">
                  <div
                    v-if="friendRequests.length === 0"
                    class="text-center py-12 sm:py-16"
                  >
                    <div
                      class="h-16 w-16 sm:h-20 sm:w-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-8 w-8 sm:h-10 sm:w-10 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                        />
                      </svg>
                    </div>
                    <p
                      class="text-lg sm:text-xl text-gray-500 font-medium mb-2"
                    >
                      受け取った友達申請はありません
                    </p>
                  </div>
                  <div v-else class="grid gap-4">
                    <div
                      v-for="request in friendRequests"
                      :key="request.id"
                      class="bg-gradient-to-r from-blue-50 to-emerald-50 rounded-lg p-4 sm:p-6 border border-blue-100"
                    >
                      <div
                        class="flex flex-row space-y-3 sm:flex-row sm:items-start sm:justify-between sm:space-y-0"
                      >
                        <div
                          class="flex items-center space-x-3 sm:space-x-4 flex-1"
                        >
                          <div class="flex-1 min-w-0">
                            <h3
                              class="font-semibold text-gray-900 text-base sm:text-lg truncate"
                            >
                              {{ request.user.name }}
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-500 mb-2">
                              友達申請を受けています
                            </p>
                            <p
                              v-if="request.message"
                              class="text-xs sm:text-sm text-gray-700 bg-white rounded-lg p-3 border border-gray-200"
                            >
                              {{ request.message }}
                            </p>
                          </div>
                        </div>
                        <div class="flex space-x-3 sm:ml-4">
                          <button
                            class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition duration-200"
                            @click="acceptRequest(request.user.id)"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-4 w-4"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"
                              />
                            </svg>
                            <span>承認</span>
                          </button>
                          <button
                            class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition duration-200"
                            @click="rejectRequest(request.user.id)"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-4 w-4"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                              />
                            </svg>
                            <span>拒否</span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- 送信済みの友達申請 -->
                <div v-if="activeTab === 'sent'">
                  <div
                    v-if="sentRequests.length === 0"
                    class="text-center py-12 sm:py-16"
                  >
                    <div
                      class="h-16 w-16 sm:h-20 sm:w-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-8 w-8 sm:h-10 sm:w-10 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                        />
                      </svg>
                    </div>
                    <p
                      class="text-lg sm:text-xl text-gray-500 font-medium mb-2"
                    >
                      送信した友達申請はありません
                    </p>
                  </div>
                  <div v-else class="grid gap-4">
                    <div
                      v-for="request in sentRequests"
                      :key="request.id"
                      class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-lg p-4 sm:p-6 border border-orange-100"
                    >
                      <div
                        class="flex flex-col sm:flex-row sm:items-start sm:justify-between space-y-3 sm:space-y-0"
                      >
                        <div
                          class="flex items-start space-x-3 sm:space-x-4 flex-1"
                        >
                          <div class="min-w-0 flex-1">
                            <h3
                              class="font-semibold text-gray-900 text-base sm:text-lg truncate"
                            >
                              {{ request.friend.name }}
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-500">
                              送信日: {{ formatDate(request.created_at) }}
                            </p>
                            <div class="flex items-center mt-1">
                              <div
                                class="h-2 w-2 bg-orange-400 rounded-full mr-2"
                              />
                              <span class="text-xs text-orange-600 font-medium"
                                >承認待ち</span
                              >
                            </div>
                            <!-- メッセージ表示 -->
                            <p
                              v-if="request.message"
                              class="text-xs sm:text-sm text-gray-700 bg-white rounded-lg p-3 border border-gray-200 mt-2"
                            >
                              {{ request.message }}
                            </p>
                          </div>
                        </div>
                        <button
                          class="self-start sm:self-center inline-flex items-center justify-center px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition duration-200"
                          @click="cancelRequest(request.id)"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          <span class="hidden sm:inline">キャンセル</span>
                          <span class="sm:hidden">キャンセル</span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>

        <!-- 友達追加確認モーダル -->
        <div
          v-if="showAddFriendModal"
          class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4"
        >
          <div
            class="bg-white rounded-lg max-w-md w-full overflow-hidden shadow-xl transform transition-all"
          >
            <div class="p-6">
              <p class="text-gray-600 mb-4">
                <span class="font-semibold">{{ pendingFriend?.name }}</span>
                さんに友達申請を送信しますか？
              </p>

              <!-- メッセージ入力欄 -->
              <div class="mb-6">
                <label
                  for="friendRequestMessage"
                  class="block text-sm font-medium text-gray-700 mb-2"
                >
                  メッセージ（任意）
                </label>
                <input
                  id="friendRequestMessage"
                  v-model="friendRequestMessage"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                  :class="{
                    'border-red-500 focus:ring-red-500 focus:border-red-500':
                      friendRequestMessage.length > 30,
                  }"
                  placeholder="よろしくお願いします！"
                  maxlength="35"
                />
                <div class="mt-1 flex justify-between items-center">
                  <p class="text-xs text-gray-500">
                    {{ friendRequestMessage.length }}/30文字
                  </p>
                  <p
                    v-if="friendRequestMessage.length > 30"
                    class="text-xs text-red-500 font-medium"
                  >
                    30文字以内で入力してください
                  </p>
                </div>
              </div>

              <div
                class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3"
              >
                <button
                  type="button"
                  class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200"
                  @click="
                    showAddFriendModal = false;
                    friendRequestMessage = '';
                  "
                >
                  キャンセル
                </button>
                <button
                  type="button"
                  class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                  :disabled="friendRequestMessage.length > 30"
                  @click="addFriend"
                >
                  送信する
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- 友達削除確認モーダル -->
        <div
          v-if="showUnfriendModal"
          class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4"
        >
          <div
            class="bg-white rounded-lg max-w-md w-full overflow-hidden shadow-xl transform transition-all"
          >
            <div class="p-6">
              <p class="text-gray-600 mb-6">
                <span class="font-semibold">{{
                  friends.find((f: User) => f.id === pendingUnfriendId)?.name
                }}</span>
                さんを友達から削除しますか？
              </p>
              <p class="text-gray-600 mb-6">
                削除すると、このユーザーとのチャットルームも削除されます。
              </p>
              <div
                class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3"
              >
                <button
                  type="button"
                  class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200"
                  @click="showUnfriendModal = false"
                >
                  キャンセル
                </button>
                <button
                  type="button"
                  class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                  @click="confirmUnfriend"
                >
                  削除する
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import { useApi } from "../../composables/useApi";
import { useToast } from "../../composables/useToast";
import { useRouter } from "vue-router";
import { useFriendRequests } from "../../composables/useFriendRequests";

import { formatDistanceToNow } from "date-fns";
import { ja } from "date-fns/locale";

// 認証ストア

// 型定義
interface User {
  id: number;
  name: string;
  friend_id: string;
  status?: number;
}

interface FriendRequest {
  id: number;
  user: User;
  friend: User;
  message?: string;
  created_at: string;
  status: number;
}

// APIレスポンス型定義
interface ApiResponse<T> {
  status: string;
  message?: string;
  data?: T;
}

// 特定のAPIレスポンス型
interface FriendsResponse extends ApiResponse<User[]> {
  friends?: User[]; // 後方互換性のため
}

interface RequestsResponse extends ApiResponse<FriendRequest[]> {
  received_requests?: FriendRequest[]; // 後方互換性のため
}

interface SentRequestsResponse extends ApiResponse<FriendRequest[]> {
  sent_requests?: FriendRequest[]; // 後方互換性のため
}

interface ConversationResponse extends ApiResponse<{ room_token: string }> {
  room_token?: string; // 後方互換性のため
}

// ページメタデータ
definePageMeta({
  layout: "default",
  title: "友達管理",
});

// API関連の設定
const { api } = useApi();
const toast = useToast();
const router = useRouter();
const { shouldShowBadge: shouldShowFriendRequestBadge, checkPendingRequests } =
  useFriendRequests();

// 状態管理
const loading = ref(true);
const friends = ref<User[]>([]);
const friendRequests = ref<FriendRequest[]>([]);
const sentRequests = ref<FriendRequest[]>([]);
const activeTab = ref("friends");

// モーダル関連の状態
const showAddFriendModal = ref(false);
const pendingFriend = ref<User | null>(null);
const friendRequestMessage = ref("");
const showUnfriendModal = ref(false);
const pendingUnfriendId = ref<number | null>(null);

// 初期データ読み込み
onMounted(async () => {
  await refreshData();
  // 友達申請のバッジ状態をチェック
  await checkPendingRequests();
});

// データのリフレッシュ
const refreshData = async () => {
  loading.value = true;
  try {
    const [friendsData, requestsData, sentData] = await Promise.all([
      api<FriendsResponse>("/friends"),
      api<RequestsResponse>("/friends/requests/received"),
      api<SentRequestsResponse>("/friends/requests/sent"),
    ]);

    // API レスポンスの構造に合わせて処理
    // API が返すプロパティ名を使用
    friends.value = friendsData.friends || [];
    friendRequests.value = requestsData.received_requests || [];
    sentRequests.value = sentData.sent_requests || [];
  } catch (error: unknown) {
    console.error("Error fetching friend data:", error);
    toast.add({
      title: "エラー",
      description: extractErrorMessage(error, "友達情報の取得に失敗しました"),
      color: "error",
    });
  } finally {
    loading.value = false;
  }
};

// 日付フォーマット
const formatDate = (dateString: string) => {
  try {
    const date = new Date(dateString);
    return formatDistanceToNow(date, {
      addSuffix: true,
      locale: ja,
    });
  } catch {
    return dateString;
  }
};

// 友達選択ハンドラー
const handleFriendSelected = (user: User) => {
  pendingFriend.value = user;
  showAddFriendModal.value = true;
};

// APIからのエラーメッセージを抽出するユーティリティ関数
const extractErrorMessage = (
  error: unknown,
  defaultMessage: string
): string => {
  if (
    typeof error === "object" &&
    error !== null &&
    "data" in error &&
    typeof error.data === "object" &&
    error.data !== null &&
    "message" in error.data
  ) {
    return error.data.message as string;
  } else if (error instanceof Error) {
    return error.message;
  }
  return defaultMessage;
};

// 友達追加
const addFriend = async () => {
  if (!pendingFriend.value) return;

  // メッセージの長さバリデーション
  if (friendRequestMessage.value.length > 30) {
    toast.add({
      title: "エラー",
      description: "メッセージは30文字以内で入力してください",
      color: "error",
    });
    return;
  }

  try {
    const requestBody: { user_id: number; message?: string } = {
      user_id: pendingFriend.value.id,
    };

    // メッセージが入力されている場合は追加
    if (friendRequestMessage.value.trim()) {
      requestBody.message = friendRequestMessage.value.trim();
    }

    const response = await api<ApiResponse<void>>("/friends/requests", {
      method: "POST",
      body: requestBody,
    });

    // レスポンスに含まれるメッセージをそのまま表示
    toast.add({
      title: "成功",
      description: response.message || "友達申請を送信しました",
      color: "success",
    });

    // 申請リストの更新
    await refreshData();
  } catch (error: unknown) {
    console.error("Error adding friend:", error);

    toast.add({
      title: "エラー",
      description: extractErrorMessage(error, "友達申請の送信に失敗しました"),
      color: "error",
    });
  } finally {
    showAddFriendModal.value = false;
    pendingFriend.value = null;
    friendRequestMessage.value = ""; // メッセージをクリア
  }
};

// 友達削除モーダル表示
const unfriend = (userId: number) => {
  pendingUnfriendId.value = userId;
  showUnfriendModal.value = true;
};

// 友達削除の確認と実行
const confirmUnfriend = async () => {
  if (!pendingUnfriendId.value) return;

  try {
    const response = await api<ApiResponse<void>>(`/friends/unfriend`, {
      method: "DELETE",
      body: { user_id: pendingUnfriendId.value },
    });

    toast.add({
      title: "成功",
      description: response.message || "友達を削除しました",
      color: "success",
    });

    friends.value = friends.value.filter(
      (f) => f.id !== pendingUnfriendId.value
    );
  } catch (error: unknown) {
    console.error("Error removing friend:", error);

    toast.add({
      title: "エラー",
      description: extractErrorMessage(error, "友達の削除に失敗しました"),
      color: "error",
    });
  } finally {
    showUnfriendModal.value = false;
    pendingUnfriendId.value = null;
  }
};

// 友達申請の承認
const acceptRequest = async (userId: number) => {
  try {
    const response = await api<ApiResponse<void>>(`/friends/requests/accept`, {
      method: "POST",
      body: { user_id: userId },
    });

    toast.add({
      title: "成功",
      description: response.message || "友達申請を承認しました",
      color: "success",
    });

    await refreshData();
  } catch (error: unknown) {
    console.error("Error accepting friend request:", error);

    toast.add({
      title: "エラー",
      description: extractErrorMessage(error, "友達申請の承認に失敗しました"),
      color: "error",
    });
  }
};

// 友達申請の拒否
const rejectRequest = async (userId: number) => {
  try {
    const response = await api<ApiResponse<void>>(`/friends/requests/reject`, {
      method: "POST",
      body: { user_id: userId },
    });

    toast.add({
      title: "成功",
      description: response.message || "友達申請を拒否しました",
      color: "success",
    });

    friendRequests.value = friendRequests.value.filter(
      (req) => req.user.id !== userId
    );
  } catch (error: unknown) {
    console.error("Error rejecting friend request:", error);

    toast.add({
      title: "エラー",
      description: extractErrorMessage(error, "友達申請の拒否に失敗しました"),
      color: "error",
    });
  }
};

// 送信済み友達申請のキャンセル
const cancelRequest = async (requestId: number) => {
  try {
    const response = await api<ApiResponse<void>>(
      `/friends/requests/cancel/${requestId}`,
      {
        method: "DELETE",
      }
    );

    toast.add({
      title: "成功",
      description: response.message || "友達申請をキャンセルしました",
      color: "success",
    });

    sentRequests.value = sentRequests.value.filter(
      (req) => req.id !== requestId
    );
  } catch (error: unknown) {
    console.error("Error canceling friend request:", error);

    toast.add({
      title: "エラー",
      description: extractErrorMessage(
        error,
        "友達申請のキャンセルに失敗しました"
      ),
      color: "error",
    });
  }
};

// チャット開始
const startChat = async (friendId: number) => {
  try {
    const response = await api<ConversationResponse>("/conversations", {
      method: "POST",
      body: { recipient_id: friendId },
    });

    if (response.room_token || (response.data && response.data.room_token)) {
      const roomToken = response.room_token || response.data?.room_token;
      router.push(`/chat/${roomToken}`);
    } else {
      throw new Error(
        response.message || "チャットルームのトークンが取得できませんでした"
      );
    }
  } catch (error: unknown) {
    console.error("Error starting chat:", error);

    toast.add({
      title: "エラー",
      description: extractErrorMessage(error, "チャットの開始に失敗しました"),
      color: "error",
    });
  }
};

// タブ切り替え時にページの最上部にスクロール
watch(activeTab, () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
});
</script>
