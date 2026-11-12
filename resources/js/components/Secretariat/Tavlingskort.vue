<template>
  <!-- A lifter's competition card (about 95 × 89 mm at its smallest), for the speaker's table: the bodyweight at the weigh-in, every
       attempt and the results are written on it. Two, four or six fit an A4 page (see cardLayouts.js): the card is
       then made larger (zoom) and wider (width, in mm before the zoom). The rack heights are on a card of their own,
       so they aren't here. All lifters lift classic (raw). -->
  <table
    class="tavlingskort table-fixed border-collapse text-[3.4mm] leading-tight text-black"
    :style="{ width: `${width}mm`, zoom }"
  >
    <colgroup>
      <col class="w-[22%]" />
      <col class="w-[17.4%]" />
      <col class="w-[17.4%]" />
      <col class="w-[17.4%]" />
      <col />
    </colgroup>
    <tbody>
      <tr class="h-[12mm]">
        <!-- The organiser's logo, in a box of its own so that it isn't read as the lifter's club. -->
        <td colspan="4" class="!p-0">
          <div class="flex h-[12mm] items-stretch">
            <div class="flex shrink-0 items-center border-r-[0.3mm] border-black px-[1.6mm]">
              <img class="h-[9mm] w-[9mm] object-contain grayscale" :src="LOGO" alt="Göteborg Kraftsportklubb" />
            </div>
            <p class="flex min-w-0 items-center px-[1.6mm]">
              <span class="truncate text-[4.8mm] font-black tracking-tight">{{ lifter.name }}</span>
            </p>
          </div>
        </td>
        <td class="text-center">
          <span class="label">Grupp</span>
          <p class="text-[5.4mm] font-black leading-none">{{ groupNumber }}</p>
        </td>
      </tr>
      <tr class="h-[8.5mm]">
        <td colspan="3">
          <span class="label">Förening</span>
          <p class="truncate font-semibold">{{ lifter.team ?? '' }}</p>
        </td>
        <td colspan="2">
          <span class="label">Licensnummer</span>
          <p class="truncate font-semibold tabular-nums">{{ lifter.license ?? '' }}</p>
        </td>
      </tr>
      <!-- Kategori, Viktklass, Kön and Lott as boxes of their own widths; the sex in capitals, so it can't be missed. -->
      <tr class="h-[9mm]">
        <td colspan="5" class="!p-0">
          <div class="flex h-[9mm] items-stretch">
            <div class="min-w-0 flex-1 px-[1.6mm] py-[0.8mm]">
              <span class="label">Kategori</span>
              <p class="truncate text-[3.8mm] font-black">{{ lifter.category ?? '' }}</p>
            </div>
            <div class="w-[22mm] shrink-0 border-l-[0.3mm] border-black px-[1.6mm] py-[0.8mm]">
              <span class="label">Viktklass</span>
              <p class="truncate text-[3.8mm] font-black tabular-nums">
                {{ formatWeightClass(lifter.weightClass) ?? '' }}
              </p>
            </div>
            <div class="w-[22mm] shrink-0 border-l-[0.3mm] border-black px-[1.6mm] py-[0.8mm]">
              <span class="label">Kön</span>
              <p class="text-[3.8mm] font-black uppercase tracking-wide">{{ SEXES[lifter.sex] ?? '' }}</p>
            </div>
            <div class="w-[13mm] shrink-0 border-l-[0.3mm] border-black px-[1.6mm] py-[0.8mm]">
              <span class="label">Lott</span>
              <p class="text-[3.8mm] font-black tabular-nums">{{ lifter.lot ?? '' }}</p>
            </div>
          </div>
        </td>
      </tr>
      <tr class="h-[10mm]">
        <td colspan="2" class="align-top">
          <span class="label">Kroppsvikt</span>
        </td>
        <td class="text-center text-[2.8mm] font-bold">Klassisk</td>
        <td colspan="2" class="align-top">
          <span class="label">Sign</span>
        </td>
      </tr>
      <tr class="h-[6mm] text-center">
        <td></td>
        <td class="font-bold">1</td>
        <td class="font-bold">2</td>
        <td class="font-bold">3</td>
        <td class="font-bold">Resultat</td>
      </tr>
      <tr v-for="lift in LIFTS" :key="lift" class="h-[10.5mm]">
        <td class="font-bold">{{ lift }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr class="h-[9.5mm]">
        <td colspan="5" class="align-top">
          <span class="label">Total</span>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script setup>
import { computed } from 'vue'
import { formatWeightClass } from '../../live/format.js'

const LOGO = 'https://goteborg-kraftsportklubb.web.app/img/logo-min.png'

const LIFTS = ['Knäböj', 'Bänkpress', 'Marklyft']

// As in the class names of Swedish powerlifting (and on the start lists).
const SEXES = { f: 'Damer', m: 'Herrar' }

const props = defineProps({
  // { lot, name, team, category, weightClass, license, sex }
  lifter: { type: Object, required: true },
  group: { type: String, required: true },
  width: { type: Number, default: 95 },
  zoom: { type: Number, default: 1 },
})

// 'Grupp 01' -> '1'; a group named otherwise is shown as it is.
const groupNumber = computed(() => props.group.match(/^grupp\s*0*(\d+)$/i)?.[1] ?? props.group)
</script>

<style scoped>
.tavlingskort {
  border: 0.5mm solid black;
}

.tavlingskort td {
  border: 0.3mm solid black;
  padding: 0.8mm 1.6mm;
  vertical-align: middle;
}

/* The fields written in by hand (bodyweight, sign, total): the label at the top, the space under it to write in. */
.tavlingskort td.align-top {
  vertical-align: top;
}

.label {
  display: block;
  font-size: 2.2mm;
  font-weight: 700;
  letter-spacing: 0.12em;
  line-height: 1.2;
  text-transform: uppercase;
  color: rgb(0 0 0 / 0.55);
}
</style>
