<template>
  <div class="container mx-auto">
    <h1 class="text-center text-3xl font-thin mb-6">Admin - Funktionärsanmälningar</h1>

    <div class="flex flex-col">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                >
                  Event
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                >
                  Sista anmälningsdag
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                >
                  Tid och plats
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                >
                  Antal tackat ja
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                >
                  Åtgärder
                </th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr
                class="event-row"
                v-for="event in events"
                :key="event.id"
                @click.prevent="location(`/admin/events/${event.id}`)"
                style="cursor: pointer"
              >
                <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm leading-5 font-medium text-gray-900">{{ event.name }}</div>
                      <div class="text-sm leading-5 text-gray-500">{{ dateString(event) }}</div>
                    </div>
                  </div>
                </td>

                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">
                    {{ (event.last_registration_at || '').slice(0, 10) }}
                  </div>
                </td>

                <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm leading-5 font-medium text-gray-900">{{ event.location }}</div>
                      <div class="text-sm leading-5 text-gray-500">{{ event.time }}</div>
                    </div>
                  </div>
                </td>

                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500">
                    {{ countYes(event) }} (av {{ event.registrations.length }})
                  </div>
                </td>

                <td @click.prevent="(e) => e.stopPropagation()" class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex">
                    <svg
                      v-tooltip="'Redigera event'"
                      class="w-6 text-gkk-light hover:text-gkk"
                      @click.prevent="edit(event)"
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
                      v-tooltip="'Skicka mail till medlemmar'"
                      class="w-6 ml-2 text-gkk-light hover:text-gkk"
                      @click.prevent="confirmSendNotification(event)"
                      fill="none"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <svg
                      v-tooltip="'Radera event'"
                      class="w-6 ml-2 text-gkk-light hover:text-gkk"
                      @click.prevent="confirmDelete(event)"
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
          >Visa även gamla events</a
        >
      </div>
    </div>

    <div class="w-full flex justify-center items-center">
      <Button class="mt-2 mx-auto" v-if="!editing" @click.prevent="showNewEvent = !showNewEvent">
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
          <div>Nytt event för funktionärsanmälningar</div>
        </div>
      </Button>
    </div>

    <form class="mt-4 mb-6 max-w-xl mx-auto" v-show="showNewEvent">
      <div>
        <div class="text-lg font-thin mt-2">Namn på event</div>
        <input
          v-model="event.name"
          class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          name="name"
        />
      </div>

      <div>
        <div class="text-lg font-thin mt-2">Datum</div>
        <input
          v-model="event.date"
          class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          type="date"
          name="date"
        />

        <div class="text-lg font-thin mt-2">till (lämna tom för endagsevent)</div>
        <input
          v-model="event.end_date"
          class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          type="date"
          name="date"
        />
      </div>

      <div>
        <div class="text-lg font-thin mt-2">Ev. sista anmälningsdag</div>
        <input
          v-model="event.last_registration_at"
          class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          type="date"
        />
      </div>

      <div class="mt-2">
        <input
          v-model="event.time"
          class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          name="time"
          placeholder="Ungefärlig tid, ex. 8 – 15"
        />
      </div>
      <div class="mt-2">
        <input
          v-model="event.location"
          class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          name="location"
          placeholder="Plats, ex Friskis Majorna"
        />
      </div>
      <div class="mt-2">
        <textarea
          v-model="event.description"
          rows="5"
          placeholder="Ev. ytterligare info"
          class="form-textarea block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5 p-2 border border-gray-300 rounded-md"
        ></textarea>
      </div>

      <div class="flex mt-2 mb-2 items-center">
        <ToggleButton v-model="event.publish_count" />
        <div class="ml-2 text-lg font-thin">Visa antal anmälda för medlemmar</div>
      </div>

      <div class="flex mb-2 items-center">
        <ToggleButton v-model="event.publish_list" />
        <div class="ml-2 text-lg font-thin">Visa anmälningslista för medlemmar (endast namn)</div>
      </div>

      <div class="flex mb-2 items-center">
        <div class="mr-2 text-lg font-thin">Visningsalternativ</div>
        <select
          v-model="event.show_status"
          class="mt-1 block form-select w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"
        >
          <option value="default">Default (visas tills datum passerat)</option>
          <option value="show">Visa</option>
          <option value="hide">Dölj</option>
        </select>
      </div>

      <div class="flex">
        <Button v-if="!editing" @click.prevent="createEvent">Skapa event</Button>
        <Button type="secondary" class="mr-2" v-if="editing" @click.prevent="cancelUpdate">Ångra</Button>
        <Button v-if="editing" @click.prevent="updateEvent">Uppdatera event</Button>
      </div>

      <div v-if="newEventError" class="mt-2">
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
                Kunde inte skapa event, kontrollera inmatning och anlutning.
              </p>
            </div>
          </div>
        </div>
      </div>
    </form>

    <Modal ref="deleteEventModal" :title="`Är du säker på att du vill radera ${ selectedEvent && selectedEvent.name }?`">
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button type="secondary" @click.prevent="close">Nej</Button>
          <Button type="danger" @click.prevent="deleteEvent">Radera</Button>
        </div>
      </template>
    </Modal>

    <Modal ref="sendNotificationModal" :title="`Skicka mail om ${ selectedEvent && selectedEvent.name }?`">
      <div v-if="notificationStatus && notificationStatus.length > 0" class="mb-4">
        <p class="text-sm text-gray-600 mb-2">Tidigare utskick:</p>
        <div v-for="log in notificationStatus" :key="log.id" class="text-xs text-gray-500 mb-1">
          {{ new Date(log.created_at).toLocaleString('sv-SE') }} - 
          {{ log.sent_by.first_name }} {{ log.sent_by.last_name }} 
          ({{ log.recipients_count }} mottagare)
        </div>
      </div>
      <p class="text-sm text-gray-700 mb-4">
        Detta kommer att skicka ett mail till alla registrerade medlemmar med information om eventet.
        <strong>Är du säker på att du vill fortsätta?</strong>
      </p>
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button type="secondary" @click.prevent="close">Avbryt</Button>
          <Button @click.prevent="sendNotification">Skicka mail</Button>
        </div>
      </template>
    </Modal>

    <Modal ref="notificationResultModal" :title="notificationResult.success ? 'Mail skickat!' : 'Fel uppstod'">
      <p v-if="notificationResult.success" class="text-sm text-gray-700">
        Mailet skickades till {{ notificationResult.recipients_count }} medlemmar.
      </p>
      <p v-else class="text-sm text-red-600">
        Ett fel uppstod när mailet skulle skickas. Försök igen eller kontakta administratör.
      </p>
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button @click.prevent="close">Stäng</Button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script>
import Modal from './ui/Modal.vue'
import Button from './ui/Button.vue'
import ToggleButton from './ui/ToggleButton.vue'

export default {
  components: { Button, ToggleButton, Modal },
  props: ['events', 'showingOld'],
  data() {
    return {
      editing: false,
      showNewEvent: false,
      newEventError: false,
      selectedEvent: null,
      notificationStatus: [],
      notificationResult: {},
      event: {
        name: '',
        date: '',
        time: '',
        location: '',
        description: '',
        publish_count: false,
        publish_list: false,
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
    dateString(event) {
      if (!event.end_date) {
        return event.date || '&nbsp;'
      }

      return `${event.date} – ${event.end_date}`
    },
    confirmDelete(event) {
      this.selectedEvent = event
      this.$refs.deleteEventModal.show()
    },
    deleteEvent() {
      window.axios.delete(`/admin/events/${this.selectedEvent.id}`).then(() => window.location.reload())
    },
    countYes(event) {
      return event.registrations.filter((registration) => registration.status == 1).length
    },
    edit(event) {
      Object.assign(this.event, event)
      this.showNewEvent = true
      this.editing = true
    },
    createEvent() {
      this.newEventError = false

      window
        .axios({
          method: 'post',
          url: '/admin/events',
          data: this.event,
        })
        .then(this.reload)
        .catch((err) => {
          this.newEventError = true
          console.log(err)
        })
    },
    cancelUpdate() {
      this.reload()
    },
    updateEvent() {
      this.newEventError = false

      window
        .axios({
          method: 'patch',
          url: `/admin/events/${this.event.id}`,
          data: this.event,
        })
        .then(this.reload)
        .catch((err) => {
          this.newEventError = true
          console.log(err)
        })
    },
    async confirmSendNotification(event) {
      this.selectedEvent = event
      this.notificationStatus = []

      try {
        const response = await window.axios.get(`/admin/events/${event.id}/notification-status`)
        this.notificationStatus = response.data.notifications
      } catch (err) {
        console.error('Failed to fetch notification status:', err)
      }

      this.$refs.sendNotificationModal.show()
    },
    async sendNotification() {
      this.$refs.sendNotificationModal.close()

      try {
        const response = await window.axios.post(`/admin/events/${this.selectedEvent.id}/send-notification`, {
          confirmed: true,
        })

        this.notificationResult = {
          success: true,
          recipients_count: response.data.recipients_count,
        }
      } catch (err) {
        console.error('Failed to send notification:', err)
        this.notificationResult = {
          success: false,
        }
      }

      this.$refs.notificationResultModal.show()
    },
  },
}
</script>
