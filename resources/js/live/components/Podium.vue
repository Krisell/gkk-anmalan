<template>
  <div class="podium" :class="{ instant }">
    <header class="heading">
      <img :src="logo" alt="" class="logo" />
      <div class="titles">
        <div class="eyebrow">Prisutdelning{{ group ? ` · ${group}` : '' }}</div>
        <div class="title">{{ label }}</div>
      </div>
    </header>
    <!-- Top to bottom by place, not in the order the lifters stand on the podium (which we can't know). -->
    <ol class="places">
      <li v-for="(lifter, index) in lifters" :key="lifter.id ?? index" class="place" :class="MEDALS[lifter.place]">
        <span class="medal">{{ lifter.place }}</span>
        <span class="who">
          <span class="name">{{ lifter.name ?? 'Okänd lyftare' }}</span>
          <span v-if="lifter.team" class="team">{{ lifter.team }}</span>
        </span>
        <span class="score">
          <template v-if="coefficient"> {{ formatPoints(lifter.points) }}<small>poäng</small> </template>
          <template v-else>{{ formatWeight(lifter.total) }}<small>kg</small></template>
        </span>
      </li>
    </ol>
  </div>
</template>

<script setup>
import { BROADCASTER_LOGO as logo } from '../clubs.js'
import { formatPoints, formatWeight } from '../format.js'

/**
 * Gold, silver and bronze of one class, for the prize ceremony: at the bottom left, over the camera picture, listed
 * top to bottom by place (the lifters may stand in any order on the podium). A coefficient class is placed by points,
 * so it shows those instead of the total. `instant` leaves out the entrance animation.
 */
defineProps({
  // The class's name, e.g. '-74 kg · Senior'.
  label: { type: String, default: '' },
  group: { type: String, default: null },
  // The top three (or fewer), by place: [{ id, name, team, place, total, points }].
  lifters: { type: Array, required: true },
  coefficient: { type: Boolean, default: false },
  instant: { type: Boolean, default: false },
})

const MEDALS = { 1: 'gold', 2: 'silver', 3: 'bronze' }
</script>

<style scoped>
.podium {
  position: absolute;
  left: 96px;
  bottom: 64px;
  width: 820px;
  filter: drop-shadow(0 18px 32px rgba(0, 0, 0, 0.45));
}

.heading {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 14px 24px;
  border-radius: 14px 14px 0 0;
  background: var(--overlay-panel-deep);
  animation: slide-in 550ms var(--motion-enter) both;
}

.logo {
  width: 64px;
  height: 64px;
}

.eyebrow {
  font-family: var(--overlay-font);
  font-size: 22px;
  font-weight: 600;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: var(--overlay-silver);
}

.title {
  font-family: var(--overlay-font);
  font-size: 44px;
  font-weight: 800;
  line-height: 1.05;
  text-transform: uppercase;
}

.places {
  margin: 0;
  padding: 0;
  list-style: none;
  border-radius: 0 0 14px 14px;
  overflow: hidden;
  background: var(--overlay-panel);
}

.place {
  --metal: #d9b54a;
  --metal-light: #ffe58a;
  --metal-dark: #9c7a1c;
  --metal-tint: rgba(217, 181, 74, 0.34);
  display: flex;
  align-items: center;
  gap: 22px;
  height: 92px;
  padding: 0 28px 0 22px;
  border-top: 1px solid rgba(166, 172, 185, 0.16);
  /* The medal's colour as a band along the left edge, fading into the panel (no color-mix: OBS's browser may be old). */
  background: linear-gradient(90deg, var(--metal-tint), transparent 55%);
  box-shadow: inset 8px 0 0 var(--metal);
  animation: slide-in 500ms var(--motion-enter) both;
}

.place:nth-child(1) {
  animation-delay: 250ms;
}

.place:nth-child(2) {
  animation-delay: 400ms;
}

.place:nth-child(3) {
  animation-delay: 550ms;
}

.silver {
  --metal: #b9c0cc;
  --metal-light: #f1f4f8;
  --metal-dark: #6f7785;
  --metal-tint: rgba(185, 192, 204, 0.3);
}

.bronze {
  --metal: #c27c45;
  --metal-light: #f0b98a;
  --metal-dark: #7d4720;
  --metal-tint: rgba(194, 124, 69, 0.34);
}

.medal {
  flex: none;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 62px;
  height: 62px;
  border-radius: 50%;
  background: radial-gradient(circle at 32% 28%, var(--metal-light), var(--metal) 55%, var(--metal-dark));
  box-shadow:
    inset 0 0 0 3px rgba(255, 255, 255, 0.35),
    0 4px 10px rgba(0, 0, 0, 0.35);
  color: rgba(0, 0, 0, 0.72);
  font-family: var(--overlay-font);
  font-size: 36px;
  font-weight: 800;
}

.who {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.name {
  overflow: hidden;
  font-family: var(--overlay-font);
  font-size: 40px;
  font-weight: 700;
  line-height: 1.05;
  text-overflow: ellipsis;
  text-transform: uppercase;
  white-space: nowrap;
}

.team {
  overflow: hidden;
  font-size: 22px;
  color: var(--overlay-silver);
  text-overflow: ellipsis;
  white-space: nowrap;
}

.score {
  flex: none;
  font-family: var(--overlay-font);
  font-size: 44px;
  font-weight: 800;
  font-variant-numeric: tabular-nums;
}

.score small {
  margin-left: 6px;
  font-size: 22px;
  font-weight: 600;
  color: var(--overlay-silver);
}

.instant .heading,
.instant .place {
  animation: none;
}

@keyframes slide-in {
  from {
    opacity: 0;
    transform: translateX(-60px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
</style>
