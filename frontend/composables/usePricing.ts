import { ref, computed } from 'vue'

export interface PlanFeatures {
  name: string
  display_name: string
  price: number
  currency: string
  formatted_price: string
  billing_interval: string | null
  stripe_price_id?: string
  features: string[]
  limits: {
    group_members: number
    groups: number
  }
  stripe_verified?: boolean
}

export interface PricingData {
  plans: {
    free: PlanFeatures
    standard: PlanFeatures
    premium: PlanFeatures
  }
  stripe_enabled: boolean
  test_mode: boolean
}

export const usePricing = () => {
  const { api } = useApi()
  
  const pricingData = ref<PricingData | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const lastFetched = ref<Date | null>(null)

  // キャッシュの有効期限（5分）
  const CACHE_DURATION = 5 * 60 * 1000

  const isDataFresh = computed(() => {
    if (!lastFetched.value) return false
    return Date.now() - lastFetched.value.getTime() < CACHE_DURATION
  })

  const fetchPricing = async (forceRefresh = false) => {
    // キャッシュが有効で強制更新でない場合はスキップ
    if (pricingData.value && isDataFresh.value && !forceRefresh) {
      return pricingData.value
    }

    isLoading.value = true
    error.value = null

    try {
      const response = await api<{ status: string; data: PricingData }>('/pricing')
      
      if (response.status === 'success') {
        pricingData.value = response.data
        lastFetched.value = new Date()
        return response.data
      } else {
        throw new Error('価格情報の取得に失敗しました')
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : '不明なエラーが発生しました'
      console.error('価格情報取得エラー:', err)
      
      // エラー時はフォールバック価格を使用
      if (!pricingData.value) {
        pricingData.value = getFallbackPricing()
        lastFetched.value = new Date()
      }
      
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const getFallbackPricing = (): PricingData => {
    return {
      plans: {
        free: {
          name: 'FREE',
          display_name: 'フリー',
          price: 0,
          currency: 'JPY',
          formatted_price: '¥0',
          billing_interval: null,
          features: [
            '基本チャット機能',
            'QRコード参加',
            'メッセージ履歴（2年間）',
            'チャットサポート',
          ],
          limits: {
            group_members: 0,
            groups: 0,
          },
        },
        standard: {
          name: 'STANDARD',
          display_name: 'スタンダード',
          price: 2980,
          currency: 'JPY',
          formatted_price: '¥2,980',
          billing_interval: 'month',
          features: [
            'フリープランの全機能',
            'グループチャット（最大50名）',
            'メンバー管理機能',
            'メッセージ履歴（5年間）',
            '優先サポート',
          ],
          limits: {
            group_members: 50,
            groups: -1,
          },
        },
        premium: {
          name: 'PREMIUM',
          display_name: 'プレミアム',
          price: 5980,
          currency: 'JPY',
          formatted_price: '¥5,980',
          billing_interval: 'month',
          features: [
            'スタンダードプランの全機能',
            'グループチャット（最大200名）',
            '一括配信機能',
            'メッセージ履歴（5年間）',
            '優先サポート',
          ],
          limits: {
            group_members: 200,
            groups: -1,
          },
        },
      },
      stripe_enabled: false,
      test_mode: true,
    }
  }

  const getPlanPrice = (planName: keyof PricingData['plans']): string => {
    if (!pricingData.value) return '¥0'
    return pricingData.value.plans[planName]?.formatted_price || '¥0'
  }

  const getPlanDisplayName = (planName: keyof PricingData['plans']): string => {
    if (!pricingData.value) return planName
    return pricingData.value.plans[planName]?.display_name || planName
  }

  const getPlanFeatures = (planName: keyof PricingData['plans']): string[] => {
    if (!pricingData.value) return []
    return pricingData.value.plans[planName]?.features || []
  }

  const calculatePriceDifference = (
    currentPlan: keyof PricingData['plans'],
    newPlan: keyof PricingData['plans']
  ): string => {
    if (!pricingData.value) return '¥0'
    
    const currentPrice = pricingData.value.plans[currentPlan]?.price || 0
    const newPrice = pricingData.value.plans[newPlan]?.price || 0
    const difference = newPrice - currentPrice

    if (difference > 0) {
      return `¥${difference.toLocaleString()}`
    }
    return '¥0'
  }

  const isStripeEnabled = computed(() => {
    return pricingData.value?.stripe_enabled || false
  })

  const isTestMode = computed(() => {
    return pricingData.value?.test_mode || false
  })

  const refreshPricing = () => {
    return fetchPricing(true)
  }

  // 初期化時に価格情報を取得
  const initializePricing = async () => {
    try {
      await fetchPricing()
    } catch (err) {
      // 初期化時のエラーは静かに処理
      console.warn('価格情報の初期取得に失敗しました:', err)
    }
  }

  return {
    pricingData: readonly(pricingData),
    isLoading: readonly(isLoading),
    error: readonly(error),
    lastFetched: readonly(lastFetched),
    isDataFresh,
    fetchPricing,
    getPlanPrice,
    getPlanDisplayName,
    getPlanFeatures,
    calculatePriceDifference,
    isStripeEnabled,
    isTestMode,
    refreshPricing,
    initializePricing,
  }
} 