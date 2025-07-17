# メッセージ既読機能実装（Phase 1）

## 概要
チャットアプリケーションに個別メッセージの既読機能を実装しました。これにより、送信者は自分のメッセージが相手に読まれたかどうかを確認できるようになります。

## 実装内容

### 1. データベース構造

#### 新テーブル: `message_reads`
```sql
CREATE TABLE message_reads (
  id BIGINT PRIMARY KEY,
  message_id BIGINT NOT NULL,         -- 既読されたメッセージ
  user_id BIGINT NOT NULL,            -- 既読したユーザー
  read_at TIMESTAMP NOT NULL,         -- 既読時刻
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  UNIQUE(message_id, user_id),
  INDEX(message_id),
  INDEX(user_id, message_id)
);
```

### 2. モデル実装

#### MessageReadモデル (`backend/app/Models/MessageRead.php`)
- `markAsRead()`: 単一メッセージを既読にする
- `markMultipleAsRead()`: 複数メッセージを一括で既読にする
- `markChatRoomMessagesAsRead()`: チャットルームの未読メッセージを一括既読

#### Messageモデルの拡張
- `messageReads()`: 既読記録とのリレーション
- `isReadByOtherParticipant()`: 1対1チャットで相手が既読したかチェック
- `getReadCount()`: グループチャットでの既読数を取得
- `getReadUsers()`: 既読したユーザーのリストを取得

### 3. API実装

#### メッセージ取得時の自動既読 (`MessagesController::index`)
- メッセージ一覧取得時に自動的に既読記録
- 自分が送信したメッセージは除外
- 既読情報を含めてレスポンスを返す

### 4. フロントエンド実装

#### 型定義の更新
```typescript
type Message = {
  // ... 既存のフィールド
  is_read?: boolean; // 1対1チャット用
  read_count?: number; // グループチャット用
  read_by?: Array<{
    user_id: number;
    user_name: string;
    read_at: string;
  }>;
};
```

#### UI実装
- 1対1チャットで「既読」表示
- 時刻の隣に既読状態を表示

## 使い方

### データベースマイグレーション
```bash
cd backend
./vendor/bin/sail artisan migrate
```

### 動作確認
1. 1対1チャットでメッセージを送信
2. 相手がチャット画面を開く（メッセージ一覧を取得）
3. 送信者側で「既読」が表示される

## 今後の拡張予定

### Phase 2: グループチャット対応
- 既読人数の表示
- 既読者リストの表示

### Phase 3: リアルタイム更新
- WebSocketによるリアルタイム既読通知
- 既読状態の即時反映

### Phase 4: パフォーマンス最適化
- 既読情報のキャッシュ
- バッチ処理の最適化

## テスト

`backend/tests/Feature/MessageReadTest.php` に以下のテストケースを実装：
- メッセージ取得時の自動既読
- 送信者による既読確認
- 複数メッセージの一括既読
- 自分のメッセージは既読記録されないこと

## 注意事項
- 現在の実装では、メッセージ一覧を取得した時点で既読になる
- リアルタイム更新は未実装（ページリロードが必要）
- グループチャットの既読表示は今後実装予定