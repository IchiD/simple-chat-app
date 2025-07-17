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
    'admin_sender_id',
    'content_type',
    'text_content',
    'sent_at',
    'edited_at',
    'deleted_at',
    'admin_deleted_at',
    'admin_deleted_by',
    'admin_deleted_reason',
  ];

  protected $casts = [
    'sent_at' => 'datetime',
    'edited_at' => 'datetime',
    'deleted_at' => 'datetime',
    'admin_deleted_at' => 'datetime',
  ];

  /**
   * このメッセージが属する会話を取得
   */
  public function conversation(): BelongsTo
  {
    return $this->belongsTo(Conversation::class);
  }

  /**
   * このメッセージの送信者を取得（ユーザー）
   */
  public function sender(): BelongsTo
  {
    return $this->belongsTo(User::class, 'sender_id');
  }

  /**
   * このメッセージの送信者を取得（管理者）
   */
  public function adminSender(): BelongsTo
  {
    return $this->belongsTo(Admin::class, 'admin_sender_id');
  }

  /**
   * 管理者による削除を実行した管理者を取得
   */
  public function adminDeletedBy(): BelongsTo
  {
    return $this->belongsTo(Admin::class, 'admin_deleted_by');
  }

  /**
   * メッセージが管理者によって削除されているかチェック
   */
  public function isAdminDeleted(): bool
  {
    return !is_null($this->admin_deleted_at);
  }

  /**
   * メッセージが管理者からのものかチェック
   */
  public function isFromAdmin(): bool
  {
    return !is_null($this->admin_sender_id);
  }

  /**
   * メッセージの実際の送信者を取得（ユーザーまたは管理者）
   */
  public function getActualSender()
  {
    if ($this->isFromAdmin()) {
      return $this->adminSender;
    }
    return $this->sender;
  }

  /**
   * 管理者によるメッセージ削除
   */
  public function deleteByAdmin(int $adminId, string $reason = null): bool
  {
    return $this->update([
      'admin_deleted_at' => now(),
      'admin_deleted_reason' => $reason,
      'admin_deleted_by' => $adminId,
    ]);
  }

  /**
   * 管理者による削除を取り消し
   */
  public function restoreByAdmin(): bool
  {
    return $this->update([
      'admin_deleted_at' => null,
      'admin_deleted_reason' => null,
      'admin_deleted_by' => null,
    ]);
  }

  /**
   * このメッセージの既読記録
   */
  public function messageReads()
  {
    return $this->hasMany(MessageRead::class);
  }

  /**
   * 相手が既読したかチェック（1対1チャット用）
   */
  public function isReadByOtherParticipant(int $currentUserId): bool
  {
    if (!$this->chatRoom) {
      return false;
    }

    // 自分が送信したメッセージでない場合はfalse
    if ($this->sender_id !== $currentUserId) {
      return false;
    }

    // 相手のユーザーIDを取得
    $otherParticipantId = null;
    if ($this->chatRoom->type === 'friend_chat' || $this->chatRoom->type === 'member_chat') {
      $otherParticipantId = $this->chatRoom->participant1_id === $currentUserId
        ? $this->chatRoom->participant2_id
        : $this->chatRoom->participant1_id;
    }

    if (!$otherParticipantId) {
      return false;
    }

    return $this->messageReads()
      ->where('user_id', $otherParticipantId)
      ->exists();
  }

  /**
   * 既読したユーザー数を取得（グループチャット用）
   */
  public function getReadCount(int $excludeUserId = null): int
  {
    $query = $this->messageReads();
    
    if ($excludeUserId) {
      $query->where('user_id', '!=', $excludeUserId);
    }
    
    return $query->count();
  }

  /**
   * 既読したユーザーのリストを取得
   */
  public function getReadUsers()
  {
    return $this->messageReads()
      ->with('user:id,name')
      ->get()
      ->map(function ($read) {
        return [
          'user_id' => $read->user_id,
          'user_name' => $read->user->name ?? '不明なユーザー',
          'read_at' => $read->read_at,
        ];
      });
  }
}
