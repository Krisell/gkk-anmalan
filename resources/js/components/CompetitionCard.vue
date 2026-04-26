<template>
  <div
    class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer p-4 flex items-stretch gap-4"
    @click="$emit('click')"
  >
    <DateBlock :date="competition.date" :end-date="competition.end_date" />

    <div class="flex-1 min-w-0 flex flex-col gap-3">
      <div class="flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
          <div class="flex items-baseline flex-wrap gap-x-2">
            <h3 class="text-base font-semibold text-gkk">{{ competition.name }}</h3>
            <span class="text-sm text-gray-500">({{ dateString }})</span>
            <span
              v-if="registration && registration.status == 1"
              class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-medium"
            >
              Anmäld!
            </span>
            <span
              v-else-if="registration && registration.status == 0"
              class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs font-medium"
            >
              Tackat nej
            </span>
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

          <div v-if="competition.pdf_url || competition.link_url" class="flex flex-wrap gap-2 mt-3">
            <a
              v-if="competition.pdf_url"
              :href="competition.pdf_url"
              target="_blank"
              @click.stop
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-gray-200 text-xs font-medium text-red-600 hover:bg-red-50 hover:border-red-200 transition-colors"
            >
              <i class="fa fa-file-pdf-o"></i>
              <span>PDF-inbjudan</span>
            </a>
            <a
              v-if="competition.link_url"
              :href="competition.link_url"
              target="_blank"
              @click.stop
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-gray-200 text-xs font-medium text-gkk hover:bg-gkk/5 hover:border-gkk/20 transition-colors"
            >
              <i class="fa fa-external-link"></i>
              <span>Mer information</span>
            </a>
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

      <div v-if="competition.publish_count_value > 0" class="text-xs text-gray-400 text-right mt-auto">
        {{ competition.publish_count_value }} medlemmar registrerade
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

      if (this.afterLastRegistration) {
        list.push('Sista anmälningsdatum har passerat.')
      }

      return list
    },
  },
}
</script>
