import { ref } from "vue";
import { useApi } from "./useApi";
import { useToast } from "./useToast";

// 通知の状態を管理するインターフェース
export interface PushNotificationState {
  isSupported: boolean;
  isSubscribed: boolean;
  subscription: PushSubscription | null;
  permissionState: NotificationPermission | null;
  isPending: boolean;
  error: string | null;
}

export function usePushNotification() {
  const { api } = useApi();
  const toast = useToast();

  // 状態管理
  const state = ref<PushNotificationState>({
    isSupported: false,
    isSubscribed: false,
    subscription: null,
    permissionState: null,
    isPending: false,
    error: null,
  });

  // ApplicationServerKeyの公開鍵（APIから取得するため初期値は空）
  let applicationServerPublicKey = "";

  // サーバーから公開鍵を取得
  const getPublicKey = async (): Promise<string> => {
    if (applicationServerPublicKey) {
      return applicationServerPublicKey;
    }

    try {
      const config = await api<{ vapid: { publicKey: string } }>("/config");
      applicationServerPublicKey = config.vapid.publicKey;

      if (!applicationServerPublicKey) {
        console.warn(
          "VAPID公開鍵が設定されていません。プッシュ通知は無効です。"
        );
        state.value.isSupported = false;
        return "";
      }

      return applicationServerPublicKey;
    } catch (error) {
      console.warn("VAPID公開鍵取得エラー（プッシュ通知は無効）:", error);
      state.value.isSupported = false;
      state.value.error = null; // エラーメッセージを表示しない
      return "";
    }
  };

  // 初期化関数
  const initialize = async () => {
    if (!("serviceWorker" in navigator) || !("PushManager" in window)) {
      state.value.isSupported = false;
      state.value.error =
        "このブラウザはプッシュ通知をサポートしていません。最新のChrome、Firefox、Edge、Safariをご利用ください。プライベートブラウジングモードではご利用いただけません。";
      return false;
    }

    state.value.isSupported = true;
    state.value.permissionState = Notification.permission;

    // サービスワーカーの登録状態を確認
    try {
      // 公開鍵を取得（失敗した場合は早期リターン）
      try {
        const publicKey = await getPublicKey();
        if (!publicKey) {
          // VAPID公開鍵が設定されていない場合はプッシュ通知を無効にする
          return false;
        }
      } catch {
        // VAPID設定エラーの場合はプッシュ通知を無効にする
        return false;
      }

      const registration = await registerServiceWorker();
      if (!registration) return false;

      // 通知の許可状態を確認
      if (state.value.permissionState === "denied") {
        state.value.error =
          "通知の許可がブロックされています。ブラウザの設定から許可してください。";
        return false;
      }

      // 購読状態を確認
      await checkSubscription(registration);
      return true;
    } catch (error) {
      console.error("初期化エラー:", error);
      state.value.error =
        "通知機能の初期化中にエラーが発生しました。最新のブラウザを使用しているか、プライベートブラウジングモードになっていないか確認してください。";
      return false;
    }
  };

  // サービスワーカー登録
  const registerServiceWorker =
    async (): Promise<ServiceWorkerRegistration | null> => {
      try {
        const registration = await navigator.serviceWorker.register("/sw.js");
        return registration;
      } catch {
        state.value.error = "サービスワーカーの登録に失敗しました";
        return null;
      }
    };

  // 購読状態の確認
  const checkSubscription = async (registration: ServiceWorkerRegistration) => {
    try {
      const subscription = await registration.pushManager.getSubscription();
      state.value.subscription = subscription;
      state.value.isSubscribed = !!subscription;
      return !!subscription;
    } catch (error) {
      console.error("購読状態の確認エラー:", error);
      state.value.error = "購読状態の確認に失敗しました";
      return false;
    }
  };

  // urlBase64ToUint8Array関数
  const urlBase64ToUint8Array = (base64String: string): Uint8Array => {
    const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding)
      .replace(/-/g, "+")
      .replace(/_/g, "/");

    const rawData = atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
    }

    return outputArray;
  };

  // 通知を購読する
  const subscribe = async () => {
    state.value.isPending = true;
    state.value.error = null;

    try {
      // サービスワーカーの登録状態を確認
      const registration = await navigator.serviceWorker.ready;

      // 通知の許可を求める（必要な場合）
      if (Notification.permission === "default") {
        const permission = await Notification.requestPermission();
        state.value.permissionState = permission;

        if (permission !== "granted") {
          state.value.error = "通知の許可が得られませんでした";
          state.value.isPending = false;
          return false;
        }
      }

      // 公開鍵を取得
      const publicKey = await getPublicKey();

      // 既存の購読を解除（再購読の場合）
      const existingSubscription =
        await registration.pushManager.getSubscription();
      if (existingSubscription) {
        await existingSubscription.unsubscribe();
      }

      // 新規購読の作成
      const applicationServerKey = urlBase64ToUint8Array(publicKey);
      const newSubscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey,
      });

      // 購読情報をサーバーに送信
      await saveSubscription(newSubscription);

      state.value.subscription = newSubscription;
      state.value.isSubscribed = true;
      toast.add({
        title: "通知設定完了",
        description: "プッシュ通知を有効にしました",
        color: "success",
      });

      return true;
    } catch (error) {
      console.error("購読エラー:", error);
      state.value.error =
        "プッシュ通知の有効化処理中にエラーが発生しました。プッシュ通知は Chrome、Firefox、Edge、Safariなどの最新ブラウザで利用できます。また、プライベートブラウジングモードではご利用いただけません。";
      toast.add({
        title: "エラー",
        description:
          "プッシュ通知の設定に失敗しました。最新のChrome、Firefox、Edge、Safariをご利用ください。",
        color: "error",
      });

      return false;
    } finally {
      state.value.isPending = false;
    }
  };

  // 購読を解除する
  const unsubscribe = async () => {
    state.value.isPending = true;
    state.value.error = null;

    try {
      if (!state.value.subscription) {
        const registration = await navigator.serviceWorker.ready;
        state.value.subscription =
          await registration.pushManager.getSubscription();
      }

      if (!state.value.subscription) {
        state.value.isSubscribed = false;
        state.value.isPending = false;
        return true;
      }

      // サーバーから購読情報を削除
      await deleteSubscription(state.value.subscription);

      // ブラウザの購読を解除
      const result = await state.value.subscription.unsubscribe();

      state.value.subscription = null;
      state.value.isSubscribed = false;

      toast.add({
        title: "通知設定解除",
        description: "プッシュ通知を無効にしました",
        color: "success",
      });

      return result;
    } catch (error) {
      console.error("プッシュ通知の無効化エラー:", error);
      state.value.error = "プッシュ通知の無効化に失敗しました";

      toast.add({
        title: "エラー",
        description: "プッシュ通知の無効化に失敗しました",
        color: "error",
      });

      return false;
    } finally {
      state.value.isPending = false;
    }
  };

  // サーバーに購読情報を保存
  const saveSubscription = async (subscription: PushSubscription) => {
    try {
      const _response = await api("/notifications/subscribe", {
        method: "POST",
        body: {
          subscription: subscription.toJSON(),
        },
      });

      return true;
    } catch (error) {
      console.error("購読情報保存エラー:", error);
      throw error;
    }
  };

  // サーバーから購読情報を削除
  const deleteSubscription = async (subscription: PushSubscription) => {
    try {
      const _response = await api("/notifications/unsubscribe", {
        method: "POST",
        body: {
          endpoint: subscription.endpoint,
        },
      });

      return true;
    } catch (error) {
      console.error("購読情報削除エラー:", error);
      throw error;
    }
  };

  // テスト通知を送信（開発用）
  const sendTestNotification = async () => {
    try {
      const _response = await api("/notifications/test", {
        method: "POST",
      });

      return true;
    } catch (error) {
      console.error("テスト通知エラー:", error);
      return false;
    }
  };

  return {
    state,
    initialize,
    subscribe,
    unsubscribe,
    sendTestNotification,
  };
}
