#!/bin/bash

# Chat App デプロイスクリプト
# 使用方法: ./deploy.sh "コミットメッセージ"

set -e  # エラー時に停止

COMMIT_MESSAGE="${1:-Update}"

echo "🚀 Chat App デプロイ開始..."
echo "📝 コミットメッセージ: $COMMIT_MESSAGE"

# 1. メインリポジトリにコミット・プッシュ
echo ""
echo "📦 メインリポジトリ (simple-chat-app) にプッシュ中..."
git add .
git commit -m "$COMMIT_MESSAGE" || echo "⚠️  メインリポジトリに変更なし"
git push origin main

# 2. フロントエンドデプロイ
echo ""
echo "🎨 フロントエンド (Vercel) デプロイ中..."
cd frontend
git add .
git commit -m "$COMMIT_MESSAGE" || echo "⚠️  フロントエンドに変更なし"
git push origin main
echo "✅ フロントエンドデプロイ完了"

# 3. バックエンドデプロイ  
echo ""
echo "⚙️  バックエンド (Railway) デプロイ中..."
cd ../backend
git add .
git commit -m "$COMMIT_MESSAGE" || echo "⚠️  バックエンドに変更なし"
git push origin main
echo "✅ バックエンドデプロイ完了"

# 4. Railway SSH経由でのマイグレーション実行
echo ""
echo "🛠️  Railway SSH経由でマイグレーション確認・実行中..."

# Railway CLIの確認
if ! command -v railway &> /dev/null; then
    echo "❌ Railway CLIがインストールされていません"
    echo "💡 インストール方法: brew install railway"
    echo "⚠️  手動でRailway Dashboardからマイグレーションを確認してください"
    echo "   https://railway.app/dashboard"
    exit 1
fi

# Railway接続確認
if ! railway status &> /dev/null; then
    echo "❌ Railway接続失敗"
    echo "💡 以下のコマンドでログインしてください:"
    echo "   railway login"
    echo "   railway connect (webサービスを選択)"
    exit 1
fi

echo "✅ Railway接続確認済み"

# デプロイ完了まで待機
echo "⏳ Railway デプロイ完了まで待機中..."
sleep 30

# Railway SSH経由でマイグレーション状況確認・実行
echo "🔍 マイグレーション状況確認中..."

# SSH経由でマイグレーション確認・実行
MIGRATION_SCRIPT=$(cat << 'EOF'
# マイグレーション状況確認
echo "📊 現在のマイグレーション状況:"
php artisan migrate:status

# 未実行のマイグレーションがあるかチェック
PENDING_MIGRATIONS=$(php artisan migrate:status --no-ansi | grep -c "Pending" || echo "0")

if [ "$PENDING_MIGRATIONS" -gt 0 ]; then
    echo ""
    echo "🔧 $PENDING_MIGRATIONS 個の未実行マイグレーションを発見しました"
    echo "🚀 マイグレーション実行中..."
    
    # マイグレーション実行
    if php artisan migrate --force --no-interaction; then
        echo "✅ マイグレーション実行完了"
        echo ""
        echo "📊 更新後のマイグレーション状況:"
        php artisan migrate:status
    else
        echo "❌ マイグレーション実行失敗"
        echo "🔧 個別マイグレーション実行を試行中..."
        
        # 失敗した場合、個別実行を試行
        for migration in $(php artisan migrate:status --no-ansi | grep "Pending" | awk '{print $2}'); do
            echo "⚡ 個別実行: $migration"
            php artisan migrate --path=database/migrations/${migration}.php --force --no-interaction || echo "⚠️  $migration 失敗 - スキップ"
        done
        
        echo ""
        echo "📊 最終マイグレーション状況:"
        php artisan migrate:status
    fi
else
    echo "✅ すべてのマイグレーションが実行済みです"
fi

# データベース接続テスト
echo ""
echo "🔌 データベース接続テスト..."
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected successfully';" || echo "❌ データベース接続失敗"

echo ""
echo "🎯 Railway SSH セッション完了"
EOF
)

echo "🌐 Railway SSH経由でマイグレーション実行中..."
echo "   (SSH接続が開始されます - 自動実行されます)"

# SSH経由でスクリプト実行
if railway ssh --command "bash -c \"$MIGRATION_SCRIPT\""; then
    echo "✅ Railway SSH経由マイグレーション完了"
else
    echo "⚠️  Railway SSH実行に失敗しました"
    echo "💡 手動での確認方法:"
    echo "   cd backend"
    echo "   railway ssh"
    echo "   php artisan migrate:status"
    echo "   php artisan migrate --force"
fi

# 5. 完了
cd ..
echo ""
echo "🎉 全てのデプロイが完了しました！"
echo "🌐 フロントエンド: Vercel (自動デプロイ)"
echo "🛠️  バックエンド: Railway (自動デプロイ + SSH マイグレーション)"
echo ""
echo "📊 確認URL:"
echo "• Frontend: https://vercel.com/dashboard"
echo "• Backend: https://railway.app/dashboard"
echo "• Admin Dashboard: https://web-production-4f969.up.railway.app/admin/dashboard"
echo ""
echo "🔧 本番環境での手動確認方法:"
echo "   cd backend && railway ssh"
echo "   php artisan migrate:status" 