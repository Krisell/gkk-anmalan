<template>
  <!-- One A4 page (portrait) of the printed start lists: up to two groups, each lifter by lot number, so that the
       list is also the order of the weigh-in. Black on white like the signs; it is posted for the lifters to see the
       order (the bodyweights are noted on the weigh-in cards, not here). -->
  <SignSheet :preview="preview" plates="top" portrait>
    <div class="flex h-full flex-col px-[14mm] pb-[10mm] pt-[12mm]">
      <header class="flex items-center gap-[5mm]">
        <img class="h-[20mm] w-[20mm] shrink-0 object-contain grayscale" :src="LOGO" alt="Göteborg Kraftsportklubb" />
        <div class="min-w-0 flex-1">
          <p class="text-[2.8mm] font-bold uppercase tracking-[0.2em] text-black/55">Startlista · Invägningsordning</p>
          <p class="mt-[0.8mm] truncate text-[7mm] font-black leading-tight tracking-tight">
            {{ competition || 'Tävling' }}
          </p>
          <p v-if="info" class="mt-[0.5mm] truncate text-[3.6mm] font-semibold text-black/70">{{ info }}</p>
        </div>
        <p v-if="pages > 1" class="self-start text-[3mm] font-semibold text-black/55">Sida {{ page }} av {{ pages }}</p>
      </header>
      <div class="mt-[5mm] flex items-center gap-[3mm]">
        <span class="h-[1.6mm] w-[18mm] shrink-0 rounded-full bg-black"></span>
        <p class="shrink-0 text-[3.8mm] font-bold">
          Lottnumret avgör också invägningsordningen
          <span class="font-semibold text-black/60">– lägst nummer först</span>
        </p>
        <span class="h-[0.4mm] flex-1 rounded-full bg-black/25"></span>
      </div>

      <section v-for="group in groups" :key="group.name" class="mt-[7mm]">
        <div class="flex items-end justify-between gap-[4mm]">
          <div class="flex items-baseline gap-[3mm]">
            <h2 class="text-[9mm] font-black leading-none tracking-tight">{{ group.name }}</h2>
            <p class="text-[3.6mm] font-semibold text-black/60">
              {{ [SEXES[group.sex], `${group.lifters.length} lyftare`].filter(Boolean).join(' · ') }}
            </p>
          </div>
          <div v-if="group.weighIn" class="flex gap-[2mm]">
            <div class="rounded-[1.5mm] border-[0.35mm] border-black px-[3mm] py-[1.2mm]">
              <p class="text-[2.4mm] font-bold uppercase tracking-[0.16em] text-black/60">Invägning</p>
              <p class="text-[4.6mm] font-black leading-tight tabular-nums">
                {{ group.weighIn }}–{{ addMinutes(group.weighIn, WEIGH_IN_MINUTES) }}
              </p>
            </div>
            <div class="rounded-[1.5mm] bg-black px-[3mm] py-[1.2mm] text-white">
              <p class="text-[2.4mm] font-bold uppercase tracking-[0.16em] text-white/70">Start</p>
              <p class="text-[4.6mm] font-black leading-tight tabular-nums">
                {{ addMinutes(group.weighIn, START_AFTER_MINUTES) }}
              </p>
            </div>
          </div>
        </div>

        <table class="mt-[3mm] w-full table-fixed border-collapse text-left text-[3.8mm]">
          <colgroup>
            <col class="w-[13mm]" />
            <col />
            <col class="w-[58mm]" />
            <col class="w-[30mm]" />
            <col class="w-[18mm]" />
          </colgroup>
          <thead>
            <tr class="border-b-[0.35mm] border-black text-[2.5mm] font-bold uppercase tracking-[0.1em] text-black/60">
              <th class="pb-[1.2mm] text-center font-bold">Lott</th>
              <th class="pb-[1.2mm] pl-[2mm] font-bold">Namn</th>
              <th class="pb-[1.2mm] font-bold">Förening</th>
              <th class="pb-[1.2mm] font-bold">Kategori</th>
              <th class="pb-[1.2mm] font-bold">Klass</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(lifter, index) in group.lifters"
              :key="lifter.id ?? index"
              class="h-[8mm] border-b-[0.2mm] border-black/15"
              :class="{ 'bg-black/[0.035]': index % 2 }"
            >
              <td class="text-center">
                <span
                  class="inline-flex h-[6mm] min-w-[8mm] items-center justify-center rounded-[1.2mm] border-[0.3mm] border-black px-[1mm] font-black tabular-nums"
                >
                  {{ lifter.lot ?? '–' }}
                </span>
              </td>
              <td class="truncate pl-[2mm] pr-[2mm] font-bold">{{ lifter.name }}</td>
              <td class="truncate pr-[2mm] text-black/80">{{ lifter.team }}</td>
              <td class="truncate pr-[2mm] text-black/80">{{ lifter.category }}</td>
              <td class="font-semibold tabular-nums">{{ formatWeightClass(lifter.weightClass) ?? '–' }}</td>
            </tr>
          </tbody>
        </table>
      </section>

      <footer
        class="mt-auto border-t-[0.2mm] border-black/25 pt-[2.5mm] text-center text-[3mm] font-semibold text-black/60"
      >
        Göteborg Kraftsportklubb och Västra Götalands Styrkelyftförbund
      </footer>
    </div>
  </SignSheet>
</template>

<script setup>
import SignSheet from './SignSheet.vue'
import { formatWeightClass } from '../../live/format.js'

const LOGO = 'https://goteborg-kraftsportklubb.web.app/img/logo-min.png'

// The weigh-in opens two hours before the start of the group and lasts an hour and a half.
const WEIGH_IN_MINUTES = 90
const START_AFTER_MINUTES = 120

const SEXES = { f: 'Damer', m: 'Herrar' }

defineProps({
  // [{ name, sex, weighIn: 'HH:MM' | '', lifters: [{ id, lot, name, team, category, weightClass }] }]
  groups: { type: Array, required: true },
  competition: { type: String, default: '' },
  info: { type: String, default: '' },
  page: { type: Number, default: 1 },
  pages: { type: Number, default: 1 },
  preview: { type: Boolean, default: false },
})

function addMinutes(time, minutes) {
  const [hours, mins] = time.split(':').map(Number)
  const total = (hours * 60 + mins + minutes) % (24 * 60)
  return `${String(Math.floor(total / 60)).padStart(2, '0')}:${String(total % 60).padStart(2, '0')}`
}
</script>
