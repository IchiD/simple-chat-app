<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Api\NotificationController;

class MessagesController extends Controller
{
  /**
   * 特定の会話のメッセージ一覧を取得する
   */
  public function index(Conversation $conversation, Request $request)
  {
    $user = Auth::user();

    // ユーザーがこの会話の参加者であることを確認
    if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
      return response()->json(['message' => 'アクセス権がありません。'], 403);
    }

    // ダイレクトメッセージの場合、友達関係を確認
    if ($conversation->type === 'direct') {
      // 会話の相手を取得
      $otherParticipant = $conversation->participants()
        ->where('users.id', '!=', $user->id)
        ->first();

      if ($otherParticipant) {
        // 友達関係を確認
        $currentFriends = $user->friends()->pluck('id')->toArray();
        if (!in_array($otherParticipant->id, $currentFriends)) {
          return response()->json([
            'message' => '友達関係が解除されたため、このチャットにアクセスできません。',
            'friendship_status' => 'unfriended'
          ], 403);
        }
      }
    }

    $messages = $conversation->messages()
      ->with(['sender' => function ($query) {
        $query->select('id', 'name', 'avatar', 'friend_id'); // 送信者の基本情報を選択
      }])
      ->orderBy('sent_at', 'desc') // 最新のメッセージから表示
      ->paginate(20); // ページネーション

    // メッセージを取得後、この会話を既読にする (オプション的な動作)
    // もしフロントエンド側で明示的に既読APIを叩く場合は不要
    $participant = $conversation->conversationParticipants()->where('user_id', $user->id)->first();
    if ($participant && $messages->isNotEmpty()) {
      $lastMessageOnPage = $messages->first(); // 現在のページの最新メッセージ
      // last_read_at の更新は、最新メッセージを見たことを示すため、常に更新しても良い
      // last_read_message_id は、そのページで最も新しいメッセージIDとするか、会話全体の最新にするか検討
      $participant->update([
        // 'last_read_message_id' => $lastMessageOnPage->id, 
        'last_read_at' => now(),
      ]);
    }

    return response()->json($messages);
  }

  /**
   * 新しいメッセージを送信する
   */
  public function store(Conversation $conversation, Request $request)
  {
    $user = Auth::user();

    // ユーザーがこの会話の参加者であることを確認
    if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
      return response()->json(['message' => 'この会話にメッセージを送信する権限がありません。'], 403);
    }

    $request->validate([
      'text_content' => 'required|string|max:5000', // 最大文字数など適宜調整
      // 'content_type' => 'sometimes|in:text,image,file' // 将来的な拡張用
    ]);

    $message = $conversation->messages()->create([
      'sender_id' => $user->id,
      'text_content' => $request->input('text_content'),
      'content_type' => 'text', // MVPではtext固定
      'sent_at' => now(),
    ]);

    $message->load(['sender' => function ($query) {
      $query->select('id', 'name', 'avatar', 'friend_id');
    }]);

    // プッシュ通知の送信
    // 同じ会話の参加者全員（自分以外）に通知を送信する
    $participants = $conversation->conversationParticipants()
      ->where('user_id', '!=', $user->id)
      ->with('user')
      ->get();

    if ($participants->isNotEmpty()) {
      // メッセージプレビュー（長い場合は短縮する）
      $messagePreview = mb_substr($message->text_content, 0, 50);
      if (mb_strlen($message->text_content) > 50) {
        $messagePreview .= '...';
      }

      $notificationController = new NotificationController();

      foreach ($participants as $participant) {
        // 各参加者にプッシュ通知を送信
        if ($participant->user) {
          $notificationController->sendNewMessageNotification(
            $participant->user,
            $user->name,
            $messagePreview,
            $conversation->id,
            $conversation->room_token
          );
        }
      }
    }

    // TODO: リアルタイムで相手にメッセージを通知するイベントを発行 (例: NewMessageEvent)
    // broadcast(new NewMessageEvent($message))->toOthers();

    return response()->json($message, 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}
