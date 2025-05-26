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
        // CSPを追加してXSS攻撃からサイトを保護
        {
          "http-equiv": "Content-Security-Policy",
          content:
            "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; connect-src 'self' http://localhost ws://localhost:*; font-src 'self'; object-src 'none'",
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
