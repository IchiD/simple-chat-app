<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\FriendshipController;
use App\Http\Controllers\API\ConversationsController;
use App\Http\Controllers\API\MessagesController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\AppConfigController;

Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify', [AuthController::class, 'verifyEmail']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');

// アプリケーション設定情報取得
Route::get('/config', [AppConfigController::class, 'getPublicConfig']);

// 認証済みユーザーのみアクセス可能なエンドポイント
Route::middleware('auth:sanctum')->group(function () {
  // 現在のユーザー情報を取得
  Route::get('/users/me', [AuthController::class, 'getCurrentUser']);
  // ユーザー名を更新
  Route::put('/user/update-name', [AuthController::class, 'updateName']);

  // パスワードを更新
  Route::put('/user/update-password', [AuthController::class, 'updatePassword']);

  // 友達関係のAPI
  Route::prefix('friends')->group(function () {
    // 友達一覧
    Route::get('/', [FriendshipController::class, 'getFriends']);

    // 友達申請の一覧
    Route::get('/requests/sent', [FriendshipController::class, 'getSentRequests']);
    Route::get('/requests/received', [FriendshipController::class, 'getReceivedRequests']);

    // フレンドIDでユーザーを検索
    Route::post('/search', [FriendshipController::class, 'searchByFriendId']);

    // 友達申請の送信
    Route::post('/request', [FriendshipController::class, 'sendRequest']);

    // 友達申請の承認/拒否
    Route::post('/accept', [FriendshipController::class, 'acceptRequest']);
    Route::post('/reject', [FriendshipController::class, 'rejectRequest']);

    // 友達削除
    Route::delete('/unfriend', [FriendshipController::class, 'unfriend']);
    // 送信済み申請の取り消し (FriendshipController にメソッド追加が必要)
    Route::delete('/cancel/{requestId}', [FriendshipController::class, 'cancelSentRequest']);
  });

  // チャット機能のAPI (会話とメッセージ)
  Route::prefix('conversations')->controller(ConversationsController::class)->group(function () {
    Route::get('/', 'index'); // ユーザーの会話一覧
    Route::post('/', 'store'); // 新規会話開始
    Route::get('/token/{room_token}', 'showByToken'); // トークンで特定の会話情報を取得
    Route::post('/{conversation}/read', 'markAsRead'); // idでの既読処理も残すか検討

    // 特定の会話のメッセージ関連 (room_token を使用)
    Route::prefix('room/{conversation:room_token}/messages')->controller(MessagesController::class)->group(function () {
      Route::get('/', 'index'); // メッセージ一覧
      Route::post('/', 'store'); // メッセージ送信
    });
  });

  // プッシュ通知関連のAPI
  Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
    Route::post('/subscribe', 'subscribe'); // プッシュ通知の購読登録
    Route::post('/unsubscribe', 'unsubscribe'); // プッシュ通知の購読解除
    Route::post('/test', 'sendTestNotification'); // テスト通知の送信（開発用）
  });

  // メールアドレス変更関連
  Route::put('/user/update-email', [AuthController::class, 'requestEmailChange']);
});

// メールアドレス変更確認（認証不要）
Route::get('/verify-email-change', [AuthController::class, 'confirmEmailChange']);

// 既存のユーザー情報取得エンドポイント
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});
