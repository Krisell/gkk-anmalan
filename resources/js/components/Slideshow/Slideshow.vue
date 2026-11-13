<template>
  <div
    class="fixed inset-0 flex flex-col overflow-hidden select-none cursor-none bg-linear-to-br from-slate-50 via-white to-slate-100"
    :style="{ '--footer-h': control.footer ? FOOTER_HEIGHT : '0px' }"
  >
    <main class="relative flex-1 min-h-0">
      <Transition name="slideshow-fade">
        <!-- The club's logo while loading, and when no slide is in the rotation (and none is held). -->
        <div v-if="state == 'loading' || showLogo" class="absolute inset-0 flex items-center justify-center p-[8vh]">
          <img
            class="max-w-[60%] max-h-full object-contain"
            :class="{ 'animate-pulse': state == 'loading' }"
            src="https://goteborg-kraftsportklubb.web.app/img/logo-min.png"
            alt=""
          />
        </div>
      </Transition>

      <template v-for="(slide, index) in slides" :key="index">
        <Transition name="slideshow-fade">
          <!-- A slide held while a web page is shown goes over the page (with the screen's background), so the page
               keeps running underneath and is where it was when the slide is let go. -->
          <Component
            :is="component(slide.type)"
            v-bind="slide.data"
            v-show="currentSlide == index"
            class="absolute inset-0 w-full h-full"
            :class="
              slideOverPage && currentSlide == index ? 'z-20 bg-linear-to-br from-slate-50 via-white to-slate-100' : ''
            "
          />
        </Transition>
      </template>

      <!-- A web page shown instead of the slideshow, from the live control, until it is taken down. -->
      <Transition name="slideshow-fade">
        <iframe
          v-if="control.page"
          :key="control.page.id"
          :src="control.page.url"
          class="absolute inset-0 z-10 w-full h-full border-0"
          :class="isOverlay(control.page.url) ? 'bg-gkk' : 'bg-white'"
          sandbox="allow-scripts allow-same-origin allow-forms allow-popups"
          allow="autoplay; fullscreen"
          referrerpolicy="no-referrer"
          title="Webbsida"
        ></iframe>
      </Transition>
    </main>

    <!-- A message from the live control (/admin/slideshow), shown until it is taken down (or by itself after a while). -->
    <Transition name="slideshow-message">
      <div
        v-if="messageShown(control.message, serverTime)"
        :key="control.message.id"
        class="shrink-0 bg-amber-300 text-gkk py-[2vh] text-center font-semibold leading-tight text-balance text-[min(3.4vw,5.8vh)]"
        :class="floatingClock ? 'px-[15vw]' : 'px-[4vw]'"
      >
        {{ control.message.text }}
      </div>
    </Transition>

    <!-- Kept while a web page is shown, so the screen still has the date and time. -->
    <footer
      v-if="control.footer"
      class="relative shrink-0 h-(--footer-h) bg-gkk text-white grid grid-cols-[1fr_auto_1fr] items-center gap-[2vw] px-[2.5vw]"
    >
      <div class="absolute top-0 inset-x-0 h-[0.5vh] min-h-[3px] bg-white/10">
        <div
          v-if="slides.length && !held && currentSlide >= 0"
          :key="slideCycle"
          class="slideshow-progress h-full bg-white/70 origin-left"
          :style="{ animationDuration: `${slideDuration}ms` }"
        ></div>
      </div>

      <div class="flex items-center gap-[1vw] min-w-0">
        <!-- The club's logo, on white so that the dark logo shows on the bar. -->
        <span
          class="flex size-[calc(var(--footer-h)*0.72)] shrink-0 items-center justify-center rounded-full bg-white p-[calc(var(--footer-h)*0.05)]"
        >
          <img
            class="size-full object-contain"
            src="https://goteborg-kraftsportklubb.web.app/img/logo-min.png"
            alt=""
          />
        </span>
        <div class="flex items-baseline gap-[1vw] min-w-0 leading-none">
          <span class="text-[min(2.2vw,3.8vh)] font-semibold capitalize whitespace-nowrap">{{ weekday }}</span>
          <span class="text-[min(2.2vw,3.8vh)] font-light whitespace-nowrap">{{ day }} {{ month }}</span>
          <span
            class="rounded-full bg-white/15 px-[0.8vw] py-[0.5vh] text-[min(1.3vw,2.3vh)] font-medium uppercase tracking-wider whitespace-nowrap"
          >
            Vecka {{ weekNumber }}
          </span>
        </div>
      </div>

      <div class="flex items-center gap-[0.6vw]">
        <template v-for="(slide, index) in slides.length > 1 && !control.page ? slides : []" :key="index">
          <span
            v-if="inRotation(slide, control)"
            class="h-[1vh] min-h-1.5 rounded-full transition-all duration-500"
            :class="currentSlide == index ? 'w-[2.5vw] bg-white' : 'w-[1vh] min-w-1.5 bg-white/35'"
          ></span>
        </template>
      </div>

      <div class="flex items-baseline justify-end leading-none tabular-nums whitespace-nowrap">
        <span class="text-[min(3vw,5vh)] font-semibold">{{ time }}</span>
        <span class="text-[min(1.6vw,2.8vh)] font-light text-white/60 ml-[0.3vw]">{{ seconds }}</span>
      </div>
    </footer>

    <!-- The clock on its own, when the bottom bar is hidden from the live control: bottom right, over everything
         (a web page and the message bar). -->
    <div
      v-if="floatingClock"
      class="absolute bottom-[2vh] right-[2vw] z-[60] flex items-baseline rounded-[1.4vh] bg-gkk px-[1.4vw] py-[1.1vh] text-white leading-none tabular-nums whitespace-nowrap shadow-xl shadow-black/20"
    >
      <span class="text-[min(3vw,5vh)] font-semibold">{{ time }}</span>
      <span class="text-[min(1.6vw,2.8vh)] font-light text-white/60 ml-[0.3vw]">{{ seconds }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue'
import Slide from './Slide.vue'
import KlubbrekordSlide from './KlubbrekordSlide.vue'
import ResultatSlide from './ResultatSlide.vue'
import Dm2026Slide from './Dm2026Slide.vue'
import Gm2027Slide from './Gm2027Slide.vue'
import LokalreglerSlide from './LokalreglerSlide.vue'
import SwishSlide from './SwishSlide.vue'
import { getISOWeek, getSwedishMonthName } from '../../utils/date'
import { onValue, ref as databaseRef } from 'firebase/database'
import { liveDatabase } from '../../live/remote/firebase.js'
import axios from 'axios'
import {
  PAGE_KEY,
  inRotation,
  messageShown,
  nextInRotation,
  pageLabel,
  rotation,
  slideKey,
  slideLabel,
  useSlideshowRemote,
} from './remote.js'

// The broadcast graphics (/live/overlay) are transparent (OBS lays them over the camera); here they get the club's navy
// behind them instead of the white that other pages get.
const isOverlay = (url) => {
  try {
    return new URL(url).pathname.replace(/\/$/, '') === '/live/overlay'
  } catch {
    return false
  }
}

const FOOTER_HEIGHT = 'clamp(2.5rem,9vh,7rem)'

let state = ref('loading')
let slides = ref([])
let time = ref('')
let seconds = ref('')
let weekday = ref('')
let day = ref('')
let month = ref('')
let weekNumber = ref('')

function updateDateTime() {
  const now = new Date()
  // Format time explicitly as 24h format, with seconds shown separately
  const hours = String(now.getHours()).padStart(2, '0')
  const minutes = String(now.getMinutes()).padStart(2, '0')

  weekday.value = now.toLocaleDateString('sv-SE', { weekday: 'long' })
  day.value = String(now.getDate())
  month.value = getSwedishMonthName(now.getMonth())
  weekNumber.value = getISOWeek(now)

  time.value = `${hours}:${minutes}`
  seconds.value = String(now.getSeconds()).padStart(2, '0')
}

// Update time immediately and then every second
updateDateTime()
const timeInterval = setInterval(updateDateTime, 1000)

// The screen only fetches its data once per page load, so a failed first request (no network yet when the screen
// boots, or the app slow to wake up) must be retried, or the screen would show the logo until the next publish.
const retryDelays = [5, 10, 30, 60].map((s) => s * 1000)

async function loadSlides(attempt = 0) {
  let response
  try {
    response = await axios.get('/slideshow/snapshot')
  } catch {
    setTimeout(() => loadSlides(attempt + 1), retryDelays[Math.min(attempt, retryDelays.length - 1)])
    return
  }

  slides.value = rotation(response.data.slides, response.data.records, response.data.results)
  state.value = 'loaded'
  // Start on a slide in the rotation (or the logo, when none is).
  if (keepInRotation()) {
    restartInterval()
  }
}

onMounted(() => loadSlides())

// Reload when the slides, the club records or the slideshow's code change. The app publishes a version to Firebase's
// Realtime Database on every such change and deploy (see App\Services\SlideshowSnapshot), so the screen gets it pushed
// instead of polling the app, which would keep it from scaling to zero on Laravel Cloud.
//
// The first value is only remembered, not compared with the loaded data, so that a version that failed to publish can
// never make the screen reload over and over. After a lost connection, Firebase sends the value again if it changed.
//
// A new build is published by a deploy command, which may run before the new code serves traffic, so the screen waits
// a while before reloading for one. Otherwise it could reload into the old code and then not hear of the new one.
const deployGrace = 2 * 60 * 1000
let published
let reloadTimer = null

onValue(databaseRef(liveDatabase(), 'slideshow/published'), (snapshot) => {
  const next = snapshot.val() ?? {}

  if (published !== undefined && next.version !== published.version && !reloadTimer) {
    reloadTimer = setTimeout(
      () => {
        clearTimeout(slideInterval)
        clearInterval(timeInterval)
        window.location.reload()
      },
      next.build !== published.build ? deployGrace : 0,
    )
  }

  published = next
})

function component(type) {
  if (type === 'Slide') {
    return Slide
  }

  if (type === 'Klubbrekord') {
    return KlubbrekordSlide
  }

  if (type === 'Resultat') {
    return ResultatSlide
  }

  if (type === 'Dm2026') {
    return Dm2026Slide
  }

  if (type === 'Gm2027') {
    return Gm2027Slide
  }

  if (type === 'Swish') {
    return SwishSlide
  }

  if (type === 'Lokalregler') {
    return LokalreglerSlide
  }

  return null
}

const currentSlide = ref(0)
// Each slide is shown for 30 seconds, or its own time (the competition results when they scroll).
const slideDuration = computed(() => slides.value[currentSlide.value]?.duration ?? 30 * 1000)
// Incremented on every slide change so the progress bar animation restarts
const slideCycle = ref(0)
let slideInterval = null

// The live control (/admin/slideshow): choose the slides in the rotation, hold a slide, show a message or a web page,
// step or reload.
//
// The broadcast shows the slideshow too, on command from the live overlay's remote control (/slideshow?stream=1 in the
// overlay, see resources/js/live/README.md). It follows the same control, so a held slide or a message is shown both
// in the hall and on the stream, except a web page shown on the hall's screen (e.g. the warm-up hall screen): the
// stream keeps showing the slides. It reports itself as "Sändningen", and the control page's preview of the overlay
// (&preview=1) doesn't report itself at all.
const params = new URLSearchParams(window.location.search)
const stream = params.get('stream') === '1'
const remote = useSlideshowRemote({
  presence: params.get('preview') !== '1',
  onCommand: ({ type }) => {
    if (type === 'next') {
      nextSlide()
    } else if (type === 'previous') {
      previousSlide()
    } else if (type === 'reload') {
      window.location.reload()
    }
  },
})
const control = stream ? computed(() => ({ ...remote.control.value, page: null })) : remote.control
const report = stream ? (entry) => remote.report({ ...entry, showing: `Sändningen: ${entry.showing}` }) : remote.report
const { serverNow } = remote

// The server's time, for taking down a message that clears by itself (the screen's own clock may be off).
const serverTime = ref(serverNow())
setInterval(() => (serverTime.value = serverNow()), 1000)

const heldIndex = computed(() =>
  control.value.slide ? slides.value.findIndex((slide) => slideKey(slide) === control.value.slide) : -1,
)
// The rotation stops while paused, while a slide is held (as long as that slide is still in the slideshow), or while a
// web page is shown instead.
const held = computed(() => control.value.paused || heldIndex.value >= 0 || !!control.value.page)
// The clock on its own, bottom right, when the bottom bar is hidden.
const floatingClock = computed(() => !control.value.footer && control.value.clock)
// No slide in the rotation and none held: the club's logo is shown.
const showLogo = computed(() => state.value === 'loaded' && currentSlide.value < 0 && !control.value.page)
// A slide held from the live control is shown over a web page.
const slideOverPage = computed(() => !!control.value.page && heldIndex.value >= 0)

watch([heldIndex, held], () => {
  if (heldIndex.value >= 0) {
    currentSlide.value = heldIndex.value
  } else {
    // A slide outside the rotation was let go: on with the rotation right away.
    keepInRotation()
  }
  restartInterval()
})

// When the rotation is changed from the live control, a slide taken out of it gives way at once (unless it is held).
watch(
  () => control.value.rotation,
  () => {
    if (keepInRotation()) {
      restartInterval()
    }
  },
)

// Moves on from a slide that is not in the rotation (or from the logo); whether it moved.
function keepInRotation() {
  if (held.value || !slides.value.length) {
    return false
  }
  const slide = slides.value[currentSlide.value]
  if (slide && inRotation(slide, control.value)) {
    return false
  }
  currentSlide.value = nextInRotation(slides.value, startOf(1), 1, control.value)
  return true
}

// Where stepping starts from: the current slide, or (on the logo) just before the first or after the last one.
function startOf(direction) {
  if (currentSlide.value >= 0) {
    return currentSlide.value
  }
  return direction > 0 ? slides.value.length - 1 : 0
}

watch([currentSlide, slides, held, () => control.value.page, slideOverPage], () => {
  const slide = slides.value[currentSlide.value]
  if (control.value.page && !slideOverPage.value) {
    report({ slide: PAGE_KEY, showing: pageLabel(control.value.page.url), held: true })
  } else if (slide) {
    report({ slide: slideKey(slide), showing: slideLabel(slide), held: held.value })
  } else if (state.value === 'loaded') {
    report({ slide: '', showing: 'Klubbens logga (inga slides i bildspelet)', held: false })
  }
})

function nextSlide() {
  step(1)
}

function previousSlide() {
  step(-1)
}

// Steps through the rotation, skipping the slides that are not in it.
function step(direction) {
  if (slides.value.length) {
    currentSlide.value = nextInRotation(slides.value, startOf(direction), direction, control.value)
  }
  restartInterval()
}

restartInterval()
function restartInterval() {
  clearTimeout(slideInterval)
  if (!held.value) {
    // Every slide change restarts this, so a timeout of the current slide's time.
    slideInterval = setTimeout(nextSlide, slideDuration.value)
  }
  slideCycle.value++
}

document.body.addEventListener('keydown', (e) => {
  if (e.key === 'ArrowRight') {
    nextSlide()
  } else if (e.key === 'ArrowLeft') {
    previousSlide()
  }
})
</script>

<style scoped>
.slideshow-fade-enter-active,
.slideshow-fade-leave-active {
  transition:
    opacity 700ms ease,
    transform 700ms ease;
}

.slideshow-fade-enter-from {
  opacity: 0;
  transform: scale(1.02);
}

.slideshow-fade-leave-to {
  opacity: 0;
  transform: scale(0.98);
}

.slideshow-progress {
  animation-name: slideshow-progress;
  animation-timing-function: linear;
  animation-fill-mode: forwards;
}

.slideshow-message-enter-active,
.slideshow-message-leave-active {
  transition:
    opacity 400ms ease,
    transform 400ms ease;
}

.slideshow-message-enter-from,
.slideshow-message-leave-to {
  opacity: 0;
  transform: translateY(100%);
}

@keyframes slideshow-progress {
  from {
    transform: scaleX(0);
  }
  to {
    transform: scaleX(1);
  }
}
</style>
