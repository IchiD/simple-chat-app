# 開発ワークフロー - 1 対多数チャット機能

## ブランチ戦略

### メインブランチ構成

```
main                                    # 本番環境（保護対象）
├── feature/group-chat-service          # メイン開発ブランチ
    ├── feat/phase1-data-models         # フェーズ1: データモデル
    ├── feat/phase2-stripe-integration  # フェーズ2: Stripe決済
    ├── feat/phase3-group-chat         # フェーズ3: グループチャット
    ├── feat/phase4-qr-code            # フェーズ4: QRコード機能
    └── feat/phase5-api-auth           # フェーズ5: API連携認証
```

### 開発フロー

#### 1. 各フェーズの開発

```bash
# フェーズ別ブランチの作成例
git checkout feature/group-chat-service
git pull origin feature/group-chat-service
git checkout -b feat/phase1-data-models

# 開発作業...

# 完了後
git add .
git commit -m "feat(phase1): implement database schema and user model extensions"
git push origin feat/phase1-data-models
```

#### 2. フェーズ完了時のマージ

```bash
# メイン開発ブランチに戻る
git checkout feature/group-chat-service

# フェーズブランチをマージ
git merge feat/phase1-data-models

# リモートに反映
git push origin feature/group-chat-service
```

#### 3. 全機能完成後の本番反映

```bash
# 最終確認後、mainブランチにマージ
git checkout main
git merge feature/group-chat-service
git push origin main
```

## 開発環境での確認方法

### ローカル環境でのテスト

```bash
# 開発ブランチで作業
git checkout feature/group-chat-service

# 依存関係のインストール（必要に応じて）
cd frontend && npm install

# 開発サーバー起動
npm run dev
```

### ステージング環境（推奨）

- `feature/group-chat-service` ブランチ専用のステージング環境を用意
- 各フェーズ完了後にステージングでの動作確認を実施
- 問題がなければ次のフェーズに進行

## マージルール

### feature/group-chat-service ブランチ

- 各フェーズブランチからのマージは開発者が実施可能
- コードレビューは推奨だが必須ではない
- CI/CD テストはパス必須

### main ブランチ

- **絶対に main ブランチへの直接コミットは禁止**
- `feature/group-chat-service` からのマージのみ許可
- **必ずプロジェクトオーナー（あなた）の承認が必要**
- 全機能のテスト完了後のみマージ

## 緊急時の対応

### 本番環境に問題が発生した場合

```bash
# 緊急修正用ブランチ作成
git checkout main
git checkout -b hotfix/urgent-fix

# 修正作業...

# 修正完了後、直接mainにマージ
git checkout main
git merge hotfix/urgent-fix
git push origin main

# 開発ブランチにも反映
git checkout feature/group-chat-service
git merge main
```

## コミットメッセージ規約

### フォーマット

```
<type>(<scope>): <description>

[optional body]
```

### 例

```bash
feat(phase1): add groups and subscriptions database schema
fix(stripe): resolve webhook signature validation issue
docs(readme): update setup instructions for group chat feature
test(api): add unit tests for group management endpoints
```

## 注意事項

1. **main ブランチの保護**

   - 現在の安定版を保持
   - 直接コミット・プッシュは禁止
   - 最終承認後のみマージ

2. **開発ブランチでの作業**

   - `feature/group-chat-service` をベースに開発
   - 各フェーズは独立したブランチで作業
   - 定期的にリモートにプッシュしてバックアップ

3. **コードレビュー**

   - 大きな変更は事前相談
   - 各フェーズ完了時の簡易レビュー
   - 最終マージ前の包括的レビュー

4. **テスト**
   - 各フェーズで機能テスト実施
   - 既存機能への影響確認
   - ステージング環境での統合テスト

## 現在の状況

- ✅ メイン開発ブランチ `feature/group-chat-service` 作成済み
- ✅ 実装計画書とユーザーモデル拡張完了
- 🚧 フェーズ 1（データモデル設計）開始準備中

## 次のステップ

1. ステージング環境の準備（推奨）
2. フェーズ 1 ブランチ作成とデータベース設計開始
3. 各フェーズの段階的実装
4. 最終テストと承認プロセス
