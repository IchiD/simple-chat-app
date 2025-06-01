import { ref, onMounted, watch } from 'vue'

const isDarkMode = ref(false)

export const useDarkMode = () => {
  // ダークモードの切り替え
  const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value
  }

  // ダークモードの設定
  const setDarkMode = (value: boolean) => {
    isDarkMode.value = value
  }

  // HTMLのクラスを更新
  const updateHtmlClass = () => {
    if (typeof window !== 'undefined') {
      if (isDarkMode.value) {
        document.documentElement.classList.add('dark')
      } else {
        document.documentElement.classList.remove('dark')
      }
    }
  }

  // ローカルストレージから設定を読み込み
  const loadFromStorage = () => {
    if (typeof window !== 'undefined') {
      const stored = localStorage.getItem('darkMode')
      if (stored !== null) {
        isDarkMode.value = stored === 'true'
      } else {
        // 初回アクセス時はシステムの設定を参照
        isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches
      }
    }
  }

  // ローカルストレージに設定を保存
  const saveToStorage = () => {
    if (typeof window !== 'undefined') {
      localStorage.setItem('darkMode', isDarkMode.value.toString())
    }
  }

  // 初期化
  onMounted(() => {
    loadFromStorage()
    updateHtmlClass()
  })

  // ダークモード状態の監視
  watch(isDarkMode, () => {
    updateHtmlClass()
    saveToStorage()
  })

  return {
    isDarkMode: readonly(isDarkMode),
    toggleDarkMode,
    setDarkMode
  }
}