<template>
  <div>
    <h1 @click="expanded = !expanded" style="margin: 20px 0; cursor: pointer">
      {{ item.title }} <span style="font-size: 12px">{{ publishDate }}</span>
      <span v-if="!expanded" class="text-xs ml-4 text-gray-500">Klicka för att visa</span>
    </h1>
    <div v-if="expanded" v-html="item.body"></div>
    <div class="flex justify-end items-center" v-if="isAdmin">
      <Button 
        type="danger" 
        class="text-xs"
        @click="location(`/admin/news/${item.id}`)"
      >
        Redigera nyhet
      </Button>
      <div
        v-tooltip.top="'Gör epostutskick'"
        class="ml-4 cursor-pointer"
        @click="location(`/admin/news/email/${item.id}`)"
        v-if="isAdmin"
      >
        <i class="fa fa-envelope text-xl"></i>
      </div>
    </div>
  </div>
</template>

<script>
import Button from './ui/Button.vue'

export default {
  components: { Button },
  props: ['item', 'user', 'default-expanded'],
  data() {
    return {
      expanded: this.defaultExpanded || false,
    }
  },
  computed: {
    isAdmin() {
      return this.user && ['admin', 'superadmin'].includes(this.user.role)
    },
    publishDate() {
      if (this.item.published_at_date) {
        return this.item.published_at_date
      }

      return this.item.created_at.slice(0, 10)
    },
  },
}
</script>

<style scoped></style>
