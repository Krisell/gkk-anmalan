<template>
  <div
    class="fixed inset-0 flex flex-col overflow-hidden select-none cursor-none bg-linear-to-br from-slate-50 via-white to-slate-100"
    :style="{ '--footer-h': control.footer ? FOOTER_HEIGHT : '0px' }"
  >
    <main class="relative flex-1 min-h-0">
      <Transition name="slideshow-fade">
        <div v-if="state == 'loading'" class="absolute inset-0 flex items-center justify-center p-[8vh]">
          <img
            class="max-w-[60%] max-h-full object-contain animate-pulse"
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
          class="absolute inset-0 z-10 w-full h-full border-0 bg-white"
          sandbox="allow-scripts allow-same-origin allow-forms allow-popups"
          allow="autoplay; fullscreen"
          referrerpolicy="no-referrer"
          title="Webbsida"
        ></iframe>
      </Transition>
    </main>

    <!-- A message from the live control (/admin/slideshow), shown until it is taken down. -->
    <Transition name="slideshow-message">
      <div
        v-if="control.message && !hidesMessage"
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
          v-if="slides.length && !held"
          :key="slideCycle"
          class="slideshow-progress h-full bg-white/70 origin-left"
          :style="{ animationDuration: `${slideDuration}ms` }"
        ></div>
      </div>

      <div class="flex items-baseline gap-[1vw] min-w-0 leading-none">
        <span class="text-[min(2.2vw,3.8vh)] font-semibold capitalize whitespace-nowrap">{{ weekday }}</span>
        <span class="text-[min(2.2vw,3.8vh)] font-light whitespace-nowrap">{{ day }} {{ month }}</span>
        <span
          class="rounded-full bg-white/15 px-[0.8vw] py-[0.5vh] text-[min(1.3vw,2.3vh)] font-medium uppercase tracking-wider whitespace-nowrap"
        >
          Vecka {{ weekNumber }}
        </span>
      </div>

      <div class="flex items-center gap-[0.6vw]">
        <template v-for="(slide, index) in slides.length > 1 && !control.page ? slides : []" :key="index">
          <span
            v-if="!slide.manual"
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

    <!-- A flash from the live control, over everything (also a web page): first the whole screen, then down where the
         message bar is, then gone. -->
    <Transition name="slideshow-flash">
      <div
        v-if="flash"
        :key="flash.id"
        class="slideshow-flash absolute inset-x-0 z-50 flex items-center justify-center overflow-hidden bg-amber-300 text-center text-gkk shadow-[0_-1vh_4vh_rgba(0,0,0,0.15)]"
        :class="[
          { 'slideshow-flash-bar': flash.phase === 'bar' },
          floatingClock && flash.phase === 'bar' ? 'px-[15vw]' : 'px-[5vw]',
        ]"
      >
        <span class="slideshow-flash-text font-bold leading-tight text-balance">{{ flash.text }}</span>
      </div>
    </Transition>

    <!-- The clock on its own, when the bottom bar is hidden from the live control: bottom right, over everything
         (a web page, the message bar and a flash). -->
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
  FLASH_BAR_MS,
  FLASH_FULL_MS,
  PAGE_KEY,
  flashText,
  nextInRotation,
  pageLabel,
  rotation,
  slideKey,
  slideLabel,
  useSlideshowRemote,
} from './remote.js'

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

// The live control (/admin/slideshow): pause the rotation, hold a slide, show a message or a web page, step or
// reload.
const { control, report } = useSlideshowRemote({
  onCommand: (command) => {
    const { type } = command
    if (type === 'flash') {
      showFlash(flashText(command))
    } else if (type === 'clearFlash') {
      clearFlash()
    } else if (type === 'next') {
      nextSlide()
    } else if (type === 'previous') {
      previousSlide()
    } else if (type === 'reload') {
      window.location.reload()
    }
  },
})

const heldIndex = computed(() =>
  control.value.slide ? slides.value.findIndex((slide) => slideKey(slide) === control.value.slide) : -1,
)
// The rotation stops while paused, while a slide is held (as long as that slide is still in the slideshow), or while a
// web page is shown instead.
const held = computed(() => control.value.paused || heldIndex.value >= 0 || !!control.value.page)
// The clock on its own, bottom right, when the bottom bar is hidden.
const floatingClock = computed(() => !control.value.footer && control.value.clock)
// Some slides (the DM welcome) use the whole screen, without the message bar.
// A slide held from the live control is shown over a web page.
const slideOverPage = computed(() => !!control.value.page && heldIndex.value >= 0)
const hidesMessage = computed(
  () => (!control.value.page || slideOverPage.value) && !!slides.value[currentSlide.value]?.hidesMessage,
)

watch([heldIndex, held], () => {
  if (heldIndex.value >= 0) {
    currentSlide.value = heldIndex.value
  } else if (slides.value[currentSlide.value]?.manual) {
    // A manual slide was let go: on with the rotation right away.
    currentSlide.value = nextInRotation(slides.value, currentSlide.value)
  }
  restartInterval()
})

watch([currentSlide, slides, held, () => control.value.page, slideOverPage], () => {
  const slide = slides.value[currentSlide.value]
  if (control.value.page && !slideOverPage.value) {
    report({ slide: PAGE_KEY, showing: pageLabel(control.value.page.url), held: true })
  } else if (slide) {
    report({ slide: slideKey(slide), showing: slideLabel(slide), held: held.value })
  }
})

function nextSlide() {
  step(1)
}

function previousSlide() {
  step(-1)
}

// Steps through the rotation; a manual slide (shown only while held) is skipped.
function step(direction) {
  if (slides.value.length) {
    currentSlide.value = nextInRotation(slides.value, currentSlide.value, direction)
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

// A flash: over the whole screen first, then down in the message bar for a while, then gone. A new one replaces it.
const flash = ref(null)
let flashTimers = []

function clearFlash() {
  flashTimers.forEach(clearTimeout)
  flashTimers = []
  flash.value = null
}

function showFlash(text) {
  if (!text) {
    return
  }
  flashTimers.forEach(clearTimeout)
  const id = `${Date.now()}`
  flash.value = { id, text, phase: 'full' }
  flashTimers = [
    setTimeout(() => (flash.value = { id, text, phase: 'bar' }), FLASH_FULL_MS),
    setTimeout(() => (flash.value = null), FLASH_FULL_MS + FLASH_BAR_MS),
  ]
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

/* The flash fills the screen above the bottom bar, then shrinks down to a bar where the message bar is. */
.slideshow-flash {
  --flash-bar-h: 12vh;
  top: 0;
  bottom: var(--footer-h);
  transition: top 1200ms cubic-bezier(0.65, 0, 0.35, 1);
}

.slideshow-flash-bar {
  top: calc(100% - var(--footer-h) - var(--flash-bar-h));
}

.slideshow-flash-text {
  font-size: min(7vw, 13vh);
  transition: font-size 1200ms cubic-bezier(0.65, 0, 0.35, 1);
}

.slideshow-flash-bar .slideshow-flash-text {
  font-size: min(3.2vw, 5.4vh);
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  overflow: hidden;
}

.slideshow-flash-enter-active {
  transition:
    opacity 500ms ease,
    transform 500ms ease;
}

.slideshow-flash-leave-active {
  transition:
    opacity 600ms ease,
    transform 600ms ease;
}

.slideshow-flash-enter-from {
  opacity: 0;
  transform: scale(1.04);
}

.slideshow-flash-leave-to {
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
