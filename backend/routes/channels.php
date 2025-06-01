<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;

// ユーザーチャンネル（既存）
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
  return (int) $user->id === (int) $id;
});

// 会話チャンネル（新規追加）
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
  // 削除されたユーザーはアクセス不可
  if ($user->isDeleted()) {
    return false;
  }

  // 会話の存在確認
  $conversation = Conversation::find($conversationId);
  if (!$conversation || $conversation->isDeleted()) {
    return false;
  }

  // ユーザーがこの会話の参加者であることを確認
  if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
    return false;
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
        return false;
      }
    } else {
      // 相手が削除されている場合
      return false;
    }
  }

  // 認証成功時はユーザー情報を返す
  return [
    'id' => $user->id,
    'name' => $user->name,
  ];
});
