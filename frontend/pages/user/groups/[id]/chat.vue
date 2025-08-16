<template>
  <div v-if="isCheckingAccess" class="p-4 text-center">
    <div
      class="h-10 w-10 mx-auto border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
    />
    <p class="mt-4 text-gray-600">アクセス権限を確認中...</p>
  </div>
  <div v-else class="p-4">
    <div class="max-w-5xl mx-auto">
      <!-- 戻るボタン -->
      <div class="mb-4">
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
            <div class="max-w-5xl mx-auto w-full">
              <div class="flex h-full w-full flex-col">
                <!-- Header for Chat Area -->
                <div
                  class="flex items-center justify-between bg-white rounded-lg shadow-sm p-3 border border-gray-200"
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
                  class="flex h-full flex-auto flex-shrink-0 flex-col bg-white shadow-sm border border-gray-200 overflow-hidden"
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
                              <!-- 自分のメッセージで既読表記がある場合：既読+時刻の縦並び -->
                              <div
                                v-if="
                                  isMyMessage(message.sender_id) &&
                                  (message.is_read ||
                                    (typeof message.read_count === 'number' &&
                                      message.read_count > 0))
                                "
                                class="flex flex-col items-end justify-end"
                              >
                                <!-- 既読表示（時刻の上に配置） -->
                                <div
                                  class="text-xs text-gray-500 mb-0.5 text-right mr-2"
                                >
                                  <!-- メンバーチャット（1対1）の場合 -->
                                  <span
                                    v-if="
                                      message.is_read &&
                                      (!message.read_count ||
                                        message.read_count === 0)
                                    "
                                  >
                                    既読
                                  </span>
                                  <!-- グループチャットの場合 -->
                                  <span
                                    v-else-if="
                                      typeof message.read_count === 'number' &&
                                      message.read_count > 0
                                    "
                                  >
                                    既読 {{ message.read_count }}
                                  </span>
                                </div>
                                <!-- 時刻表示 -->
                                <div
                                  class="text-xs min-w-[3.5rem] flex items-end self-end text-emerald-600 mr-2 justify-end"
                                >
                                  {{ formatMessageTime(message.sent_at) }}
                                </div>
                              </div>
                              <!-- 自分のメッセージで既読表記がない場合 または 相手のメッセージの場合：元通りの時刻表示 -->
                              <div
                                v-else
                                class="text-xs min-w-[3.5rem] flex items-end self-end mb-1"
                                :class="[
                                  isMyMessage(message.sender_id)
                                    ? 'text-emerald-600 mr-2 justify-end'
                                    : 'text-gray-500 ml-2 justify-start',
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
                            メッセージはありません
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Message Input Area -->
                  <div class="border-t border-gray-200 bg-white p-3">
                    <div class="flex items-center space-x-3">
                      <div class="flex flex-grow">
                        <textarea
                          ref="groupTextareaRef"
                          v-model="groupNewMessage"
                          :disabled="groupSending"
                          class="w-full p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none bg-gray-50 transition duration-200"
                          rows="1"
                          placeholder="メッセージを入力"
                          @keydown="handleGroupKeydown"
                          @compositionstart="groupIsComposing = true"
                          @compositionend="groupIsComposing = false"
                          @input="adjustGroupTextareaHeight"
                        />
                      </div>
                      <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-full w-10 h-10 transition duration-200 ease-in-out text-white font-bold focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                        :class="
                          groupSending ||
                          (!groupNewMessage.trim() && !groupIsComposing)
                            ? 'bg-gray-400'
                            : 'bg-emerald-600 hover:bg-emerald-700'
                        "
                        :disabled="isGroupSendButtonDisabled"
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
                <div class="max-w-5xl mx-auto w-full">
                  <div class="flex h-full w-full flex-col">
                    <!-- Header for Chat Area -->
                    <div
                      class="flex items-center justify-between bg-white rounded-lg shadow-sm p-3 border border-gray-200"
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
                      class="flex h-full flex-auto flex-shrink-0 flex-col bg-white shadow-sm border border-gray-200 overflow-hidden"
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
                                  <!-- 自分のメッセージで既読表記がある場合：既読+時刻の縦並び -->
                                  <div
                                    v-if="
                                      isMyMessage(message.sender_id) &&
                                      (message.is_read ||
                                        (typeof message.read_count ===
                                          'number' &&
                                          message.read_count > 0))
                                    "
                                    class="flex flex-col items-end justify-end"
                                  >
                                    <!-- 既読表示（時刻の上に配置） -->
                                    <div
                                      class="text-xs text-gray-500 mb-0.5 text-right mr-2"
                                    >
                                      <!-- メンバーチャット（1対1）の場合 -->
                                      <span
                                        v-if="
                                          message.is_read &&
                                          (!message.read_count ||
                                            message.read_count === 0)
                                        "
                                      >
                                        既読
                                      </span>
                                      <!-- グループチャットの場合 -->
                                      <span
                                        v-else-if="
                                          typeof message.read_count ===
                                            'number' && message.read_count > 0
                                        "
                                      >
                                        既読 {{ message.read_count }}
                                      </span>
                                    </div>
                                    <!-- 時刻表示 -->
                                    <div
                                      class="text-xs min-w-[3.5rem] flex items-end self-end text-emerald-600 mr-2 justify-end"
                                    >
                                      {{ formatMessageTime(message.sent_at) }}
                                    </div>
                                  </div>
                                  <!-- 自分のメッセージで既読表記がない場合 または 相手のメッセージの場合：元通りの時刻表示 -->
                                  <div
                                    v-else
                                    class="text-xs min-w-[3.5rem] flex items-end self-end mb-1"
                                    :class="[
                                      isMyMessage(message.sender_id)
                                        ? 'text-emerald-600 mr-2 justify-end'
                                        : 'text-gray-500 ml-2 justify-start',
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
                                メッセージはありません
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Message Input Area -->
                      <div class="border-t border-gray-200 bg-white p-3">
                        <div class="flex items-center space-x-3">
                          <div class="flex flex-grow">
                            <textarea
                              v-model="groupNewMessage"
                              :disabled="groupSending"
                              class="w-full p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none bg-gray-50 transition duration-200"
                              rows="1"
                              placeholder="メッセージを入力..."
                              @keydown="handleGroupKeydown"
                              @compositionstart="groupIsComposing = true"
                              @compositionend="groupIsComposing = false"
                            />
                          </div>
                          <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-full w-10 h-10 transition duration-200 ease-in-out text-white font-bold focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                            :class="
                              isGroupSendButtonDisabled
                                ? 'bg-gray-400'
                                : 'bg-emerald-600 hover:bg-emerald-700'
                            "
                            :disabled="isGroupSendButtonDisabled"
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
            <div v-if="membersPending" class="text-gray-500">
              メンバー読み込み中...
            </div>
            <div v-else-if="membersError" class="text-red-500 mb-4">
              {{ membersError }}
            </div>
            <div v-else class="space-y-3">
              <!-- 検索・フィルタコントロール -->
              <div class="mb-4 space-y-3">
                <!-- 検索フィールド -->
                <div>
                  <input
                    id="member-search"
                    v-model="keyword"
                    type="text"
                    placeholder="名前・ニックネームまたはユーザーIDで検索"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>

                <!-- フィルタ・ソート -->
                <div
                  class="flex flex-wrap items-center gap-3 p-3 bg-gray-50 rounded-lg"
                >
                  <div class="flex items-center gap-2">
                    <select
                      id="sort-key"
                      v-model="sortKey"
                      class="px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                      <option value="name">名前順</option>
                      <option value="friend_id">ID順</option>
                      <option value="joined_at">加入順</option>
                    </select>
                    <select
                      id="sort-order"
                      v-model="sortOrder"
                      class="px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                      <option value="asc">昇順</option>
                      <option value="desc">降順</option>
                    </select>
                  </div>

                  <div class="flex items-center gap-2">
                    <input
                      id="show-only-unread"
                      v-model="showOnlyUnread"
                      type="checkbox"
                      class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                    />
                    <label for="show-only-unread" class="text-sm text-gray-700">
                      新着メッセージあり
                    </label>
                  </div>

                  <div class="flex items-center gap-3 ml-auto">
                    <span class="text-sm text-gray-600">
                      {{ paginatedItems.length }}/{{ members.length }}人
                    </span>
                    <button
                      v-if="hasActiveFilters"
                      type="button"
                      class="px-3 py-1 text-sm bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors"
                      @click="resetFilters"
                    >
                      リセット
                    </button>
                  </div>
                </div>
              </div>

              <!-- 全選択オプション -->
              <div class="flex items-center mb-3 p-2 bg-blue-50 rounded border">
                <input
                  id="select-all"
                  v-model="selectAll"
                  type="checkbox"
                  class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                  @change="toggleSelectAll"
                />
                <label for="select-all" class="ml-2 text-sm text-gray-700">
                  全員を選択
                </label>
              </div>

              <!-- メンバー一覧 -->
              <div class="space-y-2">
                <div
                  v-for="member in (paginatedItems as GroupMember[])"
                  :key="member.id"
                  class="flex items-center justify-between p-3 bg-white border rounded-lg hover:shadow-sm transition-shadow"
                >
                  <div class="flex items-center flex-1">
                    <input
                      :id="`member-${member.id}`"
                      v-model="selectedMemberIds"
                      :value="member.id"
                      type="checkbox"
                      class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 mr-3"
                    />
                    <div class="flex-1">
                      <div class="font-medium text-gray-900">
                        {{ member.owner_nickname || member.name }}
                      </div>
                      <div class="text-sm text-gray-500">
                        <span v-if="member.owner_nickname"
                          >{{ member.name }} •
                        </span>
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
                      class="badge-dot absolute -top-1 -right-1 z-10"
                    />
                    <button
                      class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                      @click="startChatWithMember(member)"
                    >
                      チャット
                    </button>
                  </div>
                </div>
              </div>

              <div
                v-if="members.length === 0"
                class="text-center py-12 text-gray-500"
              >
                <div class="mb-3">
                  <svg
                    class="w-12 h-12 mx-auto text-gray-300"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-1a4 4 0 11-8 0 4 4 0 018 0z"
                    />
                  </svg>
                </div>
                <p class="font-medium text-gray-600">メンバーがいません</p>
                <p class="text-sm text-gray-500 mt-1">
                  グループにメンバーを招待してください
                </p>
              </div>

              <!-- ページネーション -->
              <div
                v-if="totalPages > 1"
                class="flex justify-center items-center gap-3 mt-6 pb-4"
              >
                <button
                  :disabled="page === 1"
                  class="px-4 py-2 text-sm bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  @click="prev"
                >
                  ← 前
                </button>
                <span
                  class="px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg"
                >
                  {{ page }} / {{ totalPages }}
                </span>
                <button
                  :disabled="page === totalPages"
                  class="px-4 py-2 text-sm bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  @click="next"
                >
                  次 →
                </button>
              </div>

              <!-- アクションボタン -->
              <div
                v-if="selectedMemberIds.length > 0"
                class="sticky bottom-0 bg-white border-t p-4 mt-4"
              >
                <div class="flex items-center justify-between">
                  <span class="text-sm font-medium text-gray-700">
                    {{ selectedMemberIds.length }}人選択中
                  </span>
                  <div class="flex space-x-2">
                    <button
                      v-if="selectedMemberIds.length === 1"
                      class="px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                      @click="startChatWithSelectedMember()"
                    >
                      個別チャット
                    </button>
                    <button
                      class="px-3 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                      @click="openBulkMessageForm"
                    >
                      一斉送信
                    </button>
                  </div>
                </div>
              </div>

              <!-- 個別チャット送信履歴ボタン -->
              <div class="mt-4 flex justify-end">
                <button
                  class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2"
                  @click="showMessageHistory = true"
                >
                  <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                  個別チャット送信履歴
                </button>
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
                placeholder="一斉送信するメッセージを入力"
              />

              <div class="flex flex-col items-center justify-between">
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
                <div class="max-w-5xl mx-auto w-full">
                  <div class="flex h-full w-full flex-col">
                    <!-- Header for Chat Area -->
                    <div
                      class="flex items-center justify-between bg-white rounded-lg shadow-sm p-3 border border-gray-200"
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
                      class="flex h-full flex-auto flex-shrink-0 flex-col bg-white shadow-sm border border-gray-200 overflow-hidden"
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
                                  <!-- 自分のメッセージで既読表記がある場合：既読+時刻の縦並び -->
                                  <div
                                    v-if="
                                      isMyMessage(message.sender_id) &&
                                      (message.is_read ||
                                        (typeof message.read_count ===
                                          'number' &&
                                          message.read_count > 0))
                                    "
                                    class="flex flex-col items-end justify-end"
                                  >
                                    <!-- 既読表示（時刻の上に配置） -->
                                    <div
                                      class="text-xs text-gray-500 mb-0.5 text-right mr-2"
                                    >
                                      <!-- メンバーチャット（1対1）の場合 -->
                                      <span
                                        v-if="
                                          message.is_read &&
                                          (!message.read_count ||
                                            message.read_count === 0)
                                        "
                                      >
                                        既読
                                      </span>
                                      <!-- グループチャットの場合 -->
                                      <span
                                        v-else-if="
                                          typeof message.read_count ===
                                            'number' && message.read_count > 0
                                        "
                                      >
                                        既読 {{ message.read_count }}
                                      </span>
                                    </div>
                                    <!-- 時刻表示 -->
                                    <div
                                      class="text-xs min-w-[3.5rem] flex items-end self-end text-emerald-600 mr-2 justify-end"
                                    >
                                      {{ formatMessageTime(message.sent_at) }}
                                    </div>
                                  </div>
                                  <!-- 自分のメッセージで既読表記がない場合 または 相手のメッセージの場合：元通りの時刻表示 -->
                                  <div
                                    v-else
                                    class="text-xs min-w-[3.5rem] flex items-end self-end mb-1"
                                    :class="[
                                      isMyMessage(message.sender_id)
                                        ? 'text-emerald-600 mr-2 justify-end'
                                        : 'text-gray-500 ml-2 justify-start',
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
                                メッセージはありません
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Message Input Area -->
                      <div class="border-t border-gray-200 bg-white p-3">
                        <div class="flex items-center space-x-3">
                          <div class="flex flex-grow">
                            <textarea
                              v-model="newMessage"
                              :disabled="sending"
                              class="w-full p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none bg-gray-50 transition duration-200"
                              rows="1"
                              placeholder="メッセージを入力..."
                              @keydown="handleKeydown"
                              @compositionstart="isComposing = true"
                              @compositionend="isComposing = false"
                            />
                          </div>
                          <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-full w-10 h-10 transition duration-200 ease-in-out text-white font-bold focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                            :class="
                              sending || (!newMessage.trim() && !isComposing)
                                ? 'bg-gray-400'
                                : 'bg-emerald-600 hover:bg-emerald-700'
                            "
                            :disabled="
                              sending || (!newMessage.trim() && !isComposing)
                            "
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

    <!-- 個別チャット送信履歴モーダル -->
    <div
      v-if="showMessageHistory"
      class="fixed inset-0 z-50 overflow-y-auto"
      @click.self="showMessageHistory = false"
    >
      <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" />

        <div
          class="relative bg-white rounded-2xl shadow-xl max-w-4xl w-full max-h-[80vh] overflow-hidden"
        >
          <div
            class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between"
          >
            <h2 class="text-xl font-bold text-gray-900">
              個別チャット送信履歴
            </h2>
            <button
              class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
              @click="showMessageHistory = false"
            >
              <svg
                class="w-5 h-5 text-gray-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>

          <div
            class="overflow-y-auto history-scroll-container"
            style="max-height: calc(80vh - 80px)"
          >
            <div v-if="messageHistoryLoading" class="p-8 text-center">
              <div
                class="h-12 w-12 mx-auto border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"
              />
              <p class="text-gray-600">送信履歴を読み込み中...</p>
            </div>

            <div v-else-if="messageHistoryError" class="p-8 text-center">
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
              <p class="text-red-600 font-medium">{{ messageHistoryError }}</p>
            </div>

            <div
              v-else-if="messageHistoryItems.length === 0"
              class="p-8 text-center"
            >
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
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                  />
                </svg>
              </div>
              <p class="text-gray-600 font-medium">まだ送信履歴がありません</p>
            </div>

            <div v-else class="divide-y divide-gray-200">
              <div
                v-for="item in messageHistoryItems"
                :key="item.id"
                class="p-4 hover:bg-gray-50 transition-colors"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                      <span
                        v-if="item.is_bulk"
                        class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded font-medium"
                      >
                        一斉送信
                      </span>
                      <span class="text-sm text-gray-500">
                        {{ formatMessageDateTime(item.sent_at) }}
                      </span>
                    </div>

                    <div v-if="item.is_bulk" class="mb-2">
                      <div class="text-sm font-medium text-gray-900 mb-1">
                        宛先: {{ item.recipients?.length || 0 }}人
                        <span
                          v-if="item.read_count !== undefined"
                          class="ml-2 text-emerald-600"
                        >
                          既読 {{ item.read_count }}/{{
                            item.recipients?.length || 0
                          }}
                        </span>
                      </div>
                      <div class="flex flex-wrap gap-2 mt-1">
                        <div
                          v-for="recipient in item.recipients || []"
                          :key="recipient.id"
                          class="flex items-center gap-1 text-xs"
                        >
                          <span
                            class="px-2 py-1 rounded"
                            :class="
                              recipient.is_read
                                ? 'bg-emerald-100 text-emerald-700'
                                : 'bg-gray-100 text-gray-600'
                            "
                          >
                            {{ recipient.name }}
                            <span v-if="recipient.is_read" class="ml-1">✓</span>
                          </span>
                        </div>
                      </div>
                    </div>

                    <div v-else class="mb-2">
                      <div class="text-sm font-medium text-gray-900">
                        {{ item.recipient_name }}
                        <span
                          v-if="item.is_read"
                          class="ml-2 text-emerald-600 text-xs"
                          >既読</span
                        >
                      </div>
                    </div>

                    <p class="text-sm text-gray-700 whitespace-pre-wrap">
                      {{ item.text_content }}
                    </p>
                  </div>

                  <button
                    v-if="!item.is_bulk"
                    class="ml-4 px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                    @click="openChatFromHistory(item)"
                  >
                    チャット
                  </button>
                </div>
              </div>
            </div>

            <!-- 追加読み込み中の表示 -->
            <div v-if="messageHistoryLoadingMore" class="p-4 text-center">
              <div
                class="h-8 w-8 mx-auto border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"
              />
              <p class="text-sm text-gray-600 mt-2">さらに読み込み中...</p>
            </div>

            <!-- 全て読み込み完了の表示 -->
            <div
              v-if="
                !messageHistoryHasMore &&
                messageHistoryItems.length > 0 &&
                !messageHistoryLoadingMore
              "
              class="p-4 text-center text-sm text-gray-500"
            >
              すべての履歴を表示しました
            </div>
          </div>
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
  unread_messages_count?: number; // 未読メッセージ数
  role?: unknown;
  joined_at?: unknown;
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

// 送信履歴アイテムの型定義
interface MessageHistoryItem {
  id: string;
  text_content: string;
  sent_at: string;
  is_bulk: boolean;
  is_read?: boolean;
  read_count?: number;
  recipient_name?: string;
  recipient_id?: number;
  chat_room_id?: number;
  recipients?: Array<{
    id: number;
    name: string;
    is_read: boolean;
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
const groupIsComposing = ref(false);
const groupMessageContainerRef = ref<HTMLElement | null>(null);
const groupTextareaRef = ref<HTMLTextAreaElement | null>(null);

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

// 日本語入力の変換状態を管理
const isComposing = ref(false);

// メッセージコンテナの参照
const messageContainerRef = ref<HTMLElement | null>(null);

// ソート・検索・ページネーション機能
const {
  keyword,
  sortKey,
  sortOrder,
  showOnlyUnread,
  page,
  totalPages,
  paginatedItems,
  next,
  prev,
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
} = useSortableMembers(members as any, 50);

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
    showOnlyUnread.value ||
    page.value !== 1 ||
    selectedMemberIds.value.length > 0
  );
});

// グループ送信ボタンの無効状態を判定
const isGroupSendButtonDisabled = computed(() => {
  return (
    groupSending.value ||
    (!groupNewMessage.value.trim() && !groupIsComposing.value)
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

const adjustGroupTextareaHeight = () => {
  const textarea = groupTextareaRef.value;
  if (!textarea) return;

  // 高さをリセットして正確な scrollHeight を取得
  textarea.style.height = "auto";

  // 最大行数を設定（例：7行）
  const maxHeight = 168; // 約7行分の高さ
  const newHeight = Math.min(textarea.scrollHeight, maxHeight);

  textarea.style.height = newHeight + "px";
};

const sendGroupMessage = async () => {
  if (!groupNewMessage.value.trim() || !group.value?.room_token) {
    return;
  }

  groupSending.value = true;
  const messageText = groupNewMessage.value;
  try {
    const sentMessage = await groupConversations.sendMessage(
      group.value.room_token,
      messageText
    );

    if (sentMessage) {
      groupMessages.value.push(sentMessage);
    }

    groupNewMessage.value = "";
    // テキストエリアの高さを初期状態に戻す
    nextTick(() => {
      if (groupTextareaRef.value) {
        groupTextareaRef.value.style.height = "auto";
      }
    });
    await scrollGroupToBottom("smooth");

    // グループメッセージ送信後にメンバーリストを再読み込み（念のため）
    await loadMembers();
  } catch (error) {
    console.error("グループメッセージ送信エラー:", error);
    groupMessagesError.value = "メッセージの送信に失敗しました";
    await loadGroupMessages();
  } finally {
    groupSending.value = false;
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
  groupPending.value = true;
  groupError.value = "";

  try {
    const groupData = await groupConversations.getGroup(id);
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
      // アクティブなメンバーのみをフィルタリング
      members.value = allMembers
        .filter((member) => member.is_active)
        .map((member) => ({
          id: member.id!,
          name: member.name,
          friend_id: member.friend_id!,
          group_member_label: member.group_member_label,
          owner_nickname: member.owner_nickname,
          unread_messages_count: member.unread_messages_count as number, // 未読メッセージ数を追加
        }));
    } else {
      // 一般メンバーの場合は通常のメンバー情報を取得
      const memberData = await groupConversations.getGroupMembers(id);
      members.value = memberData.map((member) => ({
        id: member.id!,
        name: member.name,
        friend_id: member.friend_id!,
        group_member_label: member.group_member_label,
        role: member.role,
        joined_at: member.joined_at,
        unread_messages_count: member.unread_messages_count as number, // 未読メッセージ数を追加
      }));
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
    const currentPageIds = (paginatedItems.value as GroupMember[]).map(
      (m) => m.id
    );
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
    const currentPageIds = (paginatedItems.value as GroupMember[]).map(
      (m) => m.id
    );
    selectedMemberIds.value = selectedMemberIds.value.filter(
      (id) => !currentPageIds.includes(id)
    );
  }
};

// 選択状態の監視（現在のページのメンバーに基づく）
watch(
  [selectedMemberIds, paginatedItems],
  ([newSelected, currentPageItems]) => {
    const typedItems = currentPageItems as GroupMember[];
    selectAll.value =
      typedItems.length > 0 &&
      typedItems.every((item) => newSelected.includes(item.id));
  },
  { deep: true }
);

// 検索キーワードや未読フィルタが変更されたらページを1にリセット
watch([keyword, showOnlyUnread], () => {
  page.value = 1;
});

// フィルター・ソート・選択状態をリセットする関数
const resetFilters = () => {
  keyword.value = "";
  sortKey.value = "name";
  sortOrder.value = "asc";
  showOnlyUnread.value = false;
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

    // デバッグ：取得したメッセージデータを確認
    console.log("取得したメッセージデータ:", data);
    console.log("チャットルームタイプ:", currentConversation.value.type);
    data.data.forEach((msg: GroupMessage) => {
      if (msg.sender_id === authStore.user?.id) {
        console.log(`自分のメッセージ[ID:${msg.id}] is_read:`, msg.is_read);
      }
    });

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
    if (isGroupOwner.value && message.sender) {
      const memberWithNickname = members.value.find(
        (m) => m.id === message.sender!.id
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

// 送信履歴モーダルの状態
const showMessageHistory = ref(false);
const messageHistoryItems = ref<MessageHistoryItem[]>([]);
const messageHistoryLoading = ref(false);
const messageHistoryError = ref("");
const messageHistoryPage = ref(1);
const messageHistoryHasMore = ref(true);
const messageHistoryLoadingMore = ref(false);
const allMessageHistoryData = ref<MessageHistoryItem[]>([]);

// テスト用に5件、本番は20件
const HISTORY_ITEMS_PER_PAGE = 5; // 本番では20に変更

// 送信履歴を読み込む
const loadMessageHistory = async (isLoadMore = false) => {
  if (!isLoadMore) {
    messageHistoryLoading.value = true;
    messageHistoryPage.value = 1;
    messageHistoryHasMore.value = true;
    allMessageHistoryData.value = [];
  } else {
    messageHistoryLoadingMore.value = true;
  }

  messageHistoryError.value = "";

  try {
    // 初回のみ全データを取得
    if (allMessageHistoryData.value.length === 0) {
      const processedMessageIds = new Set<number>();
      const bulkMessages = new Map<string, MessageHistoryItem>();

      // 各メンバーとの個別チャットを確認
      for (const member of members.value) {
        try {
          const { api } = useApi();
          const conversation = await api<MemberConversation>(
            `/conversations/groups/${id}/member-chat`,
            {
              method: "POST",
              body: { target_user_id: member.id },
            }
          );

          if (conversation?.room_token) {
            // メッセージを取得
            const messagesData = await groupConversations.getMessages(
              conversation.room_token
            );

            // 自分が送信したメッセージのみをフィルタリング
            const myMessages = messagesData.data.filter(
              (msg: GroupMessage) => msg.sender_id === authStore.user?.id
            );

            // 各メッセージを履歴アイテムとして追加
            for (const msg of myMessages) {
              if (!processedMessageIds.has(msg.id)) {
                processedMessageIds.add(msg.id);

                // 同じ内容・同じ時刻のメッセージがあるか確認（一斉送信の可能性）
                const messageKey = `${msg.text_content}_${msg.sent_at}`;

                if (bulkMessages.has(messageKey)) {
                  // 既存の一斉送信メッセージに受信者を追加
                  const bulkItem = bulkMessages.get(messageKey)!;
                  bulkItem.is_bulk = true;
                  if (!bulkItem.recipients) {
                    bulkItem.recipients = [];
                  }
                  bulkItem.recipients.push({
                    id: member.id,
                    name: member.owner_nickname || member.name,
                    is_read: msg.is_read || false,
                  });
                  // 既読数を更新
                  bulkItem.read_count = bulkItem.recipients.filter(
                    (r) => r.is_read
                  ).length;
                } else {
                  // 新しいメッセージアイテムを作成
                  const historyItem: MessageHistoryItem = {
                    id: `msg-${msg.id}`,
                    text_content: msg.text_content || "",
                    sent_at: msg.sent_at || new Date().toISOString(),
                    is_bulk: false,
                    is_read: msg.is_read || false,
                    recipient_name: member.owner_nickname || member.name,
                    recipient_id: member.id,
                    chat_room_id: conversation.id,
                    recipients: [
                      {
                        id: member.id,
                        name: member.owner_nickname || member.name,
                        is_read: msg.is_read || false,
                      },
                    ],
                    read_count: msg.is_read ? 1 : 0,
                  };

                  bulkMessages.set(messageKey, historyItem);
                }
              }
            }
          }
        } catch (error) {
          console.error(`メンバー${member.id}との履歴取得エラー:`, error);
        }
      }

      // Map から配列に変換
      const allItems = Array.from(bulkMessages.values());

      // 単一メッセージの場合はrecipientsを削除
      allItems.forEach((item) => {
        if (!item.is_bulk && item.recipients && item.recipients.length === 1) {
          delete item.recipients;
          delete item.read_count;
        }
      });

      // 送信日時で降順ソート（新しいものから）
      allItems.sort(
        (a, b) => new Date(b.sent_at).getTime() - new Date(a.sent_at).getTime()
      );

      allMessageHistoryData.value = allItems;
    }

    // ページネーション処理
    const startIndex = (messageHistoryPage.value - 1) * HISTORY_ITEMS_PER_PAGE;
    const endIndex = startIndex + HISTORY_ITEMS_PER_PAGE;
    const pageItems = allMessageHistoryData.value.slice(startIndex, endIndex);

    if (isLoadMore) {
      messageHistoryItems.value = [...messageHistoryItems.value, ...pageItems];
    } else {
      messageHistoryItems.value = pageItems;
    }

    // 次のページがあるかチェック
    messageHistoryHasMore.value = endIndex < allMessageHistoryData.value.length;
  } catch (error) {
    console.error("送信履歴取得エラー:", error);
    messageHistoryError.value = "送信履歴の取得に失敗しました";
  } finally {
    messageHistoryLoading.value = false;
    messageHistoryLoadingMore.value = false;
  }
};

// 追加読み込み
const loadMoreHistory = async () => {
  if (!messageHistoryHasMore.value || messageHistoryLoadingMore.value) return;

  messageHistoryPage.value++;
  await loadMessageHistory(true);
};

// 送信履歴からチャットを開く
const openChatFromHistory = async (item: MessageHistoryItem) => {
  if (!item.recipient_id) return;

  const member = members.value.find((m) => m.id === item.recipient_id);
  if (member) {
    showMessageHistory.value = false;
    await startChatWithMember(member);
  }
};

// 日時フォーマット
const formatMessageDateTime = (sentAt?: string | null): string => {
  if (!sentAt) return "";
  const date = new Date(sentAt);
  return `${date.getFullYear()}/${String(date.getMonth() + 1).padStart(
    2,
    "0"
  )}/${String(date.getDate()).padStart(2, "0")} ${date.toLocaleTimeString(
    "ja-JP",
    {
      hour: "numeric",
      minute: "2-digit",
      hour12: false,
    }
  )}`;
};

// 無限スクロールの監視設定
const handleHistoryScroll = (event: Event) => {
  const target = event.target as HTMLElement;
  const scrollBottom =
    target.scrollHeight - target.scrollTop - target.clientHeight;

  console.log("Scroll position:", {
    scrollBottom,
    hasMore: messageHistoryHasMore.value,
    loading: messageHistoryLoadingMore.value,
  });

  // 下部から100px以内にスクロールしたら追加読み込み
  if (
    scrollBottom < 100 &&
    messageHistoryHasMore.value &&
    !messageHistoryLoadingMore.value
  ) {
    loadMoreHistory();
  }
};

// モーダルが開いた時にスクロールイベントを設定
watch(showMessageHistory, async (isVisible) => {
  if (isVisible) {
    messageHistoryItems.value = [];
    allMessageHistoryData.value = [];
    await loadMessageHistory();

    // スクロールイベントの設定
    await nextTick();
    const scrollContainer = document.querySelector(".history-scroll-container");
    if (scrollContainer) {
      scrollContainer.addEventListener("scroll", handleHistoryScroll);
    }
  } else {
    // モーダルが閉じたらイベントをクリーンアップ
    const scrollContainer = document.querySelector(".history-scroll-container");
    if (scrollContainer) {
      scrollContainer.removeEventListener("scroll", handleHistoryScroll);
    }
  }
});

// 初回読み込み
await refresh();
</script>
