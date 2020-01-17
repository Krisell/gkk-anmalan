<template>
  <div style="max-width: 600px; margin: auto;">
    <h1>Funktionärsanmälan</h1>

    <div class="type" v-if="events.length">
      <gkk-event-card style="min-height: 180px; margin-bottom: 10px;" @click="choose(event)" v-for="event in events" :event="event" :registration="registrationFor(event)" :key="event.id"></gkk-event-card>
    </div>

    <div v-else>
      <h3>Just nu finns inga events att anmäla sig till.</h3>
    </div>

    <GkkLink to="/" text="Tillbaka till startsidan" />
  </div>
</template>

<script>
export default {
  props: ['events', 'user-registrations'],
  methods: {
    choose (event) {
      window.location = `/events/${event.id}`
    },
    registrationFor (event) {
      return this.userRegistrations.find(registration => Number(registration.event_id) === Number(event.id))
    }
  }
}
</script>

<style scoped lang="less">
  .type {
    margin-bottom: 40px;
    display: flex;
    flex-direction: column;
    // flex-wrap: wrap;
  }
</style>
