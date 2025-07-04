import { reactive } from "vue";

export interface Toast {
  id: number;
  title: string;
  description?: string;
  color?: "success" | "error" | "info" | "warning";
  duration?: number;
  persistent?: boolean;
  timeout?: number;
}

// グローバルなトースト通知ストア
const toasts = reactive<Toast[]>([]);
let nextId = 0;

export const useToastStore = () => {
  // トースト通知を追加する
  const add = (toast: Omit<Toast, "id">) => {
    const id = nextId++;
    const newToast = {
      ...toast,
      id,
      timeout: toast.duration || 8000, // デフォルトを8秒に変更
    };

    toasts.push(newToast);

    // 自動的に一定時間後に削除
    if (toast.duration !== undefined && !toast.persistent) {
      setTimeout(() => {
        remove(id);
      }, toast.duration);
    }

    return id;
  };

  // トースト通知を削除する
  const remove = (id: number) => {
    const index = toasts.findIndex((toast) => toast.id === id);
    if (index !== -1) {
      toasts.splice(index, 1);
    }
  };

  // すべてのトースト通知をクリアする
  const clear = () => {
    toasts.splice(0, toasts.length);
  };

  return {
    toasts,
    add,
    remove,
    removeToast: remove, // 互換性のためのエイリアス
    clear,
  };
};

// クライアントコンポーネントでの簡単な使用のためのエクスポート
export const useToast = () => {
  return useToastStore();
};
