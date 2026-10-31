<template>
  <div
    class="fixed inset-0 flex flex-col overflow-hidden select-none cursor-none bg-linear-to-br from-slate-50 via-white to-slate-100"
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
          <Component
            :is="component(slide.type)"
            v-bind="slide.data"
            v-show="currentSlide == index"
            class="absolute inset-0 w-full h-full"
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

    <!-- A message from the live control (/admin/slideshow/live), shown until it is taken down. -->
    <Transition name="slideshow-message">
      <div
        v-if="control.message"
        :key="control.message.id"
        class="shrink-0 bg-amber-300 text-gkk px-[4vw] py-[2vh] text-center font-semibold leading-tight text-balance text-[min(3.4vw,5.8vh)]"
      >
        {{ control.message.text }}
      </div>
    </Transition>

    <!-- Kept while a web page is shown, so the screen still has the date and time. -->
    <footer
      class="relative shrink-0 h-[clamp(2.5rem,9vh,7rem)] bg-gkk text-white grid grid-cols-[1fr_auto_1fr] items-center gap-[2vw] px-[2.5vw]"
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
        <span
          v-for="(slide, index) in slides.length > 1 && !control.page ? slides : []"
          :key="index"
          class="h-[1vh] min-h-1.5 rounded-full transition-all duration-500"
          :class="currentSlide == index ? 'w-[2.5vw] bg-white' : 'w-[1vh] min-w-1.5 bg-white/35'"
        ></span>
      </div>

      <div class="flex items-baseline justify-end leading-none tabular-nums whitespace-nowrap">
        <span class="text-[min(3vw,5vh)] font-semibold">{{ time }}</span>
        <span class="text-[min(1.6vw,2.8vh)] font-light text-white/60 ml-[0.3vw]">{{ seconds }}</span>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue'
import Slide from './Slide.vue'
import KlubbrekordSlide from './KlubbrekordSlide.vue'
import { getISOWeek, getSwedishMonthName } from '../../utils/date'
import { onValue, ref as databaseRef } from 'firebase/database'
import { liveDatabase } from '../../live/remote/firebase.js'
import axios from 'axios'
import { PAGE_KEY, pageLabel, slideKey, slideLabel, useSlideshowRemote } from './remote.js'

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

  slides.value = response.data.slides.filter((slide) => slide.is_visible)
  // The latest club records are always shown as the first slide in the rotation.
  slides.value.unshift({ type: 'Klubbrekord', data: { records: response.data.records } })
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
        clearInterval(slideInterval)
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

  return null
}

const currentSlide = ref(0)
const slideDuration = 30 * 1000
// Incremented on every slide change so the progress bar animation restarts
const slideCycle = ref(0)
let slideInterval = null

// The live control (/admin/slideshow/live): pause the rotation, hold a slide, show a message or a web page, step or
// reload.
const { control, report } = useSlideshowRemote({
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

const heldIndex = computed(() =>
  control.value.slide ? slides.value.findIndex((slide) => slideKey(slide) === control.value.slide) : -1,
)
// The rotation stops while paused, while a slide is held (as long as that slide is still in the slideshow), or while a
// web page is shown instead.
const held = computed(() => control.value.paused || heldIndex.value >= 0 || !!control.value.page)

watch([heldIndex, held], () => {
  if (heldIndex.value >= 0) {
    currentSlide.value = heldIndex.value
  }
  restartInterval()
})

watch([currentSlide, slides, held, () => control.value.page], () => {
  const slide = slides.value[currentSlide.value]
  if (control.value.page) {
    report({ slide: PAGE_KEY, showing: pageLabel(control.value.page.url), held: true })
  } else if (slide) {
    report({ slide: slideKey(slide), showing: slideLabel(slide), held: held.value })
  }
})

function nextSlide() {
  if (!slides.value.length) {
    return
  }
  currentSlide.value = (currentSlide.value + 1) % slides.value.length
  restartInterval()
}

function previousSlide() {
  if (!slides.value.length) {
    return
  }
  currentSlide.value = (currentSlide.value - 1 + slides.value.length) % slides.value.length
  restartInterval()
}

restartInterval()
function restartInterval() {
  clearInterval(slideInterval)
  if (!held.value) {
    slideInterval = setInterval(nextSlide, slideDuration)
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
