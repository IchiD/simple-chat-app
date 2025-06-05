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

# 4. Railway マイグレーション実行
echo ""
echo "🛠️  Railway環境でマイグレーション実行中..."
if command -v railway &> /dev/null; then
    echo "📊 マイグレーション状況確認..."
    railway run php artisan migrate:status || echo "⚠️  マイグレーション状況の確認に失敗"
    
    echo "🔄 マイグレーション実行..."
    if railway run php artisan migrate --force; then
        echo "✅ マイグレーション実行完了"
    else
        echo "❌ マイグレーション実行に失敗しました"
        echo "💡 手動でRailwayダッシュボードから確認してください"
    fi
else
    echo "⚠️  Railway CLIが見つかりません"
    echo "💡 インストール: npm install -g @railway/cli"
    echo "💡 ログイン: railway login"
    echo "💡 または手動でRailwayダッシュボードから確認してください"
fi

# 5. 完了
cd ..
echo ""
echo "🎉 全てのデプロイが完了しました！"
echo "🌐 フロントエンド: Vercel (自動デプロイ)"
echo "🛠️  バックエンド: Railway (自動デプロイ + マイグレーション)"
echo ""
echo "📊 デプロイ状況確認:"
echo "• Frontend: https://vercel.com/dashboard"
echo "• Backend: https://railway.app/dashboard" 