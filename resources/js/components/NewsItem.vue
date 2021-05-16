<template>
  <div>
    <div class="absolute bottom-2 right-2" @click="location(`/admin/news/${item.id}`)" v-if="isAdmin">
      <el-button danger style="padding: 5px; font-size: 10px">Redigera nyhet</el-button>
    </div>
    <div
      v-tooltip.left="'Gör epostutskick'"
      class="absolute top-0 right-4 mt-2 cursor-pointer"
      @click="location(`/admin/news/email/${item.id}`)"
      v-if="isAdmin"
    >
      <i class="fa fa-envelope"></i>
    </div>
    <h1 @click="expanded = !expanded" style="margin: 20px 0; cursor: pointer">
      {{ item.title }} <span style="font-size: 12px">{{ publishDate }}</span>
      <span v-if="!expanded" class="text-xs ml-4 text-gray-500">Klicka för att visa</span>
    </h1>
    <div v-if="expanded" v-html="item.body"></div>
  </div>
</template>

<script>
export default {
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
