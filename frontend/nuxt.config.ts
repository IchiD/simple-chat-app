// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  modules: [
    "@nuxt/eslint",
    "@nuxt/image",
    "@pinia/nuxt",
    "@nuxtjs/tailwindcss",
  ],

  // ランタイム設定
  runtimeConfig: {
    public: {
      apiBase:
        process.env.API_BASE_URL ||
        "https://web-production-4f969.up.railway.app/api",
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
            "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; connect-src 'self' https://web-production-4f969.up.railway.app http://localhost ws://localhost:*; font-src 'self'; object-src 'none'",
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
