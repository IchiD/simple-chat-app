import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { useAuthStore } from '~/stores/auth'

declare global {
  interface Window {
    Pusher: typeof Pusher
    Echo: Echo
  }
}

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  // Pusherをglobalに設定
  window.Pusher = Pusher

  // Laravel Echo設定
  const echoConfig = {
    broadcaster: 'reverb',
    key: config.public.reverbAppKey || 'app-key',
    wsHost: config.public.reverbHost || 'localhost',
    wsPort: config.public.reverbPort || 8080,
    wssPort: config.public.reverbPort || 8080,
    forceTLS: (config.public.reverbScheme || 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    auth: {
      headers: {} as Record<string, string>
    },
    authEndpoint: `${config.public.apiBase}/broadcasting/auth`
  }

  // 認証ヘッダーの動的設定
  const updateAuthHeaders = () => {
    if (authStore.token) {
      echoConfig.auth.headers = {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    } else {
      echoConfig.auth.headers = {}
    }
  }

  // 初期認証ヘッダー設定
  updateAuthHeaders()

  // Echo インスタンス作成
  const echo = new Echo(echoConfig)

  // トークン変更時にEchoを再設定
  watch(() => authStore.token, (newToken) => {
    if (newToken) {
      updateAuthHeaders()
      // 新しいトークンでEchoインスタンスを再作成
      if (window.Echo) {
        window.Echo.disconnect()
      }
      window.Echo = new Echo(echoConfig)
    } else {
      // ログアウト時にEchoを切断
      if (window.Echo) {
        window.Echo.disconnect()
      }
    }
  })

  // グローバルにEchoを設定
  window.Echo = echo

  // プラグインからEchoインスタンスを提供
  return {
    provide: {
      echo: echo
    }
  }
})