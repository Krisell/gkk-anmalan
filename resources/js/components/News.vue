<template>
  <div class="max-w-3xl mx-auto relative p-4">
    <h1 class="text-3xl font-hairline mb-2" v-if="news.length">Nyheter</h1>
    <div class="absolute top-0 right-4 mt-2" @click="location('/admin/news')" v-if="isAdmin">
      <el-button danger style="padding: 5px; font-size: 10px">Skapa nyhet</el-button>
    </div>

    <div
      class="shadow bg-white rounded-sm mb-4 border-gkk border-t-2 p-2 px-6"
      v-for="newsItem in news"
      :key="newsItem.id"
    >
      <h1 style="margin: 20px 0">
        {{ newsItem.title }} <span style="font-size: 12px">{{ publishDate(newsItem) }}</span>
      </h1>
      <div v-html="newsItem.body"></div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['user', 'news'],
  computed: {
    isAdmin() {
      return this.user && ['admin', 'superadmin'].includes(this.user.role)
    },
  },
  methods: {
    publishDate(newsItem) {
      if (newsItem.published_at_date) {
        return newsItem.published_at_date
      }

      return newsItem.created_at.slice(0, 10)
    },
  },
}
</script>
