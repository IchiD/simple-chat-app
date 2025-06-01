<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 p-8">
    <div class="max-w-md mx-auto bg-gray-100 dark:bg-gray-800 p-6 rounded-lg">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
        ダークモードテスト
      </h1>
      
      <p class="text-gray-700 dark:text-gray-300 mb-4">
        現在の状態: {{ isDarkMode ? 'ダークモード' : 'ライトモード' }}
      </p>
      
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        HTMLクラス: {{ htmlClass }}
      </p>
      
      <button 
        @click="testDarkMode"
        class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-800 text-white font-bold py-2 px-4 rounded"
      >
        ダークモード切り替え
      </button>
      
      <div class="mt-4 p-4 bg-gray-200 dark:bg-gray-700 rounded">
        <p class="text-gray-800 dark:text-gray-200">
          このテキストはダークモードで色が変わるはずです
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const isDarkMode = ref(false)
const htmlClass = ref('')

const updateHtmlClass = () => {
  if (typeof document !== 'undefined') {
    htmlClass.value = document.documentElement.className
  }
}

const testDarkMode = () => {
  alert('ボタンがクリックされました！')
  console.log('ダークモード切り替え前:', isDarkMode.value)
  
  isDarkMode.value = !isDarkMode.value
  
  console.log('ダークモード切り替え後:', isDarkMode.value)
  
  if (typeof document !== 'undefined') {
    if (isDarkMode.value) {
      document.documentElement.classList.add('dark')
      console.log('darkクラスを追加しました')
    } else {
      document.documentElement.classList.remove('dark')
      console.log('darkクラスを削除しました')
    }
    
    // ローカルストレージに保存
    localStorage.setItem('darkMode', isDarkMode.value.toString())
    updateHtmlClass()
  }
}

onMounted(() => {
  console.log('テストページがマウントされました')
  
  if (typeof window !== 'undefined') {
    const stored = localStorage.getItem('darkMode')
    if (stored !== null) {
      isDarkMode.value = stored === 'true'
      console.log('ローカルストレージから読み込み:', isDarkMode.value)
    }
    
    if (isDarkMode.value) {
      document.documentElement.classList.add('dark')
    }
    
    updateHtmlClass()
  }
})
</script>