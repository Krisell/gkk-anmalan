<template>
  <div style="max-width: 600px; margin: auto;">
    <h1>Tävlingsanmälan</h1>

    <div class="type" v-if="competitions.length">
      <div v-for="competition in competitions" :key="competition.id" style="display: flex; align-items: center; margin-bottom: 20px;">
        <gkk-competition-card style="" @click="location(`/competitions/${competition.id}`)" :competition="competition" :registration="registrationFor(competition)"></gkk-competition-card>
      </div>
    </div>

    <div v-else>
      <h3>Just nu finns inga tävlingar att anmäla sig till.</h3>
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

<style scoped lang="less">
  .type {
    margin-bottom: 40px;
  }
</style>
