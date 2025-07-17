<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageReadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1対1チャットでメッセージを取得すると自動的に既読になることをテスト
     */
    public function test_messages_are_marked_as_read_when_fetched_in_direct_chat()
    {
        // ユーザー作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // 友達関係を作成
        $user1->addFriend($user2->id);
        $user2->acceptFriendRequest($user1->id);

        // ダイレクトメッセージ会話を作成
        $this->actingAs($user1);
        $response = $this->postJson('/api/conversations', [
            'recipient_id' => $user2->id,
        ]);
        $conversation = Conversation::find($response->json('id'));

        // user2からメッセージを送信
        $this->actingAs($user2);
        $this->postJson("/api/conversations/room/{$conversation->room_token}/messages", [
            'text_content' => 'こんにちは',
        ]);
        $message = Message::where('conversation_id', $conversation->id)->first();

        // この時点でuser1は未読
        $this->assertDatabaseMissing('message_reads', [
            'message_id' => $message->id,
            'user_id' => $user1->id,
        ]);

        // user1がメッセージ一覧を取得
        $this->actingAs($user1);
        $response = $this->getJson("/api/conversations/room/{$conversation->room_token}/messages");
        $response->assertStatus(200);

        // メッセージが既読になっていることを確認
        $this->assertDatabaseHas('message_reads', [
            'message_id' => $message->id,
            'user_id' => $user1->id,
        ]);
    }

    /**
     * 自分が送信したメッセージの既読状態が相手によって確認できることをテスト
     */
    public function test_sender_can_see_if_message_is_read_by_recipient()
    {
        // ユーザー作成
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        // 友達関係を作成
        $sender->addFriend($recipient->id);
        $recipient->acceptFriendRequest($sender->id);

        // ダイレクトメッセージ会話を作成
        $this->actingAs($sender);
        $response = $this->postJson('/api/conversations', [
            'recipient_id' => $recipient->id,
        ]);
        $conversation = Conversation::find($response->json('id'));

        // senderからメッセージを送信
        $this->postJson("/api/conversations/room/{$conversation->room_token}/messages", [
            'text_content' => 'こんにちは',
        ]);

        // senderがメッセージ一覧を取得（まだ相手は未読）
        $response = $this->getJson("/api/conversations/room/{$conversation->room_token}/messages");
        $response->assertStatus(200);
        $messages = $response->json('data');
        $this->assertFalse($messages[0]['is_read']);

        // recipientがメッセージ一覧を取得（既読になる）
        $this->actingAs($recipient);
        $this->getJson("/api/conversations/room/{$conversation->room_token}/messages");

        // senderが再度メッセージ一覧を取得（既読状態が更新されている）
        $this->actingAs($sender);
        $response = $this->getJson("/api/conversations/room/{$conversation->room_token}/messages");
        $response->assertStatus(200);
        $messages = $response->json('data');
        $this->assertTrue($messages[0]['is_read']);
    }

    /**
     * 複数のメッセージが一括で既読になることをテスト
     */
    public function test_multiple_messages_are_marked_as_read_in_batch()
    {
        // ユーザー作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // 友達関係を作成
        $user1->addFriend($user2->id);
        $user2->acceptFriendRequest($user1->id);

        // ダイレクトメッセージ会話を作成
        $this->actingAs($user1);
        $response = $this->postJson('/api/conversations', [
            'recipient_id' => $user2->id,
        ]);
        $conversation = Conversation::find($response->json('id'));

        // user2から複数のメッセージを送信
        $this->actingAs($user2);
        $messageIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $response = $this->postJson("/api/conversations/room/{$conversation->room_token}/messages", [
                'text_content' => "メッセージ {$i}",
            ]);
            $messageIds[] = $response->json('id');
        }

        // user1がメッセージ一覧を取得
        $this->actingAs($user1);
        $this->getJson("/api/conversations/room/{$conversation->room_token}/messages");

        // 全てのメッセージが既読になっていることを確認
        foreach ($messageIds as $messageId) {
            $this->assertDatabaseHas('message_reads', [
                'message_id' => $messageId,
                'user_id' => $user1->id,
            ]);
        }
    }

    /**
     * 自分が送信したメッセージは既読記録されないことをテスト
     */
    public function test_own_messages_are_not_marked_as_read()
    {
        // ユーザー作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // 友達関係を作成
        $user1->addFriend($user2->id);
        $user2->acceptFriendRequest($user1->id);

        // ダイレクトメッセージ会話を作成
        $this->actingAs($user1);
        $response = $this->postJson('/api/conversations', [
            'recipient_id' => $user2->id,
        ]);
        $conversation = Conversation::find($response->json('id'));

        // user1からメッセージを送信
        $response = $this->postJson("/api/conversations/room/{$conversation->room_token}/messages", [
            'text_content' => 'こんにちは',
        ]);
        $message = Message::find($response->json('id'));

        // user1がメッセージ一覧を取得
        $this->getJson("/api/conversations/room/{$conversation->room_token}/messages");

        // 自分のメッセージは既読記録されていないことを確認
        $this->assertDatabaseMissing('message_reads', [
            'message_id' => $message->id,
            'user_id' => $user1->id,
        ]);
    }
}