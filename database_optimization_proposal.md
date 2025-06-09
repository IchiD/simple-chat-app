# データベース構造最適化提案

## 現在の問題点

### 1. 重複カラム

- `chat_style` と `chat_styles` が同じ情報を重複管理
- `chat_style` は全て `member_chat` で統一されており、実質的に不要

### 2. 型別の不要カラム

- `group_member` タイプのレコードに以下が不要：
  - `owner_user_id` (NULL)
  - `qr_code_token` (NULL)
  - `max_members` (親グループから継承すべき)
  - `description` (NULL)

### 3. 混在構造

- グループ管理情報と個別チャット情報が同じテーブルに混在
- 正規化が不十分

## 最適化案

### Phase 1: 即座に実行可能な最適化

#### 1.1 不要カラムの削除

```sql
-- chat_styleカラムを削除（chat_stylesで十分）
ALTER TABLE conversations DROP COLUMN chat_style;
```

#### 1.2 データクリーンアップ

```sql
-- group_memberタイプの不要なデータをクリア
UPDATE conversations
SET max_members = NULL, description = NULL
WHERE type = 'group_member';
```

### Phase 2: 構造改善（推奨）

#### 2.1 テーブル分割案

```sql
-- グループ管理専用テーブル
CREATE TABLE groups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    max_members INT DEFAULT 50,
    chat_styles JSON, -- ['group', 'group_member']
    owner_user_id INT NOT NULL,
    qr_code_token VARCHAR(32) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_owner (owner_user_id),
    INDEX idx_qr_token (qr_code_token)
);

-- 会話（チャットルーム）専用テーブル
CREATE TABLE chat_rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type ENUM('group_chat', 'member_chat') NOT NULL,
    group_id INT NULL, -- groupsテーブルのID
    participant1_id INT NULL, -- member_chatの場合
    participant2_id INT NULL, -- member_chatの場合
    room_token VARCHAR(16) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_type (type),
    INDEX idx_group (group_id),
    INDEX idx_participants (participant1_id, participant2_id),
    INDEX idx_room_token (room_token)
);
```

#### 2.2 メリット

- **正規化**: 役割別にテーブル分離
- **パフォーマンス**: インデックス最適化
- **保守性**: 構造が明確
- **スケーラビリティ**: 将来の拡張に対応

## 移行戦略

### Step 1: 現行システムでの最適化

1. `chat_style`カラム削除
2. 不要データのクリーンアップ
3. インデックス最適化

### Step 2: 段階的移行（オプション）

1. 新テーブル作成
2. データ移行スクリプト作成
3. アプリケーション層の段階的更新
4. 旧テーブル削除

## 推奨アクション

**即座に実行**:

- Phase 1 の最適化（リスク小、効果大）

**中長期検討**:

- Phase 2 の構造改善（リスク中、効果大）

## 削減効果予測

- **ストレージ**: 約 20-30%削減
- **クエリパフォーマンス**: 約 15-25%向上
- **保守性**: 大幅向上
