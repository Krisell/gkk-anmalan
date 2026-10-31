<template>
  <div class="hall">
    <Stage :height="canvasHeight">
      <div v-if="!competition" class="notice">
        Ange tävling i adressen, t.ex. <code>/live/overlay?view=hall&amp;competition=3001</code>.
      </div>

      <div v-else-if="!snapshot" class="notice">Ansluter till Powerlifting Live…</div>

      <template v-else>
        <!-- The whole lifting order of the group on the platform, in reading order over as many lines as it needs;
             the lifter on the platform first, marked as in the list. -->
        <section
          v-if="order.length"
          class="order"
          :style="{ height: `${orderHeight}px`, '--row': `${orderLayout.rowHeight}px` }"
        >
          <span class="order-label">Nästa lyftare</span>
          <ol class="order-list">
            <li
              v-for="(entry, index) in order"
              :key="`${entry.lifterId}-${index}`"
              class="order-entry"
              :class="{ current: index === 0 }"
            >
              <span class="order-number">{{ index + 1 }}</span>
              <span class="order-name">{{ entry.name ?? 'Okänd lyftare' }}</span>
              <span class="order-weight">{{ formatWeight(entry.weight) }}</span>
            </li>
          </ol>
        </section>

        <div class="board-area" :style="{ top: `${orderHeight}px`, height: `${boardHeight}px` }">
          <Scoreboard
            :scoreboard="snapshot.scoreboard"
            :discipline="snapshot.discipline"
            :group="snapshot.group"
            :current-lifter-id="snapshot.current?.lifterId ?? null"
            :height="boardHeight"
            fit
            :width="1800"
            strong-highlight
            show-body-weight
          />
        </div>

        <!-- The start order of the group after, so that its lifters know when to start warming up. -->
        <section v-if="next" class="next" :class="{ tight: nextLayout.tight }" :style="{ height: `${nextHeight}px` }">
          <header class="next-heading">
            <span class="next-label">Nästa grupp</span>
            <span class="next-title">{{
              [next.group, disciplineName(next.discipline)].filter(Boolean).join(' · ')
            }}</span>
            <span class="next-subtitle">Startordning{{ next.attempt > 1 ? ` – försök ${next.attempt}` : '' }}</span>
          </header>
          <ol
            class="next-list"
            :style="{
              '--rows': nextLayout.rows,
              '--columns': nextLayout.columns,
              '--row': `${nextLayout.rowHeight}px`,
            }"
          >
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

        <!-- Over the list, one at a time (the one leaving flips out before the next flips in):
             - the referee lights, very large in the middle, with the card of the lifter being judged: each lamp as its
               referee decides, then the decision,
             - the large lifter card when a new lifter is called to the platform, as on the broadcast. -->
        <Transition name="scrim">
          <div v-if="intro || lights" class="scrim" />
        </Transition>
        <Transition name="intro" mode="out-in" :duration="{ enter: 2800, leave: FLIP_OUT_MS }">
          <div v-if="lights" key="lights" class="verdict">
            <RefereePanel :lights="lights" large />
            <LifterIntro v-if="judged" :key="judged.lifterId" :attempt="judged" class="verdict-card" />
          </div>
          <LifterIntro v-else-if="intro" :key="intro.lifterId" :attempt="intro" class="hall-intro" />
        </Transition>

        <!-- Old data stays up (better than an empty screen in the warm-up hall), but says how old it is. -->
        <div v-if="staleSince" class="stale">Ej uppdaterat sedan {{ staleSince }}</div>
      </template>
    </Stage>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, ref, shallowRef, watch } from 'vue'
import Stage from './components/Stage.vue'
import LifterIntro from './components/LifterIntro.vue'
import RefereePanel from './components/RefereePanel.vue'
import Scoreboard from './components/Scoreboard.vue'
import { disciplineName, formatWeight } from './format.js'
import { createHttpSource } from './sources/httpSource.js'
import { createRefereeFeed, refereeFeedUrl } from './sources/refereeFeed.js'
import { useLiveFeed } from './useLiveFeed.js'
import { useNextGroup } from './useNextGroup.js'
import { initialOverlayState, reduceOverlayState } from './overlayState.js'

/**
 * The warm-up hall screen (/live/overlay?view=hall&competition=…): the results list of the group on the platform, as
 * on the broadcast, with the start order of the group after it below. Meant for a TV, e.g. shown from the slideshow's
 * live control (/admin/slideshow/live → Visa en webbsida). The referee lights are shown very large in the middle, as on
 * Powerlifting Live's own screens (?lights=0 turns them off), and a new lifter on the platform gets the broadcast's
 * large lifter card for a few seconds (?intro=0 turns it off, ?introSeconds=N sets how long).
 */

// Shown as out of date when the data is older than this (the feed is polled every second, the next group every 10 s).
const STALE_AFTER_MS = 30000

const params = new URLSearchParams(window.location.search)
const flag = (name) => !['0', 'false', 'no'].includes(params.get(name))
const competition = /^\d+$/.test(params.get('competition') ?? '') ? params.get('competition') : null

// Referee decisions come straight from Powerlifting Live, as on the broadcast (see refereeFeed.js), and reach the page
// through the feed's polling.
const referee =
  competition && flag('lights')
    ? createRefereeFeed(refereeFeedUrl(document.body.dataset.powerliftingLive), competition)
    : null
onBeforeUnmount(() => referee?.stop())

const feed = competition ? useLiveFeed(createHttpSource(competition, referee)) : null
const nextGroupFeed = competition ? useNextGroup(competition) : null

const snapshot = computed(() => feed?.snapshot.value ?? null)
const next = computed(() => nextGroupFeed?.nextGroup.value ?? null)

// The next group's lifters are listed in columns of up to 4 (5 for a large group), top to bottom, then left to right.
// A large group (up to 20 lifters, or more) gets more columns and tighter rows, so that everyone is always shown.
const nextLayout = computed(() => {
  const count = next.value?.lifters.length ?? 0
  const columns = count <= 16 ? 4 : Math.min(6, Math.ceil(count / 4))
  const rows = Math.ceil(count / columns)
  const tight = rows > 3
  return { columns, rows, tight, rowHeight: tight ? 30 : 36, chrome: tight ? 60 : 80 }
})
const nextHeight = computed(() =>
  next.value ? nextLayout.value.chrome + nextLayout.value.rows * nextLayout.value.rowHeight : 0,
)

// The lifting order at the top: everyone in it (a group can have 20 lifters), five per line, lines getting lower as
// they get more.
const ORDER_COLUMNS = 5
const order = computed(() => snapshot.value?.order ?? [])
const orderLayout = computed(() => {
  const rows = Math.ceil(order.value.length / ORDER_COLUMNS)
  return { rows, rowHeight: rows <= 1 ? 44 : rows === 2 ? 38 : 32 }
})
// Each line, the spacing between them (6 px), the padding (12 px above and below) and the border.
const orderHeight = computed(() =>
  order.value.length ? orderLayout.value.rows * (orderLayout.value.rowHeight + 6) - 6 + 24 + 4 : 0,
)

// The canvas is 1920 px wide and as high as the frame's shape allows (on the club's TV, the slideshow's bar takes some
// of the height), so that no space is lost to bands at the sides or top.
const viewport = ref({ width: window.innerWidth, height: window.innerHeight })
const onResize = () => (viewport.value = { width: window.innerWidth, height: window.innerHeight })
window.addEventListener('resize', onResize)
onBeforeUnmount(() => window.removeEventListener('resize', onResize))
const canvasHeight = computed(() =>
  Math.min(1600, Math.max(760, Math.round((1920 * viewport.value.height) / Math.max(viewport.value.width, 1)))),
)

const boardHeight = computed(() => canvasHeight.value - orderHeight.value - nextHeight.value)

// The lights: a decision stays up for DECISION_MS from when it arrives; before it, each lamp lights as its referee
// decides. Only decisions arriving while the page is open are shown (the referee feed has none on load).
const DECISION_MS = 10000
// How long the card (and the lights) take to flip out; the same flip as the card's entrance, reversed.
const FLIP_OUT_MS = 700
const shownDecision = ref(null)
let decisionTimer = null

// Who is being judged: as on the broadcast (see overlayState.js), a decision belongs to the lifter who was on the
// platform, even if the next lifter has already been called when it arrives. Updated before the decision is shown.
const overlayState = shallowRef(initialOverlayState())
watch(snapshot, (next) => {
  if (next) {
    overlayState.value = reduceOverlayState(overlayState.value, next, Date.now())
  }
})

watch(
  () => snapshot.value?.decision ?? null,
  (decision, previous) => {
    if (decision && decision.id !== previous?.id) {
      shownDecision.value = decision
      clearTimeout(decisionTimer)
      decisionTimer = setTimeout(() => (shownDecision.value = null), DECISION_MS)
    }
  },
)
onBeforeUnmount(() => clearTimeout(decisionTimer))

// Each lamp animates on by itself as its light changes (see RefereePanel.vue).
const lights = computed(() => shownDecision.value?.lights ?? snapshot.value?.signals ?? null)
// The card shown with the lights: the judged lifter once there is a decision, before it the lifter on the platform.
const judged = computed(() =>
  shownDecision.value ? (overlayState.value.result ?? null) : (snapshot.value?.current ?? null),
)

// The lifter card: shown when a new lifter is called to the platform, not for the lifter already there when the page
// opens, nor when the same lifter takes another attempt. The lights take precedence: a lifter called while they are
// up is introduced once they are gone.
//
// Powerlifting Live sometimes calls the next lifter a moment before the referee decision for the previous one reaches
// us. So a newly called lifter is introduced only after INTRO_DELAY_MS, and not while the previous lifter's attempt
// is still waiting for its result (overlayState's awaiting); lights arriving meanwhile take over instead, with the
// card of the lifter being judged, and the new lifter is introduced after them.
const INTRO_MS = (Number(params.get('introSeconds')) || 6) * 1000
const INTRO_DELAY_MS = 3000
const AWAITING_RECHECK_MS = 500
const showIntros = flag('intro')
const intro = shallowRef(null)
let introducedLifterId = null
let introTimer = null
let pendingLifterId = null
let pendingTimer = null

watch([() => snapshot.value?.current ?? null, lights], ([current, lit]) => {
  if (!snapshot.value) {
    return
  }
  if (introducedLifterId == null) {
    introducedLifterId = current?.lifterId ?? ''
    return
  }
  if (lit) {
    cancelPendingIntro()
    hideIntro()
    return
  }
  if (!current?.lifterId) {
    cancelPendingIntro()
    return
  }
  if (intro.value?.lifterId === current.lifterId) {
    // E.g. a changed weight while the card is up.
    intro.value = current
    return
  }
  if (showIntros && current.lifterId !== introducedLifterId) {
    if (pendingLifterId !== current.lifterId) {
      cancelPendingIntro()
      pendingLifterId = current.lifterId
      pendingTimer = setTimeout(introducePending, INTRO_DELAY_MS)
    }
  } else {
    cancelPendingIntro()
  }
})

function introducePending() {
  const current = snapshot.value?.current
  if (lights.value || !current?.lifterId || current.lifterId !== pendingLifterId) {
    cancelPendingIntro()
    return
  }
  // The previous lifter's result hasn't come yet: wait for it (the reducer gives up waiting after a few seconds).
  const awaiting = overlayState.value.awaiting
  if (awaiting && Date.now() < awaiting.until) {
    pendingTimer = setTimeout(introducePending, AWAITING_RECHECK_MS)
    return
  }
  pendingLifterId = null
  introducedLifterId = current.lifterId
  intro.value = current
  clearTimeout(introTimer)
  introTimer = setTimeout(hideIntro, INTRO_MS)
}

function cancelPendingIntro() {
  clearTimeout(pendingTimer)
  pendingLifterId = null
}

function hideIntro() {
  clearTimeout(introTimer)
  intro.value = null
}
onBeforeUnmount(() => {
  clearTimeout(introTimer)
  clearTimeout(pendingTimer)
})

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

.order {
  position: absolute;
  top: 0;
  left: 0;
  width: 1920px;
  box-sizing: border-box;
  display: flex;
  align-items: flex-start;
  gap: 32px;
  padding: 12px 60px;
  background: var(--overlay-panel-deep);
  border-bottom: 4px solid var(--overlay-silver);
}

.order-label {
  flex: none;
  /* Centered on the first line. */
  margin-top: calc((var(--row) - 32px) / 2);
  padding: 2px 14px;
  border-radius: 4px;
  background: #ffffff;
  color: var(--overlay-navy);
  font-family: var(--overlay-font);
  font-size: 20px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.order-list {
  flex: 1;
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  grid-auto-rows: var(--row);
  gap: 6px 16px;
  margin: 0;
  padding: 0;
  list-style: none;
}

.order-entry {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
  padding: 0 14px;
  border-radius: 6px;
  font-family: var(--overlay-font);
}

/* The lifter on the platform: the same amber as their row in the list. */
.order-entry.current {
  background: rgba(246, 201, 69, 0.22);
  box-shadow: inset 0 0 0 3px #f6c945;
  color: #f6c945;
}

.order-number {
  flex: none;
  font-size: calc(var(--row) * 0.46);
  font-weight: 600;
  color: var(--overlay-silver);
}

.order-entry.current .order-number {
  color: inherit;
}

.order-name {
  flex: 1;
  min-width: 0;
  overflow: hidden;
  font-size: calc(var(--row) * 0.54);
  font-weight: 700;
  text-overflow: ellipsis;
  text-transform: uppercase;
  white-space: nowrap;
}

.order-weight {
  flex: none;
  font-size: calc(var(--row) * 0.58);
  font-weight: 700;
  font-variant-numeric: tabular-nums;
}

.board-area {
  position: absolute;
  left: 0;
  width: 1920px;
}

.next {
  position: absolute;
  left: 0;
  bottom: 0;
  width: 1920px;
  box-sizing: border-box;
  padding: 12px 60px 16px;
  background: var(--overlay-panel-deep);
  border-top: 4px solid var(--overlay-silver);
}

.next-heading {
  display: flex;
  align-items: baseline;
  gap: 20px;
  height: 48px;
  font-family: var(--overlay-font);
  text-transform: uppercase;
}

/* A large next group: less space around its heading, for more rows. */
.next.tight {
  padding: 6px 60px 10px;
}

.next.tight .next-heading {
  height: 40px;
}

.next-label {
  padding: 2px 14px;
  border-radius: 4px;
  background: #ffffff;
  color: var(--overlay-navy);
  font-size: 20px;
  font-weight: 700;
  letter-spacing: 0.12em;
}

.next-title {
  font-size: 30px;
  font-weight: 700;
}

.next-subtitle {
  font-size: 20px;
  font-weight: 600;
  letter-spacing: 0.14em;
  color: var(--overlay-silver);
}

.next-list {
  display: grid;
  grid-auto-flow: column;
  grid-template-rows: repeat(var(--rows), var(--row));
  grid-template-columns: repeat(var(--columns), minmax(0, 1fr));
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
  line-height: calc(var(--row) - 1px);
  font-size: calc(var(--row) * 0.55);
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

.scrim {
  position: absolute;
  inset: 0;
  z-index: 1;
  background: rgba(15, 22, 44, 0.55);
}

.scrim-enter-active,
.scrim-leave-active {
  transition: opacity var(--motion-base) var(--motion-move);
}

/* Fading only once the last card has flipped out. */
.scrim-leave-active {
  transition-delay: 500ms;
}

.scrim-enter-from,
.scrim-leave-to {
  opacity: 0;
}

.hall-intro {
  z-index: 2;
}

.verdict {
  position: absolute;
  inset: 0;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 56px;
}

/* The card under the lights, in the flow instead of centered on its own. */
.verdict .verdict-card {
  position: relative;
  inset: auto;
}

/*
  The card's entrance (from LifterIntro.vue, also for the card under the lights) is a flip; leaving, the card and the
  lights flip out the same way, reversed, instead of the broadcast's flight down into the lower third.
*/
.intro-leave-active :deep(.card),
.intro-leave-active :deep(.referees) {
  animation: flip-out 700ms var(--motion-move) both;
}

@keyframes flip-out {
  from {
    opacity: 1;
    transform: perspective(2200px) rotateY(0) scale(1);
  }
  60% {
    opacity: 1;
  }
  to {
    opacity: 0;
    transform: perspective(2200px) rotateY(100deg) scale(0.85);
  }
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
