<template>
  <div class="container mx-auto max-w-lg">
    <h1 class="text-center text-3xl font-thin mb-2">Tävlingsanmälan</h1>

    <div class="bg-white shadow sm:rounded-lg text-center mb-4">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          {{ competition.name }}
        </h3>
        <div class="mt-2 max-w-xl text-sm leading-5 text-gray-500">
          <p v-html="dateString"></p>
        </div>
        <div class="mt-5">
          <h3 class="text-md font-thin mt-2 whitespace-pre-wrap">{{ competition.description }}</h3>
        </div>
      </div>
    </div>

    <div v-if="passedLastRegistration" class="mt-2">
      <div v-if="!this.registration">
        <h3 class="text-orange-400 text-lg font-thin text-center">Sista anmälningsdatum har passerat</h3>
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
        <h3 class="text-gkk text-lg font-thin text-center">Licensnummer: {{ licenceNumber }}</h3>
        <h3 class="text-gkk text-lg font-thin text-center">{{ gender }}, {{ weightClass[gender] }} kg</h3>
        <h3 class="text-gkk text-lg font-thin text-center">{{ eventsString }}</h3>
        <h3 class="text-gkk text-lg font-thin text-center">{{ comment }}</h3>
      </div>
    </div>
    <div v-else>
      <form class="mt-2">
        <div class="flex mb-2">
          <svg
            v-tooltip="
              'Licensnumret består i regel av din födelsedata (6 siffror), samt dina initialer. Ex. har Anna Persson som är född 1987-08-19 licensnummer 870819ap'
            "
            class="w-6 h-6"
            fill="none"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <h4 class="ml-2 text-md font-thin">Licensnummer</h4>
        </div>

        <input
          class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          v-model="licenceNumber"
        />

        <div class="mt-4">
          <div class="flex items-center">
            <input
              value="Kvinnor"
              v-model="gender"
              type="radio"
              class="form-radio h-6 w-6 text-gkk transition duration-150 ease-in-out"
            />
            <label class="ml-3">
              <span class="block text-sm leading-5 font-medium text-gray-700">Kvinnor</span>
            </label>
          </div>
          <div class="mt-4 flex items-center">
            <input
              value="Män"
              v-model="gender"
              type="radio"
              class="form-radio h-6 w-6 text-gkk transition duration-150 ease-in-out"
            />
            <label class="ml-3">
              <span class="block text-sm leading-5 font-medium text-gray-700">Män</span>
            </label>
          </div>
        </div>

        <div class="flex mb-2 mt-6">
          <svg
            v-tooltip="
              'Mästerskap har i regel klassfast anmälan, medan serie-tävlingar inte har viktklasser. Gruppindelningen görs ofta baserat på viktklass, så välj gärna en uppskattning, men det är också helt OK att välja ¯\\_(ツ)_/¯'
            "
            class="w-6 h-6"
            fill="none"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <h4 class="ml-2 text-md font-thin">Viktklass (kg)</h4>
        </div>
        <h4 class="ml-2 text-sm font-thin">
          Vid mästerskap krävs anmälan till viktklass, men inte vid seritävling. Se infosymbolen ovan.
        </h4>

        <select
          v-model="weightClass['Män']"
          v-show="gender === 'Män'"
          class="mt-1 block form-select w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"
        >
          <option>¯\_(ツ)_/¯</option>
          <option>53</option>
          <option>59</option>
          <option>66</option>
          <option>74</option>
          <option>83</option>
          <option>93</option>
          <option>105</option>
          <option>120</option>
          <option>120+</option>
        </select>

        <select
          v-model="weightClass['Kvinnor']"
          v-show="gender === 'Kvinnor'"
          class="mt-1 block form-select w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"
        >
          <option>¯\_(ツ)_/¯</option>
          <option>43</option>
          <option>47</option>
          <option>52</option>
          <option>57</option>
          <option>63</option>
          <option>69</option>
          <option>76</option>
          <option>84</option>
          <option>84+</option>
        </select>

        <div class="mt-4">
          <div v-if="hasEvent('ksl')" class="flex mb-2 items-center">
            <ToggleButton v-model="events.ksl" />
            <div class="ml-2 font-thin">Klassisk Styrkelyft</div>
          </div>

          <div v-if="hasEvent('kbp')" class="flex mb-2 items-center">
            <ToggleButton v-model="events.kbp" />
            <div class="ml-2 font-thin">Klassisk Bänkpress</div>
          </div>

          <div v-if="hasEvent('sl')" class="flex mb-2 items-center">
            <ToggleButton v-model="events.sl" />
            <div class="ml-2 font-thin">Styrkelyft (utrustat)</div>
          </div>

          <div v-if="hasEvent('bp')" class="flex mb-2 items-center">
            <ToggleButton v-model="events.bp" />
            <div class="ml-2 font-thin">Bänkpress (utrustat)</div>
          </div>
        </div>

        <div class="mt-2">
          <textarea
            v-model="comment"
            rows="5"
            placeholder="Ev. kommentar/ytterligare info."
            class="form-textarea block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
          ></textarea>
        </div>
        <div class="mt-2 flex flex-col justify-center items-center">
          <Button v-if="registration && canHelp" style="margin-bottom: 10px" @click="register(true)"
            ><i class="fa fa-check-circle-o" style="margin-right: 10px"></i>Ja, jag vill tävla</Button
          >
          <Button type="secondary" v-if="!registration || !canHelp" style="margin-bottom: 10px" @click="register(true)"
            >Ja, jag vill tävla</Button
          >

          <Button type="danger" v-if="registration && !canHelp" style="margin-bottom: 30px" @click="register(false)"
            ><i class="fa fa-check-circle-o" style="margin-right: 10px"></i>Jag vill inte tävla</Button
          >
          <Button type="secondary" v-if="!registration || canHelp" style="margin-bottom: 30px" @click="register(false)"
            >Jag vill inte tävla</Button
          >

          <Button v-if="justSaved" secondary disabled style="margin-bottom: 10px">Sparat!</Button>
          <Button v-else secondary style="margin-bottom: 10px" @click="save">Spara</Button>
        </div>
        <div v-if="registrationStatus === 'error'">
          <Message v-if="registrationErrorReason === 'licence_number'" danger style="margin-top: 20px">
            Du måste ange licensnummer, se info ovan.
          </Message>
          <Message v-else danger style="margin-top: 20px">
            Kunde inte skicka, kontrollera inmatning och anlutning.
          </Message>
        </div>
        <div v-if="registrationStatus === 'completed'">
          <Message v-if="!canHelp" info style="margin-top: 20px">Tack för informationen!</Message>
          <Message v-else success style="margin-top: 20px">Grymt, vi ses där!</Message>
        </div>
      </form>
    </div>

    <GkkLink to="/competitions" text="Tillbaka till alla tävlingar" />

    <div class="mt-16" v-if="competition.publish_list_value && competition.publish_list_value.length > 0">
      <h3 class="text-lg font-thin text-center">Följande medlemmar har tackat ja</h3>
      <table class="min-w-full" v-if="competition.publish_list">
        <thead>
          <tr>
            <th
              class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
            >
              Namn
            </th>
            <th
              class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
            >
              Viktklass
            </th>
            <th
              class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
            >
              Gren
            </th>
          </tr>
        </thead>
        <tbody class="bg-white text-center">
          <tr v-for="registration in competition.publish_list_value" :key="registration.id">
            <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
              {{ registration.user.first_name }} {{ registration.user.last_name }}
            </td>
            <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
              {{ registration.weight_class }}
            </td>
            <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
              {{ eventsToString(registration.events) }}
            </td>
          </tr>
        </tbody>
      </table>

      <GkkLink to="/competitions" text="Tillbaka till alla tävlingar" />
    </div>
  </div>
</template>

<script>
import Button from './ui/Button.vue'
import Date from '../modules/Date.js'
import Message from './Message.vue'
import ToggleButton from './ui/ToggleButton.vue'

export default {
  components: { Message, Button, ToggleButton },
  props: ['competition', 'user', '_registration'],
  data() {
    return {
      justSaved: false,
      registrationStatus: '',
      registrationErrorReason: '',
      registration: this._registration,
      comment: this._registration ? this._registration.comment : '',
      canHelp: this._registration ? this._registration.status == 1 : null,
      licenceNumber: this.user.licence_number || '',
      weightClass: {
        Män: '93',
        Kvinnor: '63',
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
  mounted() {
    if (this.registration) {
      this.events = JSON.parse(this.registration.events)
    }

    if (this.user.weight_class) {
      this.weightClass[this.gender] = this.user.weight_class
    }
  },

  computed: {
    dateString() {
      if (this.competition.end_date) {
        return `${Date.string(this.competition.date)} – ${Date.string(this.competition.end_date)}<br>(${
          this.competition.date
        } – ${this.competition.end_date})`
      }

      return `${Date.string(this.competition.date)} (${this.competition.date})`
    },
    eventsString() {
      return Object.entries(this.events)
        .map(([key, value]) => {
          return value ? key.toUpperCase() : ''
        })
        .filter((_) => _.length)
        .join(', ')
    },
    passedLastRegistration() {
      if (!this.competition.last_registration_at) {
        return false
      }

      return new window.Date().setHours(0, 0, 0, 0) > new window.Date(this.competition.last_registration_at)
    },
  },
  methods: {
    eventsToString(events) {
      return Object.entries(JSON.parse(events))
        .filter(([event, status]) => status)
        .map(([event]) => event.toUpperCase())
        .join(', ')
    },
    hasEvent(event) {
      return JSON.parse(this.competition.events)[event]
    },
    save() {
      this.register(this.canHelp)
    },
    register(canHelp) {
      this.registrationStatus = ''
      this.registrationErrorReason = ''

      window
        .axios({
          method: 'post',
          url: `/competitions/${this.competition.id}/registrations`,
          data: {
            status: canHelp,
            comment: this.comment,
            licence_number: this.licenceNumber,
            events: JSON.stringify(this.events),
            gender: this.gender,
            weight_class: this.weightClass[this.gender],
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
          if (err.response && err.response.status === 422) {
            if (err.response.data.errors && 'licence_number' in err.response.data.errors) {
              this.registrationErrorReason = 'licence_number'
            }
          }

          this.registrationStatus = 'error'
        })
    },
  },
}
</script>
