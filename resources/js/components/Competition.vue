<template>
  <div style="max-width: 600px; margin: auto;">
    <h1 style="margin-bottom: 20px;">Tävlingsanmälan</h1>
    <h2>{{ competition.name }}</h2>
    <h4>{{ dateString }} ({{ competition.date }})</h4>

    <form style="margin-top: 20px;">
      <div class="form-group">
        <h3>{{ competition.description }}</h3>
      </div>
      <h4>Licensnummer: <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="Licensnumret består i regel av din födelsedata (6 siffror), samt dina initialer. Ex. har Anna Persson som är född 1987-08-19 licensnummer 870819ap"></i></h4>
      <input class="form-group" v-model="licenceNumber">

      <el-radio-button
        style="margin-bottom: 20px;"
        v-model="gender"
        :options="[ { value: 'Kvinnor', label: 'Kvinnor' }, { value: 'Män', label: 'Män' } ]"
      />

      <h4>Viktklass: <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="Mästerskap har i regel klassfast anmälan, medan serie-tävlingar inte har viktklasser (men kan ordna gupperna baserat på detta, så välj gärna en uppskattning)."></i></h4>

      <el-dropdown v-show="gender === 'Män'"
        v-model="weightClass['Män']"
        :options="[
          { value: '53', label: '53 kg' },
          { value: '59', label: '59 kg' },
          { value: '66', label: '66 kg' },
          { value: '74', label: '74 kg' },
          { value: '83', label: '83 kg' },
          { value: '93', label: '93 kg' },
          { value: '105', label: '105 kg' },
          { value: '120', label: '120 kg' },
          { value: '120+', label: '120+ kg' },
        ]"
      />

      <el-dropdown v-show="gender === 'Kvinnor'"
        v-model="weightClass['Kvinnor']"
        :options="[
          { value: '43', label: '43 kg' },
          { value: '47', label: '47 kg' },
          { value: '52', label: '52 kg' },
          { value: '57', label: '57 kg' },
          { value: '63', label: '63 kg' },
          { value: '72', label: '72 kg' },
          { value: '84', label: '84 kg' },
          { value: '84+', label: '84+ kg' },
        ]"
      />

      <div style="margin-bottom: 20px;"></div>

      <div v-if="hasEvent('ksl')" style="display: flex; margin-bottom: 5px; align-items: center;">
        <el-toggle-button v-model="events.ksl" />
        <div style="margin-left: 10px;">Klassisk Styrkelyft</div>
      </div>

      <div v-if="hasEvent('kbp')" style="display: flex; margin-bottom: 5px; align-items: center;">
        <el-toggle-button v-model="events.kbp" />
        <div style="margin-left: 10px;">Klassisk Bänkpress</div>
      </div>

      <div v-if="hasEvent('sl')" style="display: flex; margin-bottom: 5px; align-items: center;">
        <el-toggle-button v-model="events.sl" />
        <div style="margin-left: 10px;">Styrkelyft (utrustat)</div>
      </div>

      <div v-if="hasEvent('bp')" style="display: flex; margin-bottom: 5px; align-items: center;">
        <el-toggle-button v-model="events.bp" />
        <div style="margin-left: 10px;">Bänkpress (utrustat)</div>
      </div>

      <div class="form-group" style="margin-top: 20px;">
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
      weightClass: {
        'Män': '93',
        'Kvinnor': '63',
      },
      events: {
        ksl: false,
        kbp: false,
        sl: false,
        bp: false,
      },
      gender: this.user.gender ? this.user.gender : 'Kvinnor',
    }
  },
  mounted () {
    $(() => { $('[data-toggle="tooltip"]').tooltip() })

    if (this.registration) {
      this.events = JSON.parse(this.registration.events)
    }

    if (this.user.weight_class) {
      this.weightClass[this.gender] = this.user.weight_class
    }
  },
  computed: {
    dateString () {
      return Date.string(this.competition.date)
    }
  },
  methods: {
    hasEvent (event) {
      return JSON.parse(this.competition.events)[event]
    },
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
          licence_number: this.licenceNumber,
          events: JSON.stringify(this.events),
          gender: this.gender,
          weight_class: this.weightClass[this.gender],
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
