import { ref, computed, readonly } from "vue";
import { useApi } from "./useApi";
import { useAuthStore } from "~/stores/auth";

type FriendRequest = {
  id: number;
  user: {
    id: number;
    name: string;
    friend_id: string;
  };
  friend: {
    id: number;
    name: string;
    friend_id: string;
  };
  message?: string;
  created_at: string;
  status: number;
};

type FriendRequestsResponse = {
  status: string;
  received_requests?: FriendRequest[];
};

export const useFriendRequests = () => {
  const hasPendingRequests = ref(false);
  const pendingRequestsCount = ref(0);
  const isLoading = ref(false);
  const error = ref<Error | null>(null);

  const authStore = useAuthStore();
  const { api } = useApi();

  const checkPendingRequests = async () => {
    if (!authStore.isAuthenticated || !authStore.token) {
      hasPendingRequests.value = false;
      pendingRequestsCount.value = 0;
      return;
    }

    try {
      isLoading.value = true;
      error.value = null;

      const response = await api<FriendRequestsResponse>(
        "/friends/requests/received"
      );

      const requests = response.received_requests || [];
      const count = requests.length;

      hasPendingRequests.value = count > 0;
      pendingRequestsCount.value = count;
    } catch (err) {
      error.value = err instanceof Error ? err : new Error("Unknown error");
      console.error("友達申請取得エラー:", err);
      hasPendingRequests.value = false;
      pendingRequestsCount.value = 0;
    } finally {
      isLoading.value = false;
    }
  };

  // 認証状態に基づく友達申請バッジの表示判定
  const shouldShowBadge = computed(() => {
    return authStore.isAuthenticated && hasPendingRequests.value;
  });

  return {
    hasPendingRequests: readonly(hasPendingRequests),
    pendingRequestsCount: readonly(pendingRequestsCount),
    shouldShowBadge,
    isLoading: readonly(isLoading),
    error: readonly(error),
    checkPendingRequests,
  };
};
