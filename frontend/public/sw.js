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
      url: data.url || "/chat",
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
    const url = event.notification.data?.url || "/chat";
    
    event.waitUntil(
      self.clients
        .matchAll({ type: "window", includeUncontrolled: true })
        .then((clients) => {
          // 既存のウィンドウがある場合はフォーカス
          for (const client of clients) {
            if (client.url.includes(url) && "focus" in client) {
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
