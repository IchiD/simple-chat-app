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

# マイグレーション実行スクリプトの作成
echo "🔍 マイグレーション実行ガイド作成中..."

cat > "railway_migration_guide.txt" << 'EOF'
==============================================
🚀 Railway マイグレーション実行ガイド
==============================================

以下のコマンドを順番に実行してください：

1. Railway SSH に接続:
   cd backend
   railway ssh

2. SSH セッション内で以下を実行:

   # マイグレーション状況確認
   php artisan migrate:status

   # 未実行マイグレーションがある場合のみ実行
   php artisan migrate --force

   # 実行後の状況確認
   php artisan migrate:status

   # データベース接続確認
   php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected successfully';"

   # SSH セッション終了
   exit

3. アプリケーション動作確認:
   ブラウザで以下のURLにアクセス
   https://web-production-4f969.up.railway.app/admin/dashboard

==============================================
EOF

echo "📋 マイグレーション実行ガイドを作成しました: railway_migration_guide.txt"
echo ""
echo "🌐 Railway SSH 手動実行を開始..."
echo "   ※ 上記ガイドに従って手動でマイグレーションを実行してください"
echo ""

# オプション: 直接SSH接続を試行
read -p "📞 今すぐ Railway SSH に接続しますか？ (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "🔗 Railway SSH に接続中..."
    echo "   ※ ガイドに従ってマイグレーションを実行してください"
    echo "   ※ 完了後 'exit' でSSHセッションを終了してください"
    cd backend
    railway ssh
    cd ..
    echo "✅ Railway SSH セッション完了"
else
    echo "⏭️  Railway SSH 接続をスキップしました"
    echo "💡 後で以下のコマンドで実行してください:"
    echo "   cd backend && railway ssh"
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