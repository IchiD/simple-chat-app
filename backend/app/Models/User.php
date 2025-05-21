<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable, HasApiTokens, HasPushSubscriptions;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'is_verified',
    'email_verification_token',
    'token_expires_at',
    'email_verified_at',
    'friend_id',
    'new_email',
    'email_change_token',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  /**
   * 6桁のランダムな英数字のコードを生成する
   * 
   * @return string
   */
  protected static function generateFriendId(): string
  {
    // 英数字の文字セット（紛らわしい文字は除外）
    $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $friendId = '';

    // 6桁のランダムコードを生成
    for ($i = 0; $i < 6; $i++) {
      $friendId .= $characters[random_int(0, strlen($characters) - 1)];
    }

    return $friendId;
  }

  protected static function booted()
  {
    static::creating(function ($user) {
      // email_verification_token が設定されていない場合、自動生成する
      if (empty($user->email_verification_token)) {
        $user->email_verification_token = Str::random(60);
      }
      // token_expires_at が設定されていない場合、自動で有効期限を設定する（1時間後）
      if (empty($user->token_expires_at)) {
        $user->token_expires_at = Carbon::now()->addHours(1);
      }

      // friend_id が設定されていない場合、自動生成する
      // 6桁のランダム文字列を使用
      if (empty($user->friend_id)) {
        // 衝突を避けるためにループでユニーク性を確保
        do {
          $friendId = static::generateFriendId();
        } while (static::where('friend_id', $friendId)->exists());

        $user->friend_id = $friendId;
      }
    });
  }

  /**
   * 仮登録ユーザーの再登録時に新しいパスワードや名前などの情報で上書きし、
   * 再度確認用のトークン・有効期限を設定する。
   *
   * @param array $attributes
   * @return bool
   */
  public function updateProvisionalRegistration(array $attributes): bool
  {
    // 再登録時は必ず新しい確認用トークンと有効期限を発行する
    $attributes['email_verification_token'] = Str::random(60);
    $attributes['token_expires_at'] = Carbon::now()->addHours(1);
    // 本登録済みの場合は更新しない
    if ($this->is_verified) {
      return false;
    }
    return $this->update($attributes);
  }

  public function sendPasswordResetNotification($token)
  {
    $this->notify(new \App\Notifications\CustomSendPasswordResetEmail($token));
  }

  /**
   * 自分が送った友達申請
   */
  public function sentFriendships(): HasMany
  {
    return $this->hasMany(Friendship::class, 'user_id');
  }

  /**
   * 自分が受け取った友達申請
   */
  public function receivedFriendships(): HasMany
  {
    return $this->hasMany(Friendship::class, 'friend_id');
  }

  /**
   * 承認済みの友達関係を全て取得
   * 
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function friends()
  {
    $sentFriendIds = $this->sentFriendships()
      ->where('status', Friendship::STATUS_ACCEPTED)
      ->pluck('friend_id')
      ->toArray();

    $receivedFriendIds = $this->receivedFriendships()
      ->where('status', Friendship::STATUS_ACCEPTED)
      ->pluck('user_id')
      ->toArray();

    $friendIds = array_merge($sentFriendIds, $receivedFriendIds);

    return User::whereIn('id', $friendIds)->get();
  }

  /**
   * 友達申請中の友達関係を取得
   * 
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function pendingFriendRequests()
  {
    return $this->sentFriendships()
      ->where('status', Friendship::STATUS_PENDING)
      ->with('friend')
      ->get();
  }

  /**
   * 受け取った友達申請を取得
   * 
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function friendRequests()
  {
    return $this->receivedFriendships()
      ->where('status', Friendship::STATUS_PENDING)
      ->with('user')
      ->get();
  }

  /**
   * フレンドIDでユーザーを検索
   * 
   * @param string $friendId
   * @return User|null
   */
  public static function findByFriendId(string $friendId)
  {
    return static::where('friend_id', $friendId)->first();
  }

  /**
   * 友達申請を送信
   * 
   * @param int $friendId
   * @param string $message
   * @return Friendship
   */
  public function sendFriendRequest(int $friendId, string $message = null)
  {
    // 既存の友達関係をチェック
    $existingFriendship = Friendship::getFriendship($this->id, $friendId);

    if ($existingFriendship) {
      return $existingFriendship;
    }

    // 新しい友達申請を作成
    return Friendship::create([
      'user_id' => $this->id,
      'friend_id' => $friendId,
      'status' => Friendship::STATUS_PENDING,
      'message' => $message,
    ]);
  }

  /**
   * 友達申請を承認
   * 
   * @param int $userId
   * @return bool
   */
  public function acceptFriendRequest(int $userId): bool
  {
    $friendship = Friendship::where('user_id', $userId)
      ->where('friend_id', $this->id)
      ->where('status', Friendship::STATUS_PENDING)
      ->first();

    if (!$friendship) {
      return false;
    }

    $friendship->status = Friendship::STATUS_ACCEPTED;
    return $friendship->save();
  }

  /**
   * 友達申請を拒否
   * 
   * @param int $userId
   * @return bool
   */
  public function rejectFriendRequest(int $userId): bool
  {
    $friendship = Friendship::where('user_id', $userId)
      ->where('friend_id', $this->id)
      ->where('status', Friendship::STATUS_PENDING)
      ->first();

    if (!$friendship) {
      return false;
    }

    $friendship->status = Friendship::STATUS_REJECTED;
    return $friendship->save();
  }

  /**
   * 友達関係を解除
   * 
   * @param int $friendId
   * @return bool
   */
  public function unfriend(int $friendId): bool
  {
    $friendship = Friendship::getFriendship($this->id, $friendId);

    if (!$friendship) {
      return false;
    }

    return (bool) $friendship->delete();
  }

  /**
   * ユーザーが参加している会話を取得 (Participantsテーブル経由)
   */
  public function conversations(): HasManyThrough
  {
    return $this->hasManyThrough(Conversation::class, Participant::class, 'user_id', 'id', 'id', 'conversation_id');
  }

  /**
   * ユーザーが直接参加しているParticipantレコードを取得
   */
  public function participations(): HasMany
  {
    return $this->hasMany(Participant::class);
  }

  /**
   * ユーザーが送信したメッセージを取得
   */
  public function messages(): HasMany
  {
    return $this->hasMany(Message::class, 'sender_id');
  }
}
