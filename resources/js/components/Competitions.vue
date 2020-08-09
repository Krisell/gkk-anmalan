<template>
  <div class="container mx-auto">
    <h1 class="text-center text-3xl font-hairline mb-6">Tävlingsanmälan</h1>

    <div v-if="competitions.length">
      <div v-for="competition in competitions" :key="competition.id" class="flex align-center mb-6">
        <gkk-competition-card @click="location(`/competitions/${competition.id}`)" :competition="competition" :registration="registrationFor(competition)"></gkk-competition-card>
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
  props: ['competitions', 'user-registrations'],
  methods: {
    registrationFor (competition) {
      return this.userRegistrations.find(registration => Number(registration.competition_id) === Number(competition.id))
    }
  }
}
</script>

