# AI エージェント開発指示書

## 基本方針

1. **バックエンド優先**: Phase 2-5 はバックエンド完了後にフロントエンド開始
2. **ブランチ指定**: 必ず開発ブランチから作業開始
3. **参照ドキュメント**: implementation_plan.md を必ず参照
4. **段階的実装**: 各フェーズ完了後に統合テスト実施

---

## Phase 1: データモデル設計（並行開発可能）

### 🔧 Backend 開発指示

```
【リポジトリ】
https://github.com/IchiD/chat-app-backend

【作業ブランチ】
feature/group-chat-api をベースに feat/phase1-database-schema ブランチを作成して作業してください。

【タスク】
implementation_plan.md のフェーズ1.1「データベース設計」に記載された以下テーブルのLaravelマイグレーションファイルを作成してください：

1. groups テーブル
2. group_members テーブル
3. subscriptions テーブル
4. group_messages テーブル

【技術要件】
- Laravel 10のマイグレーション形式
- 外部キー制約の適切な設定
- インデックスの最適化
- テストデータのシーダーファイルも作成

【完了条件】
- マイグレーションファイルが正常に実行可能
- php artisan migrate でエラーなし
- 基本的なEloquentモデルファイルも作成
```

### 🎨 Frontend 開発指示

```
【リポジトリ】
https://github.com/IchiD/chat-app-frontend

【作業ブランチ】
feature/group-chat-ui をベースに feat/phase1-user-model ブランチを作成して作業してください。

【タスク】
stores/auth.ts のUserインターフェースにサブスクリプション関連フィールドを追加してください（既に追加済みの場合は確認とテスト）：

- plan: "free" | "standard" | "premium"
- subscription_status: Stripeのステータス

【技術要件】
- TypeScript の型安全性を保持
- 既存の認証ロジックとの互換性維持
- 適切な初期値とバリデーション

【完了条件】
- npm run dev でエラーなし
- 型チェック通過
- 既存のログイン機能に影響なし
```

---

## Phase 2: Stripe 決済機能

### 🔧 Backend 開発指示（Phase 1 完了後）

```
【リポジトリ】
https://github.com/IchiD/chat-app-backend

【作業ブランチ】
feature/group-chat-api をベースに feat/phase2-stripe-integration ブランチを作成

【前提条件】
Phase 1のデータベース設計が完了していること

【タスク】
1. Stripe SDK導入とコンフィグ設定
2. APIルート作成:
   - POST /api/stripe/create-checkout-session
   - POST /api/stripe/webhook
3. サブスクリプション管理ロジック
4. 環境変数テンプレート更新

【技術要件】
- Laravel のHTTPクライアントでStripe API連携
- Webhook署名検証の実装
- エラーハンドリングの充実
- ログ出力の適切な設定

【完了条件】
- Stripeテストモードで決済フローが動作
- Webhook正常受信確認
- PHPUnitテストケース作成
```

### 🎨 Frontend 開発指示（Backend 完了後）

```
【リポジトリ】
https://github.com/IchiD/chat-app-frontend

【作業ブランチ】
feature/group-chat-ui をベースに feat/phase2-pricing-page ブランチを作成

【前提条件】
Backend Phase 2のStripe API実装が完了していること

【タスク】
1. pages/pricing.vue 作成
2. プラン選択UI実装
3. Stripe Checkoutへのリダイレクト処理
4. 決済完了後のユーザー状態更新

【技術要件】
- Tailwind CSS でモダンなデザイン
- レスポンシブ対応
- Vue 3 Composition API使用
- エラーハンドリングとローディング状態

【完了条件】
- 決済フローのE2Eテスト成功
- ユーザー情報の適切な更新確認
- モバイル・デスクトップ両対応
```

---

## Phase 3: グループチャット機能

### 🔧 Backend 開発指示（Phase 2 完了後）

```
【リポジトリ】
https://github.com/IchiD/chat-app-backend

【作業ブランチ】
feature/group-chat-api をベースに feat/phase3-group-management ブランチを作成

【前提条件】
- Phase 1: データベース設計完了
- Phase 2: Stripe決済機能完了

【タスク】
グループ管理APIエンドポイント実装:
- POST /api/groups（グループ作成）
- GET /api/groups（グループ一覧）
- GET /api/groups/{id}（グループ詳細）
- PUT /api/groups/{id}（グループ更新）
- DELETE /api/groups/{id}（グループ削除）
- POST /api/groups/{id}/members（メンバー追加）
- DELETE /api/groups/{id}/members/{memberId}（メンバー削除）
- POST /api/groups/{id}/messages（メッセージ送信）
- GET /api/groups/{id}/messages（メッセージ履歴）

【技術要件】
- RESTful API設計
- 認証・認可の実装（管理者権限確認）
- ページネーション対応
- リアルタイム通信準備（Pusherまたは類似）

【完了条件】
- APIドキュメント作成
- Postmanテストコレクション
- 単体・統合テスト実装
```

### 🎨 Frontend 開発指示（Backend 完了後）

```
【リポジトリ】
https://github.com/IchiD/chat-app-frontend

【作業ブランチ】
feature/group-chat-ui をベースに feat/phase3-admin-dashboard ブランチを作成

【前提条件】
Backend Phase 3のグループ管理API実装が完了していること

【タスク】
管理者ダッシュボード実装:
- pages/admin/groups/index.vue（グループ一覧）
- pages/admin/groups/[id]/index.vue（グループ詳細）
- pages/admin/groups/[id]/chat.vue（グループチャット）
- コンポーネント: GroupCard, MemberList, MessageThread

【技術要件】
- Nuxt 3のファイルベースルーティング活用
- リアルタイムメッセージング実装
- ドラッグ&ドロップでメンバー管理
- 権限チェック（有料ユーザーのみアクセス可能）

【完了条件】
- 管理者としてグループ作成・管理が可能
- リアルタイムチャット動作確認
- UI/UX テスト完了
```

---

## Phase 4: QR コード機能

### 🔧 Backend 開発指示（Phase 3 完了後）

```
【リポジトリ】
https://github.com/IchiD/chat-app-backend

【作業ブランチ】
feature/group-chat-api をベースに feat/phase4-qr-endpoints ブランチを作成

【タスク】
QRコード関連API:
- POST /api/groups/{id}/qr-code（QRコード生成）
- PUT /api/groups/{id}/qr-code（QRコード再生成）
- GET /api/join/{token}（QRコード検証・参加処理）

【技術要件】
- QRコード生成ライブラリ導入
- トークンの暗号化・有効期限管理
- ゲストユーザー参加ロジック

【完了条件】
- QRコード画像生成確認
- 参加フロー動作確認
- セキュリティテスト実施
```

### 🎨 Frontend 開発指示（Backend 完了後）

```
【リポジトリ】
https://github.com/IchiD/chat-app-frontend

【作業ブランチ】
feature/group-chat-ui をベースに feat/phase4-qr-components ブランチを作成

【タスク】
QRコード関連UI:
- components/QRCodeGenerator.vue
- pages/join/[token].vue（参加ランディング）
- QRコード表示・共有機能

【技術要件】
- QRコード表示コンポーネント
- スマートフォン対応の参加フロー
- ニックネーム入力とバリデーション

【完了条件】
- QRコード読み取りでの参加動作確認
- モバイル端末での動作確認
```

---

## Phase 5: API 連携ログイン

### 🔧 Backend 開発指示（Phase 4 完了後）

```
【リポジトリ】
https://github.com/IchiD/chat-app-backend

【作業ブランチ】
feature/group-chat-api をベースに feat/phase5-external-auth ブランチを作成

【タスク】
外部システム向け認証API:
- POST /api/auth/external/token（アクセストークン発行）
- POST /api/auth/external/verify（トークン検証）
- OAuth2風の認証フロー実装

【技術要件】
- セキュアなトークン管理
- レート制限実装
- APIキー管理システム

【完了条件】
- 外部システム連携テスト
- セキュリティ監査
- API使用ガイド作成
```

---

## 🔄 統合・マージフロー

### 各フェーズ完了時

```bash
# 1. フェーズブランチから開発ブランチにマージ
git checkout feature/group-chat-api
git merge feat/phase1-database-schema
git push origin feature/group-chat-api

# 2. 統合テスト実施
# 3. 問題なければ次フェーズ開始
```

### 全フェーズ完了後

```bash
# 最終確認後、mainブランチにマージ（要承認）
git checkout main
git merge feature/group-chat-api
git push origin main
```

---

## ⚠️ 重要な注意事項

1. **必ずブランチ指定**: main ブランチでの作業は絶対禁止
2. **段階的実装**: 前のフェーズ完了確認後に次フェーズ開始
3. **テスト必須**: 各フェーズでの動作確認とテスト実装
4. **ドキュメント更新**: API 変更時は必ずドキュメント更新
5. **セキュリティ配慮**: 特に認証・決済関連は慎重に実装

---

## 📞 サポート体制

各フェーズで問題が発生した場合：

1. implementation_plan.md の該当セクション再確認
2. エラーログの詳細共有
3. 具体的な実装方針の相談
