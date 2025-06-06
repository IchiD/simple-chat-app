// https://nuxt.com/docs/api/configuration/nuxt-config

export default defineNuxtConfig({
  modules: [
    "@nuxt/eslint",
    "@nuxt/image",
    "@pinia/nuxt",
    "@nuxtjs/tailwindcss",
  ],
  // @ts-expect-error Runtime config type inference issue
  runtimeConfig: {
    public: {
      apiBase:
        process.env.API_BASE_URL ||
        (process.env.NODE_ENV === "production"
          ? "https://web-production-4f969.up.railway.app/api"
          : "http://localhost/api"),
    },
  },
  app: {
    head: {
      title: "LumoChat",
      meta: [
        { name: "viewport", content: "width=device-width, initial-scale=1" },
        {
          name: "description",
          content: "LumoChat - 光のようにつながるシンプルなチャットアプリ",
        },
      ],
      link: [
        // 基本のfavicon
        { rel: "icon", type: "image/x-icon", href: "/favicon.ico" },
        // PNG版のfavicon（より鮮明）
        {
          rel: "icon",
          type: "image/png",
          href: "/favicon.png",
          sizes: "32x32",
        },
        // Apple端末用
        {
          rel: "apple-touch-icon",
          href: "/apple-touch-icon.png",
          sizes: "180x180",
        },
      ],
    },
  },
  css: ["~/assets/css/main.css"],
  ssr: false,
  typescript: {
    strict: true,
  },
  tailwindcss: {
    exposeConfig: true,
    viewer: true,
  },
});
