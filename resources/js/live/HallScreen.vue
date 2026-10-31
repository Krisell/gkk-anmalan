<template>
  <div class="hall">
    <Stage>
      <div v-if="!competition" class="notice">
        Ange tävling i adressen, t.ex. <code>/live/overlay?view=hall&amp;competition=3001</code>.
      </div>

      <div v-else-if="!snapshot" class="notice">Ansluter till Powerlifting Live…</div>

      <template v-else>
        <div class="board-area" :style="{ height: `${boardHeight}px` }">
          <Scoreboard
            :scoreboard="snapshot.scoreboard"
            :discipline="snapshot.discipline"
            :group="snapshot.group"
            :current-lifter-id="snapshot.current?.lifterId ?? null"
            :height="boardHeight"
            :list-height="boardHeight - 260"
            :width="1800"
          />
        </div>

        <!-- The start order of the group after, so that its lifters know when to start warming up. -->
        <section v-if="next" class="next" :style="{ height: `${nextHeight}px` }">
          <header class="next-heading">
            <span class="next-label">Nästa grupp</span>
            <span class="next-title">{{
              [next.group, disciplineName(next.discipline)].filter(Boolean).join(' · ')
            }}</span>
            <span class="next-subtitle">Startordning{{ next.attempt > 1 ? ` – försök ${next.attempt}` : '' }}</span>
          </header>
          <ol class="next-list" :style="{ '--rows': nextRows }">
            <li v-for="(lifter, index) in next.lifters" :key="lifter.id ?? index" class="next-lifter">
              <span class="next-order">{{ index + 1 }}</span>
              <span class="next-who">
                <span class="next-name">{{ lifter.name ?? 'Okänd lyftare' }}</span>
                <span v-if="lifter.team" class="next-team">{{ lifter.team }}</span>
              </span>
              <span class="next-weight">{{ formatWeight(lifter.weight) }}</span>
            </li>
          </ol>
        </section>

        <!-- Old data stays up (better than an empty screen in the warm-up hall), but says how old it is. -->
        <div v-if="staleSince" class="stale">Ej uppdaterat sedan {{ staleSince }}</div>
      </template>
    </Stage>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, ref } from 'vue'
import Stage from './components/Stage.vue'
import Scoreboard from './components/Scoreboard.vue'
import { disciplineName, formatWeight } from './format.js'
import { createHttpSource } from './sources/httpSource.js'
import { useLiveFeed } from './useLiveFeed.js'
import { useNextGroup } from './useNextGroup.js'

/**
 * The warm-up hall screen (/live/overlay?view=hall&competition=…): the results list of the group on the platform, as
 * on the broadcast, with the start order of the group after it below. Meant for a TV, e.g. shown from the slideshow's
 * live control (/admin/slideshow/live → Visa en webbsida).
 */

// Shown as out of date when the data is older than this (the feed is polled every 2 s, the next group every 10 s).
const STALE_AFTER_MS = 30000

const params = new URLSearchParams(window.location.search)
const competition = /^\d+$/.test(params.get('competition') ?? '') ? params.get('competition') : null

const feed = competition ? useLiveFeed(createHttpSource(competition), { interval: 2000 }) : null
const nextGroupFeed = competition ? useNextGroup(competition) : null

const snapshot = computed(() => feed?.snapshot.value ?? null)
const next = computed(() => nextGroupFeed?.nextGroup.value ?? null)

// The next group's lifters are listed in columns of up to 4 (5 for a large group), top to bottom, then left to right.
const COLUMNS = 4
const ROW_HEIGHT = 44
const nextRows = computed(() => (next.value ? Math.min(5, Math.ceil(next.value.lifters.length / COLUMNS)) : 0))
const nextHeight = computed(() => (next.value ? 96 + nextRows.value * ROW_HEIGHT : 0))
const boardHeight = computed(() => 1080 - nextHeight.value)

const now = ref(Date.now())
const ticker = setInterval(() => (now.value = Date.now()), 5000)
onBeforeUnmount(() => clearInterval(ticker))

const staleSince = computed(() => {
  const times = [feed?.lastSuccessAt.value, nextGroupFeed?.lastSuccessAt.value].filter((at) => at != null)
  const oldest = times.length ? Math.min(...times) : null
  return oldest != null && now.value - oldest > STALE_AFTER_MS
    ? new Date(oldest).toLocaleTimeString('sv-SE', { hour: '2-digit', minute: '2-digit' })
    : null
})
</script>

<style scoped>
/* Unlike the broadcast graphics, this page is opaque: it is the whole picture on a TV. */
.hall {
  position: fixed;
  inset: 0;
  background: var(--overlay-navy-deep);
}

.notice {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 36px;
  color: var(--overlay-silver);
}

code {
  margin-left: 0.4em;
  color: #ffffff;
}

.board-area {
  position: absolute;
  top: 0;
  left: 0;
  width: 1920px;
}

.next {
  position: absolute;
  left: 0;
  bottom: 0;
  width: 1920px;
  box-sizing: border-box;
  padding: 16px 60px 24px;
  background: var(--overlay-panel-deep);
  border-top: 4px solid var(--overlay-silver);
}

.next-heading {
  display: flex;
  align-items: baseline;
  gap: 20px;
  height: 52px;
  font-family: var(--overlay-font);
  text-transform: uppercase;
}

.next-label {
  padding: 2px 14px;
  border-radius: 4px;
  background: #ffffff;
  color: var(--overlay-navy);
  font-size: 24px;
  font-weight: 700;
  letter-spacing: 0.12em;
}

.next-title {
  font-size: 34px;
  font-weight: 700;
}

.next-subtitle {
  font-size: 24px;
  font-weight: 600;
  letter-spacing: 0.14em;
  color: var(--overlay-silver);
}

.next-list {
  display: grid;
  grid-auto-flow: column;
  grid-template-rows: repeat(var(--rows), 44px);
  grid-template-columns: repeat(4, minmax(0, 1fr));
  column-gap: 40px;
  margin: 0;
  padding: 0;
  list-style: none;
}

.next-lifter {
  display: flex;
  align-items: baseline;
  gap: 12px;
  min-width: 0;
  border-bottom: 1px solid rgba(166, 172, 185, 0.14);
  line-height: 43px;
  font-size: 24px;
}

.next-order {
  flex: none;
  width: 28px;
  text-align: right;
  font-family: var(--overlay-font);
  font-weight: 600;
  color: var(--overlay-silver);
}

.next-who {
  flex: 1;
  min-width: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.next-name {
  font-family: var(--overlay-font);
  font-size: 1.15em;
  font-weight: 700;
  text-transform: uppercase;
}

.next-team {
  margin-left: 10px;
  font-size: 0.8em;
  color: var(--overlay-silver);
}

.next-weight {
  flex: none;
  font-family: var(--overlay-font);
  font-size: 1.2em;
  font-weight: 700;
  font-variant-numeric: tabular-nums;
}

.stale {
  position: absolute;
  top: 16px;
  right: 24px;
  z-index: 1;
  padding: 8px 18px;
  border-radius: 6px;
  background: var(--overlay-failed);
  font-size: 24px;
  font-weight: 600;
}
</style>
