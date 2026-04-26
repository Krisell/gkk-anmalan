<template>
  <div
    class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer p-4 flex items-stretch gap-4"
    @click="$emit('click')"
  >
    <DateBlock :date="event.date" :end-date="event.end_date" />

    <div class="flex-1 min-w-0 flex flex-col gap-3">
      <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
          <div class="flex items-baseline flex-wrap gap-x-2">
            <h3 class="text-base font-semibold text-gkk">{{ event.name }}</h3>
            <span class="text-sm text-gray-500">({{ dateString }})</span>
          </div>

          <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5 text-sm text-gray-600">
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
    messages() {
      const list = []

      if (this.registration && this.registration.status == 1) {
        list.push('Du är anmäld som funktionär, tack!')
      } else if (this.registration && this.registration.status == 0) {
        list.push('Du har anmält att du inte kan komma.')
      } else {
        list.push('Du har inte meddelat om du kan delta ännu.')
      }

      if (this.afterLastRegistration) {
        list.push('Sista anmälningsdatum har passerat.')
      }

      if (this.event.publish_count_value > 0) {
        list.push(`Hittills har ${this.event.publish_count_value} GKK-medlemmar tackat ja till detta event.`)
      }

      return list
    },
  },
}
</script>
