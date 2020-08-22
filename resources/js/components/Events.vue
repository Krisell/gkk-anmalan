<template>
  <div class="container mx-auto">
    <h1 class="text-center text-3xl font-hairline mb-6">Funktionärsanmälan</h1>

    <div v-if="events.length">
      <div v-for="event in events" :key="event.id" class="flex align-center mb-6">
        <gkk-event-card
          @click="location(`/events/${event.id}`)"
          :event="event"
          :registration="registrationFor(event)"
        ></gkk-event-card>
      </div>
    </div>

    <div v-else>
      <h2 class="text-center text-xl font-thin m-6">Just nu finns inga tävlingar att anmäla sig till.</h2>
    </div>

    <GkkLink to="/" text="Tillbaka till startsidan" />
  </div>
</template>

<script>
export default {
  props: ['events', 'user-registrations'],
  methods: {
    registrationFor(event) {
      return this.userRegistrations.find((registration) => Number(registration.event_id) === Number(event.id))
    },
  },
}
</script>
