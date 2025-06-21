import { ref, computed, readonly } from "vue";
import { useApi } from "./useApi";
import { useAuthStore } from "~/stores/auth";

type GroupSummary = {
  id: number;
  name: string;
  unread_messages_count?: number;
  role?: string;
  // その他のプロパティ...
};

export const useGroupUnreadMessages = () => {
  const hasUnreadGroupMessages = ref(false);
  const totalGroupUnreadCount = ref(0);
  const isLoading = ref(false);
  const error = ref<Error | null>(null);

  const authStore = useAuthStore();
  const { api } = useApi();

  const checkGroupUnreadMessages = async () => {
    if (!authStore.isAuthenticated || !authStore.token) {
      hasUnreadGroupMessages.value = false;
      totalGroupUnreadCount.value = 0;
      return;
    }

    try {
      isLoading.value = true;
      error.value = null;

      // グループ一覧を取得
      const groups = await api<GroupSummary[]>("/conversations/groups");

      // デバッグ用ログ
      console.log("グループ一覧取得結果:", groups);
      console.log("グループ数:", groups.length);
      if (groups.length > 0) {
        console.log("最初のグループの構造:", groups[0]);
      }

      // オーナーのグループのみをフィルタ（roleがownerのもの）
      const ownedGroups = groups.filter((group) => group.role === "owner");

      console.log("オーナーグループ数:", ownedGroups.length);

      // 未読メッセージ数を集計
      const totalUnread = ownedGroups.reduce(
        (sum, group) => sum + (group.unread_messages_count || 0),
        0
      );

      console.log("合計未読メッセージ数:", totalUnread);

      hasUnreadGroupMessages.value = totalUnread > 0;
      totalGroupUnreadCount.value = totalUnread;
    } catch (err) {
      error.value = err instanceof Error ? err : new Error("Unknown error");
      console.error("グループ未読メッセージ取得エラー:", err);
      hasUnreadGroupMessages.value = false;
      totalGroupUnreadCount.value = 0;
    } finally {
      isLoading.value = false;
    }
  };

  // 認証状態とプレミアムプランに基づくグループバッジの表示判定
  const shouldShowGroupBadge = computed(() => {
    return (
      authStore.isAuthenticated &&
      authStore.user?.plan &&
      authStore.user.plan !== "free" &&
      hasUnreadGroupMessages.value
    );
  });

  return {
    hasUnreadGroupMessages: readonly(hasUnreadGroupMessages),
    totalGroupUnreadCount: readonly(totalGroupUnreadCount),
    shouldShowGroupBadge,
    isLoading: readonly(isLoading),
    error: readonly(error),
    checkGroupUnreadMessages,
  };
};
