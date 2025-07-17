<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Controllers\API\NotificationController;
use Illuminate\Support\Facades\Log;

class MessagesController extends Controller
{
  /**
   * 特定の会話のメッセージ一覧を取得する
   */
  public function index(Conversation $conversation, Request $request)
  {
    $user = Auth::user();

    // 削除されたユーザーはアクセス不可
    if ($user->isDeleted()) {
      return response()->json(['message' => 'アカウントが削除されています。'], 403);
    }

    // 削除された会話にはアクセス不可
    if ($conversation->isDeleted()) {
      return response()->json(['message' => 'この会話は削除されています。'], 403);
    }

    // ユーザーがこの会話の参加者であることを確認
    if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
      return response()->json(['message' => 'アクセス権がありません。'], 403);
    }

    // ダイレクトメッセージの場合、友達関係を確認
    if ($conversation->type === 'direct') {
      // 会話の相手を取得（削除されていないユーザーのみ）
      $otherParticipant = $conversation->participants()
        ->where('users.id', '!=', $user->id)
        ->whereNull('users.deleted_at')
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
      } else {
        // 相手が削除されている場合
        return response()->json([
          'message' => '相手のアカウントが削除されたため、このチャットにアクセスできません。',
          'friendship_status' => 'user_deleted'
        ], 403);
      }
    }

    $messages = $conversation->messages()
      ->whereNull('admin_deleted_at') // 管理者によって削除されていないメッセージのみ
      ->with(['sender' => function ($query) {
        $query->select('id', 'name', 'friend_id'); // 送信者の基本情報を選択
      }, 'adminSender' => function ($query) {
        $query->select('id', 'name'); // 管理者送信者の基本情報を選択
      }, 'messageReads' => function ($query) {
        $query->select('message_id', 'user_id', 'read_at'); // 既読情報を選択
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
      
      // 個別メッセージの既読記録
      $unreadMessageIds = [];
      foreach ($messages as $message) {
        // 自分が送信したメッセージは除外
        if ($message->sender_id !== $user->id && $message->sender_id !== null) {
          // 既に既読していないメッセージのみ
          $alreadyRead = $message->messageReads->where('user_id', $user->id)->isNotEmpty();
          if (!$alreadyRead) {
            $unreadMessageIds[] = $message->id;
          }
        }
      }
      
      if (!empty($unreadMessageIds)) {
        \App\Models\MessageRead::markMultipleAsRead($unreadMessageIds, $user->id);
      }
    }

    // 各メッセージに既読情報を追加
    foreach ($messages as $message) {
      // 1対1チャットの場合
      if ($conversation->type === 'direct') {
        $message->is_read = $message->isReadByOtherParticipant($user->id);
      }
      // TODO: グループチャット対応は今後実装予定
    }

    return response()->json($messages);
  }

  /**
   * 新しいメッセージを送信する
   */
  public function store(Conversation $conversation, Request $request)
  {
    try {
      Log::info('メッセージ送信処理を開始', [
        'conversation_id' => $conversation->id,
        'room_token' => $conversation->room_token,
        'request_data' => $request->all()
      ]);

      $user = Auth::user();

      Log::info('認証ユーザー確認完了', [
        'user_id' => $user->id,
        'is_deleted' => $user->isDeleted()
      ]);

      // 削除されたユーザーはメッセージ送信不可
      if ($user->isDeleted()) {
        return response()->json(['message' => 'アカウントが削除されています。'], 403);
      }

      // 削除された会話にはメッセージ送信不可
      if ($conversation->isDeleted()) {
        return response()->json(['message' => 'この会話は削除されています。'], 403);
      }

      // ユーザーがこの会話の参加者であることを確認
      if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
        return response()->json(['message' => 'この会話にメッセージを送信する権限がありません。'], 403);
      }

      Log::info('基本権限チェック完了');

      // ダイレクトメッセージの場合、友達関係を確認
      if ($conversation->type === 'direct') {
        // 会話の相手を取得（削除されていないユーザーのみ）
        $otherParticipant = $conversation->participants()
          ->where('users.id', '!=', $user->id)
          ->whereNull('users.deleted_at')
          ->first();

        if ($otherParticipant) {
          // 友達関係を確認
          $currentFriends = $user->friends()->pluck('id')->toArray();
          if (!in_array($otherParticipant->id, $currentFriends)) {
            return response()->json([
              'message' => '友達関係が解除されたため、このチャットにメッセージを送信できません。',
              'friendship_status' => 'unfriended'
            ], 403);
          }
        } else {
          // 相手が削除されている場合
          return response()->json([
            'message' => '相手のアカウントが削除されたため、このチャットにメッセージを送信できません。',
            'friendship_status' => 'user_deleted'
          ], 403);
        }
      }

      Log::info('友達関係チェック完了');

      $request->validate([
        'text_content' => 'required|string|max:5000', // 最大文字数など適宜調整
        // 'content_type' => 'sometimes|in:text,image,file' // 将来的な拡張用
      ]);

      Log::info('バリデーション完了');

      $message = $conversation->messages()->create([
        'sender_id' => $user->id,
        'text_content' => $request->input('text_content'),
        'content_type' => 'text', // MVPではtext固定
        'sent_at' => now(),
      ]);

      Log::info('メッセージ作成完了', ['message_id' => $message->id]);

      $message->load(['sender' => function ($query) {
        $query->select('id', 'name', 'friend_id');
      }, 'adminSender' => function ($query) {
        $query->select('id', 'name');
      }]);

      Log::info('メッセージリレーション読み込み完了');

      // プッシュ通知の送信
      // 同じ会話の参加者全員（自分以外）に通知を送信する
      $participants = $conversation->conversationParticipants()
        ->where('user_id', '!=', $user->id)
        ->whereHas('user', function ($query) {
          $query->whereNull('deleted_at'); // 削除されていないユーザーのみ
        })
        ->with('user')
        ->get();

      Log::info('参加者取得完了', ['participants_count' => $participants->count()]);

      if ($participants->isNotEmpty()) {
        // メッセージプレビュー（長い場合は短縮する）
        $messagePreview = mb_substr($message->text_content, 0, 50);
        if (mb_strlen($message->text_content) > 50) {
          $messagePreview .= '...';
        }

        Log::info('通知送信開始');

        $notificationController = new NotificationController();

        foreach ($participants as $participant) {
          // 各参加者にプッシュ通知を送信
          if ($participant->user) {
            try {
              $notificationController->sendNewMessageNotification(
                $participant->user,
                $user->name,
                $messagePreview,
                $conversation->id,
                $conversation->room_token
              );
            } catch (\Exception $e) {
              Log::warning('新しいメッセージ通知の送信に失敗しました', [
                'recipient_user_id' => $participant->user->id,
                'sender_user_id' => $user->id,
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage()
              ]);
              // 通知エラーは無視して処理を続行
            }
          }
        }

        Log::info('通知送信完了');
      }

      Log::info('メッセージ送信処理完了', ['message_id' => $message->id]);

      // TODO: リアルタイムで相手にメッセージを通知するイベントを発行 (例: NewMessageEvent)
      // broadcast(new NewMessageEvent($message))->toOthers();

      return response()->json($message, 201);
    } catch (\Exception $e) {
      Log::error('メッセージ送信処理でエラーが発生しました', [
        'conversation_id' => $conversation->id ?? 'unknown',
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);

      return response()->json([
        'message' => 'メッセージの送信に失敗しました。'
      ], 500);
    }
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
