<template>
  <div class="max-w-3xl mx-auto relative p-4">
    <h1 class="text-3xl font-thin mb-2" v-if="news.length">Nyheter</h1>
    <div class="absolute top-0 right-4 mt-2" @click="location('/admin/news')" v-if="isAdmin">
      <Button type="danger" style="padding: 5px; font-size: 10px">Skapa nyhet</Button>
    </div>

    <NewsItem
      class="relative shadow bg-white rounded-sm mb-4 border-gkk border-t-2 p-2 px-6"
      v-for="(item, index) in news"
      :key="item.id"
      :item="item"
      :user="user"
      :default-expanded="index === 0"
    ></NewsItem>
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
  },
}
</script>
