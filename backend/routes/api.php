<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\FriendshipController;
use App\Http\Controllers\API\ConversationsController;
use App\Http\Controllers\API\MessagesController;

Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify', [AuthController::class, 'verifyEmail']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');

// 認証済みユーザーのみアクセス可能なエンドポイント
Route::middleware('auth:sanctum')->group(function () {
  // 現在のユーザー情報を取得
  Route::get('/users/me', [AuthController::class, 'getCurrentUser']);
  // ユーザー名を更新
  Route::put('/user/update-name', [AuthController::class, 'updateName']);

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
    Route::get('/{conversation}', 'show'); // 特定の会話情報を取得 (詳細表示用、任意)
    Route::post('/{conversation}/read', 'markAsRead'); // 既読にする

    // 特定の会話のメッセージ関連
    Route::prefix('{conversation}/messages')->controller(MessagesController::class)->group(function () {
      Route::get('/', 'index'); // メッセージ一覧
      Route::post('/', 'store'); // メッセージ送信
    });
  });
});

// 既存のユーザー情報取得エンドポイント
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});
