<template>
  <div class="container mx-auto">
    <h1 class="text-center text-3xl font-thin mb-6 mt-8">Funktionärsanmälan</h1>
    <AdministrateButton v-if="isAdmin" path="/admin/events" />

    <div v-if="events.length">
      <div v-for="event in orderedEvents" :key="event.id" class="flex align-center mb-6">
        <gkk-event-card
          @click="location(`/events/${event.id}`)"
          :event="event"
          :registration="registrationFor(event)"
        ></gkk-event-card>
      </div>
    </div>

    <div v-else>
      <h2 class="text-center text-xl font-thin m-6">Just nu finns inga funktionärsuppdrag eller event att anmäla sig till.</h2>
    </div>
  </div>
</template>

<script>
import AdministrateButton from './AdministrateButton.vue'

export default {
  components: { AdministrateButton },
  props: ['events', 'user-registrations', 'user'],
  computed: {
    orderedEvents() {
      return this.events.slice().sort((a, b) => {
        if (this.userRegistrations.find((reg) => Number(reg.event_id) === Number(b.id))) {
          return -1
        }

        if (this.userRegistrations.find((reg) => Number(reg.event_id) === Number(a.id))) {
          return 1
        }

        return 0
      })
    },
    isAdmin() {
      return this.user && ['admin', 'superadmin'].includes(this.user.role)
    },
  },
  methods: {
    registrationFor(event) {
      return this.userRegistrations.find((registration) => Number(registration.event_id) === Number(event.id))
    },
  },
}
</script>
