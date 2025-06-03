# Railway Laravel バックエンド デプロイガイド

## 📋 概要

Laravel バックエンドアプリケーションを Railway にデプロイするための完全ガイド

---

## 🚀 前提条件

- [x] Laravel プロジェクトが完成している
- [x] GitHub リポジトリにコードがプッシュされている
- [x] Railway アカウントを作成済み
- [x] Railway CLI がインストール済み

```bash
npm install -g @railway/cli
```

---

## 📁 1. リポジトリの準備

### Git リポジトリの分離

```bash
# バックエンド用リポジトリの初期化
cd backend
git init
git add .
git commit -m "Initial backend commit - Laravel API"
git remote add origin https://github.com/YOUR_USERNAME/your-app-backend.git
git branch -M main
git push -u origin main
```

---

## 🏗️ 2. Railway プロジェクト作成

### 2.1 Railway Dashboard でプロジェクト作成

1. [Railway Dashboard](https://railway.app) にアクセス
2. **New Project** をクリック
3. **Deploy from GitHub repo** を選択
4. バックエンドリポジトリを選択
5. プロジェクト名を設定

### 2.2 CLI でログイン（オプション）

```bash
railway login --browserless
# 表示されたURLにアクセスしてペアリングコードを入力
```

---

## 🗄️ 3. データベース設定

### 3.1 MySQL サービス追加

1. Railway Dashboard でプロジェクトを開く
2. **+ Add Service** → **MySQL** を選択
3. MySQL サービスが作成されるまで待機

### 3.2 データベース接続情報確認

MySQL サービスの **Connect** タブで接続情報を確認：

- **MYSQLHOST**: `mysql.railway.internal`
- **MYSQLPORT**: `3306`
- **MYSQLDATABASE**: `railway`
- **MYSQLUSER**: `root`
- **MYSQLPASSWORD**: `自動生成されたパスワード`

---

## ⚙️ 4. 環境変数設定

### 4.1 必須環境変数

Railway Dashboard で **web サービス** → **Variables** タブ：

```bash
# アプリケーション基本設定
APP_KEY=base64:YOUR_GENERATED_KEY
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-production.up.railway.app

# データベース設定
DB_CONNECTION=mysql
DB_HOST=mysql.railway.internal
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=YOUR_MYSQL_PASSWORD

# HTTPS強制設定
FORCE_HTTPS=true
APP_FORCE_HTTPS=true

# セッション設定
SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=none

# その他の設定
LOG_CHANNEL=stderr
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
BROADCAST_DRIVER=log
```

### 4.2 MySQL 環境変数の Reference 設定

**Variables** タブで **Reference** を使用：

- `DB_HOST` ← MySQL の `MYSQLHOST`
- `DB_PORT` ← MySQL の `MYSQLPORT`
- `DB_DATABASE` ← MySQL の `MYSQLDATABASE`
- `DB_USERNAME` ← MySQL の `MYSQLUSER`
- `DB_PASSWORD` ← MySQL の `MYSQLPASSWORD`

---

## 🔧 5. Laravel コード修正

### 5.1 HTTPS 強制設定

`app/Providers/AppServiceProvider.php` を修正：

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 本番環境でHTTPS強制
        if (app()->environment('production')) {
            URL::forceScheme('https');

            // Railway特有の設定
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                $_SERVER['HTTPS'] = 'on';
            }
        }
    }
}
```

### 5.2 Vite バージョン互換性修正

`package.json` の Vite バージョンを修正：

```json
{
  "devDependencies": {
    "vite": "^5.4.8"
  }
}
```

### 5.3 変更をプッシュ

```bash
git add .
git commit -m "Add Railway production configurations"
git push origin main
```

---

## 🚀 6. デプロイ実行

### 6.1 自動デプロイ

- GitHub への `push` で自動的にデプロイが開始されます
- Railway Dashboard の **Deployments** タブで進行状況を確認

### 6.2 デプロイ設定ファイル（オプション）

`Procfile` をプロジェクトルートに作成：

```bash
web: php artisan serve --host=0.0.0.0 --port=$PORT
```

---

## 🗃️ 7. データベースセットアップ

### 7.1 Railway SSH でマイグレーション実行

```bash
# CLI でプロジェクトにリンク
cd backend
railway link --project your-project-name --service web

# SSH 接続
railway ssh

# マイグレーション実行（SSH内）
php artisan migrate --force

# シーダー実行（SSH内）
php artisan db:seed --class=AdminSeeder --force

# SSH終了
exit
```

### 7.2 マイグレーション順序エラーの対処

テーブル作成前に ALTER TABLE を実行してエラーが発生する場合：

```bash
# 問題のあるマイグレーションを一時的に無効化
mv database/migrations/problem_migration.php database/migrations/problem_migration.php.disabled

# 基本テーブル作成
php artisan migrate --force

# 問題のマイグレーションを再有効化
mv database/migrations/problem_migration.php.disabled database/migrations/problem_migration.php

# 残りのマイグレーション実行
php artisan migrate --force
```

---

## ✅ 8. デプロイ確認

### 8.1 アプリケーション動作確認

1. **Railway Dashboard** で Public URL を確認
2. `https://your-app-production.up.railway.app` にアクセス
3. HTTPS 接続が正常に動作することを確認

### 8.2 管理者ログイン確認

```
URL: https://your-app-production.up.railway.app/admin/login
Email: admin@example.com
Password: password
```

---

## 🔍 9. トラブルシューティング

### 9.1 よくある問題と解決方法

#### Vite バージョンエラー

```bash
# package.json で Vite バージョンを修正
"vite": "^5.4.8"

# 依存関係再インストール
npm install --legacy-peer-deps
```

#### データベース接続エラー

- Railway Dashboard で MySQL 環境変数が正しく設定されているか確認
- `railway variables` コマンドで環境変数を確認

#### HTTPS リダイレクトループ

- `AppServiceProvider.php` の HTTPS 強制設定を確認
- `APP_URL` が HTTPS になっているか確認

#### マイグレーションエラー

- テーブル作成順序を確認
- 問題のマイグレーションを一時的に無効化

### 9.2 ログ確認方法

```bash
# Railway Dashboard
- Deploy Logs タブでビルドログを確認
- Service Logs タブでアプリケーションログを確認

# CLI でログ確認
railway logs
```

---

## 📝 10. 継続的デプロイメント

### 10.1 自動デプロイフロー

1. **ローカル開発** → コード修正
2. **Git コミット** → `git commit -m "修正内容"`
3. **GitHub プッシュ** → `git push origin main`
4. **自動デプロイ** → Railway が自動検知してデプロイ実行
5. **確認** → Railway Dashboard でデプロイ完了を確認

### 10.2 環境変数の管理

- 新しい環境変数が必要な場合は Railway Dashboard で追加
- 環境変数変更後は自動的に再デプロイされる

---

## 🔗 参考リンク

- [Railway Documentation](https://docs.railway.app/)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Railway CLI Reference](https://docs.railway.app/reference/cli-api)

---

## 📞 サポート

デプロイで問題が発生した場合：

1. Railway Dashboard のログを確認
2. GitHub リポジトリの設定を確認
3. 環境変数の設定を再確認

---

**作成日**: 2025 年 6 月 2 日  
**更新日**: 2025 年 6 月 2 日  
**バージョン**: 1.0
