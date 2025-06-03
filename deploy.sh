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

# 4. 完了
cd ..
echo ""
echo "🎉 全てのデプロイが完了しました！"
echo "🌐 フロントエンド: Vercel (自動デプロイ)"
echo "🛠️  バックエンド: Railway (自動デプロイ)"
echo ""
echo "📊 デプロイ状況確認:"
echo "• Frontend: https://vercel.com/dashboard"
echo "• Backend: https://railway.app/dashboard" 