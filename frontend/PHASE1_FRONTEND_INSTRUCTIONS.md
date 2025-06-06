# Phase 1 フロントエンド実装指示書

## 🎯 **実装目標**

**Phase 1** として、バックエンドで完了したデータモデル変更に対応する **フロントエンド型定義の拡張** を実装してください。

---

## 📋 **具体的な実装内容**

### ✅ **必須実装**

#### 1. User インターフェース拡張

**ファイル**: `stores/auth.ts`

**現在の状態**:

```typescript
interface User {
  id: number;
  name: string;
  email: string;
  friend_id?: string;
  google_id?: string;
  avatar?: string;
  social_type?: string;
}
```

**実装が必要な修正**:

```typescript
interface User {
  id: number;
  name: string;
  email: string;
  friend_id?: string;
  google_id?: string;
  avatar?: string;
  social_type?: string;
  plan?: "free" | "standard" | "premium";
  subscription_status?:
    | "active"
    | "canceled"
    | "past_due"
    | "incomplete"
    | "incomplete_expired"
    | "trialing"
    | "unpaid";
}
```

#### 2. 型安全性の確保

- **型定義の一貫性**: バックエンドのモデルと一致させる
- **Optional フィールド**: `plan` と `subscription_status` は nullable として定義
- **Enum 型の活用**: 有効な値のみを許可する Union 型を使用

---

## 🔧 **技術的要件**

### 📚 **TypeScript ベストプラクティス**

1. **厳密な型定義**

   ```typescript
   // ✅ Good
   plan?: "free" | "standard" | "premium";

   // ❌ Bad
   plan?: string;
   ```

2. **一貫した命名規則**

   - `subscription_status` (snake_case) - バックエンド API との一貫性
   - TypeScript 側でも同じ命名を維持

3. **将来性を考慮した設計**
   - Phase 2 での Stripe 連携で使用される型定義
   - Phase 3 でのグループ機能でも参照される可能性

### 🎨 **実装上の注意点**

#### ✅ **実装してください**

- User インターフェースの拡張のみ
- 型安全性の確保
- 既存機能への影響ゼロ

#### ❌ **実装しないでください**

- UI コンポーネントの変更
- 新しいページやビューの作成
- API エンドポイントの呼び出し
- ストア機能の追加（認証ストア以外）

---

## 📊 **バックエンド連携状況**

### ✅ **完了済み（参考情報）**

**バックエンド側の User モデル拡張**:

```php
// User.php - 既に実装済み
protected $fillable = [
    // ... 既存フィールド
    'plan',
    'subscription_status',
];

protected $casts = [
    // ... 既存キャスト
    'plan' => 'string',
    'subscription_status' => 'string',
];
```

**マイグレーション**:

```php
// add_subscription_fields_to_users_table.php - 実行済み
$table->enum('plan', ['free', 'standard', 'premium'])->nullable();
$table->enum('subscription_status', [
    'active', 'canceled', 'past_due', 'incomplete',
    'incomplete_expired', 'trialing', 'unpaid'
])->nullable();
```

---

## 🧪 **テスト・検証方法**

### 1. **型安全性チェック**

TypeScript コンパイラでエラーが出ないことを確認:

```bash
npm run build
# または
yarn build
```

### 2. **開発サーバー起動確認**

```bash
npm run dev
# または
yarn dev
```

### 3. **型定義確認**

開発者ツールでユーザーオブジェクトの型ヒントが正常に表示されることを確認。

---

## 📅 **完了後のステップ**

### ✅ **Phase 1 完了確認**

1. **User インターフェース拡張**: ✅
2. **型安全性確保**: ✅
3. **既存機能への影響なし**: ✅
4. **ビルドエラーなし**: ✅

### 🚀 **次の予定**

Phase 1 完了後、以下が開始可能になります：

- **Phase 2**: Stripe 決済連携（並行開発）
- **Phase 3**: グループチャット機能（並行開発）

---

## 🎯 **期待される結果**

### 📊 **完了指標**

- ✅ TypeScript コンパイルエラー 0 件
- ✅ 既存機能の動作確認完了
- ✅ User 型に plan, subscription_status フィールド追加
- ✅ IDE での型ヒント正常表示

### 📝 **成果物**

**修正ファイル**:

- `stores/auth.ts` - User インターフェース拡張

**変更行数**: 約 2-3 行の追加（非常にシンプル）

---

## 💡 **重要な注意事項**

### 🔒 **制約事項**

- **main ブランチは変更禁止** - `feature/group-chat-ui` ブランチでのみ作業
- **UI/UX 変更禁止** - 型定義のみの修正
- **追加機能実装禁止** - Phase 1 は型定義拡張のみ

### 🎨 **品質基準**

- **TypeScript Best Practices** 遵守
- **既存コードスタイル** 維持
- **コメント不要** - シンプルな型定義のため

---

## 📞 **サポート情報**

### 📚 **参考ドキュメント**

- バックエンド Phase 1 実装: `chat-app-backend/feature/group-chat-service` ブランチ
- 全体計画: `implementation_plan.md`
- 詳細指示: `AGENTS.md`

### 🔍 **確認事項**

実装完了後、以下をレポートしてください：

1. 修正したファイルとその内容
2. TypeScript ビルド結果
3. 想定通りに型ヒントが表示されるかの確認

---

**Phase 1 フロントエンド実装は非常にシンプルです。型定義を 2 行追加するだけで完了します！** 🚀
