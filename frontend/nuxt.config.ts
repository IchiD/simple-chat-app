// https://nuxt.com/docs/api/configuration/nuxt-config

export default defineNuxtConfig({
  modules: [
    "@nuxt/eslint",
    "@nuxt/image",
    "@pinia/nuxt",
    "@nuxtjs/tailwindcss",
  ],
  // @ts-ignore
  runtimeConfig: {
    public: {
      apiBase:
        process.env.API_BASE_URL ||
        "https://web-production-4f969.up.railway.app/api",
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
