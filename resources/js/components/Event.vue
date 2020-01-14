<template>
  <div style="max-width: 600px; margin: auto;">
    <h1>Funktionärsanmälan</h1>
    <h2>{{ event.name }}</h2>
    <h4>{{ event.date }}</h4>

    <form style="margin-top: 20px;">
      <div class="form-group">
        <h2>{{ user.first_name }} {{ user.last_name }}</h2>
      </div>
      <div class="form-group">
        <textarea v-model="comment" class="form-control" name="description" placeholder="Ev. kommentar/ytterligare info, exempelvis om du måste gå tidigare" rows="5"></textarea>
      </div>
      <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <el-button v-if="registration && canHelp" style="margin-bottom: 10px;" @click="register(true)"><i class="fa fa-check-circle-o" style="margin-right: 10px;"></i>Jag kan hjälpa till</el-button>
        <el-button secondary v-if="!registration || !canHelp" style="margin-bottom: 10px;" @click="register(true)">Jag kan hjälpa till</el-button>

        <el-button danger v-if="registration && !canHelp" style="margin-bottom: 10px;" @click="register(false)"><i class="fa fa-check-circle-o" style="margin-right: 10px;"></i>Jag kan inte hjälpa till</el-button>
        <el-button secondary danger v-if="!registration || canHelp" style="margin-bottom: 10px;" @click="register(false)">Jag kan inte hjälpa till</el-button>

      </div>
      <div v-if="registrationStatus === 'error'">
        <el-message danger style="margin-top: 20px;">
          Kunde inte skicka, kontrollera inmatning och anlutning.
        </el-message>
      </div>
      <div v-if="registrationStatus === 'completed'">
        <el-message info style="margin-top: 20px;">
          Din anmälan är mottagen, tack!
        </el-message>
      </div>
    </form>

    <GkkLink to="/events" text="Tillbaka till alla event" />
  </div>
</template>

<script>
import _ from 'vue-el-element'
export default {
  props: ['event', 'user', '_registration'],
  data () {
    return {
      registrationStatus: '',
      registration: this._registration,
      comment: this._registration ? this._registration.comment : '',
      canHelp: this._registration ? this._registration.status == 1 : null,
    }
  },
  methods: {
    back () {
      window.location = '/'
    },
    register (canHelp) {
      this.registrationStatus = ''

      window.axios({
        method: 'post',
        url: `/events/${this.event.id}/registrations`,
        data: {
          status: canHelp,
          comment: this.comment,
        }
      }).then(response => {
        this.canHelp = canHelp
        this.registration = response.data
        this.registrationStatus = 'completed'
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
