<template>
  <div class="container mx-auto">
    <h1 class="text-center text-3xl font-thin mb-6">Admin - Tävlingsanmälan</h1>
    <div class="flex flex-col">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                >
                  Tävling
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                >
                  Anmälan senast
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                >
                  Tid
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                >
                  Plats
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                >
                  Antal anmälda
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                >
                  Åtgärder
                </th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr
                v-for="competition in competitions"
                :key="competition.id"
                @click.prevent="location(`/admin/competitions/${competition.id}`)"
                style="cursor: pointer"
              >
                <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm leading-5 font-medium text-gray-900">{{ competition.name }}</div>
                      <div class="text-sm leading-5 text-gray-500">{{ dateString(competition) }}</div>
                    </div>
                  </div>
                </td>
                <!-- <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ competition.name }}</div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ (competition.last_registration_at || '').slice(0, 10) }}</div>
                </td> -->
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">
                    {{ (competition.last_registration_at || '').slice(0, 10) }}
                  </div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ competition.time }}</div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ competition.location }}</div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">
                    {{ countYes(competition) }} (av {{ competition.registrations.length }})
                  </div>
                </td>
                <td @click.prevent="(e) => e.stopPropagation()" class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center justify-center">
                    <svg
                      v-tooltip="'Redigera tävling'"
                      class="w-6 text-gkk-light hover:text-gkk"
                      @click.prevent="edit(competition)"
                      fill="none"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                      ></path>
                      <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <svg
                      v-tooltip="'Radera tävling'"
                      class="w-6 ml-2 text-gkk-light hover:text-gkk"
                      @click.prevent="confirmDelete(competition)"
                      fill="none"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                      ></path>
                    </svg>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <a
          v-if="!showingOld"
          href="?all"
          class="text-xs mt-2 cursor-pointer hover:underline text-right font-extralight block"
          >Visa även gamla tävlingar</a
        >
      </div>
    </div>

    <div class="w-full flex justify-center items-center">
      <Button class="mt-2 mx-auto" v-if="!editing" @click.prevent="showNewCompetition = !showNewCompetition">
        <div class="flex items-center justify-center">
          <svg
            class="w-6 -ml-2 mr-2"
            fill="none"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div>Ny tävling</div>
        </div>
      </Button>
    </div>

    <form class="mt-4 mb-6 max-w-xl mx-auto" v-show="showNewCompetition">
      <div>
        <div class="text-lg font-thin mt-2">Tävlingsnamn</div>
        <input
          v-model="competition.name"
          class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          name="name"
        />
      </div>
      <div>
        <div class="text-lg font-thin mt-2">Datum</div>
        <input
          v-model="competition.date"
          class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          type="date"
          name="date"
        />

        <div class="text-lg font-thin mt-2">till (lämna tom för endagstävling)</div>
        <input
          v-model="competition.end_date"
          class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          type="date"
          name="date"
        />
      </div>

      <div>
        <div class="text-lg font-thin mt-2">Ev. sista anmälningsdag</div>
        <input
          v-model="competition.last_registration_at"
          class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          type="date"
          name="last_registration_at"
        />
      </div>

      <div class="flex mt-4 mb-2 items-center">
        <ToggleButton v-model="events.ksl" />
        <div class="ml-2 text-lg font-thin">KSL</div>
      </div>

      <div class="flex mb-2 items-center">
        <ToggleButton v-model="events.kbp" />
        <div class="ml-2 text-lg font-thin">KBP</div>
      </div>

      <div class="flex mb-2 items-center">
        <ToggleButton v-model="events.sl" />
        <div class="ml-2 text-lg font-thin">SL</div>
      </div>

      <div class="flex mb-2 items-center">
        <ToggleButton v-model="events.bp" />
        <div class="ml-2 text-lg font-thin">BP</div>
      </div>

      <div class="mt-2">
        <input
          v-model="competition.time"
          class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          name="time"
          placeholder="Ungefärlig tid, ex. 8 – 15"
        />
      </div>
      <div class="mt-2">
        <input
          v-model="competition.location"
          class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          name="location"
          placeholder="Plats, ex Friskis Majorna"
        />
      </div>
      <div class="mt-2">
        <textarea
          v-model="competition.description"
          rows="5"
          placeholder="Ev. ytterligare info"
          class="form-textarea block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5 p-2 border border-gray-300 rounded-md"
        ></textarea>
      </div>

      <div class="flex mt-2 mb-2 items-center">
        <ToggleButton v-model="competition.publish_count" />
        <div class="ml-2 text-lg font-thin">Visa antal anmälda för medlemmar</div>
      </div>

      <div class="flex mb-2 items-center">
        <ToggleButton v-model="competition.publish_list" />
        <div class="ml-2 text-lg font-thin">Visa anmälningslista för medlemmar (namn, vikt, gren)</div>
      </div>

      <div class="flex mb-2 items-center">
        <div class="mr-2 text-lg font-thin">Visningsalternativ</div>
        <select
          v-model="competition.show_status"
          class="mt-1 block form-select w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"
        >
          <option value="default">Default (visas tills datum passerat)</option>
          <option value="show">Visa</option>
          <option value="hide">Dölj</option>
        </select>
      </div>

      <div class="flex">
        <Button v-if="!editing" @click.prevent="createCompetition">Skapa tävling</Button>
        <Button type="secondary" class="mr-2" v-if="editing" @click.prevent="cancelUpdate">Ångra</Button>
        <Button v-if="editing" @click.prevent="updateCompetition">Uppdatera tävling</Button>
      </div>

      <div v-if="newCompetitionError" class="mt-2">
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path
                  fill-rule="evenodd"
                  d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm leading-5 text-yellow-700">
                Kunde inte skapa tävling, kontrollera inmatning och anlutning.
              </p>
            </div>
          </div>
        </div>
      </div>
    </form>

    <Modal ref="deleteCompetitionModal" :title="`Är du säker på att du vill radera ${ selectedCompetition && selectedCompetition.name }?`">
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button type="secondary" @click.prevent="close">Nej</Button>
          <Button type="danger" @click.prevent="deleteCompetition">Radera</Button>
        </div>
        </template>
    </Modal>
  </div>
</template>

<script>
import ToggleButton from './ui/ToggleButton.vue'
import Button from './ui/Button.vue'
import Modal from './ui/Modal.vue'

export default {
  components: { ToggleButton, Button, Modal },
  props: ['competitions', 'showingOld'],
  data() {
    return {
      editing: false,
      showNewCompetition: false,
      newCompetitionError: false,
      selectedCompetition: null,
      events: {
        ksl: true,
        kbp: true,
        sl: false,
        bp: false,
      },
      competition: {
        name: '',
        date: '',
        time: '',
        location: '',
        description: '',
        publish_count: false,
        publish_list: false,
        last_registration_at: null,
        show_status: 'default',
      },
      showStatusOptions: [
        { value: 'default', label: 'Default (visas tills datum passerat)' },
        { value: 'show', label: 'Visa' },
        { value: 'hide', label: 'Dölj' },
      ],
    }
  },
  methods: {
    dateString(competition) {
      if (!competition.end_date) {
        return competition.date || '&nbsp;'
      }

      return `${competition.date} – ${competition.end_date}`
    },
    confirmDelete(competition) {
      this.selectedCompetition = competition
      this.$refs.deleteCompetitionModal.show()
    },
    deleteCompetition() {
      window.axios.delete(`/admin/competitions/${this.selectedCompetition.id}`).then(this.reload)
    },
    countYes(competition) {
      return competition.registrations.filter((registration) => registration.status == 1).length
    },
    edit(competition) {
      Object.assign(this.competition, competition)

      if (this.competition.events) {
        this.events = JSON.parse(this.competition.events)
      }

      this.showNewCompetition = true
      this.editing = true
    },
    createCompetition() {
      this.newCompetitionError = false

      window
        .axios({
          method: 'post',
          url: '/admin/competitions',
          data: {
            ...this.competition,
            events: JSON.stringify(this.events),
          },
        })
        .then(this.reload)
        .catch((err) => {
          this.newCompetitionError = true
          console.log(err)
        })
    },
    cancelUpdate() {
      this.reload()
    },
    updateCompetition() {
      this.newCompetitionError = false

      window
        .axios({
          method: 'patch',
          url: `/admin/competitions/${this.competition.id}`,
          data: {
            ...this.competition,
            events: JSON.stringify(this.events),
          },
        })
        .then(this.reload)
        .catch((err) => {
          this.newCompetitionError = true
          console.log(err)
        })
    },
  },
}
</script>
