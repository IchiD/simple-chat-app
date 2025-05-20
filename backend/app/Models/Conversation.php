<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Conversation extends Model
{
  use HasFactory;

  protected $fillable = [
    'type',
  ];

  /**
   * この会話に参加しているユーザーを取得 (Participantsテーブル経由)
   */
  public function participants(): HasManyThrough
  {
    return $this->hasManyThrough(User::class, Participant::class, 'conversation_id', 'id', 'id', 'user_id');
  }

  /**
   * この会話のParticipantレコードを取得
   */
  public function conversationParticipants(): HasMany // より直接的なリレーション名
  {
    return $this->hasMany(Participant::class);
  }

  /**
   * この会話のメッセージを取得
   */
  public function messages(): HasMany
  {
    return $this->hasMany(Message::class)->orderBy('sent_at', 'desc');
  }

  /**
   * この会話の最新メッセージを取得
   */
  public function latestMessage()
  {
    return $this->hasOne(Message::class)->latest('sent_at');
  }
}
