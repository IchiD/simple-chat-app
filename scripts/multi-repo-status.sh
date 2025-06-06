#!/bin/bash

# マルチリポジトリ状況確認スクリプト
echo "============================================="
echo "🔍 Multi-Repository Status Check"
echo "============================================="

echo ""
echo "📁 Main Repository (simple-chat-app)"
echo "---------------------------------------------"
echo "Current branch: $(git branch --show-current)"
echo "Status:"
git status --short
echo ""

echo "📁 Backend Repository (chat-app-backend)"
echo "---------------------------------------------"
cd backend
echo "Current branch: $(git branch --show-current)"
echo "Status:"
git status --short
cd ..
echo ""

echo "📁 Frontend Repository (chat-app-frontend)"
echo "---------------------------------------------"
cd frontend
echo "Current branch: $(git branch --show-current)"
echo "Status:"
git status --short
cd ..
echo ""

echo "============================================="
echo "✅ Status check complete!"
echo "=============================================" 