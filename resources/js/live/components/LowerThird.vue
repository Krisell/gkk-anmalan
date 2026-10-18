<template>
  <div class="lower-third">
    <div class="stack">
      <div class="tabs">
        <Transition name="tab" mode="out-in">
          <div v-if="upcoming" :key="upcoming.id" class="tab next">
            <span class="eyebrow">Därefter</span>
            <span class="next-name">{{ upcoming.name ?? 'Okänd lyftare' }}</span>
            <span v-if="upcoming.weight" class="next-weight">{{ formatWeight(upcoming.weight) }} kg</span>
          </div>
        </Transition>

        <Transition name="tab">
          <div v-if="attempt.outcome" :key="`${attempt.id}:${attempt.shownAt}`" class="tab result">
            <RefereeLights v-if="attempt.decisions" class="lights" :lights="attempt.decisions" />
            <span class="verdict" :class="attempt.outcome">
              {{ attempt.outcome === 'good' ? 'Godkänt' : 'Underkänt' }}
            </span>
            <span v-if="placeChange" class="place-change">{{ placeChange }}</span>
          </div>
          <div v-else-if="clockLeft != null" key="clock" class="tab clock" :class="{ urgent: clockLeft <= 10 }">
            <span class="eyebrow">Tid</span>
            <span class="clock-time">{{ clockText }}</span>
          </div>
        </Transition>
      </div>

      <div class="panel">
        <div v-if="crest" class="brand">
          <img :src="crest" :alt="attempt.team ?? ''" class="logo" />
        </div>

        <div class="identity">
          <Transition name="swap" mode="out-in">
            <div :key="attempt.lifterId ?? attempt.name">
              <div class="name">{{ attempt.name ?? 'Okänd lyftare' }}</div>
              <div class="meta">
                <span v-for="(part, index) in meta" :key="index" class="meta-part">{{ part }}</span>
              </div>
            </div>
          </Transition>
        </div>

        <div v-if="attempt.discipline" class="attempts">
          <div class="eyebrow">{{ discipline }}</div>
          <div class="slots">
            <div v-for="(slot, index) in slots" :key="index" class="slot" :class="slot.state">
              {{ slot.label }}
            </div>
          </div>
        </div>

        <div class="lift" :class="attempt.outcome">
          <div class="eyebrow">{{ attempt.attempt ? `Försök ${attempt.attempt}` : 'Begärd vikt' }}</div>
          <div class="weight">
            <Transition name="swap" mode="out-in">
              <span :key="attempt.weight ?? 'none'" class="weight-value">{{ formatWeight(attempt.weight) }}</span>
            </Transition>
            <span class="unit">kg</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import RefereeLights from './RefereeLights.vue'
import { clubCrest } from '../clubs.js'
import { disciplineName, formatWeight, formatWeightClass } from '../format.js'

const props = defineProps({
  // A normalized Attempt; while a result is shown it also has outcome, decisions and shownAt.
  attempt: { type: Object, required: true },
  // The lifter after the one shown, if known.
  upcoming: { type: Object, default: null },
  // When the attempt clock runs out (ms timestamp), or null when it isn't running.
  clockEndsAt: { type: Number, default: null },
})

// Attempt clock: counted down locally, like Powerlifting Live's own screens do.
const now = ref(Date.now())
let ticker = null

watch(
  () => props.clockEndsAt,
  (endsAt) => {
    clearInterval(ticker)
    now.value = Date.now()
    if (endsAt) {
      ticker = setInterval(() => (now.value = Date.now()), 200)
    }
  },
  { immediate: true },
)

onBeforeUnmount(() => clearInterval(ticker))

const clockLeft = computed(() => {
  if (!props.clockEndsAt) {
    return null
  }
  const left = Math.ceil((props.clockEndsAt - now.value) / 1000)
  return left > 0 ? left : null
})

const clockText = computed(() => {
  const left = clockLeft.value ?? 0
  return `${Math.floor(left / 60)}:${String(left % 60).padStart(2, '0')}`
})

// "Tar ledningen" or "↑ 2 placeringar" after a good lift that improved the lifter's place.
const placeChange = computed(() => {
  const change = props.attempt.placeChange
  if (!change?.to || !change.from || change.to >= change.from) {
    return null
  }
  if (change.to === 1) {
    return 'Tar ledningen'
  }
  const places = change.from - change.to
  return `↑ ${places} ${places === 1 ? 'placering' : 'placeringar'}`
})

// The lifter's club crest, if we have one; no crest rather than ours (see clubs.js).
const crest = computed(() => clubCrest(props.attempt.team))

const discipline = computed(() => disciplineName(props.attempt.discipline))

const meta = computed(() => [props.attempt.team, formatWeightClass(props.attempt.weightClass)].filter(Boolean))

const slots = computed(() =>
  props.attempt.attempts.map((slot, index) => {
    const isThis = index + 1 === props.attempt.attempt
    const outcome = isThis ? (props.attempt.outcome ?? slot.outcome) : slot.outcome
    const weight = isThis ? (props.attempt.weight ?? slot.weight) : slot.weight

    return {
      label: weight == null ? '' : formatWeight(weight),
      state: outcome ?? (isThis ? 'active' : weight == null ? 'empty' : 'pending'),
    }
  }),
)
</script>

<style scoped>
/* Centered at the bottom of the 1920×1080 stage, clear of the lifting platform above it. */
.lower-third {
  position: absolute;
  left: 0;
  right: 0;
  bottom: var(--safe-y);
  display: flex;
  justify-content: center;
}

.stack {
  display: flex;
  flex-direction: column;
  filter: drop-shadow(0 12px 28px rgba(0, 0, 0, 0.35));
}

/* Hidden while the lifter card animates out; then this panel flips in as the card's back side. */
.lower-third.is-hidden .stack {
  opacity: 0;
}

.eyebrow {
  font-family: var(--overlay-font);
  font-size: 17px;
  font-weight: 600;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: var(--overlay-silver);
}

/* Tabs sit on the top edge of the panel: next lifter to the left, result to the right. */
.tabs {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 24px;
  min-height: 44px;
  padding: 0 12px;
}

.tab {
  display: flex;
  align-items: center;
  height: 44px;
  border-radius: 8px 8px 0 0;
  background: var(--overlay-panel-deep);
  overflow: hidden;
}

.next {
  gap: 14px;
  padding: 0 20px;
}

.next-name {
  font-family: var(--overlay-font);
  font-size: 24px;
  font-weight: 600;
  letter-spacing: 0.02em;
  text-transform: uppercase;
}

.next-weight {
  font-family: var(--overlay-font);
  font-size: 24px;
  font-weight: 600;
  color: var(--overlay-silver);
}

.result {
  margin-left: auto;
}

.lights {
  padding: 0 18px;
}

.verdict {
  display: flex;
  align-items: center;
  height: 100%;
  padding: 0 22px;
  font-family: var(--overlay-font);
  font-size: 24px;
  font-weight: 700;
  letter-spacing: 0.16em;
  text-transform: uppercase;
}

.verdict.good {
  background: var(--overlay-good);
}

.verdict.failed {
  background: var(--overlay-failed);
}

.place-change {
  display: flex;
  align-items: center;
  height: 100%;
  padding: 0 20px;
  font-family: var(--overlay-font);
  font-size: 24px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  white-space: nowrap;
  animation: place-change-in var(--motion-base) var(--motion-enter) 600ms both;
}

.clock {
  gap: 14px;
  margin-left: auto;
  padding: 0 22px;
  transition: background-color var(--motion-fast) var(--motion-move);
}

.clock-time {
  min-width: 64px;
  font-family: var(--overlay-font);
  font-size: 30px;
  font-weight: 700;
  font-variant-numeric: tabular-nums;
  text-align: right;
}

.clock.urgent {
  background: var(--overlay-failed);
}

.clock.urgent .eyebrow {
  color: rgba(255, 255, 255, 0.85);
}

@keyframes place-change-in {
  from {
    opacity: 0;
    transform: translateX(-12px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.panel {
  position: relative;
  display: flex;
  align-items: stretch;
  height: 124px;
  border-radius: 10px;
  overflow: hidden;
  background: var(--overlay-panel);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
}

.brand {
  display: grid;
  place-items: center;
  width: 132px;
  background: var(--overlay-panel-deep);
}

/* The logo is navy on transparent, so it sits on a white disc to stay visible on the navy panel. */
.logo {
  display: block;
  width: 96px;
  height: 96px;
  padding: 4px;
  border-radius: 50%;
  background: #ffffff;
}

.identity {
  display: flex;
  flex-direction: column;
  justify-content: center;
  min-width: 400px;
  max-width: 600px;
  padding: 0 36px 4px 30px;
}

/* Without a club crest the name comes first in the panel. */
.identity:first-child {
  padding-left: 40px;
}

.name {
  font-family: var(--overlay-font);
  font-size: 48px;
  font-weight: 700;
  line-height: 1;
  letter-spacing: 0.01em;
  text-transform: uppercase;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.meta {
  margin-top: 8px;
  font-size: 21px;
  font-weight: 500;
  color: var(--overlay-silver);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.meta-part + .meta-part::before {
  content: '·';
  margin: 0 10px;
  opacity: 0.7;
}

.attempts {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 10px;
  padding: 0 30px 4px;
  border-left: 1px solid var(--overlay-divider);
}

.slots {
  display: flex;
  gap: 6px;
}

.slot {
  width: 80px;
  height: 36px;
  border-radius: 4px;
  font-family: var(--overlay-font);
  font-size: 23px;
  font-weight: 600;
  line-height: 36px;
  text-align: center;
  background: rgba(255, 255, 255, 0.1);
  transition:
    background-color var(--motion-fast) var(--motion-move),
    box-shadow var(--motion-fast) var(--motion-move);
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

.lift {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 250px;
  padding-bottom: 4px;
  background: var(--overlay-weight-bg);
  color: var(--overlay-weight-text);
  transition:
    background-color var(--motion-fast) var(--motion-move),
    color var(--motion-fast) var(--motion-move);
}

.lift .eyebrow {
  color: var(--overlay-navy-light);
}

.lift.good,
.lift.failed {
  color: #ffffff;
}

.lift.good {
  background: var(--overlay-good);
}

.lift.failed {
  background: var(--overlay-failed);
}

.lift.good .eyebrow,
.lift.failed .eyebrow {
  color: rgba(255, 255, 255, 0.8);
}

.weight {
  display: flex;
  align-items: baseline;
  gap: 6px;
  font-family: var(--overlay-font);
  font-weight: 700;
  line-height: 1;
}

.weight-value {
  font-size: 76px;
  font-variant-numeric: tabular-nums;
}

.unit {
  font-size: 30px;
}

.swap-enter-active,
.swap-leave-active {
  transition:
    opacity var(--motion-fast) var(--motion-move),
    transform var(--motion-fast) var(--motion-move);
}

.swap-enter-from {
  opacity: 0;
  transform: translateY(8px);
}

.swap-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

.tab-enter-active,
.tab-leave-active {
  transition:
    opacity var(--motion-fast) var(--motion-move),
    transform var(--motion-fast) var(--motion-move);
}

.tab-enter-from,
.tab-leave-to {
  opacity: 0;
  transform: translateY(44px);
}
</style>
