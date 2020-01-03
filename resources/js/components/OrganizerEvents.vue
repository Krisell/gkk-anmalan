<template>
  <div>
    <h1 style="text-align: center;">Funktionärsanmälan</h1>

    <div class="type">
      <gkk-action-card @click="choose(event)" v-for="event in events" :description="event.name" icon="users" :key="event.id"></gkk-action-card>
    </div>
  </div>
</template>

<script>
export default {
  props: ['events'],
  methods: {
    choose (event) {
      window.location = `/organizer-events/${event.id}`
    }
  }
}
</script>

<style scoped lang="less">
  .type {
    margin-bottom: 40px;
  }
</style>
