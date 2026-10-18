<template>
  <Stage>
    <!-- Pages for their own OBS scene or transition, needing no competition data. -->
    <template v-if="standaloneView">
      <Stinger v-if="view === 'stinger'" />
      <ReplayTag v-else-if="view === 'replay'" :key="activationKey" />
      <CommentatorStrap v-else-if="view === 'commentators'" :key="activationKey" :names="commentatorNames" />
      <Slate
        v-else
        :key="activationKey"
        :kind="params.get('slate') ?? 'start'"
        :title="title"
        :time="params.get('time')"
      />
    </template>

    <div v-else-if="!source" class="config-error">
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

      <Transition name="board">
        <Slate
          v-if="showSlate"
          :key="`slate-${editorState.slate}`"
          :kind="editorState.slate"
          :title="eventTitle"
          :time="demoPauseTime"
        />
      </Transition>

      <Transition name="board">
        <Scoreboard
          v-if="showScoreboard"
          :key="activationKey"
          :scoreboard="feed.snapshot.value.scoreboard"
          :discipline="feed.snapshot.value.discipline"
          :group="feed.snapshot.value.group"
          :current-lifter-id="feed.snapshot.value.current?.lifterId ?? null"
        />
      </Transition>

      <Transition name="intro" :duration="{ enter: 2800, leave: 0 }">
        <LifterIntro v-if="intro && displayed && !fullScreen" :key="intro.id" :attempt="intro" />
      </Transition>

      <Transition :name="exiting ? '' : 'panel'">
        <LowerThird
          v-if="displayed && (!intro || exiting) && !fullScreen"
          :class="{ 'is-hidden': exiting === 'card' }"
          :attempt="displayed"
          :upcoming="upcoming"
          :clock-ends-at="clockEndsAt"
        />
      </Transition>

      <PbStamp v-if="pb" :key="pb.key" :attempt="pb.attempt" />

      <DemoControls
        v-if="demoSource && showControls"
        :steps="demoSource.steps"
        :index="demoIndex"
        :playing="playing"
        @go="goToStep"
        @toggle="playing = !playing"
      />

      <DemoEditor
        v-if="editorState && showControls"
        :state="editorState"
        :busy="Boolean(exiting)"
        @clear-result="clearEditorResult"
      />

      <DebugStatus
        v-if="debug"
        :status="status"
        :source="
          demoSource || editorState ? 'demo' : `tävling ${competition}${delayMs ? ` (+${delayMs / 1000} s)` : ''}`
        "
        :age="age"
        :failures="feed.failures.value"
        :error="feed.error.value"
        :current-id="feed.snapshot.value?.current?.id ?? null"
        :result-id="state.result?.id ?? null"
      />
    </template>

    <!-- Our logo in the corner, on every view (but not in the stinger, which is a transition). -->
    <CornerBug v-if="view !== 'stinger'" />

    <!-- The stinger as an in-page transition when the results list or a slate comes or goes. -->
    <Stinger v-if="stinger" :key="stinger" class="in-page-stinger" />
  </Stage>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, reactive, ref, shallowRef, watch } from 'vue'
import Stage from './components/Stage.vue'
import LowerThird from './components/LowerThird.vue'
import LifterIntro from './components/LifterIntro.vue'
import DebugStatus from './components/DebugStatus.vue'
import DemoControls from './components/DemoControls.vue'
import DemoEditor from './components/DemoEditor.vue'
import Scoreboard from './components/Scoreboard.vue'
import PbStamp from './components/PbStamp.vue'
import CornerBug from './components/CornerBug.vue'
import Slate from './components/Slate.vue'
import ReplayTag from './components/ReplayTag.vue'
import CommentatorStrap from './components/CommentatorStrap.vue'
import Stinger from './components/Stinger.vue'
import { useLiveFeed } from './useLiveFeed.js'
import { createHttpSource } from './sources/httpSource.js'
import { createDemoSource } from './sources/demoSource.js'
import { createEditorSource, defaultEditorState } from './sources/editorSource.js'
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
// ?demo=true opens the demo editor (everything set by hand); ?demo=scenario steps through a simulated competition.
const demoSource = flag('demo') && params.get('demo') === 'scenario' ? createDemoSource() : null
const editorState = flag('demo') && !demoSource ? reactive(defaultEditorState()) : null
// The demo editor's view as shown: follows editorState.mode, but full-screen views switch under the stinger.
const displayMode = ref(editorState?.mode ?? null)
// Testing against an external stream: ?video=<YouTube link> plays it behind the overlay, and ?delay=<seconds>
// holds the HUD back by the stream's latency so that the two line up.
const videoId = youTubeId(params.get('video'))
const delayMs = Math.max(0, Number(params.get('delay')) || 0) * 1000
const liveSource = /^\d+$/.test(competition ?? '') ? createHttpSource(competition) : null
const source =
  demoSource ??
  (editorState ? createEditorSource(editorState) : null) ??
  (liveSource && delayMs ? createDelayedSource(liveSource, delayMs) : liveSource)
const debug = flag('debug')
const showControls = flag('controls', true)
// ?view=… makes the page a single graphic for its own OBS scene (or transition):
// - scoreboard: the results list of the current group (needs ?competition=…),
// - slate (&slate=start|pause|end&time=14:30), replay ("REPRIS"), commentators (&names=A,B), stinger.
const view = params.get('view')
const scoreboardView = view === 'scoreboard'
const standaloneView = ['slate', 'replay', 'commentators', 'stinger'].includes(view)
const commentatorNames = (params.get('names') ?? '')
  .split(',')
  .map((name) => name.trim())
  .filter(Boolean)
// The competition's name for the slates (?title=…); editable in the demo.
const title = params.get('title')
// In the demo editor the lifter card is shown and hidden by hand instead.
const showIntros = flag('intro', true) && !editorState && !scoreboardView && !standaloneView
// How long the lifter introduction stays up (?introSeconds=N); shorter in the demo so the flow is quick to watch.
const INTRO_MS = (Number(params.get('introSeconds')) || (demoSource ? 3.5 : 5)) * 1000
// A competition photo standing in for the camera, so the demo shows the complete picture in a browser.
// Hidden inside OBS (which exposes window.obsstudio), where the real camera or a test video is underneath.
const showBackdrop = Boolean(demoSource || editorState) && flag('backdrop', !window.obsstudio)

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

// The results list replaces the lower third and the lifter card while it is shown.
const showScoreboard = computed(
  () =>
    (scoreboardView || displayMode.value === 'list' || roundBoard.value) &&
    Boolean(displayed.value || feed?.snapshot.value?.scoreboard?.lifters.length) &&
    status.value !== 'loading' &&
    status.value !== 'offline',
)

// In the demo editor, a slate ("Skylt") can be previewed like the results list.
const showSlate = computed(() => displayMode.value === 'slate')

// Full-screen graphics replace the lower third, the lifter card and the corner graphic while shown.
const fullScreen = computed(() => showScoreboard.value || showSlate.value)

// The competition's name: from ?title=…, or the demo editor's field.
const eventTitle = computed(() => editorState?.title?.trim() || title || null)

// In the demo the break slate says the next group starts in about a quarter of an hour.
const demoPauseTime = (() => {
  const at = new Date(Date.now() + 15 * 60 * 1000)
  at.setMinutes(Math.ceil(at.getMinutes() / 5) * 5)
  return at.toLocaleTimeString('sv-SE', { hour: '2-digit', minute: '2-digit' })
})()

// Attempt clock: Powerlifting Live publishes the seconds left when it starts; the overlay counts down from there.
// Only a changed value restarts it, so repeated polls of the same message don't.
const clockEndsAt = ref(null)
watch(
  () => feed?.snapshot.value?.clock,
  (seconds) => (clockEndsAt.value = seconds ? Date.now() + seconds * 1000 : null),
)

// Changing the key replays a graphic's entrance animation, e.g. each time its OBS scene goes live.
const activationKey = ref(0)

function onObsShown(event) {
  if (event.detail?.active || event.detail?.visible) {
    activationKey.value++
  }
}

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

// Between rounds: when the last lift of a round has been judged, the results list is shown automatically, 3 s after
// the lights and for 10 s (unless ?roundBoard=0). In the demo, "Sista i omgången" makes a result end the round.
const ROUND_BOARD_DELAY_MS = 3000
const ROUND_BOARD_MS = 10000
const showRoundBoards = flag('roundBoard', true) && !scoreboardView && !standaloneView
const roundBoard = ref(false)
let roundBoardFor = null
let roundBoardTimers = []

watch(
  () => state.value.result,
  (result) => {
    const endsRound = result && (editorState ? editorState.lastInRound : result.confirmed && result.endsRound)
    const key = result && `${result.id}:${result.shownAt}`
    if (!showRoundBoards || !endsRound || key === roundBoardFor) {
      return
    }

    roundBoardFor = key
    stopRoundBoard()
    const startIn = Math.max(0, result.shownAt + ROUND_BOARD_DELAY_MS - Date.now())
    roundBoardTimers = [
      setTimeout(
        () =>
          withStinger(() => {
            roundBoard.value = true
            activationKey.value++
          }),
        startIn,
      ),
      setTimeout(() => withStinger(stopRoundBoard), startIn + STINGER_COVERED_MS + ROUND_BOARD_MS),
    ]
  },
)

// The stinger as an in-page transition: bands sweep in, the change happens while the picture is fully covered
// (0.6 s, like OBS's transition point), and the bands sweep out (1.2 s in all).
const STINGER_COVERED_MS = 600
const STINGER_MS = 1200
const stinger = ref(0)
let stingerTimers = []

function withStinger(change) {
  stingerTimers.forEach(clearTimeout)
  stinger.value++
  const key = stinger.value
  stingerTimers = [
    setTimeout(change, STINGER_COVERED_MS),
    setTimeout(() => stinger.value === key && (stinger.value = 0), STINGER_MS),
  ]
}

function stopRoundBoard() {
  roundBoardTimers.forEach(clearTimeout)
  roundBoardTimers = []
  roundBoard.value = false
}

watch(displayed, considerIntro)
// A lifter called while the results list was shown is introduced once it is gone.
watch(roundBoard, (shown) => !shown && considerIntro(displayed.value))

function considerIntro(attempt) {
  // Results take precedence; the introduction waits until the result has been shown.
  if (!attempt || attempt.outcome) {
    cancelIntro()
    return
  }

  // Only a new lifter is introduced, not the same lifter taking another attempt, and not under the results list.
  if (!showIntros || roundBoard.value || introducedLifterId == null || attempt.lifterId === introducedLifterId) {
    return
  }

  introducedLifterId = attempt.lifterId
  intro.value = attempt
  clearTimeout(introTimer)
  introTimer = setTimeout(endIntro, INTRO_MS)
}

function cancelIntro() {
  if (editorState?.mode === 'card') {
    editorState.mode = 'bar'
  }
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

// Demo editor: the mode buttons show the lifter card (flip in) or turn it into the lower third (flip over),
// and while the card is up it follows the edits.
if (editorState) {
  // The results list and the slates come and go under the stinger, like separate OBS scenes; the card and the lower
  // third switch with their own animations.
  watch(
    () => editorState.mode,
    (mode) => {
      const fullScreenModes = ['list', 'slate']
      if (fullScreenModes.includes(mode) || fullScreenModes.includes(displayMode.value)) {
        withStinger(() => applyMode(mode))
      } else {
        applyMode(mode)
      }
    },
  )

  async function applyMode(mode) {
    if (editorState.mode !== mode) {
      return // changed again meanwhile
    }

    displayMode.value = mode

    // Choosing another full view ends the results list shown between rounds. (Switching to the lower third
    // doesn't: that also happens on its own when a result arrives while the card is up.)
    if (mode !== 'bar') {
      stopRoundBoard()
    }
    if (mode === 'bar') {
      endIntro()
      return
    }

    if (mode === 'list' || mode === 'slate') {
      cancelIntro()
      return
    }

    // Asking for the card while a result is shown cuts the result short.
    if (state.value.result) {
      state.value = { ...state.value, result: null }
      await nextTick()
    }

    if (!exiting.value && displayed.value && !displayed.value.outcome) {
      intro.value = displayed.value
    } else {
      editorState.mode = 'bar'
    }
  }

  // While the card mode is chosen, the card shows the current attempt: it flips in once the first data has loaded
  // (the card is the default when the page opens), and then follows the edits.
  watch(displayed, (attempt) => {
    if (editorState.mode === 'card' && !exiting.value && attempt && !attempt.outcome) {
      intro.value = attempt
    }
  })

  watch(editorState, () => feed.refresh(), { deep: true })
}

// Demo editor's "Rensa": remove the result from the attempt and take it off screen right away.
function clearEditorResult() {
  editorState.result = null
  state.value = { ...state.value, result: null }
}

// "PB ALERT" stamp, triggered with P for now (shown for the stamp's 3.4 s animation). The lifter on screen is
// shown below it.
const PB_MS = 3400
const pb = shallowRef(null)
let pbTimer = null

function showPb() {
  clearTimeout(pbTimer)
  pb.value = { key: (pb.value?.key ?? 0) + 1, attempt: displayed.value }
  pbTimer = setTimeout(() => (pb.value = null), PB_MS)
}

// Esc (via OBS "Interact") cuts short whatever is on screen: the introduction, or else a shown result.
function skip() {
  if (pb.value) {
    clearTimeout(pbTimer)
    pb.value = null
  } else if (roundBoard.value) {
    withStinger(stopRoundBoard)
  } else if (intro.value) {
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
  // Typing in the demo editor's fields.
  if (event.target instanceof Element && event.target.closest('input, select, textarea')) {
    return
  }

  if (event.key.toLowerCase() === 'p' && !event.metaKey && !event.ctrlKey && !event.altKey) {
    showPb()
    event.preventDefault()
    return
  }

  // Demo editor: S, N, R and K switch between the lifter card, the lower third, the results list and a slate
  // ("skylt"), also while the toolbar is folded.
  const editorModes = { s: 'card', n: 'bar', r: 'list', k: 'slate' }
  if (editorState && editorModes[event.key.toLowerCase()] && !event.metaKey && !event.ctrlKey && !event.altKey) {
    if (!exiting.value) {
      editorState.mode = editorModes[event.key.toLowerCase()]
    }
    event.preventDefault()
    return
  }

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
  if (scoreboardView || standaloneView) {
    // OBS tells the page when its scene goes live or the source is shown.
    window.addEventListener('obsSourceActiveChanged', onObsShown)
    window.addEventListener('obsSourceVisibleChanged', onObsShown)
  }
  if (demoSource) {
    scheduleDemoStep()
  }
})

onBeforeUnmount(() => {
  clearInterval(clock)
  clearTimeout(expiryTimer)
  clearTimeout(introTimer)
  clearTimeout(pbTimer)
  roundBoardTimers.forEach(clearTimeout)
  stingerTimers.forEach(clearTimeout)
  clearTimeout(demoTimer)
  window.removeEventListener('keydown', onKeydown)
  window.removeEventListener('obsSourceActiveChanged', onObsShown)
  window.removeEventListener('obsSourceVisibleChanged', onObsShown)
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

.in-page-stinger {
  z-index: 5;
}

.board-leave-active {
  transition: opacity var(--motion-fast) var(--motion-move);
}

.board-leave-to {
  opacity: 0;
}

.panel-enter-active,
.panel-leave-active {
  transition:
    opacity var(--motion-base) var(--motion-move),
    transform var(--motion-base) var(--motion-move);
}

.panel-enter-from,
.panel-leave-to {
  opacity: 0;
  transform: translateY(24px);
}
</style>
