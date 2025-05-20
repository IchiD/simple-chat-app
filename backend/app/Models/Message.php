<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
  use HasFactory;

  protected $fillable = [
    'conversation_id',
    'sender_id',
    'content_type',
    'text_content',
    'sent_at',
    'edited_at',
    'deleted_at',
  ];

  protected $casts = [
    'sent_at' => 'datetime',
    'edited_at' => 'datetime',
    'deleted_at' => 'datetime',
  ];

  /**
   * このメッセージが属する会話を取得
   */
  public function conversation(): BelongsTo
  {
    return $this->belongsTo(Conversation::class);
  }

  /**
   * このメッセージの送信者を取得
   */
  public function sender(): BelongsTo
  {
    return $this->belongsTo(User::class, 'sender_id');
  }
}
