<template>
  <div class="container mx-auto max-w-lg">
    <h1 class="text-center text-3xl font-hairline mb-2">Funktionärsanmälan</h1>

    <div class="bg-white shadow sm:rounded-lg text-center mb-4">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          {{ event.name }}
        </h3>
        <div class="mt-2 max-w-xl text-sm leading-5 text-gray-500">
          <p v-html="dateString"></p>
        </div>
        <div class="mt-5">
          <h3 class="text-md font-hairline mt-2 whitespace-pre-wrap">{{ event.description }}</h3>
        </div>
      </div>
    </div>

    <div v-if="passedLastRegistration" class="mt-2">
      <div v-if="!this.registration">
        <h3 class="text-orange-400 text-lg font-thin text-center">Sista anmälningsdatum har passerat.</h3>
      </div>

      <div v-if="this.registration && this.registration.status == 0">
        <h3 class="text-orange-400 text-lg font-thin text-center">
          Sista anmälningsdatum har passerat, och du har anmält att du inte vill/kan delta.
        </h3>
      </div>

      <div v-if="this.registration && this.registration.status == 1">
        <h3 class="text-green-400 text-lg font-thin text-center">
          Sista anmälningsdatum har passerat, och du är anmäld.
        </h3>
        <h3 class="text-gkk text-lg font-thin text-center">{{ comment }}</h3>
      </div>
    </div>

    <div v-else>
      <form class="mt-2">
        <div class="mt-2">
          <textarea
            v-model="comment"
            rows="5"
            placeholder="Ev. kommentar/ytterligare info."
            class="form-textarea block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
          ></textarea>
        </div>
        <div class="mt-2 flex flex-col justify-center items-center">
          <el-button v-if="registration && canHelp" style="margin-bottom: 10px" @click="register(true)"
            ><i class="fa fa-check-circle-o" style="margin-right: 10px"></i>Jag kan delta</el-button
          >
          <el-button secondary v-if="!registration || !canHelp" style="margin-bottom: 10px" @click="register(true)"
            >Jag kan delta</el-button
          >

          <el-button danger v-if="registration && !canHelp" style="margin-bottom: 30px" @click="register(false)"
            ><i class="fa fa-check-circle-o" style="margin-right: 10px"></i>Jag kan inte hjälpa till</el-button
          >
          <el-button
            secondary
            danger
            v-if="!registration || canHelp"
            style="margin-bottom: 30px"
            @click="register(false)"
            >Jag kan inte hjälpa till</el-button
          >

          <el-button v-if="justSaved" secondary disabled style="margin-bottom: 10px">Sparat!</el-button>
          <el-button v-else secondary style="margin-bottom: 10px" @click="save">Spara</el-button>
        </div>
        <div v-if="registrationStatus === 'error'">
          <el-message danger style="margin-top: 20px">
            Kunde inte skicka, kontrollera inmatning och anlutning.
          </el-message>
        </div>
        <div v-if="registrationStatus === 'completed'">
          <el-message v-if="!canHelp" info style="margin-top: 20px">Synd, men tack för informationen!</el-message>
          <el-message v-else success style="margin-top: 20px">Grymt, vi ses där!</el-message>
        </div>
      </form>
    </div>

    <GkkLink to="/events" text="Tillbaka till alla event" />

    <div class="mt-16" v-if="event.publish_list_value && event.publish_list_value.length > 0">
      <h3 class="text-lg font-thin text-center">Följande medlemmar har tackat ja</h3>
      <table class="min-w-full" v-if="event.publish_list">
        <thead>
          <tr>
            <th
              class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
            >
              Namn
            </th>
          </tr>
        </thead>
        <tbody class="bg-white text-center">
          <tr v-for="registration in event.publish_list_value" :key="registration.id">
            <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
              {{ registration.user.first_name }} {{ registration.user.last_name }}
            </td>
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
  data() {
    return {
      justSaved: false,
      registrationStatus: '',
      registration: this._registration,
      comment: this._registration ? this._registration.comment : '',
      canHelp: this._registration ? this._registration.status == 1 : null,
    }
  },
  computed: {
    dateString() {
      if (this.event.end_date) {
        return `${Date.string(this.event.date)} – ${Date.string(this.event.end_date)}<br>(${this.event.date} – ${
          this.event.end_date
        })`
      }

      return `${Date.string(this.event.date)} (${this.event.date})`
    },
    passedLastRegistration() {
      if (!this.event.last_registration_at) {
        return false
      }

      return new window.Date().setHours(0, 0, 0, 0) > new window.Date(this.event.last_registration_at)
    },
  },
  methods: {
    save() {
      this.register(this.canHelp)
    },
    register(canHelp) {
      this.registrationStatus = ''

      window
        .axios({
          method: 'post',
          url: `/events/${this.event.id}/registrations`,
          data: {
            status: canHelp,
            comment: this.comment,
          },
        })
        .then((response) => {
          this.canHelp = canHelp
          this.registration = response.data
          this.registrationStatus = 'completed'

          this.justSaved = true
          setTimeout(() => (this.justSaved = false), 2000)
        })
        .catch((err) => {
          this.registrationStatus = 'error'
        })
    },
  },
}
</script>
