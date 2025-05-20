import { reactive } from "vue";

type ToastType = "success" | "error" | "info" | "warning";

interface Toast {
  id: number;
  title: string;
  description?: string;
  color: ToastType;
  timeout?: number;
}

// グローバルなトースト通知ストア
const toasts = reactive<Toast[]>([]);
let nextId = 0;

export function useToast() {
  // トースト通知を追加する
  const add = (toast: Omit<Toast, "id">) => {
    const id = nextId++;
    const newToast = {
      ...toast,
      id,
      timeout: toast.timeout || 5000, // デフォルトは5秒
    };

    toasts.push(newToast);

    // 自動的に一定時間後に削除
    setTimeout(() => {
      remove(id);
    }, newToast.timeout);

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
    clear,
  };
}
