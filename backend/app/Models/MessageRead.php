<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'user_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * 既読記録に関連するメッセージを取得
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * 既読記録に関連するユーザーを取得
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * メッセージを既読にする
     */
    public static function markAsRead(int $messageId, int $userId): self
    {
        return static::firstOrCreate([
            'message_id' => $messageId,
            'user_id' => $userId,
        ], [
            'read_at' => now(),
        ]);
    }

    /**
     * 複数のメッセージを一括で既読にする
     */
    public static function markMultipleAsRead(array $messageIds, int $userId): int
    {
        if (empty($messageIds)) {
            return 0;
        }

        $data = [];
        $now = now();
        
        foreach ($messageIds as $messageId) {
            $data[] = [
                'message_id' => $messageId,
                'user_id' => $userId,
                'read_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 既存の既読記録をスキップしながら一括挿入
        return static::insertOrIgnore($data);
    }

    /**
     * 特定のチャットルームの未読メッセージを一括で既読にする
     */
    public static function markChatRoomMessagesAsRead(int $chatRoomId, int $userId): int
    {
        $unreadMessageIds = Message::where('chat_room_id', $chatRoomId)
            ->where('sender_id', '!=', $userId)
            ->whereNotExists(function ($query) use ($userId) {
                $query->select('id')
                    ->from('message_reads')
                    ->whereColumn('message_reads.message_id', 'messages.id')
                    ->where('message_reads.user_id', $userId);
            })
            ->pluck('id')
            ->toArray();

        return static::markMultipleAsRead($unreadMessageIds, $userId);
    }
}