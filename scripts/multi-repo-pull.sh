#!/bin/bash

# マルチリポジトリ一括プルスクリプト
echo "============================================="
echo "🔄 Multi-Repository Pull Script"
echo "============================================="

echo ""
echo "📁 Updating Main Repository..."
echo "---------------------------------------------"
git pull origin feature/group-chat-service
echo "✅ Main repository updated"

echo ""
echo "📁 Updating Backend Repository..."
echo "---------------------------------------------"
cd backend
git pull origin feature/group-chat-api
cd ..
echo "✅ Backend repository updated"

echo ""
echo "📁 Updating Frontend Repository..."
echo "---------------------------------------------"
cd frontend
git pull origin feature/group-chat-ui
cd ..
echo "✅ Frontend repository updated"

echo ""
echo "============================================="
echo "🎉 All repositories updated successfully!"
echo "=============================================" 