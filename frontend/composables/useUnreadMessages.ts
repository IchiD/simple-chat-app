import { ref, computed, readonly } from "vue";
import { useApi } from "./useApi";
import { useAuthStore } from "~/stores/auth";

type ConversationSummary = {
  id: number;
  unread_messages_count: number;
};

type ConversationsResponse = {
  data: ConversationSummary[];
};

export const useUnreadMessages = () => {
  const hasUnreadMessages = ref(false);
  const totalUnreadCount = ref(0);
  const isLoading = ref(false);
  const error = ref<Error | null>(null);

  const authStore = useAuthStore();
  const { api } = useApi();

  const checkUnreadMessages = async () => {
    if (!authStore.isAuthenticated || !authStore.token) {
      hasUnreadMessages.value = false;
      totalUnreadCount.value = 0;
      return;
    }

    try {
      isLoading.value = true;
      error.value = null;

      const response = await api<ConversationsResponse>(
        "/conversations?per_page=100"
      );

      const conversations = response.data || [];
      const totalUnread = conversations.reduce(
        (sum, conv) => sum + (conv.unread_messages_count || 0),
        0
      );

      hasUnreadMessages.value = totalUnread > 0;
      totalUnreadCount.value = totalUnread;
    } catch (err) {
      error.value = err instanceof Error ? err : new Error("Unknown error");
      console.error("未読メッセージ取得エラー:", err);
      hasUnreadMessages.value = false;
      totalUnreadCount.value = 0;
    } finally {
      isLoading.value = false;
    }
  };

  // 認証状態に基づく未読メッセージの有無
  const shouldShowBadge = computed(() => {
    return authStore.isAuthenticated && hasUnreadMessages.value;
  });

  return {
    hasUnreadMessages: readonly(hasUnreadMessages),
    totalUnreadCount: readonly(totalUnreadCount),
    shouldShowBadge,
    isLoading: readonly(isLoading),
    error: readonly(error),
    checkUnreadMessages,
  };
};
