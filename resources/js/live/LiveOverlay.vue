<template>
  <Stage>
    <div v-if="!source" class="config-error">
      Ange tävling i adressen, t.ex. <code>/live/overlay?competition=3001</code>, eller testa med
      <code>/live/overlay?demo=true</code>.
    </div>

    <template v-else>
      <iframe
        v-if="videoId"
        :src="`https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&playsinline=1`"
        class="backdrop"
        title="Videoström"
        allow="autoplay; encrypted-media"
      />
      <img v-else-if="showBackdrop" :src="demoBackdrop" alt="" class="backdrop" />

      <Transition name="intro" :duration="{ enter: 1900, leave: 0 }">
        <LifterIntro v-if="intro && displayed" :key="intro.id" :attempt="intro" />
      </Transition>

      <Transition :name="exiting ? '' : 'panel'">
        <LowerThird
          v-if="displayed && (!intro || exiting)"
          :class="{ 'is-hidden': exiting === 'card' }"
          :attempt="displayed"
          :upcoming="upcoming"
        />
      </Transition>

      <DemoControls
        v-if="demoSource && showControls"
        :steps="demoSource.steps"
        :index="demoIndex"
        :playing="playing"
        @go="goToStep"
        @toggle="playing = !playing"
      />

      <DebugStatus
        v-if="debug"
        :status="status"
        :source="demoSource ? 'demo' : `tävling ${competition}${delayMs ? ` (+${delayMs / 1000} s)` : ''}`"
        :age="age"
        :failures="feed.failures.value"
        :error="feed.error.value"
        :current-id="feed.snapshot.value?.current?.id ?? null"
        :result-id="state.result?.id ?? null"
      />
    </template>
  </Stage>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, shallowRef, watch } from 'vue'
import Stage from './components/Stage.vue'
import LowerThird from './components/LowerThird.vue'
import LifterIntro from './components/LifterIntro.vue'
import DebugStatus from './components/DebugStatus.vue'
import DemoControls from './components/DemoControls.vue'
import { useLiveFeed } from './useLiveFeed.js'
import { createHttpSource } from './sources/httpSource.js'
import { createDemoSource } from './sources/demoSource.js'
import { createDelayedSource, youTubeId } from './sources/delayedSource.js'
import { initialOverlayState, reduceOverlayState, RESULT_DURATION_MS } from './overlayState.js'
import demoBackdrop from './assets/demo-backdrop.jpg'

// Data older than this is flagged in debug mode, and hidden entirely after OFFLINE_AFTER_MS so the
// stream never shows a lifter that may have left the platform long ago.
const STALE_AFTER_MS = 4000
const OFFLINE_AFTER_MS = 20000
const DEMO_STEP_MS = 6000

const params = new URLSearchParams(window.location.search)
const flag = (name, fallback = false) =>
  params.has(name) ? !['0', 'false', 'no'].includes(params.get(name)) : fallback

const competition = params.get('competition')
const demoSource = flag('demo') ? createDemoSource() : null
// Testing against an external stream: ?video=<YouTube link> plays it behind the overlay, and ?delay=<seconds>
// holds the HUD back by the stream's latency so that the two line up.
const videoId = youTubeId(params.get('video'))
const delayMs = Math.max(0, Number(params.get('delay')) || 0) * 1000
const liveSource = /^\d+$/.test(competition ?? '') ? createHttpSource(competition) : null
const source = demoSource ?? (liveSource && delayMs ? createDelayedSource(liveSource, delayMs) : liveSource)
const debug = flag('debug')
const showControls = flag('controls', true)
const showIntros = flag('intro', true)
// How long the lifter introduction stays up (?introSeconds=N); shorter in the demo so the flow is quick to watch.
const INTRO_MS = (Number(params.get('introSeconds')) || (demoSource ? 2.5 : 5)) * 1000
// A competition photo standing in for the camera, so the demo shows the complete picture in a browser.
// Hidden inside OBS (which exposes window.obsstudio), where the real camera or a test video is underneath.
const showBackdrop = demoSource !== null && flag('backdrop', !window.obsstudio)

const feed = source ? useLiveFeed(source) : null

// Result/transition state, advanced only when the normalized snapshot changes.
const state = shallowRef(initialOverlayState())
let expiryTimer = null

// Lifter introduction: a large card shown for a few seconds when a new lifter is called to the platform.
const intro = shallowRef(null)
// Ending the introduction: the card travels down and shrinks, then flips over and its back side is the
// lower third. During the flip the shape morphs from the card's proportions to the lower third's.
// exiting: null, 'card' while the card is animating, 'bar' while the lower third flips in.
const exiting = ref(null)
let exitAnimations = []
let introducedLifterId = null
let introTimer = null

watch(
  () => feed?.snapshot.value,
  (snapshot) => {
    if (snapshot) {
      // The lifter on the platform when the overlay starts is not introduced (e.g. after a reload).
      introducedLifterId ??= snapshot.current?.lifterId ?? ''
      state.value = reduceOverlayState(state.value, snapshot, Date.now())
    }
  },
)

watch(
  () => state.value.result,
  (result) => {
    clearTimeout(expiryTimer)
    if (!result) {
      return
    }

    expiryTimer = setTimeout(
      () => {
        if (state.value.result === result) {
          state.value = { ...state.value, result: null }
        }
      },
      Math.max(0, result.shownAt + RESULT_DURATION_MS - Date.now()),
    )
  },
)

// A coarse clock for staleness; computed values only trigger re-renders when they change.
const now = ref(Date.now())
let clock = null

const age = computed(() => {
  const at = feed?.lastSuccessAt.value
  return at == null ? null : Math.max(0, Math.floor((now.value - at) / 1000))
})

const status = computed(() => {
  if (age.value == null) {
    return 'loading'
  }
  if (age.value * 1000 >= OFFLINE_AFTER_MS) {
    return 'offline'
  }
  return age.value * 1000 >= STALE_AFTER_MS ? 'stale' : 'live'
})

const displayed = computed(() => {
  if (status.value === 'loading' || status.value === 'offline') {
    return null
  }

  return state.value.result ?? feed.snapshot.value?.current ?? null
})

// Who lifts after the attempt being shown. While a result is still on screen the next lifter has
// usually already been called, and is then the feed's current lifter.
const upcoming = computed(() => {
  const snapshot = feed?.snapshot.value
  const result = state.value.result
  if (!displayed.value || !snapshot) {
    return null
  }

  if (result && snapshot.current && snapshot.current.id !== result.id) {
    return snapshot.current
  }

  return snapshot.next
})

watch(displayed, (attempt) => {
  // Results take precedence; the introduction waits until the result has been shown.
  if (!attempt || attempt.outcome) {
    cancelIntro()
    return
  }

  // Only a new lifter is introduced, not the same lifter taking another attempt.
  if (!showIntros || introducedLifterId == null || attempt.lifterId === introducedLifterId) {
    return
  }

  introducedLifterId = attempt.lifterId
  intro.value = attempt
  clearTimeout(introTimer)
  introTimer = setTimeout(endIntro, INTRO_MS)
})

function cancelIntro() {
  clearTimeout(introTimer)
  exitAnimations.forEach((animation) => animation.cancel())
  exitAnimations = []
  intro.value = null
  exiting.value = null
}

async function endIntro() {
  clearTimeout(introTimer)
  if (!intro.value || exiting.value) {
    return
  }

  const current = intro.value
  exiting.value = 'card' // mounts the lower third, hidden, so that it can be measured
  await nextTick()

  const card = document.querySelector('.intro .card')
  const stack = document.querySelector('.lower-third .stack')
  const geometry = card && stack ? measureExit(card, stack) : null
  if (!geometry) {
    cancelIntro()
    return
  }

  const perspective = 'perspective(2200px)'
  const { dx, dy, scale, front, back, origin } = geometry
  const run = (element, keyframes, options) => {
    const animation = element.animate(keyframes, { fill: 'forwards', ...options })
    exitAnimations.push(animation)
    return animation.finished
  }

  try {
    // 1. Travel down and shrink, centered on the lower third.
    await run(
      card,
      [
        { transform: `${perspective} translate(0, 0) rotateX(0deg) scale(1)` },
        { transform: `${perspective} translate(${dx}px, ${dy}px) rotateX(0deg) scale(${scale})` },
      ],
      { duration: 500, easing: 'cubic-bezier(0.55, 0, 0.25, 1)' },
    )

    // 2. Flip the card edge-on while its shape stretches towards the lower third's proportions.
    await run(
      card,
      [
        { transform: `${perspective} translate(${dx}px, ${dy}px) rotateX(0deg) scale(${scale})` },
        { transform: `${perspective} translate(${dx}px, ${dy}px) rotateX(90deg) scale(${front.x}, ${front.y})` },
      ],
      { duration: 300, easing: 'cubic-bezier(0.55, 0, 1, 0.45)' },
    )
  } catch {
    return // cancelled, e.g. by a result
  }

  if (intro.value !== current) {
    return
  }

  // 3. The lower third is the back side: it flips in from edge-on and grows into its own shape.
  stack.style.transformOrigin = origin
  const flipIn = run(
    stack,
    [
      { transform: `${perspective} rotateX(-90deg) scale(${back.x}, ${back.y})` },
      { transform: `${perspective} rotateX(0deg) scale(1, 1)` },
    ],
    { duration: 350, easing: 'cubic-bezier(0, 0.55, 0.45, 1)' },
  )
  intro.value = null
  exiting.value = 'bar'

  try {
    await flipIn
  } catch {
    return
  }

  exitAnimations.forEach((animation) => animation.cancel())
  exitAnimations = []
  stack.style.transformOrigin = ''
  exiting.value = null
}

// Geometry (in 1920×1080 stage pixels) for moving the card onto the lower third's panel, and the
// scale factors for the shape halfway through the flip, as seen from the card and from the lower third.
function measureExit(card, stack) {
  const stage = document.querySelector('.stage')?.getBoundingClientRect()
  const panel = stack.querySelector('.panel')?.getBoundingClientRect()
  if (!stage || !panel) {
    return null
  }

  const ratio = 1920 / stage.width
  const cardBox = card.getBoundingClientRect()
  const stackBox = stack.getBoundingClientRect()
  const cardWidth = cardBox.width * ratio
  const cardHeight = cardBox.height * ratio
  const panelWidth = panel.width * ratio
  const panelHeight = panel.height * ratio

  // Shrunk so that the card is a bit narrower than the panel before the flip.
  const scale = Math.min(0.6, (panelWidth * 0.75) / cardWidth)
  const midWidth = (cardWidth * scale + panelWidth) / 2
  const midHeight = (cardHeight * scale + panelHeight) / 2

  return {
    dx: (panel.left + panel.width / 2 - (cardBox.left + cardBox.width / 2)) * ratio,
    dy: (panel.top + panel.height / 2 - (cardBox.top + cardBox.height / 2)) * ratio,
    scale,
    front: { x: midWidth / cardWidth, y: midHeight / cardHeight },
    back: { x: midWidth / panelWidth, y: midHeight / panelHeight },
    origin: `${(panel.left + panel.width / 2 - stackBox.left) * ratio}px ${(panel.top + panel.height / 2 - stackBox.top) * ratio}px`,
  }
}

// Esc (via OBS "Interact") cuts short whatever is on screen: the introduction, or else a shown result.
function skip() {
  if (intro.value) {
    endIntro()
  } else if (state.value.result) {
    state.value = { ...state.value, result: null }
  }
}

// Demo mode: step through the simulated competition automatically or with the keyboard.
const demoIndex = ref(0)
const playing = ref(flag('autoplay', true))
let demoTimer = null

function goToStep(index) {
  demoSource.go(index)
  demoIndex.value = demoSource.index
  feed.refresh()
  scheduleDemoStep()
}

function scheduleDemoStep() {
  clearTimeout(demoTimer)
  if (demoSource && playing.value) {
    demoTimer = setTimeout(() => goToStep(demoIndex.value + 1), DEMO_STEP_MS)
  }
}

function onKeydown(event) {
  if (event.key === 'Escape') {
    skip()
  } else if (!demoSource) {
    return
  } else if (event.key === 'ArrowRight') {
    goToStep(demoIndex.value + 1)
  } else if (event.key === 'ArrowLeft') {
    goToStep(demoIndex.value - 1)
  } else if (event.key === ' ') {
    playing.value = !playing.value
  } else {
    return
  }

  event.preventDefault()
}

watch(playing, scheduleDemoStep)

onMounted(() => {
  clock = setInterval(() => (now.value = Date.now()), 1000)

  window.addEventListener('keydown', onKeydown)
  if (demoSource) {
    scheduleDemoStep()
  }
})

onBeforeUnmount(() => {
  clearInterval(clock)
  clearTimeout(expiryTimer)
  clearTimeout(introTimer)
  clearTimeout(demoTimer)
  window.removeEventListener('keydown', onKeydown)
})
</script>

<style scoped>
.backdrop {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  border: 0;
}

.config-error {
  position: absolute;
  left: 96px;
  bottom: 72px;
  max-width: 1100px;
  padding: 20px 28px;
  border-radius: 8px;
  background: var(--overlay-panel-deep);
  font-size: 28px;
  line-height: 1.5;
}

code {
  font-size: 0.9em;
  color: var(--overlay-silver);
}

.panel-enter-active,
.panel-leave-active {
  transition:
    opacity 350ms ease,
    transform 350ms ease;
}

.panel-enter-from,
.panel-leave-to {
  opacity: 0;
  transform: translateY(24px);
}
</style>
