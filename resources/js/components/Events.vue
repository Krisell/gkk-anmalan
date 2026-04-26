<template>
  <div class="max-w-6xl mx-auto px-4 pt-6">
    <div class="flex items-start justify-between gap-4 flex-wrap mb-6">
      <div>
        <h1 class="text-2xl font-semibold">
          <a href="/insidan" class="inline-flex items-center gap-2 text-gray-400 hover:text-gkk transition-colors group">
            <i class="fa fa-angle-left"></i>
            <span class="underline underline-offset-4 decoration-gray-300 group-hover:decoration-gkk">Start</span>
          </a>
          <span class="text-gray-300 mx-2">/</span>
          <span class="text-gkk">Funktionärsanmälan</span>
        </h1>
        <p class="text-gray-500 mt-1 text-sm">Anmäl dig som funktionär till tävlingar och event.</p>
      </div>
      <a
        v-if="isAdmin"
        href="/admin/events"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gkk text-white font-medium text-sm hover:bg-gkk-light transition-colors"
      >
        <i class="fa fa-lock"></i>
        <span>Klicka för att administrera</span>
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

    <div v-else class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 text-center text-gray-500">
      Just nu finns inga funktionärsuppdrag eller event att anmäla sig till.
    </div>
  </div>
</template>

<script>
export default {
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
