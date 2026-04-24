<template>
  <div v-if="news.length || isAdmin" class="max-w-6xl mx-auto px-4 mt-8">
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
      <div class="min-w-0">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-semibold text-gkk inline-block border-b-2 border-gkk pb-1">Nyheter</h2>
          <Button v-if="isAdmin" type="primary" class="text-xs" @click="location('/admin/news')">Skapa nyhet</Button>
        </div>

        <div class="flex flex-col gap-3">
          <NewsItem
            v-for="(item, index) in news"
            :key="item.id"
            :item="item"
            :user="user"
            :default-expanded="index === 0"
          ></NewsItem>
        </div>
      </div>

      <aside v-if="news.length" class="lg:sticky lg:top-24 self-start">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
          <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gkk">Senaste nyheterna</h3>
          </div>
          <ul class="flex flex-col gap-3">
            <li v-for="item in latestNews" :key="item.id">
              <a :href="`#news-${item.id}`" @click.prevent="scrollTo(item.id)" class="flex items-start gap-3 group">
                <div class="w-8 h-8 rounded-full bg-gkk/10 text-gkk flex items-center justify-center shrink-0">
                  <i class="fa fa-bullhorn text-sm"></i>
                </div>
                <div class="min-w-0">
                  <div class="text-sm text-gray-900 group-hover:text-gkk transition-colors truncate">{{ item.title }}</div>
                  <div class="text-xs text-gray-400 mt-0.5">{{ publishDate(item) }}</div>
                </div>
              </a>
            </li>
          </ul>
        </div>
      </aside>
    </div>
  </div>
</template>

<script>
import NewsItem from './NewsItem.vue'
import Button from './ui/Button.vue'

export default {
  components: { NewsItem, Button },
  props: ['user', 'news'],
  computed: {
    isAdmin() {
      return this.user && ['admin', 'superadmin'].includes(this.user.role)
    },
    latestNews() {
      return this.news.slice(0, 5)
    },
  },
  methods: {
    publishDate(item) {
      if (item.published_at_date) {
        return item.published_at_date
      }

      return item.created_at?.slice(0, 10)
    },
    scrollTo(id) {
      const el = document.getElementById(`news-${id}`)
      if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' })
      }
    },
  },
}
</script>
