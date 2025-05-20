import { ref } from "vue";

export interface Toast {
  id: number;
  title: string;
  description?: string;
  color?: "success" | "error" | "info" | "warning";
  duration?: number;
  persistent?: boolean;
}

// シングルトンインスタンス用の状態
const toasts = ref<Toast[]>([]);
let nextId = 1;

export const useToastStore = () => {
  // トースト追加メソッド
  const add = (toast: Omit<Toast, "id">) => {
    const id = nextId++;
    toasts.value.push({
      id,
      ...toast,
      color: toast.color || "info",
    });
    return id;
  };

  // トースト削除メソッド
  const remove = (id: number) => {
    const index = toasts.value.findIndex((t) => t.id === id);
    if (index !== -1) {
      toasts.value.splice(index, 1);
    }
  };

  // トースト一括削除メソッド
  const clear = () => {
    toasts.value = [];
  };

  return {
    toasts,
    add,
    removeToast: remove,
    clear,
  };
};

// クライアントコンポーネントでの簡単な使用のためのエクスポート
export const useToast = () => {
  return useToastStore();
};
