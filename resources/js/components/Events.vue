<template>
  <div style="max-width: 600px; margin: auto;">
    <h1>Funktionärsanmälan</h1>

    <div class="type" v-if="events.length">
      <div v-for="event in events" :key="event.id" style="display: flex; align-items: center; margin-bottom: 20px;">
        <gkk-event-card style="" @click="location(`/events/${event.id}`)" :event="event" :registration="registrationFor(event)"></gkk-event-card>
      </div>
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
    registrationFor (event) {
      return this.userRegistrations.find(registration => Number(registration.event_id) === Number(event.id))
    }
  }
}
</script>

<style scoped lang="less">
  .type {
    margin-bottom: 40px;
  }
</style>
