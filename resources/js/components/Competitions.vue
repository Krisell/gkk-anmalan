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
          <span class="text-gkk">Tävlingsanmälan</span>
        </h1>
        <p class="text-gray-500 mt-1 text-sm">Här kan du se aktuella tävlingar och anmäla ditt intresse.</p>
      </div>
      <a
        v-if="isAdmin"
        href="/admin/competitions"
        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gkk/10 text-gkk text-[10px] font-semibold uppercase tracking-wider hover:bg-gkk/20"
      >
        <i class="fa fa-lock"></i>
        Admin
      </a>
    </div>

    <div v-if="competitions.length" class="flex flex-col gap-4">
      <gkk-competition-card
        v-for="competition in competitions"
        :key="competition.id"
        :competition="competition"
        :registration="registrationFor(competition)"
        @click="location(`/competitions/${competition.id}`)"
      ></gkk-competition-card>
    </div>

    <div v-else class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 text-center text-gray-500">
      Just nu finns inga tävlingar att anmäla sig till.
    </div>
  </div>
</template>

<script>
export default {
  props: ['competitions', 'user-registrations', 'user'],
  computed: {
    isAdmin() {
      return this.user && ['admin', 'superadmin'].includes(this.user.role)
    },
  },
  methods: {
    registrationFor(competition) {
      return this.userRegistrations.find(
        (registration) => Number(registration.competition_id) === Number(competition.id),
      )
    },
  },
}
</script>
