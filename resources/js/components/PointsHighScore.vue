<template>
  <div class="container mx-auto max-w-6xl">
    <h1 class="text-center text-3xl font-thin mt-24">Poängtoppen {{ currentYear }}</h1>

    <div v-if="loading" class="text-center mt-8">
      <p class="text-gray-600">Laddar ranking...</p>
    </div>

    <div v-else-if="error" class="text-center mt-8">
      <p class="text-red-600">{{ error }}</p>
      <button 
        @click="fetchRanking"
        class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
      >
        Försök igen
      </button>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
      <div v-for="category in categories" :key="category.title" class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="font-thin text-2xl mb-4 text-center">{{ category.title }}</h2>
        
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Plats
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Namn
                </th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Poäng
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr 
                v-for="(item, index) in ranking[category.gender][category.event]" 
                :key="item.id || index"
              >
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="flex items-center">
                    <span 
                      v-if="index < 3"
                      class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold"
                    >
                      {{ index + 1 }}
                    </span>
                    <span v-else class="text-sm text-gray-900">{{ index + 1 }}</span>
                  </div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                  <div v-if="item.club" class="text-sm text-gray-500">{{ item.club }}</div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right">
                  <div v-if="category.event === 'kbp'" class="text-sm font-medium text-gray-900">{{ formatPoints(item.points.ipfglbp) }}</div>
                  <div v-else class="text-sm font-medium text-gray-900">{{ formatPoints(item.points.ipfgl) }}</div>
                  <div class="text-xs text-gray-500 mt-1">{{ formatResultWithWeight(item, category.event) }}</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PointsHighScore',
  data() {
    return {
      loading: true,
      error: null,
      ranking: {
        men: {
          ksl: [],
          kbp: [],
        },
        women: {
          ksl: [],
          kbp: [],
        },
      },
      currentYear: new Date().getFullYear(),
      categories: [
        { gender: 'men', event: 'ksl', title: 'Män KSL' },
        { gender: 'women', event: 'ksl', title: 'Kvinnor KSL' },
        { gender: 'men', event: 'kbp', title: 'Män KBP' },
        { gender: 'women', event: 'kbp', title: 'Kvinnor KBP' }
      ]
    }
  },
  mounted() {
    this.fetchRanking()
  },
  methods: {
    async fetchRanking() {
      this.loading = true
      this.error = null
      
      try {
        const response1 = await fetch(`/api/ranking/points?year=${this.currentYear}&competition_type=1`)
        const response2 = await fetch(`/api/ranking/points?year=${this.currentYear}&competition_type=2`)
        
        const data1 = await response1.json()
        const data2 = await response2.json()

        this.ranking.men.ksl = data1.data.men_ksl
        this.ranking.women.ksl = data1.data.women_ksl

        // KSL-bench presses also count as KBP
        this.ranking.men.kbp = data2.data.men_ksl
        this.ranking.women.kbp = data2.data.women_ksl
        this.ranking.men.kbp = this.ranking.men.kbp.concat(data2.data.men_kbp)
        this.ranking.women.kbp = this.ranking.women.kbp.concat(data2.data.women_kbp)

        this.ranking.men.ksl.sort((a, b) => b.points.ipfgl - a.points.ipfgl)
        this.ranking.women.ksl.sort((a, b) => b.points.ipfgl - a.points.ipfgl)
        this.ranking.men.kbp.sort((a, b) => b.points.ipfglbp - a.points.ipfglbp)
        this.ranking.women.kbp.sort((a, b) => b.points.ipfglbp - a.points.ipfglbp)

      } catch (error) {
        console.error('Error fetching ranking:', error)
        this.error = error.message || 'Ett fel uppstod vid hämtning av data'
      } finally {
        this.loading = false
      }
    },
    formatPoints(points) {
      return Number(points).toFixed(2)
    },
    formatResultWithWeight(item, eventType) {
      if (!item.body_weight) return ''
      
      let result
      if (eventType === 'kbp') {
        result = item.benchpress || '-'
      } else {
        result = item.total || '-'
      }
      
      if (result === '-') return ''
      
      return `${result}kg, bw${item.body_weight}`
    }
  }
}
</script>