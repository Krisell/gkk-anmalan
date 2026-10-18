<template>
  <div class="intro">
    <div class="card">
      <div class="main">
        <div v-if="crest" class="brand">
          <img :src="crest" :alt="attempt.team ?? ''" class="logo" />
        </div>

        <div class="who">
          <div class="eyebrow">På flaket</div>
          <div class="name" :class="{ long: (attempt.name ?? '').length > 18 }">
            {{ attempt.name ?? 'Okänd lyftare' }}
          </div>
          <div v-if="meta.length" class="meta">
            <span v-for="(part, index) in meta" :key="index" class="meta-part">{{ part }}</span>
          </div>
        </div>

        <div class="lift">
          <div class="eyebrow">
            <span v-if="discipline">{{ discipline }}</span>
            <span v-if="attempt.attempt">Försök {{ attempt.attempt }}</span>
          </div>
          <div class="weight">
            <span class="weight-value" :class="weightSize">{{ weight }}</span>
            <span class="unit">kg</span>
          </div>
        </div>
      </div>

      <div class="stats" :class="{ dense: rows.length >= 3 }">
        <div v-for="row in rows" :key="row.discipline" class="stat discipline">
          <div class="eyebrow">{{ row.name }}</div>
          <div class="slots">
            <div v-for="(slot, index) in row.slots" :key="index" class="slot" :class="slot.state">
              {{ slot.label }}
            </div>
          </div>
        </div>

        <div class="spacer" />

        <div v-if="attempt.total" class="stat">
          <div class="eyebrow">Total</div>
          <div class="value">{{ formatWeight(attempt.total) }} <small>kg</small></div>
        </div>
        <div v-if="attempt.place" class="stat">
          <div class="eyebrow">Placering</div>
          <div class="value">{{ attempt.place }}</div>
        </div>
        <div v-if="attempt.predictedPlace" class="stat">
          <div class="eyebrow">Prognos</div>
          <div class="value">{{ attempt.predictedPlace }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { clubCrest } from '../clubs.js'
import { DISCIPLINES } from '../normalize.js'
import { disciplineName, formatWeight, formatWeightClass } from '../format.js'

const props = defineProps({
  // The normalized Attempt of the lifter just called to the platform.
  attempt: { type: Object, required: true },
})

// The lifter's club crest, if we have one; no crest rather than ours (see clubs.js).
const crest = computed(() => clubCrest(props.attempt.team))

const discipline = computed(() => disciplineName(props.attempt.discipline))

// The weight is the largest text on the card; longer weights ("97,5", "202,5") get a smaller size to fit the box.
const weight = computed(() => formatWeight(props.attempt.weight))
const weightSize = computed(() => (weight.value.length >= 5 ? 'long' : weight.value.length === 4 ? 'medium' : ''))

const meta = computed(() => [props.attempt.team, formatWeightClass(props.attempt.weightClass)].filter(Boolean))

// The disciplines the lifter has attempts in so far (plus the current one), with this attempt highlighted.
const rows = computed(() =>
  DISCIPLINES.filter(
    (d) => d === props.attempt.discipline || props.attempt.lifts?.[d]?.some((slot) => slot.weight != null),
  ).map((d) => ({
    discipline: d,
    name: disciplineName(d),
    slots: props.attempt.lifts[d].map((slot, index) => {
      const isThis = d === props.attempt.discipline && index + 1 === props.attempt.attempt
      const weight = isThis ? (props.attempt.weight ?? slot.weight) : slot.weight
      return {
        label: weight == null ? '' : formatWeight(weight),
        state: slot.outcome ?? (isThis ? 'active' : weight == null ? 'empty' : 'pending'),
      }
    }),
  })),
)
</script>

<style scoped>
/*
  A large card for the lifter just called to the platform. It flips in at the center of the screen and,
  when it leaves, it travels down, shrinks and flips over into the lower third (see endIntro in LiveOverlay.vue).
*/
.intro {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.card {
  position: relative;
  width: 1320px;
  border-radius: 14px;
  overflow: hidden;
  background: var(--overlay-panel);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.14),
    0 24px 60px rgba(0, 0, 0, 0.45);
  backface-visibility: hidden;
}

/* A soft sheen that sweeps across the card once it has opened. */
.card::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(105deg, transparent 35%, rgba(255, 255, 255, 0.16) 50%, transparent 65%);
  transform: translateX(-100%);
  pointer-events: none;
}

.eyebrow {
  display: flex;
  gap: 14px;
  font-family: var(--overlay-font);
  font-size: 22px;
  font-weight: 600;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: var(--overlay-silver);
}

.main {
  display: flex;
  align-items: stretch;
  height: 250px;
}

.brand {
  display: grid;
  place-items: center;
  width: 210px;
  background: var(--overlay-panel-deep);
}

.logo {
  display: block;
  width: 160px;
  height: 160px;
  padding: 6px;
  border-radius: 50%;
  background: #ffffff;
}

.who {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  min-width: 0;
  padding: 0 44px;
}

/* Without a club crest the name comes first on the card. */
.who:first-child {
  padding-left: 60px;
}

.name {
  margin-top: 6px;
  font-family: var(--overlay-font);
  font-size: 92px;
  font-weight: 700;
  line-height: 0.95;
  text-transform: uppercase;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.name.long {
  font-size: 72px;
}

.meta {
  margin-top: 12px;
  font-size: 30px;
  font-weight: 500;
  color: var(--overlay-silver);
}

.meta-part + .meta-part::before {
  content: '·';
  margin: 0 14px;
  opacity: 0.7;
}

.lift {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 320px;
  background: var(--overlay-weight-bg);
  color: var(--overlay-weight-text);
}

.lift .eyebrow {
  color: var(--overlay-navy-light);
}

.weight {
  display: flex;
  align-items: baseline;
  gap: 8px;
  font-family: var(--overlay-font);
  font-weight: 700;
  line-height: 1;
}

.weight-value {
  font-size: 140px;
  font-variant-numeric: tabular-nums;
}

.weight-value.medium {
  font-size: 124px;
}

.weight-value.long {
  font-size: 106px;
}

.unit {
  font-size: 48px;
}

.stats {
  display: flex;
  align-items: center;
  gap: 48px;
  height: 116px;
  padding: 0 44px;
  background: var(--overlay-panel-deep);
  border-top: 1px solid var(--overlay-divider);
}

.stat {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.spacer {
  flex: 1;
}

.value {
  font-family: var(--overlay-font);
  font-size: 44px;
  font-weight: 700;
  line-height: 1;
  white-space: nowrap;
}

/* With all three disciplines (9 attempts) the band is tighter, so total and placings still fit. */
.stats.dense {
  gap: 28px;
}

.stats.dense .eyebrow {
  font-size: 18px;
  letter-spacing: 0.14em;
}

.stats.dense .slot {
  width: 72px;
  font-size: 24px;
}

.stats.dense .value {
  font-size: 40px;
}

.value small {
  font-size: 24px;
  color: var(--overlay-silver);
}

.slots {
  display: flex;
  gap: 6px;
}

.slot {
  width: 92px;
  height: 44px;
  border-radius: 5px;
  font-family: var(--overlay-font);
  font-size: 27px;
  font-weight: 600;
  line-height: 44px;
  text-align: center;
  background: rgba(255, 255, 255, 0.1);
}

.slot.empty {
  background: rgba(255, 255, 255, 0.05);
}

.slot.active {
  box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.9);
}

.slot.good {
  background: var(--overlay-good);
}

.slot.failed {
  background: var(--overlay-failed);
  text-decoration: line-through;
  text-decoration-thickness: 2px;
}

/* Enter: the card flips around into view, then its contents slide into place. */
.intro-enter-active .card {
  animation: card-flip-in 1400ms var(--motion-enter) both;
}

.intro-enter-active .card::after {
  animation: sheen 1500ms ease-in-out 1300ms both;
}

.intro-enter-active .logo {
  animation: logo-in 900ms var(--motion-enter) 550ms both;
}

.intro-enter-active .who > * {
  animation: slide-right 800ms var(--motion-enter) both;
}

.intro-enter-active .who > :nth-child(1) {
  animation-delay: 650ms;
}

.intro-enter-active .who > :nth-child(2) {
  animation-delay: 780ms;
}

.intro-enter-active .who > :nth-child(3) {
  animation-delay: 910ms;
}

.intro-enter-active .lift > * {
  animation: slide-left 800ms var(--motion-enter) 800ms both;
}

.intro-enter-active .stats > * {
  animation: rise 700ms var(--motion-enter) 1050ms both;
}

@keyframes card-flip-in {
  from {
    opacity: 0;
    transform: perspective(2200px) rotateY(-100deg) scale(0.85);
  }
  40% {
    opacity: 1;
  }
  to {
    opacity: 1;
    transform: perspective(2200px) rotateY(0) scale(1);
  }
}

@keyframes sheen {
  from {
    transform: translateX(-100%);
  }
  to {
    transform: translateX(100%);
  }
}

@keyframes logo-in {
  from {
    opacity: 0;
    transform: scale(0.6) rotate(-25deg);
  }
  to {
    opacity: 1;
    transform: scale(1) rotate(0);
  }
}

@keyframes slide-right {
  from {
    opacity: 0;
    transform: translateX(-40px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slide-left {
  from {
    opacity: 0;
    transform: translateX(40px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes rise {
  from {
    opacity: 0;
    transform: translateY(16px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
