<template>
  <div style="max-width: 600px; margin: auto;">
    <h1 style="margin-bottom: 20px;">Tävlingsanmälan</h1>
    <h2>{{ competition.name }}</h2>
    <h4>{{ dateString }} ({{ competition.date }})</h4>

    <form style="margin-top: 20px;">
      <div class="form-group">
        <h3>{{ competition.description }}</h3>
      </div>
      Licensenummer: <input class="form-group" v-model="licenceNumber"> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="Licensnumret består i regel av din födelsedata (6 siffror), samt dina initialer. Ex. har Anna Persson som är född 1987-08-19 licensnummer 870819ap"></i>
      <div class="form-group">
        <textarea v-model="comment" class="form-control" name="description" placeholder="Ev. kommentar/ytterligare info." rows="5"></textarea>
      </div>
      <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <el-button v-if="registration && canHelp" style="margin-bottom: 10px;" @click="register(true)"><i class="fa fa-check-circle-o" style="margin-right: 10px;"></i>Ja, jag vill tävla</el-button>
        <el-button secondary v-if="!registration || !canHelp" style="margin-bottom: 10px;" @click="register(true)">Ja, jag vill tävla</el-button>

        <el-button danger v-if="registration && !canHelp" style="margin-bottom: 30px;" @click="register(false)"><i class="fa fa-check-circle-o" style="margin-right: 10px;"></i>Jag vill inte tävla</el-button>
        <el-button secondary danger v-if="!registration || canHelp" style="margin-bottom: 30px;" @click="register(false)">Jag vill inte tävla</el-button>

        <el-button v-if="justSaved" secondary disabled style="margin-bottom: 10px;">Sparat!</el-button>
        <el-button v-else secondary style="margin-bottom: 10px;" @click="save">Spara</el-button>

      </div>
      <div v-if="registrationStatus === 'error'">
        <el-message danger style="margin-top: 20px;">
          Kunde inte skicka, kontrollera inmatning och anlutning.
        </el-message>
      </div>
      <div v-if="registrationStatus === 'completed'">
        <el-message v-if="!canHelp" info style="margin-top: 20px;">Tack för informationen!</el-message>
        <el-message v-else success style="margin-top: 20px;">Grymt, vi ses där!</el-message>
      </div>
    </form>

    <GkkLink to="/competitions" text="Tillbaka till alla tävlingar" />
  </div>
</template>

<script>
import _ from 'vue-el-element'
import Date from '../modules/Date.js'

export default {
  props: ['competition', 'user', '_registration'],
  data () {
    return {
      justSaved: false,
      registrationStatus: '',
      registration: this._registration,
      comment: this._registration ? this._registration.comment : '',
      canHelp: this._registration ? this._registration.status == 1 : null,
      licenceNumber: this.user.licence_number || '',
    }
  },
  mounted () {
    $(() => { $('[data-toggle="tooltip"]').tooltip() })
  },
  computed: {
    dateString () {
      return Date.string(this.competition.date)
    }
  },
  methods: {
    save () {
      this.register(this.canHelp)
    },
    register (canHelp) {
      this.registrationStatus = ''

      window.axios({
        method: 'post',
        url: `/competitions/${this.competition.id}/registrations`,
        data: {
          status: canHelp,
          comment: this.comment,
          licenceNumber: this.licenceNumber,
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