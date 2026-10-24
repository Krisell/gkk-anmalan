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

    <div v-else class="graphics" :class="{ off: hideAll }">
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
          :key="`slate-${slateKind}-${slateTime}`"
          :kind="slateKind"
          :title="eventTitle"
          :time="slateTime"
        />
      </Transition>

      <Transition name="board">
        <Scoreboard
          v-if="showScoreboard"
          :key="`${activationKey}-${board.group}`"
          :scoreboard="board.scoreboard"
          :discipline="board.discipline"
          :group="board.group"
          :current-lifter-id="board.currentLifterId"
          :final="board.final"
        />
      </Transition>

      <Transition name="intro" :duration="{ enter: 2800, leave: 0 }">
        <LifterIntro v-if="intro && displayed && !fullScreen && !commentators" :key="intro.id" :attempt="intro" />
      </Transition>

      <Transition :name="exiting ? '' : 'panel'">
        <LowerThird
          v-if="displayed && (!intro || exiting) && !fullScreen && !commentators"
          :class="{ 'is-hidden': exiting === 'card' }"
          :attempt="displayed"
          :upcoming="upcoming"
          :clock-ends-at="clockEndsAt"
        />
      </Transition>

      <Transition name="panel">
        <RefereePanel v-if="refereeLights" :key="displayed.id" :lights="refereeLights" />
      </Transition>

      <CommentatorStrap
        v-if="commentators && !fullScreen"
        :key="commentators.key"
        class="commentators"
        :names="commentators.names"
      />

      <InfoStrap v-if="control.message" :key="control.message.id" :text="control.message.text" />

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
    </div>

    <!-- Our logo in the corner, on every view except the results list (it has our logo in its heading) and the
         stinger (a transition). -->
    <CornerBug v-if="view !== 'stinger' && view !== 'scoreboard' && !showScoreboard && !hideAll" />

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
import InfoStrap from './components/InfoStrap.vue'
import RefereePanel from './components/RefereePanel.vue'
import { useLiveFeed } from './useLiveFeed.js'
import { useGroupResults } from './useGroupResults.js'
import { createHttpSource } from './sources/httpSource.js'
import { createRefereeFeed, refereeFeedUrl } from './sources/refereeFeed.js'
import { createDemoSource } from './sources/demoSource.js'
import { createEditorSource, defaultEditorState } from './sources/editorSource.js'
import { createDelayedSource, youTubeId } from './sources/delayedSource.js'
import { initialOverlayState, reduceOverlayState, RESULT_DURATION_MS } from './overlayState.js'
import { useRemoteControl } from './remote/useRemoteControl.js'
import { DEFAULT_CONTROL } from './remote/controlState.js'
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
const liveCompetition = /^\d+$/.test(competition ?? '') ? competition : null
// Referee decisions come straight from Powerlifting Live (the proxy can't see them); ?lights=0 turns this off.
const referee =
  liveCompetition && flag('lights', true)
    ? createRefereeFeed(refereeFeedUrl(document.body.dataset.powerliftingLive), liveCompetition)
    : null
const liveSource = liveCompetition ? createHttpSource(liveCompetition, referee) : null
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

// Remote control (e.g. from an iPad, /live/control): pause slates, messages, PB stamps and more, through Firebase.
// The control page's preview (?preview=1) follows it too, but doesn't report itself as the overlay on air.
const remote =
  liveCompetition && !editorState && !demoSource && !standaloneView && !scoreboardView && flag('remote', true)
    ? useRemoteControl(liveCompetition, { onCommand: runCommand, presence: !flag('preview') })
    : null
const control = computed(() => remote?.control.value ?? DEFAULT_CONTROL)

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

// A lifter replaced on the platform before their result arrived is shown until it does, or until the wait is over.
let awaitTimer = null
watch(
  () => state.value.awaiting,
  (awaiting) => {
    clearTimeout(awaitTimer)
    if (awaiting) {
      awaitTimer = setTimeout(
        () => {
          if (state.value.awaiting === awaiting) {
            state.value = { ...state.value, awaiting: null }
          }
        },
        Math.max(0, awaiting.until - Date.now()),
      )
    }
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

  return state.value.result ?? state.value.awaiting?.attempt ?? feed.snapshot.value?.current ?? null
})

// The results list replaces the lower third and the lifter card while it is shown.
const showScoreboard = computed(
  () =>
    (scoreboardView || displayMode.value === 'list' || remoteMode.value === 'list' || roundBoard.value) &&
    Boolean(displayed.value || board.value.scoreboard?.lifters.length) &&
    status.value !== 'loading' &&
    status.value !== 'offline',
)

// A slate ("Skylt"): chosen in the demo editor, or from the remote control.
const showSlate = computed(() => displayMode.value === 'slate' || remoteMode.value === 'slate')
const slateKind = computed(() => (editorState ? editorState.slate : control.value.slate))
const slateTime = computed(() => (editorState ? demoPauseTime : control.value.pauseTime))

// Full-screen graphics replace the lower third, the lifter card and the corner graphic while shown.
const fullScreen = computed(() => showScoreboard.value || showSlate.value)

// The competition's name: the demo editor's field, the remote control's, or ?title=….
const eventTitle = computed(() => editorState?.title?.trim() || control.value.title || title || null)

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

// Who lifts after the attempt being shown. While a result is still on screen (or awaited) the next lifter has
// usually already been called, and is then the feed's current lifter.
const upcoming = computed(() => {
  const snapshot = feed?.snapshot.value
  const result = state.value.result ?? state.value.awaiting?.attempt
  if (!displayed.value || !snapshot) {
    return null
  }

  if (result && snapshot.current && snapshot.current.id !== result.id) {
    return snapshot.current
  }

  return snapshot.next
})

// Between rounds: when the last lift of a round has been judged, the results list is shown automatically and for 10 s
// (unless ?roundBoard=0). The stinger starts 6 s after the lights, so that the lifter's reaction is seen first, and the
// list takes over 0.6 s later, while the result is still up (RESULT_DURATION_MS), so that the next lifter isn't
// introduced first. In the demo, "Sista i omgången" makes a result end the round.
const ROUND_BOARD_DELAY_MS = 6000
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
    if (!showRoundBoards || !control.value.roundBoards || !endsRound || key === roundBoardFor) {
      return
    }

    roundBoardFor = key
    startRoundBoard(Math.max(0, result.shownAt + ROUND_BOARD_DELAY_MS - Date.now()))
  },
)

// Shows the results list for ROUND_BOARD_MS, coming and going with the stinger, after `startIn` ms.
function startRoundBoard(startIn = 0) {
  stopRoundBoard()
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
}

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
  if (
    !showIntros ||
    !control.value.intros ||
    roundBoard.value ||
    introducedLifterId == null ||
    attempt.lifterId === introducedLifterId
  ) {
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
  endFlight()
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

  // The current discipline's attempts are carried over: they travel down with the card, and when the card flips
  // they come loose and fly flat straight into the lower third's attempts while the card flips around them.
  const carried = prepareFlight(card, stack, current.discipline)
  let flight = null
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
    flight = startFlight(carried)
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
    endFlight()
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
    await Promise.all([flipIn, flight?.finished])
  } catch {
    return
  }

  endFlight()
  exitAnimations.forEach((animation) => animation.cancel())
  exitAnimations = []
  stack.style.transformOrigin = ''
  exiting.value = null
}

// The flying copies of the card's current attempts (see endIntro), with the real elements hidden meanwhile.
let flyer = null
const FLIGHT_MS = 650 // the card's flip (300) plus the lower third flipping in (350)

// The carried-over box flies as two parts, the discipline heading and the attempt boxes, each straight onto its
// counterpart in the lower third, so that both land exactly even though the heading is styled differently there.
const FLIGHT_PARTS = ['.eyebrow', '.slots']

// Measures, in stage pixels, the card's current attempts at full size (before the card shrinks) and where they go
// in the lower third.
function prepareFlight(card, stack, discipline) {
  const from = card.querySelector(`.stats .stat[data-discipline="${discipline}"]`)
  const to = stack.querySelector('.attempts')
  if (!from || !to) {
    return null
  }

  return {
    from,
    to,
    natural: FLIGHT_PARTS.map((part) => stageBox(from.querySelector(part))),
    end: FLIGHT_PARTS.map((part) => stageBox(to.querySelector(part))),
  }
}

// An element's box in stage pixels; for the heading, the box of its text (it is as wide as its container).
function stageBox(element) {
  const stage = document.querySelector('.stage').getBoundingClientRect()
  const ratio = 1920 / stage.width
  let box = element.getBoundingClientRect()
  if (element.matches('.eyebrow')) {
    const text = document.createRange()
    text.selectNodeContents(element)
    box = text.getBoundingClientRect()
  }
  return {
    left: (box.left - stage.left) * ratio,
    top: (box.top - stage.top) * ratio,
    width: box.width * ratio,
    height: box.height * ratio,
  }
}

// Lifts the attempts out of the (now travelled and shrunk) card and flies them into the lower third.
function startFlight(carried) {
  const stage = document.querySelector('.stage')
  if (!carried || !stage) {
    return null
  }

  const { from, to, natural, end } = carried
  const wrappers = []
  const animations = FLIGHT_PARTS.map((part, index) => {
    const start = stageBox(from.querySelector(part))

    // Each copy sits in shallow clones of the card's row and its container, so that the card's (scoped) styles
    // still apply to it.
    const wrapper = from.parentElement.cloneNode(false)
    const row = from.cloneNode(false)
    row.removeAttribute('style') // its place in the card's grid of disciplines
    wrapper.style.display = 'block'
    const copy = from.querySelector(part).cloneNode(true)
    copy.style.display = 'inline-flex' // as wide as its text, like the measured boxes
    copy.style.margin = '0'
    row.appendChild(copy)
    wrapper.appendChild(row)
    Object.assign(wrapper.style, {
      position: 'absolute',
      left: `${start.left}px`,
      top: `${start.top}px`,
      height: 'auto',
      margin: '0',
      padding: '0',
      border: 'none',
      background: 'none',
      zIndex: '6',
      pointerEvents: 'none',
      transformOrigin: '0 0',
    })
    stage.appendChild(wrapper)
    wrappers.push(wrapper)

    // The copy only scales uniformly, from the card's shrunk size to full size, while its own sizes (box size,
    // font size, letter spacing) change from the card's to the lower third's. Nothing is ever stretched, and at
    // the end the copy is identical to the lower third's elements, so the swap can't be seen.
    const target = end[index]
    const options = { duration: FLIGHT_MS, easing: 'cubic-bezier(0.65, 0, 0.35, 1)', fill: 'forwards' }
    const sizes = (element, properties) => {
      const style = getComputedStyle(element)
      return Object.fromEntries(properties.map((property) => [property, style[property]]))
    }
    const morphs =
      part === '.slots'
        ? [...copy.children].map((slot, n) => {
            const properties = ['width', 'height', 'lineHeight', 'fontSize', 'borderRadius']
            const cardSlot = from.querySelectorAll('.slot')[n]
            const barSlot = to.querySelectorAll('.slot')[n] ?? to.querySelector('.slot')
            return slot.animate([sizes(cardSlot, properties), sizes(barSlot, properties)], options)
          })
        : [
            copy.animate(
              [
                sizes(from.querySelector(part), ['fontSize', 'letterSpacing']),
                sizes(to.querySelector(part), ['fontSize', 'letterSpacing']),
              ],
              options,
            ),
          ]

    return Promise.all([
      wrapper.animate(
        [
          { transform: `translate(0, 0) scale(${start.width / natural[index].width})` },
          { transform: `translate(${target.left - start.left}px, ${target.top - start.top}px) scale(1)` },
        ],
        options,
      ).finished,
      ...morphs.map((animation) => animation.finished),
    ])
  })

  // Hide the card's box and only the contents of the lower third's (its divider line flips in with the panel).
  const hidden = [from, ...FLIGHT_PARTS.map((part) => to.querySelector(part))]
  hidden.forEach((element) => (element.style.visibility = 'hidden'))
  flyer = { wrappers, hidden: hidden.slice(1) }

  return { finished: Promise.all(animations) }
}

function endFlight() {
  if (flyer) {
    flyer.wrappers.forEach((wrapper) => wrapper.remove())
    flyer.hidden.forEach((element) => (element.style.visibility = ''))
    flyer = null
  }
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

// Remote control: what it asks the overlay to show. The results list and the slates come and go under the stinger,
// like separate OBS scenes; hiding all graphics fades them out.
const remoteMode = ref(control.value.mode)
const hideAll = computed(() => remoteMode.value === 'hidden')
watch(
  () => control.value.mode,
  (mode) => {
    const fullScreenModes = ['list', 'slate']
    if (fullScreenModes.includes(mode) || fullScreenModes.includes(remoteMode.value)) {
      withStinger(() => {
        remoteMode.value = mode
        if (mode !== 'auto') {
          stopRoundBoard()
          cancelIntro()
        }
      })
    } else {
      remoteMode.value = mode
    }
  },
)

// The results list: of the group on the platform, or (from the remote control) of another group, e.g. an earlier one.
// A group that hasn't started is shown as a start list with the declared openers.
const listGroup = computed(() => (remote && remoteMode.value === 'list' ? control.value.listGroup : null))
const groupResults = remote ? useGroupResults(liveCompetition, listGroup) : null
const board = computed(() => {
  const snapshot = feed?.snapshot.value
  const other = groupResults?.value
  if (listGroup.value && other && other.name !== snapshot?.group) {
    const upcoming = other.status === 'upcoming'
    return {
      scoreboard: other.scoreboard,
      discipline: upcoming ? (other.scoreboard.disciplines[0] ?? null) : null,
      group: other.name,
      currentLifterId: null,
      final: !upcoming,
    }
  }
  return {
    scoreboard: snapshot?.scoreboard ?? { disciplines: [], lifters: [] },
    discipline: snapshot?.discipline ?? null,
    group: snapshot?.group ?? null,
    currentLifterId: snapshot?.current?.lifterId ?? null,
    final: false,
  }
})

// The referee lights, large in the bottom right corner: each lamp as its referee decides (from referee/signal), then
// the decision, which stays with the result.
const refereeLights = computed(() => {
  const attempt = displayed.value
  if (!attempt || fullScreen.value) {
    return null
  }
  return attempt.outcome ? (attempt.decisions ?? null) : (feed?.snapshot.value?.signals ?? null)
})

// The commentator strap, shown on command for its 9 s animation, in the lower third's place.
const COMMENTATORS_MS = 9800
const commentators = shallowRef(null)
let commentatorTimer = null

function runCommand(command) {
  if (command.type === 'pb') {
    showPb()
  } else if (command.type === 'skip') {
    skip()
  } else if (command.type === 'list') {
    startRoundBoard()
  } else if (command.type === 'commentators' && control.value.commentators.length) {
    clearTimeout(commentatorTimer)
    commentators.value = { key: (commentators.value?.key ?? 0) + 1, names: control.value.commentators }
    commentatorTimer = setTimeout(() => (commentators.value = null), COMMENTATORS_MS)
  }
}

// What is on air, for the control page.
const onAir = computed(() => {
  const name = displayed.value?.name ?? ''
  if (hideAll.value) {
    return 'Ingen grafik'
  }
  if (showSlate.value) {
    return `Skylt: ${{ start: 'Startar snart', pause: 'Paus', end: 'Tack för idag' }[slateKind.value] ?? slateKind.value}`
  }
  if (showScoreboard.value) {
    return board.value.final ? `Resultat: ${board.value.group}` : 'Resultatlista'
  }
  if (intro.value) {
    return `Lyftarkort: ${intro.value.name ?? ''}`
  }
  if (displayed.value?.outcome) {
    return `Resultat: ${name} (${displayed.value.outcome === 'good' ? 'godkänt' : 'underkänt'})`
  }
  return displayed.value ? `Nederkant: ${name}` : 'Väntar på tävlingsdata'
})
watch([onAir, status], ([showing, feedStatus]) => remote?.report({ showing, feed: feedStatus }), { immediate: true })

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
  referee?.stop()
  clearInterval(clock)
  clearTimeout(expiryTimer)
  clearTimeout(awaitTimer)
  clearTimeout(introTimer)
  clearTimeout(pbTimer)
  clearTimeout(commentatorTimer)
  endFlight()
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

/* All graphics, faded out together when the remote control hides them ("Ingen grafik"). */
.graphics {
  position: absolute;
  inset: 0;
  transition: opacity var(--motion-slow) var(--motion-move);
}

.graphics.off {
  opacity: 0;
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
