// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  modules: [
    "@nuxt/eslint",
    "@nuxt/image",
    "@pinia/nuxt",
    "@nuxtjs/tailwindcss",
  ],

  // 互換性日付を追加
  compatibilityDate: "2025-05-26",

  // ランタイム設定
  runtimeConfig: {
    public: {
      apiBase: process.env.API_BASE_URL || "http://localhost/api",
      // Laravel Reverb設定
      reverbAppKey: process.env.REVERB_APP_KEY || "app-key",
      reverbHost: process.env.REVERB_HOST || "localhost",
      reverbPort: process.env.REVERB_PORT || "8080",
      reverbScheme: process.env.REVERB_SCHEME || "http",
    },
  },

  // アプリケーション設定
  app: {
    head: {
      title: "LumoChat",
      meta: [
        { name: "viewport", content: "width=device-width, initial-scale=1" },
        {
          name: "description",
          content: "LumoChat - 光のようにつながるシンプルなチャットアプリ",
        },
        // CSPを更新してWebSocket接続を許可
        {
          "http-equiv": "Content-Security-Policy",
          content:
            "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; connect-src 'self' http://localhost ws://localhost:* wss://localhost:* http://127.0.0.1 ws://127.0.0.1:* wss://127.0.0.1:*; font-src 'self'; object-src 'none'",
        },
      ],
    },
  },

  // CSSの設定
  css: ["~/assets/css/main.css"],

  // SSR無効化（クライアントサイドのみ）
  ssr: false,

  // TypeScript設定
  typescript: {
    strict: true,
  },

  // TailwindCSS設定
  tailwindcss: {
    exposeConfig: true,
    viewer: true,
  },
});
