<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Friendship extends Model
{
  use HasFactory;

  /**
   * 複数代入可能な属性
   */
  protected $fillable = [
    'user_id',
    'friend_id',
    'status',
    'message',
  ];

  /**
   * ステータスの定数定義
   */
  const STATUS_PENDING = 0;  // 申請中
  const STATUS_ACCEPTED = 1; // 承認済み
  const STATUS_REJECTED = 2; // 拒否

  /**
   * 友達申請を送ったユーザーとのリレーション
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  /**
   * 友達申請を受けたユーザーとのリレーション
   */
  public function friend(): BelongsTo
  {
    return $this->belongsTo(User::class, 'friend_id');
  }

  /**
   * 特定のユーザー間の友達関係を取得
   */
  public static function getFriendship(int $userId, int $friendId)
  {
    return self::where(function ($query) use ($userId, $friendId) {
      $query->where('user_id', $userId)
        ->where('friend_id', $friendId);
    })->orWhere(function ($query) use ($userId, $friendId) {
      $query->where('user_id', $friendId)
        ->where('friend_id', $userId);
    })->first();
  }

  /**
   * 友達関係のステータスを取得（どちらがリクエストしたかに関わらず）
   */
  public static function getFriendshipStatus(int $userId, int $friendId): int
  {
    $friendship = self::getFriendship($userId, $friendId);

    if (!$friendship) {
      return -1; // 関係なし
    }

    return $friendship->status;
  }
}
