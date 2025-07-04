# 🎨 LumoChat Frontend

**Nuxt 3 + Vue 3 + TypeScript + Tailwind CSS** で構築された高機能チャットアプリケーションのフロントエンド

---

## 🎯 概要

LumoChat のフロントエンドは、モダンな Web アプリケーションの技術スタックを使用して構築されています。

### 🌟 主な特徴

- **⚡ 高速**: Nuxt 3 の最適化機能を活用
- **🎨 美しい UI**: Tailwind CSS によるレスポンシブデザイン
- **📱 PWA 対応**: プッシュ通知とオフライン対応
- **🔐 セキュア**: Sanctum 認証と CSRF 保護
- **♿ アクセシブル**: WAI-ARIA ガイドラインに準拠

---

## 🛠️ 技術スタック

### Core

- **Framework**: Nuxt 3.11+ (Vue 3.4+)
- **Language**: TypeScript 5.0+
- **Build Tool**: Vite 5.0+
- **Package Manager**: npm 8.0+

### UI/UX

- **CSS Framework**: Tailwind CSS 3.4+
- **Components**: Headless UI
- **Icons**: Heroicons
- **Animations**: CSS Transitions

### 状態管理・通信

- **State Management**: Pinia
- **HTTP Client**: Nuxt $fetch (ofetch)
- **Real-time**: Server-Sent Events (SSE)
- **Authentication**: Laravel Sanctum

### 開発・品質

- **Linting**: ESLint + Prettier
- **Testing**: Vitest (予定)
- **Type Checking**: TypeScript strict mode

---

## 🚀 セットアップ

### 前提条件

- Node.js 18.0+
- npm 8.0+
- Git

### インストール手順

```bash
# 1. リポジトリクローン
git clone https://github.com/your-username/chat-app_nuxt.git
cd chat-app_nuxt/frontend

# 2. 依存関係インストール
npm install

# 3. 環境変数設定
cp .env.example .env

# 4. 開発サーバー起動
npm run dev
```

### 環境変数設定

`.env`ファイルで以下を設定：

```env
# API設定
NUXT_PUBLIC_API_BASE_URL=http://localhost:8000

# Stripe設定
NUXT_PUBLIC_STRIPE_PUBLISHABLE_KEY=pk_test_xxxxx

# プッシュ通知設定
NUXT_PUBLIC_VAPID_KEY=your-vapid-public-key

# 外部認証設定
NUXT_PUBLIC_GOOGLE_CLIENT_ID=your-google-client-id
```

---

## 🔧 開発コマンド

### 基本コマンド

```bash
# 開発サーバー起動
npm run dev

# プロダクションビルド
npm run build

# プロダクションプレビュー
npm run preview

# 型チェック
npm run typecheck

# リンター実行
npm run lint

# リンター修正
npm run lint:fix
```

### 開発補助コマンド

```bash
# 依存関係更新
npm update

# キャッシュクリア
rm -rf .nuxt .output node_modules/.cache

# 新規インストール
rm -rf node_modules package-lock.json
npm install
```

---

## 📁 プロジェクト構造

```
frontend/
├── 📁 assets/          # 静的リソース
│   ├── css/           # グローバルCSS
│   └── images/        # 画像ファイル
├── 📁 components/      # Vueコンポーネント
│   ├── AppToast.vue   # 通知コンポーネント
│   ├── ChatSidebar.vue # チャットサイドバー
│   └── ...
├── 📁 composables/     # Composition API
│   ├── useApi.ts      # API通信
│   ├── useAuth.ts     # 認証管理
│   ├── useToast.ts    # 通知管理
│   └── ...
├── 📁 layouts/         # レイアウト
│   └── default.vue    # デフォルトレイアウト
├── 📁 middleware/      # ミドルウェア
│   ├── auth.global.ts # 認証チェック
│   └── ...
├── 📁 pages/           # ページコンポーネント
│   ├── auth/          # 認証ページ
│   ├── chat/          # チャットページ
│   ├── user/          # ユーザーページ
│   └── ...
├── 📁 plugins/         # Nuxtプラグイン
├── 📁 public/          # 静的ファイル
│   ├── sw.js          # Service Worker
│   └── ...
├── 📁 server/          # サーバーサイド
├── 📁 stores/          # Pinia ストア
│   └── auth.ts        # 認証ストア
├── 📁 types/           # TypeScript型定義
├── 📁 utils/           # ユーティリティ
├── 📄 nuxt.config.ts   # Nuxt設定
├── 📄 tailwind.config.js # Tailwind設定
└── 📄 tsconfig.json    # TypeScript設定
```

---

## 🎨 デザインシステム

### カラーパレット

```css
/* プライマリーカラー */
--primary-50: #eff6ff;
--primary-500: #3b82f6;
--primary-900: #1e3a8a;

/* セカンダリーカラー */
--secondary-50: #f8fafc;
--secondary-500: #64748b;
--secondary-900: #0f172a;

/* 状態カラー */
--success: #10b981;
--warning: #f59e0b;
--error: #ef4444;
--info: #3b82f6;
```

### タイポグラフィ

```css
/* 見出し */
.text-h1 {
  @apply text-4xl font-bold;
}
.text-h2 {
  @apply text-3xl font-semibold;
}
.text-h3 {
  @apply text-2xl font-medium;
}

/* 本文 */
.text-body {
  @apply text-base;
}
.text-caption {
  @apply text-sm text-gray-600;
}
```

---

## 🔌 主要機能

### 認証システム

```typescript
// composables/useAuth.ts
export const useAuth = () => {
  const login = async (credentials: LoginCredentials) => {
    // ログイン処理
  };

  const register = async (userData: RegisterData) => {
    // 登録処理
  };

  const logout = async () => {
    // ログアウト処理
  };
};
```

### チャット機能

```typescript
// composables/useChat.ts
export const useChat = () => {
  const sendMessage = async (message: string, roomId: string) => {
    // メッセージ送信
  };

  const markAsRead = async (messageId: string) => {
    // 既読処理
  };
};
```

### プッシュ通知

```typescript
// composables/usePushNotification.ts
export const usePushNotification = () => {
  const requestPermission = async () => {
    // 通知許可要求
  };

  const subscribe = async () => {
    // プッシュ通知登録
  };
};
```

---

## 📱 PWA 機能

### Service Worker

```javascript
// public/sw.js
self.addEventListener("push", (event) => {
  const options = {
    body: data.body,
    icon: "/icon-192x192.png",
    badge: "/badge-72x72.png",
    vibrate: [100, 50, 100],
    data: data.url,
  };

  event.waitUntil(self.registration.showNotification(data.title, options));
});
```

### Manifest 設定

```json
// public/manifest.json
{
  "name": "LumoChat",
  "short_name": "LumoChat",
  "description": "高機能チャットアプリケーション",
  "theme_color": "#3b82f6",
  "background_color": "#ffffff",
  "display": "standalone",
  "start_url": "/",
  "icons": [
    {
      "src": "/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png"
    }
  ]
}
```

---

## 🧪 テスト

### 単体テスト

```bash
# テスト実行
npm run test

# テストウォッチモード
npm run test:watch

# カバレッジ確認
npm run test:coverage
```

### E2E テスト

```bash
# E2Eテスト実行
npm run test:e2e

# ヘッドレスモード
npm run test:e2e:headless
```

---

## 🚀 デプロイ

### Vercel デプロイ

```bash
# Vercel CLI インストール
npm i -g vercel

# デプロイ
vercel

# プロダクション デプロイ
vercel --prod
```

### 環境変数設定

Vercel ダッシュボードで以下を設定：

```env
NUXT_PUBLIC_API_BASE_URL=https://your-api-domain.com
NUXT_PUBLIC_STRIPE_PUBLISHABLE_KEY=pk_live_xxxxx
NUXT_PUBLIC_VAPID_KEY=your-vapid-key
```

---

## 🔧 カスタマイズ

### 新しいページの追加

```vue
<!-- pages/example.vue -->
<template>
  <div>
    <h1>新しいページ</h1>
    <p>ここに内容を追加</p>
  </div>
</template>

<script setup lang="ts">
// ページのメタデータ
definePageMeta({
  title: "新しいページ",
  requiresAuth: true,
});
</script>
```

### 新しいコンポーネントの追加

```vue
<!-- components/MyComponent.vue -->
<template>
  <div class="my-component">
    <slot />
  </div>
</template>

<script setup lang="ts">
interface Props {
  title?: string;
}

const props = withDefaults(defineProps<Props>(), {
  title: "Default Title",
});
</script>

<style scoped>
.my-component {
  @apply p-4 bg-white rounded-lg shadow;
}
</style>
```

---

## 🔍 デバッグ

### Vue Devtools

```bash
# Vue Devtools インストール
npm install --save-dev @vue/devtools
```

### デバッグ設定

```typescript
// nuxt.config.ts
export default defineNuxtConfig({
  devtools: { enabled: true },

  // 開発時のみ有効
  ...(process.env.NODE_ENV === "development" && {
    css: ["~/assets/css/debug.css"],
  }),
});
```

---

## 📖 参考資料

### 公式ドキュメント

- [Nuxt 3 Documentation](https://nuxt.com/docs)
- [Vue 3 Documentation](https://vuejs.org/guide/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Pinia Documentation](https://pinia.vuejs.org/)

### 実装ガイド

- [📋 実装計画](../implementation_plan.md)
- [🎯 開発戦略](../DEVELOPMENT_STRATEGY.md)
- [🔧 バックエンド連携](../backend/README.md)

---

## 🤝 コントリビューション

### 開発ガイドライン

1. **コード品質**: ESLint + Prettier に準拠
2. **コミット**: Conventional Commits 形式
3. **ブランチ**: `feature/機能名` 形式
4. **プルリクエスト**: テンプレートに従って作成

### 開発フロー

```bash
# 1. 新機能ブランチ作成
git checkout -b feature/new-feature

# 2. 開発・テスト
npm run dev
npm run test

# 3. リンター実行
npm run lint

# 4. コミット・プッシュ
git add .
git commit -m "feat: 新機能を追加"
git push origin feature/new-feature
```

---

## 📞 サポート

### 問題報告

- **バグ**: [GitHub Issues](https://github.com/your-username/chat-app_nuxt/issues)
- **機能要望**: [GitHub Discussions](https://github.com/your-username/chat-app_nuxt/discussions)

### 開発者向け情報

- **API 仕様**: [バックエンド API](../backend/README.md)
- **デプロイ**: [Railway + Vercel](../RAILWAY_DEPLOYMENT_GUIDE.md)
- **テスト**: [テストガイド](../backend/TESTING.md)

---

<div align="center">
  <p>🚀 <strong>LumoChat Frontend</strong> - 美しく、高速で、使いやすい</p>
  
  [📚 メインドキュメント](../README.md) | 
  [🔧 バックエンド](../backend/README.md) | 
  [🎯 実装計画](../implementation_plan.md)
</div>
