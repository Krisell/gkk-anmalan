<template>
  <div class="max-w-6xl mx-auto px-4 pt-6">
    <div class="flex items-start justify-between gap-4 flex-wrap mb-6">
      <div>
        <Breadcrumbs title="Funktionärsanmälan" />
        <p class="text-gray-500 mt-1 text-sm">Anmäl dig som funktionär till tävlingar och event.</p>
      </div>
      <a
        v-if="isAdmin"
        href="/admin/events"
        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gkk/10 text-gkk text-[10px] font-semibold uppercase tracking-wider hover:bg-gkk/20"
      >
        <i class="fa fa-lock"></i>
        Admin
      </a>
    </div>

    <div v-if="events.length" class="flex flex-col gap-4">
      <gkk-event-card
        v-for="event in orderedEvents"
        :key="event.id"
        :event="event"
        :registration="registrationFor(event)"
        @click="location(`/events/${event.id}`)"
      ></gkk-event-card>
    </div>

    <div v-else class="bg-white rounded-xl border border-gray-100 shadow-xs p-8 text-center text-gray-500">
      Just nu finns inga funktionärsuppdrag eller event att anmäla sig till.
    </div>
  </div>
</template>

<script>
import Breadcrumbs from './ui/Breadcrumbs.vue'
export default {
  components: { Breadcrumbs },
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
