<template>
  <div class="news">
    <h1 v-if="news.length">Nyheter</h1>
    <div style="position: absolute; right: 0px; top: 0px;" @click="location('/admin/news')" v-if="isAdmin">
      <el-button danger style="padding: 5px; font-size: 10px;">Administrera nyheter</el-button>
    </div>

    <div class="news-item" v-for="newsItem in news" :key="newsItem.id">
      <h1 style="margin: 20px 0;">{{ newsItem.title }} <span style="font-size: 12px">{{ newsItem.created_at.slice(0, 10) }}</span></h1>
      <div v-html="newsItem.body"></div>
    </div>
  </div>

</template>

<script>
export default {
  props: ['user', 'news'],
  computed: {
    isAdmin () {
      return this.user && ['admin', 'superadmin'].includes(this.user.role)
    },
  },
}
</script>

<style scoped>
  .news {
    max-width: 80%;
    margin: auto;
    position: relative;
  }

  .news-item {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    border-radius: 2px;
    padding: 10px;
    margin-bottom: 15px;
    /* padding-left: 20px; */
    border-top: 2px solid #243868;
    background: white;
  }
</style>