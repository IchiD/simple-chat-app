<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <div class="mx-auto h-12 w-12 text-blue-600">
          <svg class="animate-spin h-12 w-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          認証処理中...
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Googleアカウントでの認証を完了しています
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

// メタ情報設定
useHead({
  title: 'Google認証処理中',
})

// 認証コールバック処理
onMounted(async () => {
  try {
    const token = route.query.token as string
    const userParam = route.query.user as string
    const error = route.query.error as string

    if (error) {
      // エラーパラメータがある場合
      let errorMessage = 'Google認証でエラーが発生しました'
      
      switch (error) {
        case 'google_redirect_failed':
          errorMessage = 'Google認証への接続に失敗しました'
          break
        case 'google_auth_failed':
          errorMessage = 'Google認証に失敗しました'
          break
        default:
          errorMessage = '認証処理中にエラーが発生しました'
      }

      toast.add({
        title: 'ログインエラー',
        description: errorMessage,
        color: 'error',
      })
      
      router.push('/auth/login')
      return
    }

    if (token && userParam) {
      // 成功時の処理
      try {
        const user = JSON.parse(decodeURIComponent(userParam))
        
        // トークンをCookieに保存
        const cookieToken = useCookie("token", {
          default: () => null,
          httpOnly: false,
          secure: true,
          sameSite: "lax",
        })
        cookieToken.value = token

        // ユーザー情報を設定
        authStore.setUser(user)

        toast.add({
          title: 'ログイン成功',
          description: 'Googleアカウントでログインしました',
          color: 'success',
        })

        // ユーザーページにリダイレクト
        router.push('/user')
      } catch (parseError) {
        console.error('User data parse error:', parseError)
        toast.add({
          title: 'ログインエラー',
          description: 'ユーザー情報の処理に失敗しました',
          color: 'error',
        })
        router.push('/auth/login')
      }
    } else {
      // パラメータが不足している場合
      toast.add({
        title: 'ログインエラー',
        description: '認証情報が不足しています',
        color: 'error',
      })
      router.push('/auth/login')
    }
  } catch (error) {
    console.error('Callback processing error:', error)
    toast.add({
      title: 'ログインエラー',
      description: '認証処理中にエラーが発生しました',
      color: 'error',
    })
    router.push('/auth/login')
  }
})
</script>