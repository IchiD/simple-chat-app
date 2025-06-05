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
echo "🛠️  Railway環境でマイグレーション確認中..."
echo "📋 Procfileで自動マイグレーションが設定されました"
echo "   release: php artisan migrate --force"
echo ""
echo "⏳ Railwayでのデプロイとマイグレーションが進行中..."
echo "💡 以下で進行状況を確認してください:"
echo "   • Railway Dashboard: https://railway.app/dashboard"
echo "   • デプロイログでマイグレーション実行を確認"
echo "   • 数分後に本番サイトでエラーが解消されることを確認"

# オプション: Railway CLIが利用可能な場合のみ実行
if command -v railway &> /dev/null; then
    echo ""
    echo "🔧 Railway CLI利用可能 - 接続テスト中..."
    if railway status &> /dev/null; then
        echo "✅ Railway接続成功 - マイグレーション状況確認中..."
        railway run php artisan migrate:status || echo "⚠️  マイグレーション状況確認失敗（通常の動作）"
    else
        echo "⚠️  Railway接続失敗 - Procfileの自動マイグレーションに依存"
    fi
else
    echo "ℹ️  Railway CLIなし - Procfileの自動マイグレーションに依存"
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