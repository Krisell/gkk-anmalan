<template>
  <div
    class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer p-4 flex items-stretch gap-4"
    @click="$emit('click')"
  >
    <DateBlock :date="competition.date" :end-date="competition.end_date" />

    <div class="flex-1 min-w-0 flex flex-col gap-3">
      <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
          <div class="flex items-baseline flex-wrap gap-x-2">
            <h3 class="text-base font-semibold text-gkk">{{ competition.name }}</h3>
            <span class="text-sm text-gray-500">({{ dateString }})</span>
            <a
              v-if="competition.pdf_url"
              :href="competition.pdf_url"
              target="_blank"
              @click.stop
              v-tooltip="'PDF-inbjudan bifogad'"
              class="text-red-500 hover:text-red-600"
            >
              <i class="fa fa-file-pdf-o"></i>
            </a>
            <a
              v-if="competition.link_url"
              :href="competition.link_url"
              target="_blank"
              @click.stop
              v-tooltip="'Länk bifogad'"
              class="text-gkk hover:text-gkk-light"
            >
              <i class="fa fa-external-link"></i>
            </a>
          </div>

          <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5 text-sm text-gray-600">
            <div v-if="competition.last_registration_at" class="flex items-center gap-1.5">
              <i class="fa fa-calendar-o text-gray-400"></i>
              <span>Sista anmälningsdag: {{ competition.last_registration_at }}</span>
            </div>
            <div v-if="competition.description" class="flex items-start gap-1.5 min-w-0">
              <i class="fa fa-users text-gray-400 mt-1"></i>
              <span class="whitespace-pre-wrap">{{ competition.description }}</span>
            </div>
          </div>
        </div>

        <i class="fa fa-angle-right text-gray-400 group-hover:text-gkk transition-colors text-2xl shrink-0 self-center"></i>
      </div>

      <div v-if="messages.length" class="flex flex-col gap-2">
        <Message v-for="(msg, i) in messages" :key="i">
          <div class="flex items-center gap-2">
            <i class="fa fa-info-circle shrink-0 text-[#439cd6]"></i>
            <span>{{ msg }}</span>
          </div>
        </Message>
      </div>
    </div>
  </div>
</template>

<script>
import Date from '../modules/Date.js'
import Message from './Message.vue'
import DateBlock from './DateBlock.vue'

export default {
  components: { Message, DateBlock },
  props: ['competition', 'registration'],
  computed: {
    dateString() {
      if (this.competition.end_date) {
        return `${Date.string(this.competition.date)} – ${Date.string(this.competition.end_date)}`
      }

      return Date.string(this.competition.date)
    },
    afterLastRegistration() {
      if (!this.competition.last_registration_at) {
        return false
      }

      return new window.Date().setHours(0, 0, 0, 0) > new window.Date(this.competition.last_registration_at)
    },
    messages() {
      const list = []

      if (this.registration && this.registration.status == 1) {
        list.push('Du har anmält intresse att tävla!')
      } else if (this.registration && this.registration.status == 0) {
        list.push('Du har tackat nej till denna tävling.')
      }

      if (this.afterLastRegistration) {
        list.push('Sista anmälningsdatum har passerat.')
      }

      if (this.competition.publish_count_value > 0) {
        list.push(`Just nu är ${this.competition.publish_count_value} GKK-medlemmar registrerade på denna tävling.`)
      }

      return list
    },
  },
}
</script>
