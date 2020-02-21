<template>
  <div style="max-width: 600px; margin: auto;">
    <h1 style="margin-bottom: 20px;">Funktionärsanmälan</h1>
    <h2>{{ event.name }}</h2>
    <h4>{{ dateString }} ({{ event.date }})</h4>
    <h3 style="line-height: 1.4; font-size: 16px;">{{ event.description }}</h3>

    <div v-if="passedLastRegistration" style="margin-top: 20px;">
      <div v-if="!this.registration">
        <h3 style="color: orange;">Sista anmälningsdatum har passerat.</h3>
      </div>

      <div v-if="this.registration && this.registration.status == 0">
        <h3 style="color: orange;">Sista anmälningsdatum har passerat, och du har anmält att du inte vill/kan delta.</h3>
      </div>

      <div v-if="this.registration && this.registration.status == 1">
        <h3 style="color: green;">Sista anmälningsdatum har passerat, och du är anmäld.</h3>
        <div>{{ comment }}</div>
      </div>
    </div>

    <div v-else>
      <form style="margin-top: 20px;">
        <div class="form-group">
          <textarea v-model="comment" class="form-control" name="description" placeholder="Ev. kommentar/ytterligare info, exempelvis om du måste gå tidigare" rows="5"></textarea>
        </div>
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
          <el-button v-if="registration && canHelp" style="margin-bottom: 10px;" @click="register(true)"><i class="fa fa-check-circle-o" style="margin-right: 10px;"></i>Jag kan hjälpa till</el-button>
          <el-button secondary v-if="!registration || !canHelp" style="margin-bottom: 10px;" @click="register(true)">Jag kan hjälpa till</el-button>

          <el-button danger v-if="registration && !canHelp" style="margin-bottom: 30px;" @click="register(false)"><i class="fa fa-check-circle-o" style="margin-right: 10px;"></i>Jag kan inte hjälpa till</el-button>
          <el-button secondary danger v-if="!registration || canHelp" style="margin-bottom: 30px;" @click="register(false)">Jag kan inte hjälpa till</el-button>

          <el-button v-if="justSaved" secondary disabled style="margin-bottom: 10px;">Sparat!</el-button>
          <el-button v-else secondary style="margin-bottom: 10px;" @click="save">Spara</el-button>

        </div>
        <div v-if="registrationStatus === 'error'">
          <el-message danger style="margin-top: 20px;">
            Kunde inte skicka, kontrollera inmatning och anlutning.
          </el-message>
        </div>
        <div v-if="registrationStatus === 'completed'">
          <el-message v-if="!canHelp" info style="margin-top: 20px;">Synd, men tack för informationen!</el-message>
          <el-message v-else success style="margin-top: 20px;">Grymt, vi ses där!</el-message>
        </div>
      </form>
    </div>

    <GkkLink to="/events" text="Tillbaka till alla event" />

    <el-delimiter></el-delimiter>
    <div v-if="event.publish_list_value && event.publish_list_value.length > 0">
      <h3>Följande medlemmar har tackat ja</h3>
      <table class="table" id="datatable" v-if="event.publish_list">
          <thead>
            <tr><th scope="col">Namn</th></tr>
          </thead>
          <tbody>
            <tr v-for="registration in event.publish_list_value" :key="registration.id">
              <td>{{ registration.user.first_name }} {{ registration.user.last_name }}</td>
            </tr>
          </tbody>
        </table>

        <GkkLink to="/events" text="Tillbaka till alla event" />
      </div>
  </div>
</template>

<script>
import _ from 'vue-el-element'
import Date from '../modules/Date.js'

export default {
  props: ['event', 'user', '_registration'],
  data () {
    return {
      justSaved: false,
      registrationStatus: '',
      registration: this._registration,
      comment: this._registration ? this._registration.comment : '',
      canHelp: this._registration ? this._registration.status == 1 : null,
    }
  },
  computed: {
    dateString () {
      return Date.string(this.event.date)
    },
    passedLastRegistration () {
      if (!this.event.last_registration_at) {
        return false
      }

      return (new window.Date().setHours(0,0,0,0)) > (new window.Date(this.event.last_registration_at))
    },
  },
  methods: {
    save () {
      this.register(this.canHelp)
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

        this.justSaved = true
        setTimeout(() => this.justSaved = false, 2000)
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
