<template>
  <div
    class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer p-4 flex items-stretch gap-4"
    @click="$emit('click')"
  >
    <DateBlock :date="event.date" :end-date="event.end_date" />

    <div class="flex-1 min-w-0 flex flex-col gap-3">
      <div class="flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
          <div class="flex items-baseline flex-wrap gap-x-2">
            <h3 class="text-base font-semibold text-gkk">{{ event.name }}</h3>
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
            <span
              v-else
              class="inline-flex items-center px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-xs font-medium"
            >
              Ej svarat
            </span>
            <span
              v-if="afterLastRegistration"
              class="inline-flex items-center px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-xs font-medium"
            >
              Anmälan stängd
            </span>
          </div>

          <div class="flex flex-col gap-1 mt-1.5 text-sm text-gray-600">
            <div v-if="event.last_registration_at" class="flex items-center gap-1.5">
              <i class="fa fa-calendar-o text-gray-400"></i>
              <span>Sista anmälningsdag: {{ event.last_registration_at }}</span>
            </div>
            <div v-if="event.description" class="flex items-start gap-1.5 min-w-0">
              <i class="fa fa-users text-gray-400 mt-1"></i>
              <span class="whitespace-pre-wrap">{{ event.description }}</span>
            </div>
          </div>
        </div>

        <i class="fa fa-angle-right text-gray-400 group-hover:text-gkk transition-colors text-2xl shrink-0 self-center"></i>
      </div>

      <div v-if="event.publish_count_value > 0" class="text-xs text-gray-400 text-right mt-auto">
        {{ event.publish_count_value }} medlemmar har tackat ja
      </div>
    </div>
  </div>
</template>

<script>
import Date from '../modules/Date.js'
import DateBlock from './DateBlock.vue'

export default {
  components: { DateBlock },
  props: ['event', 'registration'],
  computed: {
    dateString() {
      if (this.event.end_date) {
        return `${Date.string(this.event.date)} – ${Date.string(this.event.end_date)}`
      }

      return Date.string(this.event.date)
    },
    afterLastRegistration() {
      if (!this.event.last_registration_at) {
        return false
      }

      return new window.Date().setHours(0, 0, 0, 0) > new window.Date(this.event.last_registration_at)
    },
  },
}
</script>
