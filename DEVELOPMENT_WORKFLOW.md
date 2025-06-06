# 開発ワークフロー - 1 対多数チャット機能

## リポジトリ構成

このプロジェクトは 3 つの独立したリポジトリで構成されています：

```
simple-chat-app (メイン)       # GitHub: IchiD/simple-chat-app
├── backend/                    # GitHub: IchiD/chat-app-backend
├── frontend/                   # GitHub: IchiD/chat-app-frontend
└── 統合管理ファイル
```

## マルチリポジトリ ブランチ戦略

### 各リポジトリのブランチ構成

```
# メインリポジトリ (simple-chat-app)
main                                    # 統合管理・ドキュメント
└── feature/group-chat-service          # 今回の開発ブランチ

# バックエンドリポジトリ (chat-app-backend)
main                                    # 本番環境（保護対象）
└── feature/group-chat-api              # API開発ブランチ
    ├── feat/phase1-database-schema     # データベース設計
    ├── feat/phase2-stripe-api          # Stripe API統合
    ├── feat/phase3-group-management    # グループ管理API
    ├── feat/phase4-qr-endpoints        # QRコード関連API
    └── feat/phase5-external-auth       # 外部認証API

# フロントエンドリポジトリ (chat-app-frontend)
main                                    # 本番環境（保護対象）
└── feature/group-chat-ui               # UI開発ブランチ
    ├── feat/phase1-user-model          # ユーザーモデル拡張
    ├── feat/phase2-pricing-page        # 課金ページUI
    ├── feat/phase3-admin-dashboard     # 管理者ダッシュボード
    ├── feat/phase4-qr-components       # QRコード関連UI
    └── feat/phase5-external-auth-ui    # 外部認証UI
```

### 開発フロー

#### 1. 各リポジトリでの開発ブランチ作成

```bash
# メインリポジトリ（既に作成済み）
git checkout feature/group-chat-service

# バックエンドリポジトリ
cd backend
git checkout -b feature/group-chat-api
git push -u origin feature/group-chat-api

# フロントエンドリポジトリ
cd ../frontend
git checkout -b feature/group-chat-ui
git push -u origin feature/group-chat-ui
cd ..
```

#### 2. フェーズ別の開発作業

```bash
# 例: フェーズ1（データベース設計）の場合

# バックエンド
cd backend
git checkout feature/group-chat-api
git checkout -b feat/phase1-database-schema
# データベース設計・マイグレーション作成...
git add .
git commit -m "feat(phase1): add groups and subscriptions database schema"
git push origin feat/phase1-database-schema

# フロントエンド
cd ../frontend
git checkout feature/group-chat-ui
git checkout -b feat/phase1-user-model
# ユーザーモデル拡張...
git add .
git commit -m "feat(phase1): extend User interface for subscription features"
git push origin feat/phase1-user-model

# メインリポジトリ
cd ..
# 統合テスト・ドキュメント更新...
git add .
git commit -m "docs(phase1): update integration status and testing notes"
git push origin feature/group-chat-service
```

#### 3. フェーズ完了時のマージ

```bash
# 各リポジトリでフェーズブランチをマージ
cd backend
git checkout feature/group-chat-api
git merge feat/phase1-database-schema
git push origin feature/group-chat-api

cd ../frontend
git checkout feature/group-chat-ui
git merge feat/phase1-user-model
git push origin feature/group-chat-ui
```

## 開発コーディネーション

### フェーズ間の依存関係管理

| フェーズ | バックエンド     | フロントエンド       | 依存関係                      |
| -------- | ---------------- | -------------------- | ----------------------------- |
| Phase 1  | データベース設計 | ユーザーモデル拡張   | 並行開発可能                  |
| Phase 2  | Stripe API       | 課金ページ UI        | バックエンド → フロントエンド |
| Phase 3  | グループ管理 API | 管理者ダッシュボード | バックエンド → フロントエンド |
| Phase 4  | QR コード API    | QR コンポーネント    | バックエンド → フロントエンド |
| Phase 5  | 外部認証 API     | 外部認証 UI          | バックエンド → フロントエンド |

### 統合テストのタイミング

```bash
# 各フェーズ完了後の統合テスト
# 1. バックエンドのテスト
cd backend
composer test  # または php artisan test

# 2. フロントエンドのテスト
cd ../frontend
npm run test

# 3. 統合テスト（E2E）
cd ..
npm run test:e2e  # 全体のE2Eテスト
```

## ブランチ保護とマージルール

### 開発ブランチ（各リポジトリ）

- `feature/group-chat-api` (backend)
- `feature/group-chat-ui` (frontend)
- `feature/group-chat-service` (main)
- フェーズブランチからのマージは開発者が実施可能
- CI/CD テストはパス必須

### main ブランチ（各リポジトリ）

- **絶対に main ブランチへの直接コミットは禁止**
- 開発ブランチからのマージのみ許可
- **必ずプロジェクトオーナー（あなた）の承認が必要**
- 全機能のテスト完了後のみマージ

## リポジトリ間の連携コマンド

### 開発ブランチの一括作成

```bash
# 新機能開発時の一括セットアップ
./scripts/setup-feature-branches.sh group-chat-service
```

### 一括ステータス確認

```bash
# 全リポジトリのステータス確認
echo "=== Main Repository ==="
git status --short

echo "=== Backend Repository ==="
cd backend && git status --short && cd ..

echo "=== Frontend Repository ==="
cd frontend && git status --short && cd ..
```

### 一括プル

```bash
# 全リポジトリの最新状態を取得
git pull origin feature/group-chat-service
cd backend && git pull origin feature/group-chat-api && cd ..
cd frontend && git pull origin feature/group-chat-ui && cd ..
```

## デプロイメント戦略

### ステージング環境

- **バックエンド**: Railway（`feature/group-chat-api` ブランチ専用）
- **フロントエンド**: Vercel（`feature/group-chat-ui` ブランチ専用）
- **統合テスト**: メインリポジトリから各ステージング環境への接続テスト

### 本番デプロイ

```bash
# 1. バックエンドデプロイ
cd backend
git checkout main
git merge feature/group-chat-api
git push origin main  # 本番デプロイ実行

# 2. フロントエンドデプロイ
cd ../frontend
git checkout main
git merge feature/group-chat-ui
git push origin main  # 本番デプロイ実行

# 3. メインリポジトリ更新
cd ..
git checkout main
git merge feature/group-chat-service
git push origin main
```

## 緊急時の対応

### 本番環境に問題が発生した場合

```bash
# 各リポジトリで緊急修正
cd backend
git checkout main
git checkout -b hotfix/urgent-backend-fix
# 修正作業...
git checkout main && git merge hotfix/urgent-backend-fix && git push

cd ../frontend
git checkout main
git checkout -b hotfix/urgent-frontend-fix
# 修正作業...
git checkout main && git merge hotfix/urgent-frontend-fix && git push

# 開発ブランチにも反映
cd ../backend
git checkout feature/group-chat-api && git merge main

cd ../frontend
git checkout feature/group-chat-ui && git merge main
```

## 現在の状況

- ✅ メインリポジトリ: `feature/group-chat-service` ブランチ作成済み
- ✅ 実装計画書とユーザーモデル拡張完了
- 🚧 バックエンド: `feature/group-chat-api` ブランチ作成予定
- 🚧 フロントエンド: `feature/group-chat-ui` ブランチ作成予定
- 🚧 フェーズ 1（データモデル設計）開始準備中

## 次のステップ

1. バックエンドとフロントエンドの開発ブランチ作成
2. ステージング環境の準備（各リポジトリ）
3. フェーズ 1 ブランチ作成とデータベース設計開始
4. 各フェーズの段階的実装
5. 統合テストと最終承認プロセス
