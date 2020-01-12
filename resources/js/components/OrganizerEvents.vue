<template>
  <div>
    <h1>Funktionärsanmälan</h1>

    <div class="type" v-if="events.length">
      <gkk-action-card style="min-width: 180px; margin-bottom: 10px;" @click="choose(event)" v-for="event in events" :description="event.name" icon="users" :key="event.id"></gkk-action-card>
    </div>

    <div v-else>
      <h3>Just nu finns inga events att anmäla sig till.</h3>
    </div>

    <GkkLink to="/" text="Tillbaka till startsidan" />
  </div>
</template>

<script>
export default {
  props: ['events'],
  methods: {
    choose (event) {
      window.location = `/organizer-events/${event.id}`
    },
  }
}
</script>

<style scoped lang="less">
  .type {
    margin-bottom: 40px;
    display: flex;
    flex-wrap: wrap;
  }
</style>
