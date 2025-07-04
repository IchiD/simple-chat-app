# 🚀 LumoChat - 高機能チャットアプリケーション

<div align="center">
  <img src="./frontend/public/images/rogo.png" alt="LumoChat Logo" width="200"/>
  
  **リアルタイム通信 × 有料課金 × 管理機能を統合したモダンなチャットプラットフォーム**
  
  [![Deploy Status](https://img.shields.io/badge/Deploy-Railway%20%2B%20Vercel-blue)](https://railway.app)
  [![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
  [![Version](https://img.shields.io/badge/Version-1.0.0-orange)](CHANGELOG.md)
</div>

---

## 🎯 プロジェクト概要

LumoChat は、**個人 ↔ 個人** および **1 対多数のグループチャット** 機能を持つ、次世代のチャットアプリケーションです。

### 🌟 主要機能

| 機能カテゴリ        | 機能詳細                                            |
| ------------------- | --------------------------------------------------- |
| **👥 チャット機能** | 1 対 1 チャット、グループチャット、サポートチャット |
| **💳 決済・課金**   | Stripe 統合、サブスクリプション管理、プラン変更     |
| **👑 管理機能**     | ユーザー管理、決済管理、サポート対応                |
| **🔐 認証システム** | 通常登録、Google 認証、パスワードリセット           |
| **📱 リアルタイム** | プッシュ通知、未読メッセージ管理                    |
| **🎨 UI/UX**        | モダンなデザイン、レスポンシブ対応                  |

---

## 🛠️ 技術スタック

### Frontend

- **Framework**: Nuxt 3 + Vue 3 + TypeScript
- **UI**: Tailwind CSS + Headless UI
- **状態管理**: Pinia
- **認証**: Sanctum Token
- **リアルタイム**: Server-Sent Events (SSE)

### Backend

- **Framework**: Laravel 11 + PHP 8.2
- **API**: RESTful API + JSON
- **認証**: Laravel Sanctum
- **決済**: Stripe API
- **通知**: Web Push API

### Database

- **開発**: MySQL
- **テスト**: SQLite
- **本番**: MySQL (Railway)

### Infrastructure

- **Frontend**: Vercel
- **Backend**: Railway
- **監視**: Laravel Telescope
- **メール**: Gmail SMTP

---

## 🚀 クイックスタート

### 前提条件

- Node.js 18.0+ / npm 8.0+
- PHP 8.2+ / Composer 2.0+
- MySQL（開発環境）

### 1. リポジトリクローン

```bash
git clone https://github.com/IchiD/simple-chat-app
cd chat-app_nuxt
```

### 2. バックエンドセットアップ

```bash
cd backend
composer install
cp .env.example .env
sail up -d
sail artisan key:generate
sail artisan migrate
sail artisan db:seed
sail artisan serve
```

### 3. フロントエンドセットアップ

```bash
cd frontend
npm install
cp .env.example .env
npm run dev
```

### 4. 起動確認

- **Backend**: http://localhost
- **Frontend**: http://localhost:3000
- **Admin**: http://localhost/admin/login

---

## 📚 ドキュメント一覧

### 🏗️ 開発・設計ドキュメント

| ドキュメント                                                 | 概要                           | 対象者                   |
| ------------------------------------------------------------ | ------------------------------ | ------------------------ |
| [📋 実装計画](./implementation_plan.md)                      | 全体的な実装計画とフェーズ管理 | 開発者全般               |
| [🎯 開発戦略](./DEVELOPMENT_STRATEGY.md)                     | 並行開発 vs 順次開発の戦略     | プロジェクトマネージャー |
| [🔄 開発ワークフロー](./DEVELOPMENT_WORKFLOW.md)             | Git 運用とコードレビュー       | 開発者                   |
| [📊 データベース最適化](./database_optimization_proposal.md) | DB 最適化の提案書              | バックエンドエンジニア   |

### ⚙️ セットアップ・設定ガイド

| サービス            | ガイド                                                               | 詳細                    |
| ------------------- | -------------------------------------------------------------------- | ----------------------- |
| **💳 Stripe**       | [Stripe 設定ガイド](./backend/STRIPE_SETUP.md)                       | 決済機能の設定手順      |
| **🚀 Railway**      | [Railway デプロイガイド](./RAILWAY_DEPLOYMENT_GUIDE.md)              | 本番環境へのデプロイ    |
| **📧 Gmail**        | [Gmail SMTP 設定](./backend/GMAIL_SETUP_GUIDE.md)                    | メール送信機能の設定    |
| **🐳 Docker**       | [Docker 再起動チェックリスト](./backend/DOCKER_RESTART_CHECKLIST.md) | Docker 環境の管理       |
| **⚓ Laravel Sail** | [Sail 起動ガイド](./backend/SAIL_STARTUP_GUIDE.md)                   | Laravel Sail の使用方法 |

### 🧪 テスト・品質管理

| ドキュメント                                                      | 概要                         | 対象者     |
| ----------------------------------------------------------------- | ---------------------------- | ---------- |
| [🧪 テストガイド](./backend/TESTING.md)                           | 単体・結合・E2E テストの実行 | 開発者・QA |
| [🔧 トラブルシューティング](./backend/RAILWAY_TROUBLESHOOTING.md) | よくある問題と解決策         | 運用担当者 |

### 🎛️ 管理・運用ドキュメント

| ドキュメント                                        | 概要                  | 対象者 |
| --------------------------------------------------- | --------------------- | ------ |
| [👑 管理者セットアップ](./backend/ADMIN_SETUP.md)   | 管理画面の初期設定    | 管理者 |
| [💰 決済機能実装](./管理画面_決済機能実装指示書.md) | 決済機能の詳細仕様    | 開発者 |
| [📬 Webhook 設定](./backend/WEBHOOK_SETUP_GUIDE.md) | Stripe Webhook の設定 | 開発者 |

### 🔧 個別機能ドキュメント

| 機能                          | ドキュメント                                                      | 詳細                     |
| ----------------------------- | ----------------------------------------------------------------- | ------------------------ |
| **📩 新着メッセージフィルタ** | [新着メッセージフィルタ機能](./新着メッセージフィルタ機能実装.md) | フィルタリング機能の実装 |
| **💎 決済機能改善**           | [決済機能改善計画](./決済機能改善計画書.md)                       | 決済フローの改善提案     |
| **🤖 AI エージェント**        | [AI エージェント設定](./AI_AGENT_INSTRUCTIONS.md)                 | 開発支援 AI の活用       |

---

## 🏗️ プロジェクト構造

```
chat-app_nuxt/
├── 📁 backend/          # Laravel バックエンド
│   ├── app/            # アプリケーションロジック
│   ├── database/       # マイグレーション・シーダー
│   ├── routes/         # API・Web ルーティング
│   └── 📖 README.md    # バックエンド専用ドキュメント
├── 📁 frontend/         # Nuxt フロントエンド
│   ├── pages/          # ページコンポーネント
│   ├── components/     # 再利用可能コンポーネント
│   ├── composables/    # Vue Composition API
│   └── 📖 README.md    # フロントエンド専用ドキュメント
├── 📁 scripts/         # 自動化スクリプト
└── 📖 README.md        # 🎯 このファイル（プロジェクトの中心）
```

---

## 🔄 開発フロー

### 1. 新機能開発

```bash
# 1. 実装計画確認
cat implementation_plan.md

# 2. 開発戦略確認
cat DEVELOPMENT_STRATEGY.md

# 3. 機能開発
git checkout -b feature/new-feature
# 開発作業...
git push origin feature/new-feature
```

### 2. テスト実行

```bash
# バックエンドテスト
cd backend && php artisan test

# フロントエンドテスト
cd frontend && npm run test
```

### 3. デプロイ

```bash
# Railway（バックエンド）
git push railway main

# Vercel（フロントエンド）
# GitHub連携で自動デプロイ
```

### 4. 環境変数管理

プロジェクトには`.env`ファイルの安全なバックアップ管理システムが含まれています：

```bash
# バックエンドディレクトリで実行
cd backend

# 現在の.envファイルをバックアップ
./env-backup.sh backup

# バックアップから.envファイルを復元
./env-backup.sh restore

# 古いバックアップファイルを削除
./env-backup.sh cleanup
```

**特徴**:

- ✅ 自動的にタイムスタンプ付きバックアップを作成
- ✅ 最大 5 個までのバックアップを保持（自動クリーンアップ）
- ✅ 復元時の安全性（復元前に現在の設定をバックアップ）
- ✅ .gitignore で機密情報を適切に除外

---

## 📊 プロジェクト状況

### 🚀 現在の実装状況

| フェーズ    | 機能             | 進捗 | 状態      |
| ----------- | ---------------- | ---- | --------- |
| **Phase 1** | データモデル設計 | 100% | ✅ 完了   |
| **Phase 2** | Stripe 決済機能  | 100% | ✅ 完了   |
| **Phase 3** | グループチャット | 100% | ✅ 完了   |
| **Phase 4** | QR コード機能    | 100% | ✅ 完了   |
| **Phase 5** | API 連携ログイン | 90%  | 🔄 進行中 |

### 🎯 次のマイルストーン

- [ ] API 連携ログイン機能完成
- [ ] 負荷テスト実行
- [ ] セキュリティ監査
- [ ] 本番環境最適化

---

## 🤝 コントリビューション

### 開発参加方法

1. **Issues 確認**: [GitHub Issues](https://github.com/your-username/chat-app_nuxt/issues)
2. **開発環境構築**: [セットアップガイド](#-クイックスタート)参照
3. **コーディング規約**: [開発ワークフロー](./DEVELOPMENT_WORKFLOW.md)参照
4. **Pull Request**: 機能実装後、PR を作成

### 報告・要望

- **バグ報告**: [Issue 作成](https://github.com/your-username/chat-app_nuxt/issues/new?template=bug_report.md)
- **機能要望**: [Feature Request](https://github.com/your-username/chat-app_nuxt/issues/new?template=feature_request.md)
- **質問**: [Discussions](https://github.com/your-username/chat-app_nuxt/discussions)

---

## 📞 サポート・連絡先

### 技術サポート

| 問題の種類   | 連絡方法                     | 対応時間    |
| ------------ | ---------------------------- | ----------- |
| **緊急障害** | Email: support@lumo-chat.com | 24 時間以内 |
| **開発質問** | GitHub Issues                | 1-2 営業日  |
| **機能要望** | GitHub Discussions           | 1 週間以内  |

### 開発チーム

- **プロジェクトマネージャー**: [名前](mailto:pm@lumo-chat.com)
- **バックエンドエンジニア**: [名前](mailto:backend@lumo-chat.com)
- **フロントエンドエンジニア**: [名前](mailto:frontend@lumo-chat.com)

---

## 📄 ライセンス

このプロジェクトは [MIT License](LICENSE) のもとで公開されています。

---

## 🎉 謝辞

このプロジェクトは以下の優れたオープンソースプロジェクトを使用しています：

- [Laravel](https://laravel.com) - 素晴らしい PHP フレームワーク
- [Nuxt 3](https://nuxt.com) - 最高の Vue.js フレームワーク
- [Stripe](https://stripe.com) - 信頼できる決済プラットフォーム
- [Tailwind CSS](https://tailwindcss.com) - ユーティリティファースト CSS

---

<div align="center">
  <p>🚀 <strong>LumoChat</strong>で、次世代のコミュニケーションを体験しよう！</p>
  
  [🌟 GitHub Star](https://github.com/your-username/chat-app_nuxt) | 
  [🐛 Issue報告](https://github.com/your-username/chat-app_nuxt/issues) | 
  [💬 Discussion](https://github.com/your-username/chat-app_nuxt/discussions)
</div>
