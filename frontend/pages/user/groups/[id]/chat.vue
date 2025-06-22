<template>
  <div v-if="isCheckingAccess" class="p-4 text-center">
    <div
      class="h-10 w-10 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
    />
    <p class="mt-4 text-gray-600">アクセス権限を確認中...</p>
  </div>
  <div v-else class="p-4">
    <div class="max-w-4xl mx-auto">
      <!-- 戻るボタン -->
      <div class="mb-4">
        <button
          class="group flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 active:bg-gray-300 transition-all duration-200 hover:shadow-md active:scale-95"
          @click="goBack"
        >
          <svg
            class="w-5 h-5 text-gray-600 group-hover:text-gray-800 transition-colors duration-200"
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

      <!-- グループ読み込み中 -->
      <div v-if="groupPending" class="text-center py-8">
        <div
          class="h-12 w-12 mx-auto border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"
        />
        <p class="text-gray-600 font-medium">グループを読み込み中...</p>
      </div>

      <!-- グループ読み込みエラー -->
      <div v-else-if="groupError" class="text-center py-8">
        <div
          class="h-16 w-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-8 w-8 text-red-600"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
        <p class="text-red-600 font-medium mb-2">{{ groupError }}</p>
        <button
          class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors"
          @click="loadGroup"
        >
          再読み込み
        </button>
      </div>

      <!-- 無効なグループまたはチャットスタイル -->
      <div v-else-if="isInvalidGroup" class="text-center py-8 text-gray-500">
        無効なグループまたはチャットスタイルです
      </div>

      <!-- グループ全体チャットのみの場合 -->
      <div
        v-else-if="isGroupChatOnly"
        class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100"
      >
        <div
          class="relative flex antialiased text-gray-800"
          style="height: calc(100vh - 4rem)"
        >
          <div class="flex h-full w-full">
            <!-- Main Chat Area -->
            <div class="max-w-4xl mx-auto w-full">
              <div class="flex h-full w-full flex-col">
                <!-- Header for Chat Area -->
                <div
                  class="mb-2 flex items-center justify-between bg-white rounded-lg shadow-sm p-3 border border-gray-200"
                >
                  <div class="flex items-center">
                    <button
                      class="rounded-md p-2 text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500 mr-3"
                      @click="router.push(`/user/groups/${id}`)"
                    >
                      <span class="sr-only">戻る</span>
                      <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                        />
                      </svg>
                    </button>
                    <div>
                      <h2 class="text-base font-semibold text-gray-900">
                        {{ group?.name || "グループチャット" }}
                      </h2>
                      <p class="text-sm text-gray-500">
                        メンバー {{ group?.member_count || 0 }}人
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="flex h-full flex-auto flex-shrink-0 flex-col rounded-2xl bg-white shadow-sm border border-gray-200 overflow-hidden"
                >
                  <!-- Messages Display Area -->
                  <div
                    ref="groupMessageContainerRef"
                    class="flex flex-col h-full overflow-x-auto p-6 bg-gradient-to-b from-gray-50/50 to-gray-100/50"
                  >
                    <div
                      v-if="groupMessagesPending"
                      class="flex items-center justify-center h-full"
                    >
                      <div class="text-center">
                        <div
                          class="h-12 w-12 mx-auto border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"
                        />
                        <p class="text-gray-600 font-medium">
                          メッセージを読み込み中...
                        </p>
                      </div>
                    </div>
                    <div
                      v-else-if="groupMessagesError"
                      class="flex items-center justify-center h-full"
                    >
                      <div class="text-center">
                        <div
                          class="h-16 w-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-red-600"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                              clip-rule="evenodd"
                            />
                          </svg>
                        </div>
                        <p class="text-red-600 font-medium mb-2">
                          {{ groupMessagesError }}
                        </p>
                      </div>
                    </div>
                    <div v-else>
                      <div class="grid grid-cols-12 gap-y-1">
                        <template
                          v-for="(message, index) in groupMessages"
                          :key="message.id"
                        >
                          <div
                            v-if="
                              shouldShowDateSeparator(
                                message,
                                index,
                                groupMessages
                              )
                            "
                            class="col-span-12 text-center my-4"
                          >
                            <div class="relative">
                              <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300" />
                              </div>
                              <div class="relative flex justify-center">
                                <span
                                  class="text-xs text-gray-500 bg-white px-3 py-1 border border-gray-300 shadow-sm"
                                >
                                  {{ formatDateSeparatorText(message.sent_at) }}
                                </span>
                              </div>
                            </div>
                          </div>
                          <div
                            :class="
                              isMyMessage(message.sender_id)
                                ? 'col-start-4 col-end-13'
                                : 'col-start-1 col-end-10'
                            "
                            class="p-1 rounded-lg"
                          >
                            <div
                              :class="
                                isMyMessage(message.sender_id)
                                  ? 'flex justify-start flex-row-reverse'
                                  : 'flex flex-row'
                              "
                            >
                              <div
                                class="relative text-sm py-2 px-4 shadow-sm rounded-2xl"
                                :class="[
                                  isMyMessage(message.sender_id)
                                    ? 'bg-emerald-500 text-white max-w-sm lg:max-w-lg'
                                    : 'bg-white border border-gray-200 max-w-md lg:max-w-xl',
                                ]"
                              >
                                <div
                                  v-if="!isMyMessage(message.sender_id)"
                                  class="text-xs text-gray-500 mb-1"
                                >
                                  {{ getMessageSenderName(message) }}
                                </div>
                                <div
                                  class="whitespace-pre-line leading-relaxed break-all"
                                >
                                  {{ message.text_content }}
                                </div>
                              </div>
                              <div
                                class="text-xs min-w-[3.5rem] flex items-end self-end mb-1"
                                :class="[
                                  isMyMessage(message.sender_id)
                                    ? 'text-emerald-600 mr-2 justify-end'
                                    : 'text-gray-500 ml-2 justify-end',
                                ]"
                              >
                                {{ formatMessageTime(message.sent_at) }}
                              </div>
                            </div>
                          </div>
                        </template>
                      </div>
                      <div
                        v-if="groupMessages.length === 0"
                        class="flex items-center justify-center h-full"
                      >
                        <div class="text-center">
                          <div
                            class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-8 w-8 text-gray-400"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke="currentColor"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-6a2 2 0 012-2h8z"
                              />
                            </svg>
                          </div>
                          <p class="text-gray-600 font-medium">
                            まだメッセージはありません
                          </p>
                          <p class="text-gray-500 text-sm mt-1">
                            最初のメッセージを送信してみましょう
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Message Input Area -->
                  <div class="border-t border-gray-200 bg-white p-4">
                    <div class="flex items-center space-x-3">
                      <div class="flex flex-grow">
                        <textarea
                          v-model="groupNewMessage"
                          :disabled="groupSending"
                          class="w-full p-3 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none bg-gray-50 transition duration-200"
                          rows="1"
                          placeholder="メッセージを入力..."
                          @keydown="handleGroupKeydown"
                        />
                      </div>
                      <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-full w-12 h-12 transition duration-200 ease-in-out text-white font-bold focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                        :class="
                          groupSending || !groupNewMessage.trim()
                            ? 'bg-gray-400'
                            : 'bg-emerald-600 hover:bg-emerald-700'
                        "
                        :disabled="groupSending || !groupNewMessage.trim()"
                        @click="
                          () => {
                            console.log('Send button clicked!');
                            sendGroupMessage();
                          }
                        "
                      >
                        <svg
                          v-if="groupSending"
                          class="animate-spin h-5 w-5 text-white"
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
                        <svg
                          v-else
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"
                          />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 個別チャット選択UI（既存のUI） -->
      <div v-else-if="isMemberChatOnly || hasBothStyles">
        <h1 class="text-xl font-bold mb-4">
          {{ group?.name }} メンバーチャット
        </h1>

        <!-- 両方のスタイルがある場合のタブ切り替え -->
        <div v-if="hasBothStyles" class="mb-6">
          <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
            <button
              class="flex-1 py-2 px-4 text-sm font-medium rounded-md transition-colors"
              :class="
                currentView === 'group'
                  ? 'bg-white text-gray-900 shadow-sm'
                  : 'text-gray-500 hover:text-gray-700'
              "
              @click="currentView = 'group'"
            >
              グループ全体チャット
            </button>
            <button
              class="flex-1 py-2 px-4 text-sm font-medium rounded-md transition-colors"
              :class="
                currentView === 'member'
                  ? 'bg-white text-gray-900 shadow-sm'
                  : 'text-gray-500 hover:text-gray-700'
              "
              @click="currentView = 'member'"
            >
              個別チャット
            </button>
          </div>
        </div>

        <!-- グループ全体チャット表示（両方のスタイルがある場合） -->
        <div v-if="hasBothStyles && currentView === 'group'">
          <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
            <div
              class="relative flex antialiased text-gray-800"
              style="height: calc(100vh - 4rem)"
            >
              <div class="flex h-full w-full">
                <!-- Main Chat Area -->
                <div class="max-w-4xl mx-auto w-full">
                  <div class="flex h-full w-full flex-col">
                    <!-- Header for Chat Area -->
                    <div
                      class="mb-2 flex items-center justify-between bg-white rounded-lg shadow-sm p-3 border border-gray-200"
                    >
                      <div class="flex items-center">
                        <div>
                          <h2 class="text-base font-semibold text-gray-900">
                            {{ group?.name || "グループチャット" }}
                          </h2>
                          <p class="text-sm text-gray-500">
                            メンバー {{ group?.member_count || 0 }}人
                          </p>
                        </div>
                      </div>
                    </div>

                    <div
                      class="flex h-full flex-auto flex-shrink-0 flex-col rounded-2xl bg-white shadow-sm border border-gray-200 overflow-hidden"
                    >
                      <!-- Messages Display Area -->
                      <div
                        ref="groupMessageContainerRef"
                        class="flex flex-col h-full overflow-x-auto p-6 bg-gradient-to-b from-gray-50/50 to-gray-100/50"
                      >
                        <div
                          v-if="groupMessagesPending"
                          class="flex items-center justify-center h-full"
                        >
                          <div class="text-center">
                            <div
                              class="h-12 w-12 mx-auto border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"
                            />
                            <p class="text-gray-600 font-medium">
                              メッセージを読み込み中...
                            </p>
                          </div>
                        </div>
                        <div
                          v-else-if="groupMessagesError"
                          class="flex items-center justify-center h-full"
                        >
                          <div class="text-center">
                            <div
                              class="h-16 w-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-red-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                            </div>
                            <p class="text-red-600 font-medium mb-2">
                              {{ groupMessagesError }}
                            </p>
                          </div>
                        </div>
                        <div v-else>
                          <div class="grid grid-cols-12 gap-y-1">
                            <template
                              v-for="(message, index) in groupMessages"
                              :key="message.id"
                            >
                              <div
                                v-if="
                                  shouldShowDateSeparator(
                                    message,
                                    index,
                                    groupMessages
                                  )
                                "
                                class="col-span-12 text-center my-4"
                              >
                                <div class="relative">
                                  <div
                                    class="absolute inset-0 flex items-center"
                                  >
                                    <div
                                      class="w-full border-t border-gray-300"
                                    />
                                  </div>
                                  <div class="relative flex justify-center">
                                    <span
                                      class="text-xs text-gray-500 bg-white px-3 py-1 border border-gray-300 shadow-sm"
                                    >
                                      {{
                                        formatDateSeparatorText(message.sent_at)
                                      }}
                                    </span>
                                  </div>
                                </div>
                              </div>
                              <div
                                :class="
                                  isMyMessage(message.sender_id)
                                    ? 'col-start-4 col-end-13'
                                    : 'col-start-1 col-end-10'
                                "
                                class="p-1 rounded-lg"
                              >
                                <div
                                  :class="
                                    isMyMessage(message.sender_id)
                                      ? 'flex justify-start flex-row-reverse'
                                      : 'flex flex-row'
                                  "
                                >
                                  <div
                                    class="relative text-sm py-2 px-4 shadow-sm rounded-2xl"
                                    :class="[
                                      isMyMessage(message.sender_id)
                                        ? 'bg-emerald-500 text-white max-w-sm lg:max-w-lg'
                                        : 'bg-white border border-gray-200 max-w-md lg:max-w-xl',
                                    ]"
                                  >
                                    <div
                                      v-if="!isMyMessage(message.sender_id)"
                                      class="text-xs text-gray-500 mb-1"
                                    >
                                      {{ getMessageSenderName(message) }}
                                    </div>
                                    <div
                                      class="whitespace-pre-line leading-relaxed break-all"
                                    >
                                      {{ message.text_content }}
                                    </div>
                                  </div>
                                  <div
                                    class="text-xs min-w-[3.5rem] flex items-end self-end mb-1"
                                    :class="[
                                      isMyMessage(message.sender_id)
                                        ? 'text-emerald-600 mr-2 justify-end'
                                        : 'text-gray-500 ml-2 justify-end',
                                    ]"
                                  >
                                    {{ formatMessageTime(message.sent_at) }}
                                  </div>
                                </div>
                              </div>
                            </template>
                          </div>
                          <div
                            v-if="groupMessages.length === 0"
                            class="flex items-center justify-center h-full"
                          >
                            <div class="text-center">
                              <div
                                class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
                              >
                                <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  class="h-8 w-8 text-gray-400"
                                  fill="none"
                                  viewBox="0 0 24 24"
                                  stroke="currentColor"
                                >
                                  <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-6a2 2 0 012-2h8z"
                                  />
                                </svg>
                              </div>
                              <p class="text-gray-600 font-medium">
                                まだメッセージはありません
                              </p>
                              <p class="text-gray-500 text-sm mt-1">
                                最初のメッセージを送信してみましょう
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Message Input Area -->
                      <div class="border-t border-gray-200 bg-white p-4">
                        <div class="flex items-center space-x-3">
                          <div class="flex flex-grow">
                            <textarea
                              v-model="groupNewMessage"
                              :disabled="groupSending"
                              class="w-full p-3 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none bg-gray-50 transition duration-200"
                              rows="1"
                              placeholder="メッセージを入力..."
                              @keydown="handleGroupKeydown"
                            />
                          </div>
                          <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-full w-12 h-12 transition duration-200 ease-in-out text-white font-bold focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                            :class="
                              groupSending || !groupNewMessage.trim()
                                ? 'bg-gray-400'
                                : 'bg-emerald-600 hover:bg-emerald-700'
                            "
                            :disabled="groupSending || !groupNewMessage.trim()"
                            @click="
                              () => {
                                console.log('Send button clicked!');
                                sendGroupMessage();
                              }
                            "
                          >
                            <svg
                              v-if="groupSending"
                              class="animate-spin h-5 w-5 text-white"
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
                            <svg
                              v-else
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-5 w-5"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"
                              />
                            </svg>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 個別チャット選択UI -->
        <div
          v-if="(hasBothStyles && currentView === 'member') || isMemberChatOnly"
        >
          <div class="mb-6">
            <h2 class="text-lg font-semibold mb-3">
              メンバーを選択してください
            </h2>

            <div v-if="membersPending" class="text-gray-500">
              メンバー読み込み中...
            </div>
            <div v-else-if="membersError" class="text-red-500 mb-4">
              {{ membersError }}
            </div>
            <div v-else class="space-y-3">
              <!-- 検索・ソートコントロール -->
              <div class="mb-4 p-4 bg-gray-50 rounded-lg space-y-3">
                <!-- 検索フィールド -->
                <div>
                  <label
                    for="member-search"
                    class="block text-sm font-medium text-gray-700 mb-1"
                  >
                    メンバー検索
                  </label>
                  <input
                    id="member-search"
                    v-model="keyword"
                    type="text"
                    placeholder="名前・ニックネームまたはユーザーIDで検索"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>

                <!-- ソートコントロール -->
                <div class="flex flex-wrap gap-3 items-end">
                  <div>
                    <label
                      for="sort-key"
                      class="block text-sm font-medium text-gray-700 mb-1"
                    >
                      並び順
                    </label>
                    <select
                      id="sort-key"
                      v-model="sortKey"
                      class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="name">名前</option>
                      <option value="friend_id">ユーザーID</option>
                    </select>
                  </div>
                  <div>
                    <label
                      for="sort-order"
                      class="block text-sm font-medium text-gray-700 mb-1"
                    >
                      順序
                    </label>
                    <select
                      id="sort-order"
                      v-model="sortOrder"
                      class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="asc">昇順</option>
                      <option value="desc">降順</option>
                    </select>
                  </div>
                  <div v-if="hasActiveFilters">
                    <button
                      type="button"
                      class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors flex items-center gap-2"
                      @click="resetFilters"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                        />
                      </svg>
                      リセット
                    </button>
                  </div>
                </div>

                <!-- 結果情報 -->
                <div class="text-sm text-gray-600">
                  全 {{ members.length }}人中
                  <span v-if="keyword.trim()">
                    検索結果 {{ paginatedItems.length }}人を表示
                  </span>
                  <span v-else>
                    {{ paginatedItems.length }}人を表示 ({{ page }}/{{
                      totalPages
                    }}ページ)
                  </span>
                </div>
              </div>

              <!-- 全選択オプション -->
              <div class="flex items-center mb-4 p-3 bg-gray-50 rounded-lg">
                <input
                  id="select-all"
                  v-model="selectAll"
                  type="checkbox"
                  class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                  @change="toggleSelectAll"
                />
                <label
                  for="select-all"
                  class="ml-2 text-sm font-medium text-gray-900"
                >
                  全員を選択
                </label>
              </div>

              <!-- メンバー一覧 -->
              <div
                v-for="member in paginatedItems"
                :key="member.id"
                class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50"
              >
                <div class="flex items-center">
                  <input
                    :id="`member-${member.id}`"
                    v-model="selectedMemberIds"
                    :value="member.id"
                    type="checkbox"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 mr-3"
                  />
                  <!-- <div
                class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3"
              >
                <span class="text-blue-600 font-semibold">{{
                  member.name.charAt(0)
                }}</span>
              </div> -->
                  <div>
                    <div class="text-sm font-medium text-gray-900">
                      {{ member.owner_nickname || member.name }}
                    </div>
                    <div
                      v-if="member.owner_nickname"
                      class="text-xs text-gray-400"
                    >
                      {{ member.name }}
                    </div>
                    <div class="text-xs text-gray-400">
                      ID: {{ member.friend_id }}
                    </div>
                  </div>
                </div>
                <div class="relative">
                  <!-- 未読メッセージバッジ -->
                  <div
                    v-if="
                      member.unread_messages_count &&
                      member.unread_messages_count > 0
                    "
                    class="badge-dot absolute -top-2 -right-2 z-10"
                  />
                  <button
                    class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200"
                    @click="startChatWithMember(member)"
                  >
                    個別チャット
                  </button>
                </div>
              </div>

              <div
                v-if="members.length === 0"
                class="text-center py-8 text-gray-500"
              >
                このグループにはまだ他のメンバーがいません
              </div>

              <!-- ページネーション -->
              <div
                v-if="totalPages > 1"
                class="flex justify-center items-center gap-2 mt-4"
              >
                <button
                  :disabled="page === 1"
                  class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                  @click="prev"
                >
                  前へ
                </button>
                <span class="text-sm text-gray-600">
                  {{ page }} / {{ totalPages }}
                </span>
                <button
                  :disabled="page === totalPages"
                  class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                  @click="next"
                >
                  次へ
                </button>
              </div>

              <!-- アクションボタン -->
              <div
                v-if="selectedMemberIds.length > 0"
                class="bg-blue-50 p-4 rounded-lg"
              >
                <div class="flex items-center justify-between mb-3">
                  <span class="text-sm text-gray-700">
                    {{ selectedMemberIds.length }}人のメンバーが選択されています
                  </span>
                </div>
                <div class="flex space-x-3">
                  <button
                    v-if="selectedMemberIds.length === 1"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                    @click="startChatWithSelectedMember()"
                  >
                    選択メンバーと個別チャット
                  </button>
                  <button
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                    @click="openBulkMessageForm"
                  >
                    選択メンバーに一斉送信
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- 一斉送信フォーム -->
          <div
            v-if="showBulkMessageForm && !currentChatMember"
            class="mb-6 bg-green-50 p-4 rounded-lg border border-green-200"
          >
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-lg font-semibold text-green-800">
                一斉メッセージ送信
              </h3>
              <button
                class="text-green-600 hover:text-green-800"
                @click="showBulkMessageForm = false"
              >
                ✕
              </button>
            </div>

            <div class="mb-3">
              <p class="text-sm text-green-700 mb-2">
                送信先: {{ selectedMemberIds.length }}人
              </p>
              <div class="text-xs text-green-600">
                {{
                  selectedMembers
                    .map((m) => m.owner_nickname || m.name)
                    .join(", ")
                }}
              </div>
            </div>

            <div class="space-y-3">
              <textarea
                v-model="bulkMessage"
                class="w-full border rounded px-3 py-2 resize-none"
                rows="4"
                placeholder="一斉送信するメッセージを入力してください..."
              />

              <div class="flex items-center justify-between">
                <div class="text-sm text-green-600">
                  選択中: {{ selectedMemberIds.length }}人のメンバー
                </div>
                <div class="space-x-2">
                  <button
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
                    @click="showBulkMessageForm = false"
                  >
                    キャンセル
                  </button>
                  <button
                    class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
                    :disabled="sending || !bulkMessage.trim()"
                    @click="sendBulkMessage"
                  >
                    {{ sending ? "送信中..." : "一斉送信" }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- 送信結果 -->
          <div
            v-if="sendResult"
            class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg"
          >
            <h4 class="font-semibold text-green-800 mb-2">送信完了</h4>
            <p class="text-sm text-green-700">
              {{ sendResult.sent_count }}人のメンバーにメッセージを送信しました
            </p>
          </div>

          <!-- エラー表示 -->
          <div
            v-if="sendError"
            class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg"
          >
            <h4 class="font-semibold text-red-800 mb-2">送信エラー</h4>
            <p class="text-sm text-red-700">{{ sendError }}</p>
          </div>

          <!-- 現在のチャット表示 -->
          <div
            v-if="currentChatMember"
            class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100"
          >
            <div
              class="relative flex antialiased text-gray-800"
              style="height: calc(100vh - 4rem)"
            >
              <div class="flex h-full w-full">
                <!-- Main Chat Area -->
                <div class="max-w-4xl mx-auto w-full">
                  <div class="flex h-full w-full flex-col">
                    <!-- Header for Chat Area -->
                    <div
                      class="mb-2 flex items-center justify-between bg-white rounded-lg shadow-sm p-3 border border-gray-200"
                    >
                      <div class="flex items-center">
                        <button
                          class="rounded-md p-2 text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500 mr-3"
                          @click="
                            currentChatMember = null;
                            currentConversation = null;
                            messages = [];
                          "
                        >
                          <span class="sr-only">戻る</span>
                          <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            aria-hidden="true"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                            />
                          </svg>
                        </button>
                        <div>
                          <h2 class="text-base font-semibold text-gray-900">
                            {{
                              currentChatMember.owner_nickname ||
                              currentChatMember.name
                            }}とのチャット
                          </h2>
                          <p
                            v-if="currentChatMember.owner_nickname"
                            class="text-xs text-gray-500"
                          >
                            {{ currentChatMember.name }}
                          </p>
                        </div>
                      </div>
                    </div>

                    <div
                      class="flex h-full flex-auto flex-shrink-0 flex-col rounded-2xl bg-white shadow-sm border border-gray-200 overflow-hidden"
                    >
                      <!-- Messages Display Area -->
                      <div
                        ref="messageContainerRef"
                        class="flex flex-col h-full overflow-x-auto p-6 bg-gradient-to-b from-gray-50/50 to-gray-100/50"
                      >
                        <div
                          v-if="messagesPending"
                          class="flex items-center justify-center h-full"
                        >
                          <div class="text-center">
                            <div
                              class="h-12 w-12 mx-auto border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"
                            />
                            <p class="text-gray-600 font-medium">
                              メッセージを読み込み中...
                            </p>
                          </div>
                        </div>
                        <div
                          v-else-if="messagesError"
                          class="flex items-center justify-center h-full"
                        >
                          <div class="text-center">
                            <div
                              class="h-16 w-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-red-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                            </div>
                            <p class="text-red-600 font-medium mb-2">
                              {{ messagesError }}
                            </p>
                          </div>
                        </div>
                        <div v-else>
                          <div class="grid grid-cols-12 gap-y-1">
                            <template
                              v-for="(message, index) in messages"
                              :key="message.id"
                            >
                              <div
                                v-if="
                                  shouldShowDateSeparator(
                                    message,
                                    index,
                                    messages
                                  )
                                "
                                class="col-span-12 text-center my-4"
                              >
                                <div class="relative">
                                  <div
                                    class="absolute inset-0 flex items-center"
                                  >
                                    <div
                                      class="w-full border-t border-gray-300"
                                    />
                                  </div>
                                  <div class="relative flex justify-center">
                                    <span
                                      class="text-xs text-gray-500 bg-white px-3 py-1 border border-gray-300 shadow-sm"
                                    >
                                      {{
                                        formatDateSeparatorText(message.sent_at)
                                      }}
                                    </span>
                                  </div>
                                </div>
                              </div>
                              <div
                                :class="
                                  isMyMessage(message.sender_id)
                                    ? 'col-start-4 col-end-13'
                                    : 'col-start-1 col-end-10'
                                "
                                class="p-1 rounded-lg"
                              >
                                <div
                                  :class="
                                    isMyMessage(message.sender_id)
                                      ? 'flex justify-start flex-row-reverse'
                                      : 'flex flex-row'
                                  "
                                >
                                  <div
                                    class="relative text-sm py-2 px-4 shadow-sm rounded-2xl"
                                    :class="[
                                      isMyMessage(message.sender_id)
                                        ? 'bg-emerald-500 text-white max-w-sm lg:max-w-lg'
                                        : 'bg-white border border-gray-200 max-w-md lg:max-w-xl',
                                    ]"
                                  >
                                    <div
                                      class="whitespace-pre-line leading-relaxed break-all"
                                    >
                                      {{ message.text_content }}
                                    </div>
                                  </div>
                                  <div
                                    class="text-xs min-w-[3.5rem] flex items-end self-end mb-1"
                                    :class="[
                                      isMyMessage(message.sender_id)
                                        ? 'text-emerald-600 mr-2 justify-end'
                                        : 'text-gray-500 ml-2 justify-end',
                                    ]"
                                  >
                                    {{ formatMessageTime(message.sent_at) }}
                                  </div>
                                </div>
                              </div>
                            </template>
                          </div>
                          <div
                            v-if="messages.length === 0"
                            class="flex items-center justify-center h-full"
                          >
                            <div class="text-center">
                              <div
                                class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
                              >
                                <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  class="h-8 w-8 text-gray-400"
                                  fill="none"
                                  viewBox="0 0 24 24"
                                  stroke="currentColor"
                                >
                                  <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-6a2 2 0 012-2h8z"
                                  />
                                </svg>
                              </div>
                              <p class="text-gray-600 font-medium">
                                まだメッセージはありません
                              </p>
                              <p class="text-gray-500 text-sm mt-1">
                                最初のメッセージを送信してみましょう
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Message Input Area -->
                      <div class="border-t border-gray-200 bg-white p-4">
                        <div class="flex items-center space-x-3">
                          <div class="flex flex-grow">
                            <textarea
                              v-model="newMessage"
                              :disabled="sending"
                              class="w-full p-3 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none bg-gray-50 transition duration-200"
                              rows="1"
                              placeholder="メッセージを入力..."
                              @keydown="handleKeydown"
                            />
                          </div>
                          <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-full w-12 h-12 transition duration-200 ease-in-out text-white font-bold focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                            :class="
                              sending || !newMessage.trim()
                                ? 'bg-gray-400'
                                : 'bg-emerald-600 hover:bg-emerald-700'
                            "
                            :disabled="sending || !newMessage.trim()"
                            @click="sendMessage"
                          >
                            <svg
                              v-if="sending"
                              class="animate-spin h-5 w-5 text-white"
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
                            <svg
                              v-else
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-5 w-5"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"
                              />
                            </svg>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- メンバーチャットのみまたは両方のチャットスタイルがある場合 -->
      <div v-else class="text-center py-8 text-gray-600">
        <div class="max-w-md mx-auto">
          <div
            class="h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8 text-blue-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-6a2 2 0 012-2h8z"
              />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">
            {{ group?.name || "グループ" }}
          </h3>
          <p class="text-sm text-gray-500 mb-4">
            このグループは個別チャットスタイルです。<br />
            グループ詳細ページから個別チャットを開始してください。
          </p>
          <button
            class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors"
            @click="router.push(`/user/groups/${id}`)"
          >
            グループ詳細へ戻る
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick } from "vue";
import { useRoute, useRouter } from "#app";
import { useAuthStore } from "~/stores/auth";
import type { GroupConversation, GroupMessage } from "~/types/group";
import { useSortableMembers } from "~/composables/useSortableMembers";

interface GroupMember {
  id: number;
  name: string;
  friend_id: string;
  group_member_label: string;
  owner_nickname?: string | null; // オーナー専用ニックネーム
}

interface MemberConversation {
  id: number;
  type: string;
  name: string;
  room_token: string;
  group_conversation_id: number;
}

interface BulkMessageResponse {
  message: string;
  sent_count: number;
  sent_messages: Array<{
    conversation_id: number;
    target_user_id: number;
    message_id: number;
  }>;
}

// ページメタデータでプレミアム認証をミドルウェアで制御
definePageMeta({
  middleware: ["premium-required"],
});

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const groupConversations = useGroupConversations();
const id = Number(route.params.id as string);

function goBack() {
  if (window.history.length > 1) {
    window.history.back();
  } else {
    router.push(`/user/groups/${id}`);
  }
}

const isCheckingAccess = ref(true);

// リアクティブなプラン状態チェック
const hasPremiumAccess = computed(() => {
  const userPlan = authStore.user?.plan;
  return userPlan && userPlan !== "free";
});

// プラン状態を監視してリダイレクト
watch(
  hasPremiumAccess,
  async (hasAccess) => {
    if (hasAccess === false && authStore.user) {
      await router.push("/pricing");
    } else if (hasAccess === true) {
      isCheckingAccess.value = false;
    }
  },
  { immediate: true }
);

// 認証状態を監視
watch(
  () => authStore.user,
  async (user) => {
    if (user) {
      await nextTick();
      if (user.plan === "free") {
        await router.push("/pricing");
      } else {
        isCheckingAccess.value = false;
      }
    }
  },
  { immediate: true }
);

// グループ情報とメンバーの状態
const group = ref<GroupConversation | null>(null);
const members = ref<GroupMember[]>([]);
const membersPending = ref(true);
const membersError = ref("");

// グループ読み込み状態を追加
const groupPending = ref(true);
const groupError = ref("");

// チャットスタイル分岐用
const currentView = ref<"group" | "member">("group");

// グループ全体チャット状態
const groupMessages = ref<GroupMessage[]>([]);
const groupMessagesPending = ref(false);
const groupMessagesError = ref("");
const groupNewMessage = ref("");
const groupSending = ref(false);
const groupMessageContainerRef = ref<HTMLElement | null>(null);

// チャットスタイルに基づく表示判定
const isGroupChatOnly = computed(() => {
  const chatStyles = group.value?.chat_styles;
  if (!chatStyles || !Array.isArray(chatStyles)) return false;
  return chatStyles.includes("group") && !chatStyles.includes("group_member");
});

const isMemberChatOnly = computed(() => {
  const chatStyles = group.value?.chat_styles;
  if (!chatStyles || !Array.isArray(chatStyles)) return false;
  return !chatStyles.includes("group") && chatStyles.includes("group_member");
});

const hasBothStyles = computed(() => {
  const chatStyles = group.value?.chat_styles;
  if (!chatStyles || !Array.isArray(chatStyles)) return false;
  return chatStyles.includes("group") && chatStyles.includes("group_member");
});

// グループが無効かどうかを判定
const isInvalidGroup = computed(() => {
  // グループ読み込み中またはエラーの場合は無効ではない（別の状態）
  if (groupPending.value || groupError.value) return false;

  // グループが存在しない場合は無効
  if (!group.value) return true;

  // チャットスタイルが存在しないか、適切でない場合は無効
  const chatStyles = group.value.chat_styles;
  if (!chatStyles || !Array.isArray(chatStyles) || chatStyles.length === 0)
    return true;

  // サポートされていないチャットスタイルのみの場合は無効
  if (!chatStyles.includes("group") && !chatStyles.includes("group_member"))
    return true;

  return false;
});

// メンバー選択状態
const selectedMemberIds = ref<number[]>([]);
const selectAll = ref(false);

// 一斉送信状態
const showBulkMessageForm = ref(false);
const bulkMessage = ref("");
const sendResult = ref<BulkMessageResponse | null>(null);
const sendError = ref("");

// 現在のチャット状態
const currentChatMember = ref<GroupMember | null>(null);
const currentConversation = ref<MemberConversation | null>(null);
const messages = ref<GroupMessage[]>([]);
const messagesPending = ref(false);
const messagesError = ref("");

// メッセージ送信の状態
const newMessage = ref("");
const sending = ref(false);

// メッセージコンテナの参照
const messageContainerRef = ref<HTMLElement | null>(null);

// ソート・検索・ページネーション機能
const {
  keyword,
  sortKey,
  sortOrder,
  page,
  totalPages,
  paginatedItems,
  next,
  prev,
} = useSortableMembers(members, 50);

// 選択されたメンバー情報を取得
const selectedMembers = computed(() => {
  return members.value.filter((member) =>
    selectedMemberIds.value.includes(member.id)
  );
});

// フィルターが適用されているかどうかを判定
const hasActiveFilters = computed(() => {
  return (
    keyword.value.trim() !== "" ||
    sortKey.value !== "name" ||
    sortOrder.value !== "asc" ||
    page.value !== 1 ||
    selectedMemberIds.value.length > 0
  );
});

// グループ全体チャット用関数
const loadGroupMessages = async () => {
  if (!group.value?.room_token) return;

  groupMessagesPending.value = true;
  groupMessagesError.value = "";

  try {
    const data = await groupConversations.getMessages(group.value.room_token);
    groupMessages.value = data.data.sort(
      (a: GroupMessage, b: GroupMessage) =>
        new Date(a.sent_at).getTime() - new Date(b.sent_at).getTime()
    );
  } catch (error) {
    console.error("グループメッセージ取得エラー:", error);
    groupMessagesError.value = "メッセージの取得に失敗しました";
  } finally {
    groupMessagesPending.value = false;
    await nextTick();
    await scrollGroupToBottom("auto");
  }
};

const sendGroupMessage = async () => {
  console.log("sendGroupMessage called!");
  console.log("groupNewMessage:", groupNewMessage.value);
  console.log("group object:", group.value);
  console.log("group room_token:", group.value?.room_token);

  if (!groupNewMessage.value.trim() || !group.value?.room_token) {
    console.log("Validation failed - message or room_token is empty");
    return;
  }

  console.log("Validation passed, sending message...");
  groupSending.value = true;
  const messageText = groupNewMessage.value;
  try {
    console.log("About to call sendMessage with:", {
      room_token: group.value.room_token,
      messageText,
    });

    const sentMessage = await groupConversations.sendMessage(
      group.value.room_token,
      messageText
    );

    console.log("Message sent successfully:", sentMessage);

    if (sentMessage) {
      groupMessages.value.push(sentMessage);
      console.log(
        "Message added to groupMessages, count:",
        groupMessages.value.length
      );
    }

    groupNewMessage.value = "";
    await scrollGroupToBottom("smooth");

    // グループメッセージ送信後にメンバーリストを再読み込み（念のため）
    await loadMembers();
  } catch (error) {
    console.error("グループメッセージ送信エラー:", error);
    groupMessagesError.value = "メッセージの送信に失敗しました";
    await loadGroupMessages();
  } finally {
    groupSending.value = false;
    console.log("sendGroupMessage finished");
  }
};

const handleGroupKeydown = (event: KeyboardEvent) => {
  if (event.key === "Enter" && event.shiftKey) {
    event.preventDefault();
    sendGroupMessage();
  }
};

const scrollGroupToBottom = async (behavior: "auto" | "smooth" = "auto") => {
  await nextTick();
  if (groupMessageContainerRef.value) {
    groupMessageContainerRef.value.scrollTo({
      top: groupMessageContainerRef.value.scrollHeight,
      behavior: behavior,
    });
  }
};

// グループ情報を取得
const loadGroup = async () => {
  console.log("loadGroup called with id:", id);
  groupPending.value = true;
  groupError.value = "";

  try {
    const groupData = await groupConversations.getGroup(id);
    console.log("Group data loaded:", groupData);
    group.value = groupData;
  } catch (error) {
    console.error("グループ取得エラー:", error);
    groupError.value = "グループの取得に失敗しました";
  } finally {
    groupPending.value = false;
  }
};

// グループオーナーかどうかを判定
const isGroupOwner = computed(() => {
  return (
    group.value &&
    authStore.user &&
    group.value.owner_user_id === authStore.user.id
  );
});

// メンバー一覧を取得
const loadMembers = async () => {
  membersPending.value = true;
  membersError.value = "";

  try {
    if (isGroupOwner.value) {
      // オーナーの場合はニックネーム情報を含む全メンバー情報を取得
      const allMembers = await groupConversations.getAllGroupMembers(id);
      console.log("Debug: getAllGroupMembers response:", allMembers);
      // アクティブなメンバーのみをフィルタリング
      members.value = allMembers
        .filter((member) => member.is_active)
        .map((member) => ({
          id: member.id,
          name: member.name,
          friend_id: member.friend_id,
          group_member_label: member.group_member_label,
          owner_nickname: member.owner_nickname,
          unread_messages_count: member.unread_messages_count, // 未読メッセージ数を追加
        }));
      console.log("Debug: Processed members with nicknames:", members.value);
    } else {
      // 一般メンバーの場合は通常のメンバー情報を取得
      const memberData = await groupConversations.getGroupMembers(id);
      members.value = memberData.map((member) => ({
        id: member.id,
        name: member.name,
        friend_id: member.friend_id,
        group_member_label: member.group_member_label,
        role: member.role,
        joined_at: member.joined_at,
        unread_messages_count: member.unread_messages_count, // 未読メッセージ数を追加
      }));
      console.log("Debug: getGroupMembers response:", members.value);
    }
  } catch (error) {
    console.error("メンバー取得エラー:", error);
    membersError.value = "メンバーの取得に失敗しました";
  } finally {
    membersPending.value = false;
  }
};

// 全選択/全解除（現在のページのメンバーのみ）
const toggleSelectAll = async () => {
  if (selectAll.value) {
    // 現在のページのメンバーを選択状態に追加
    const currentPageIds = paginatedItems.value.map((m) => m.id);
    const newSelected = [
      ...new Set([...selectedMemberIds.value, ...currentPageIds]),
    ];
    selectedMemberIds.value = newSelected;

    // 「全員を選択」にチェックが入った場合、画面最下部にスクロール
    await nextTick();
    window.scrollTo({
      top: document.body.scrollHeight,
      behavior: "smooth",
    });
  } else {
    // 現在のページのメンバーを選択状態から除去
    const currentPageIds = paginatedItems.value.map((m) => m.id);
    selectedMemberIds.value = selectedMemberIds.value.filter(
      (id) => !currentPageIds.includes(id)
    );
  }
};

// 選択状態の監視（現在のページのメンバーに基づく）
watch(
  [selectedMemberIds, paginatedItems],
  ([newSelected, currentPageItems]) => {
    selectAll.value =
      currentPageItems.length > 0 &&
      currentPageItems.every((item) => newSelected.includes(item.id));
  },
  { deep: true }
);

// 検索キーワードが変更されたらページを1にリセット
watch(keyword, () => {
  page.value = 1;
});

// フィルター・ソート・選択状態をリセットする関数
const resetFilters = () => {
  keyword.value = "";
  sortKey.value = "name";
  sortOrder.value = "asc";
  page.value = 1;
  selectedMemberIds.value = [];
  selectAll.value = false;
};

// 選択したメンバーと個別チャット開始
const startChatWithSelectedMember = () => {
  if (selectedMemberIds.value.length === 1) {
    const member = members.value.find(
      (m) => m.id === selectedMemberIds.value[0]
    );
    if (member) {
      startChatWithMember(member);
    }
  }
};

// 一斉送信フォームを開く
const openBulkMessageForm = async () => {
  // 個別チャットが開いている場合は閉じる
  if (currentChatMember.value) {
    currentChatMember.value = null;
    currentConversation.value = null;
    messages.value = [];
  }

  showBulkMessageForm.value = true;

  // 画面最下部にスクロール
  await nextTick();
  window.scrollTo({
    top: document.body.scrollHeight,
    behavior: "smooth",
  });
};

// メンバーとのチャットを開始
const startChatWithMember = async (member: GroupMember) => {
  currentChatMember.value = member;

  // 一斉送信フォームが表示中の場合は閉じる
  if (showBulkMessageForm.value) {
    showBulkMessageForm.value = false;
  }

  // メッセージとエラー状態をリセット
  messages.value = [];
  messagesError.value = "";

  console.log("個別チャット開始:", { member, target_user_id: member.id });

  try {
    // メンバー間チャットルームを取得/作成
    const { api } = useApi();
    const conversation = await api<MemberConversation>(
      `/conversations/groups/${id}/member-chat`,
      {
        method: "POST",
        body: { target_user_id: member.id },
      }
    );

    currentConversation.value = conversation;
    await loadMessages();

    // チャットルームを既読にマーク
    try {
      const { api } = useApi();
      await api(`/conversations/room/${conversation.id}/read`, {
        method: "POST",
      });
      console.log("チャットルームを既読にマークしました");

      // 既読後にメンバーリストを再読み込みしてバッジを更新
      await loadMembers();
    } catch (error) {
      console.error("既読マークエラー:", error);
    }

    // 個別チャット開始時に画面最下部にスクロール
    await nextTick();
    window.scrollTo({
      top: document.body.scrollHeight,
      behavior: "smooth",
    });

    // チャット画面表示後に確実にスクロール
    setTimeout(() => {
      scrollToBottom("auto");
    }, 100);
  } catch (error: unknown) {
    console.error("チャットルーム作成エラー:", error);
    if (error && typeof error === "object" && "response" in error) {
      const httpError = error as {
        response?: { data?: { message?: string } };
        message?: string;
      };
      console.error("エラー詳細:", httpError.response?.data);
      messagesError.value = `チャットルームの作成に失敗しました: ${
        httpError.response?.data?.message || httpError.message || "不明なエラー"
      }`;
    } else {
      messagesError.value = "チャットルームの作成に失敗しました";
    }
  }
};

// メッセージを読み込み
const loadMessages = async () => {
  if (!currentConversation.value?.room_token) return;

  messagesPending.value = true;
  messagesError.value = "";

  try {
    const data = await groupConversations.getMessages(
      currentConversation.value.room_token
    );
    // メッセージを送信日時で昇順ソート（古いものから新しいものへ）
    messages.value = data.data.sort(
      (a: GroupMessage, b: GroupMessage) =>
        new Date(a.sent_at).getTime() - new Date(b.sent_at).getTime()
    );
  } catch (error) {
    console.error("メッセージ取得エラー:", error);
    messagesError.value = "メッセージの取得に失敗しました";
  } finally {
    messagesPending.value = false;
    // メッセージ読み込み完了後にスクロール
    await nextTick();
    await scrollToBottom("auto");
  }
};

// 個別チャットメッセージ送信
const sendMessage = async () => {
  if (!newMessage.value.trim() || !currentConversation.value?.room_token)
    return;

  sending.value = true;
  const messageText = newMessage.value;
  try {
    const sentMessage = await groupConversations.sendMessage(
      currentConversation.value.room_token,
      messageText
    );

    // 送信されたメッセージを一覧に追加（最下部に表示）
    if (sentMessage) {
      messages.value.push(sentMessage);
    }

    newMessage.value = "";
    await scrollToBottom("smooth");

    // メッセージ送信後にメンバーリストを再読み込みして未読数を更新
    await loadMembers();
  } catch (error) {
    console.error("メッセージ送信エラー:", error);
    messagesError.value = "メッセージの送信に失敗しました";
    // エラーが発生した場合は、メッセージ一覧を再読み込みして同期を保つ
    await loadMessages();
  } finally {
    sending.value = false;
  }
};

// 一斉メッセージ送信
const sendBulkMessage = async () => {
  if (!bulkMessage.value.trim() || selectedMemberIds.value.length === 0) return;

  sending.value = true;
  sendError.value = "";
  sendResult.value = null;

  try {
    const result = await groupConversations.sendBulkMessage(id, {
      target_user_ids: selectedMemberIds.value,
      text_content: bulkMessage.value,
    });

    sendResult.value = result;
    bulkMessage.value = "";
    selectedMemberIds.value = [];
    selectAll.value = false;
    showBulkMessageForm.value = false;

    // 一斉送信後にメンバーリストを再読み込みして未読数を更新
    await loadMembers();

    // 数秒後に結果メッセージを消す
    setTimeout(() => {
      sendResult.value = null;
    }, 5000);
  } catch (error: unknown) {
    console.error("一斉送信エラー:", error);
    sendError.value =
      error instanceof Error ? error.message : "メッセージの送信に失敗しました";

    // 数秒後にエラーメッセージを消す
    setTimeout(() => {
      sendError.value = "";
    }, 5000);
  } finally {
    sending.value = false;
  }
};

// 個別チャット用の時刻フォーマット
const formatMessageTime = (sentAt?: string | null): string => {
  if (!sentAt) return "";
  return new Date(sentAt).toLocaleTimeString("ja-JP", {
    hour: "numeric",
    minute: "2-digit",
    hour12: false,
  });
};

// 日付セパレーター用のフォーマット
const formatDateSeparatorText = (sentAt?: string | null): string => {
  if (!sentAt) return "";
  const date = new Date(sentAt);
  return `${date.getFullYear()}.${String(date.getMonth() + 1).padStart(
    2,
    "0"
  )}.${String(date.getDate()).padStart(2, "0")}`;
};

// 日付セパレーターを表示するかどうか
const shouldShowDateSeparator = (
  message: GroupMessage,
  index: number,
  allMessages: GroupMessage[]
): boolean => {
  if (index === 0) return true;
  const prevMessage = allMessages[index - 1];
  if (!prevMessage?.sent_at || !message.sent_at) return false;
  return (
    new Date(prevMessage.sent_at).toDateString() !==
    new Date(message.sent_at).toDateString()
  );
};

// 自分のメッセージかどうか
const isMyMessage = (senderId: number | null): boolean => {
  return senderId === authStore.user?.id;
};

// メッセージの発言者名を取得
const getMessageSenderName = (message: GroupMessage): string => {
  if (message.sender) {
    // オーナーの場合はニックネームがあればニックネームを、なければ通常の名前を表示
    let displayName = message.sender.name;
    if (isGroupOwner.value) {
      const memberWithNickname = members.value.find(
        (m) => m.id === message.sender.id
      );
      if (memberWithNickname?.owner_nickname) {
        displayName = memberWithNickname.owner_nickname;
      }
    }

    // 退室済みの場合は（退室済み）を追加
    if (message.sender_has_left) {
      return `${displayName}（退室済み）`;
    }
    return displayName;
  }
  return "不明なユーザー";
};

// キーボード入力処理
const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === "Enter" && event.shiftKey) {
    event.preventDefault();
    sendMessage();
  }
};

// メッセージコンテナを最下部にスクロール
const scrollToBottom = async (behavior: "auto" | "smooth" = "auto") => {
  await nextTick();
  if (messageContainerRef.value) {
    messageContainerRef.value.scrollTo({
      top: messageContainerRef.value.scrollHeight,
      behavior: behavior,
    });
  }
};

// グループメッセージの自動スクロール監視
watch(
  groupMessages,
  async (newMessages, oldMessages) => {
    if (newMessages.length > (oldMessages?.length || 0)) {
      await scrollGroupToBottom("smooth");
    }
  },
  { deep: true }
);

// 個別チャット画面が表示された時にスクロール
watch(currentChatMember, async (newMember) => {
  if (newMember) {
    // DOM更新を待ってからスクロール
    await nextTick();
    setTimeout(() => {
      scrollToBottom("auto");
    }, 200);
  }
});

// メッセージとチャット状態の両方を監視
watch(
  [messages, currentChatMember],
  async ([newMessages, newMember]) => {
    if (newMember && newMessages.length > 0 && !messagesPending.value) {
      // DOM更新を確実に待つ
      await nextTick();
      setTimeout(() => {
        scrollToBottom("auto");
      }, 100);
    }
  },
  { deep: true }
);

// 初期データ読み込み
const refresh = async () => {
  await loadGroup();
  await loadMembers();

  // グループ全体チャットのみの場合は、グループメッセージを読み込み
  if (isGroupChatOnly.value) {
    await loadGroupMessages();
  }
};

// グループ情報が更新された時、チャットスタイルに group が含まれていればメッセージをロード
watch(
  group,
  async (newGroup) => {
    const chatStyles = newGroup?.chat_styles || [];
    const includesGroupStyle = Array.isArray(chatStyles)
      ? chatStyles.includes("group")
      : false;
    if (newGroup && includesGroupStyle) {
      await loadGroupMessages();
    }
  },
  { deep: true }
);

// タブ切り替えでグループ全体チャットに移動した時にメッセージをロード
watch(currentView, async (view) => {
  if (view === "group" && hasBothStyles.value) {
    await loadGroupMessages();
  }
});

// 一斉送信フォームが表示された時に画面最下部にスクロール
watch(showBulkMessageForm, async (isVisible) => {
  if (isVisible) {
    await nextTick();
    window.scrollTo({
      top: document.body.scrollHeight,
      behavior: "smooth",
    });
  }
});

// 初回読み込み
await refresh();
</script>
