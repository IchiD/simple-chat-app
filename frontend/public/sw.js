// サービスワーカーのバージョン管理
const SW_VERSION = "1.0.0";

// インストール時の処理
self.addEventListener("install", (event) => {
  event.waitUntil(self.skipWaiting());
});

// アクティベーション時の処理
self.addEventListener("activate", (event) => {
  event.waitUntil(self.clients.claim());
});

// プッシュ通知受信時の処理
self.addEventListener("push", function (event) {
  if (!event.data) {
    return;
  }

  let data;
  try {
    data = event.data.json();
  } catch (error) {
    return;
  }

  const options = {
    body: data.body,
    icon: "/icon-192x192.png",
    badge: "/icon-192x192.png",
    data: {
      url: data.url || "https://chat-app-frontend-sigma-puce.vercel.app/chat",
      room_token: data.room_token,
      type: data.type,
    },
    actions: [
      {
        action: "open",
        title: "開く",
      },
      {
        action: "close",
        title: "閉じる",
      },
    ],
    requireInteraction: true,
    tag: data.tag || "default",
  };

  try {
    event.waitUntil(self.registration.showNotification(data.title, options));
  } catch (error) {
    console.error("[Service Worker] Error processing push event:", error);
  }
});

// 通知クリック時の処理
self.addEventListener("notificationclick", function (event) {
  event.notification.close();

  if (event.action === "open" || event.action === "") {
    let url =
      event.notification.data?.url ||
      "https://chat-app-frontend-sigma-puce.vercel.app/chat";

    // 相対URLの場合は絶対URLに変換
    if (url.startsWith("/")) {
      url = "https://chat-app-frontend-sigma-puce.vercel.app" + url;
    }

    // 特定のチャットルームがある場合は、適切なページに遷移
    if (
      event.notification.data?.room_token &&
      event.notification.data?.type === "new_message"
    ) {
      url = `https://chat-app-frontend-sigma-puce.vercel.app/chat?room=${event.notification.data.room_token}`;
    }

    event.waitUntil(
      self.clients
        .matchAll({ type: "window", includeUncontrolled: true })
        .then((clients) => {
          // 既存のウィンドウがある場合
          for (const client of clients) {
            // チャットアプリのURLが含まれている場合
            if (
              client.url.includes("chat-app-frontend-sigma-puce.vercel.app") &&
              "focus" in client
            ) {
              // URLを更新してフォーカス
              client.navigate(url);
              return client.focus();
            }
          }

          // 既存のウィンドウがない場合は新しいウィンドウを開く
          if (self.clients.openWindow) {
            return self.clients.openWindow(url);
          }
        })
    );
  }
});
