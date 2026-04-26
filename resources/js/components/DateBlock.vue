<template>
  <div class="bg-gkk/5 rounded-xl px-4 py-3 flex flex-col items-center justify-center min-w-[96px] shrink-0 self-stretch">
    <div class="text-[11px] font-semibold text-gkk/70 uppercase tracking-wider">{{ monthLabel }}</div>
    <div class="text-2xl font-bold text-gkk leading-tight mt-0.5">{{ dayLabel }}</div>
    <div class="text-[11px] font-semibold text-gkk/70 uppercase tracking-wider mt-0.5">{{ weekdayLabel }}</div>
  </div>
</template>

<script>
import moment from 'moment'

const MONTHS = ['JAN', 'FEB', 'MAR', 'APR', 'MAJ', 'JUN', 'JUL', 'AUG', 'SEP', 'OKT', 'NOV', 'DEC']
const WEEKDAY_ABBR = { 1: 'MÅN', 2: 'TIS', 3: 'ONS', 4: 'TOR', 5: 'FRE', 6: 'LÖR', 7: 'SÖN' }
const WEEKDAY_FULL = { 1: 'MÅNDAG', 2: 'TISDAG', 3: 'ONSDAG', 4: 'TORSDAG', 5: 'FREDAG', 6: 'LÖRDAG', 7: 'SÖNDAG' }

export default {
  props: ['date', 'end-date'],
  computed: {
    start() {
      return this.date ? moment(this.date) : null
    },
    end() {
      return this.endDate ? moment(this.endDate) : null
    },
    monthLabel() {
      if (!this.start) {
        return ''
      }

      if (this.end && this.end.month() !== this.start.month()) {
        return `${MONTHS[this.start.month()]}–${MONTHS[this.end.month()]}`
      }

      return MONTHS[this.start.month()]
    },
    dayLabel() {
      if (!this.start) {
        return '?'
      }

      if (this.end) {
        return `${this.start.date()}–${this.end.date()}`
      }

      return String(this.start.date())
    },
    weekdayLabel() {
      if (!this.start) {
        return ''
      }

      if (this.end) {
        return `${WEEKDAY_ABBR[this.start.isoWeekday()]}–${WEEKDAY_ABBR[this.end.isoWeekday()]}`
      }

      return WEEKDAY_FULL[this.start.isoWeekday()]
    },
  },
}
</script>
