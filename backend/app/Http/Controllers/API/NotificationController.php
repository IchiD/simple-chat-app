<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\PushNotification;
use NotificationChannels\WebPush\PushSubscription;

class NotificationController extends Controller
{
  /**
   * ユーザーのプッシュ通知購読情報を保存
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function subscribe(Request $request): JsonResponse
  {
    $request->validate([
      'subscription' => 'required|array',
      'subscription.endpoint' => 'required|string',
      'subscription.keys' => 'required|array',
      'subscription.keys.p256dh' => 'required|string',
      'subscription.keys.auth' => 'required|string',
    ]);

    $user = Auth::user();

    // 既存の購読を探す
    $subscription = PushSubscription::findByEndpoint(
      $request->input('subscription.endpoint')
    );

    // 既存の購読がある場合は更新、なければ新規追加
    if ($subscription) {
      $subscription->public_key = $request->input('subscription.keys.p256dh');
      $subscription->auth_token = $request->input('subscription.keys.auth');
      $subscription->save();
    } else {
      $user->updatePushSubscription(
        $request->input('subscription.endpoint'),
        $request->input('subscription.keys.p256dh'),
        $request->input('subscription.keys.auth')
      );
    }

    return response()->json([
      'success' => true,
      'message' => 'プッシュ通知の購読が登録されました'
    ]);
  }

  /**
   * プッシュ通知の購読を解除
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function unsubscribe(Request $request): JsonResponse
  {
    $request->validate([
      'endpoint' => 'required|string',
    ]);

    $deleted = PushSubscription::findByEndpoint(
      $request->input('endpoint')
    )?->delete();

    return response()->json([
      'success' => true,
      'message' => 'プッシュ通知の購読が解除されました'
    ]);
  }

  /**
   * テスト通知を送信 (開発用)
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function sendTestNotification(): JsonResponse
  {
    $user = Auth::user();

    // テスト通知の作成と送信
    $notification = new PushNotification(
      'テスト通知',
      'これはテスト通知です。',
      [
        'url' => '/chat',
        'timestamp' => now()->timestamp
      ],
      [
        'tag' => 'test-notification',
        'requireInteraction' => true
      ]
    );

    $user->notify($notification);

    return response()->json([
      'success' => true,
      'message' => 'テスト通知が送信されました'
    ]);
  }

  /**
   * 新しいメッセージの通知を送信
   *
   * @param User $recipient 受信者
   * @param string $senderName 送信者名
   * @param string $messagePreview メッセージプレビュー
   * @param int $roomId チャットルームID
   * @return void
   */
  public function sendNewMessageNotification(User $recipient, string $senderName, string $messagePreview, int $roomId): void
  {
    $notification = new PushNotification(
      $senderName . 'からのメッセージ',
      $messagePreview,
      [
        'url' => '/chat/' . $roomId,
        'type' => 'new_message',
        'room_id' => $roomId,
        'timestamp' => now()->timestamp
      ],
      [
        'tag' => 'chat-' . $roomId,
        'requireInteraction' => true
      ]
    );

    $recipient->notify($notification);
  }

  /**
   * フレンド申請の通知を送信
   *
   * @param User $recipient 受信者
   * @param string $senderName 送信者名
   * @return void
   */
  public function sendFriendRequestNotification(User $recipient, string $senderName): void
  {
    $notification = new PushNotification(
      'フレンド申請',
      $senderName . 'さんからフレンド申請が届きました',
      [
        'url' => '/friends',
        'type' => 'friend_request',
        'timestamp' => now()->timestamp
      ],
      [
        'tag' => 'friend-request',
        'requireInteraction' => true
      ]
    );

    $recipient->notify($notification);
  }
}
