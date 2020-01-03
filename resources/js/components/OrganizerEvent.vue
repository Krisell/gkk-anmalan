<template>
  <div>
    <h1 style="text-align: center;">Funktionärsanmälan</h1>
    <h2>{{ event.name }}</h2>
    <h4>{{ event.date }}</h4>

    <form style="margin-top: 20px;">
      <div class="form-group">
        <input v-model="registration.name" class="form-control" name="name" placeholder="För- och efternamn">
      </div>
      <div class="form-group form-check">
        <input v-model="registration.status" type="checkbox" class="form-check-input" id="status">
        <label class="form-check-label" for="status">Jag kan hjälpa till denna dag</label>
      </div>
      <div class="form-group">
        <textarea v-model="registration.comment" class="form-control" name="description" placeholder="Ev. kommentar/ytterligare info"></textarea>
      </div>
      <button @click="register" type="button" class="btn btn-primary">Skicka</button>
      <div v-if="registrationStatus === 'error'" style="color: red;">
        Kunde inte skicka, kontrollera inmatning och anlutning.
      </div>
      <div v-if="registrationStatus === 'completed'" style="color: green;">
        Din anmälan är mottagen, tack!
      </div>
    </form>
  </div>
</template>

<script>
export default {
  props: ['event'],
  data () {
    return {
      registrationStatus: '',
      registration: {
        name: '',
        status: false,
        comment: '',
      },
    }
  },
  methods: {
    register () {
      this.registrationStatus = ''

      window.axios({
        method: 'post',
        url: `/organizer-events/${this.event.id}/registrations`,
        data: this.registration
      }).then(response => {
        this.registrationStatus = 'completed'
        this.registration = { name: '', status: false, comment: '' }
      }).catch(err => {
        this.registrationStatus = 'error'
      })
    },
  }
}
</script>

<style scoped lang="less">
  .type {
    margin-bottom: 40px;
  }
</style>
