// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  modules: ["@nuxt/eslint", "@nuxt/image", "@pinia/nuxt"],

  // 互換性日付を追加
  nitro: {
    compatibilityDate: "2025-05-15",
  },

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

  // CSSの設定を追加
  css: ["~/assets/css/main.css"],

  // PostCSSの設定
  postcss: {
    plugins: {
      tailwindcss: {},
      autoprefixer: {},
    },
  },
});
