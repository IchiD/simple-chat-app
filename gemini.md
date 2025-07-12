# Gemini CLI プロジェクト参加ガイド

## 🎯 プロジェクト概要

このプロジェクトは、既存のチャットアプリケーションに**有料 1 対多チャットサービス機能**を追加する開発プロジェクトです。

### システム構成

- **Backend**: Laravel 10 (PHP 8.1+)
- **Frontend**: Nuxt 3 + Vue 3 + TypeScript
- **Database**: MySQL
- **Payment**: Stripe API
- **Authentication**: Laravel Sanctum

### 主要機能

1. **グループチャット機能** - 有料プランユーザーが複数人とチャット
2. **Stripe 決済機能** - サブスクリプション管理
3. **QR コード参加機能** - QR コードでグループ参加
4. **外部 API 連携** - 他システムとの認証連携
5. **管理者機能** - 決済・ユーザー管理

---

## 📋 開発ルールと制約

### 🚨 絶対遵守事項

1. **ブランチ保護**: `main` ブランチでの直接作業は**絶対禁止**
2. **段階的実装**: 指定されたフェーズ順序を厳密に遵守
3. **技術スタック固定**: 既存の技術バージョンを変更禁止
4. **UI/UX 変更禁止**: 明示的指示なしでのデザイン変更禁止
5. **日本語対応**: 解説・コメントは日本語で記述

### 🔄 開発フロー

```
Phase 1: データモデル設計 (並行開発可能)
    ↓
Phase 2: Stripe 決済機能 (Backend → Frontend)
    ↓
Phase 3: グループチャット機能 (Backend → Frontend)
    ↓
Phase 4: QR コード機能 (Backend → Frontend)
    ↓
Phase 5: API 連携ログイン (Backend → Frontend)
```

---

## 📂 ディレクトリ構成

```
chat-app_nuxt/
├── backend/              # Laravel 10 アプリケーション
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   └── Services/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── routes/
├── frontend/             # Nuxt 3 アプリケーション
│   ├── components/
│   ├── pages/
│   ├── composables/
│   └── stores/
└── ドキュメント/
    ├── implementation_plan.md
    ├── data_models.md
    ├── technologystack.md
    └── setup_guide.md
```

---

## 🛠️ 技術仕様

### Backend (Laravel 10)

- **認証**: Laravel Sanctum トークン認証
- **Database**: MySQL with Eloquent ORM
- **Testing**: PHPUnit
- **API**: RESTful API design
- **Payment**: Stripe Webhook integration

### Frontend (Nuxt 3)

- **Framework**: Vue 3 + Composition API
- **Language**: TypeScript
- **State**: Pinia store
- **Styling**: Tailwind CSS
- **Routing**: Nuxt file-based routing
- **Testing**: Vitest

---

## 📋 重要ファイル

### 必読ドキュメント

1. **`implementation_plan.md`** - 詳細な技術仕様とフェーズ別実装計画
2. **`data_models.md`** - データベース設計とモデル定義
3. **`technologystack.md`** - 技術スタックの詳細
4. **`setup_guide.md`** - 環境構築手順
5. **`AGENTS.md`** - AI エージェント向け指示書

### 設定ファイル

- **Backend**: `backend/config/`, `backend/database/`
- **Frontend**: `frontend/nuxt.config.ts`, `frontend/tailwind.config.js`

---

## 💻 開発環境セットアップ

### 前提条件

```bash
# Backend
- PHP 8.1+
- Composer
- MySQL 8.0+
- Laravel 10

# Frontend
- Node.js 18+
- npm/yarn
- TypeScript 5+
```

### 起動手順

```bash
# Backend
cd backend
composer install
php artisan serve

# Frontend
cd frontend
npm install
npm run dev
```

---

## 🎯 各フェーズの重要ポイント

### Phase 1: データモデル設計

- **並行開発可**: Backend と Frontend で同時実行可能
- **Backend**: Laravel マイグレーション + Eloquent モデル
- **Frontend**: User インターフェース拡張

### Phase 2: Stripe 決済機能

- **Backend 優先**: API 実装完了後に Frontend 開始
- **重要**: Webhook 署名検証、エラーハンドリング
- **Frontend**: 価格表示ページ、決済フロー UI

### Phase 3: グループチャット機能

- **Backend**: RESTful API
- **Frontend**: 管理者ダッシュボード、チャット UI
- **重要**: 認可制御、メンバー管理

### Phase 4: QR コード機能

- **Backend**: QR 生成、トークン管理、参加ロジック
- **Frontend**: QR 表示、参加ランディングページ
- **重要**: セキュリティ、有効期限管理

### Phase 5: API 連携ログイン

- **Backend**: 外部システム向け認証 API
- **Frontend**: 連携フロー UI
- **重要**: OAuth2 風実装、レート制限

---

## ⚠️ 注意事項

### セキュリティ

- **認証**: 全 API エンドポイントでトークン検証
- **認可**: ユーザー権限の適切なチェック
- **入力検証**: XSS、SQL インジェクション対策
- **決済**: Stripe Webhook 署名検証

### パフォーマンス

- **Database**: 適切なインデックス設定
- **API**: ページネーション実装
- **Frontend**: レスポンシブデザイン対応

### テスト

- **Backend**: PHPUnit でのユニット・統合テスト
- **Frontend**: Vitest でのコンポーネントテスト
- **E2E**: 決済フローの完全テスト

---

## 🔍 gemini CLI 利用時の推奨事項

### 質問する際の情報

1. **フェーズ番号**: 現在作業中のフェーズ
2. **リポジトリ**: Backend / Frontend
3. **エラー内容**: 詳細なエラーメッセージ
4. **実行環境**: OS、Node.js/PHP バージョン等
5. **期待動作**: 本来期待していた結果

### 効率的な開発のために

- **コード生成**: TypeScript/PHP の型定義重視
- **テスト作成**: TDD アプローチの採用
- **ドキュメント**: 変更時は必ず更新
- **リファクタリング**: 既存コードの改善提案

---

## 📞 サポート

### トラブルシューティング

1. **環境問題**: `setup_guide.md` を参照
2. **API エラー**: Laravel ログとブラウザのネットワークタブを確認
3. **決済エラー**: Stripe ダッシュボードで Webhook ログを確認
4. **フロントエンド**: Vue DevTools での状態確認

### 参考リソース

- [Laravel 10 Documentation](https://laravel.com/docs/10.x)
- [Nuxt 3 Documentation](https://nuxt.com/docs)
- [Stripe API Documentation](https://stripe.com/docs/api)
- [Vue 3 Composition API](https://vuejs.org/guide/extras/composition-api-faq.html)

---

## 🏁 最終目標

このプロジェクトの最終的な目標は、**安全で使いやすい有料グループチャットサービス**を既存のチャットアプリに統合することです。

### 成功基準

- [ ] 全フェーズの統合テスト成功
- [ ] セキュリティ監査の完了
- [ ] パフォーマンステストの成功
- [ ] 本番デプロイの準備完了
- [ ] 運用ドキュメントの整備完了

---

_このドキュメントは gemini CLI がプロジェクトに効率的に参加するための重要な指針です。開発開始前に必ず熟読してください。_
