# Phase 1 最適化 - 手動実行手順

## 事前確認

以下の SQL を実行して現在の状況を確認してください：

```sql
-- 現在のテーブル状況を確認
SELECT
    type,
    COUNT(*) as count,
    COUNT(CASE WHEN owner_user_id IS NOT NULL THEN 1 END) as has_owner,
    COUNT(CASE WHEN qr_code_token IS NOT NULL THEN 1 END) as has_qr,
    COUNT(CASE WHEN max_members IS NOT NULL THEN 1 END) as has_max_members,
    COUNT(CASE WHEN description IS NOT NULL THEN 1 END) as has_description
FROM conversations
GROUP BY type;
```

## Phase 1 実行手順

### 1. chat_style カラムの削除

```sql
-- chat_styleカラムが存在するか確認
SHOW COLUMNS FROM conversations LIKE 'chat_style';

-- 存在する場合は削除
ALTER TABLE conversations DROP COLUMN chat_style;
```

### 2. group_member タイプの不要データクリーンアップ

```sql
-- 影響を受けるレコード数を確認
SELECT COUNT(*) as affected_records
FROM conversations
WHERE type = 'group_member'
AND (
    owner_user_id IS NOT NULL
    OR qr_code_token IS NOT NULL
    OR max_members IS NOT NULL
    OR description IS NOT NULL
);

-- 実際のクリーンアップ実行
UPDATE conversations
SET
    max_members = NULL,
    description = NULL,
    owner_user_id = NULL,
    qr_code_token = NULL
WHERE type = 'group_member';
```

### 3. インデックス最適化

```sql
-- 既存インデックスの確認
SHOW INDEX FROM conversations;

-- 新しいインデックスの追加
CREATE INDEX idx_conversations_type ON conversations(type);
CREATE INDEX idx_conversations_owner ON conversations(owner_user_id);
CREATE INDEX idx_conversations_group_parent ON conversations(group_conversation_id);
```

### 4. 最適化後の確認

```sql
-- 最適化後の状況を確認
SELECT
    type,
    COUNT(*) as count,
    COUNT(CASE WHEN owner_user_id IS NOT NULL THEN 1 END) as has_owner,
    COUNT(CASE WHEN qr_code_token IS NOT NULL THEN 1 END) as has_qr,
    COUNT(CASE WHEN max_members IS NOT NULL THEN 1 END) as has_max_members,
    COUNT(CASE WHEN description IS NOT NULL THEN 1 END) as has_description
FROM conversations
GROUP BY type;

-- テーブル最適化（MySQL）
OPTIMIZE TABLE conversations;
ANALYZE TABLE conversations;
```

## 結果の期待値

- **group_member タイプ**: owner_user_id, qr_code_token, max_members, description が全て NULL
- **group タイプ**: 必要なデータはそのまま保持
- **ストレージ**: 20-30%の削減
- **パフォーマンス**: 15-25%の向上

## 注意事項

- 実行前に必ずデータベースのバックアップを取ってください
- 本番環境では低負荷時間帯に実行してください
- エラーが発生した場合は即座に処理を停止してください
