// サービスワーカーのバージョン管理
const SW_VERSION = "1.0.0";

// インストール時の処理
self.addEventListener("install", (event) => {
  console.log(`[Service Worker] Installing version ${SW_VERSION}`);
  // 即時アクティベーション
  self.skipWaiting();
});

// アクティベーション時の処理
self.addEventListener("activate", (event) => {
  console.log(`[Service Worker] Activated version ${SW_VERSION}`);
  // 即時制御権取得
  event.waitUntil(clients.claim());
});

// プッシュ通知受信時の処理
self.addEventListener("push", (event) => {
  if (!event.data) {
    console.log("[Service Worker] Push received but no data");
    return;
  }

  try {
    const data = event.data.json();
    console.log("[Service Worker] Push received:", data);

    const options = {
      body: data.body || "メッセージが届きました",
      icon: data.icon || "/favicon.ico",
      badge: data.badge || "/favicon.ico",
      data: data.data || {},
      tag: data.tag || "default",
      requireInteraction: data.requireInteraction || false,
      actions: data.actions || [],
    };

    event.waitUntil(
      self.registration.showNotification(
        data.title || "チャットアプリ通知",
        options
      )
    );
  } catch (error) {
    console.error("[Service Worker] Error processing push event:", error);
  }
});

// 通知クリック時の処理
self.addEventListener("notificationclick", (event) => {
  console.log("[Service Worker] Notification click received", event);

  event.notification.close();

  // カスタムデータから遷移先URLを取得
  const urlToOpen = event.notification.data.url || "/";

  // アプリを開く、または既存のウィンドウにフォーカスする
  event.waitUntil(
    clients
      .matchAll({ type: "window", includeUncontrolled: true })
      .then((clientList) => {
        // 既に開いているウィンドウがあればそれにフォーカス
        for (const client of clientList) {
          if (client.url.includes(self.location.origin) && "focus" in client) {
            client.navigate(urlToOpen);
            return client.focus();
          }
        }
        // なければ新しいウィンドウを開く
        if (clients.openWindow) {
          return clients.openWindow(urlToOpen);
        }
      })
  );
});
