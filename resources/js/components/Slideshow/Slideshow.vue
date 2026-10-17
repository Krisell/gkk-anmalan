<template>
  <div
    class="fixed inset-0 flex flex-col overflow-hidden select-none bg-linear-to-br from-slate-50 via-white to-slate-100"
    :class="{ 'cursor-default': inIframe, 'cursor-none': !inIframe }"
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
    </main>

    <footer
      class="relative shrink-0 h-[clamp(2.5rem,9vh,7rem)] bg-gkk text-white grid grid-cols-[1fr_auto_1fr] items-center gap-[2vw] px-[2.5vw]"
    >
      <div class="absolute top-0 inset-x-0 h-[0.5vh] min-h-[3px] bg-white/10">
        <div
          v-if="slides.length"
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
          v-for="(slide, index) in slides.length > 1 ? slides : []"
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
import { ref, onMounted } from 'vue'
import Slide from './Slide.vue'
import KlubbrekordSlide from './KlubbrekordSlide.vue'
import axios from 'axios'
import { getISOWeek, getSwedishMonthName } from '../../utils/date'

let state = ref('loading')
let slides = ref([])
let slidesHash = ref('')
let time = ref('')
let seconds = ref('')
let weekday = ref('')
let day = ref('')
let month = ref('')
let weekNumber = ref('')
let inIframe = window.self !== window.top

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

onMounted(async () => {
  const response = await axios.get('/slideshow/slides')
  slides.value = response.data.slides.filter((slide) => slide.is_visible)
  // The latest club records are always shown as the first slide in the rotation.
  slides.value.unshift({ type: 'Klubbrekord', data: {} })
  slidesHash.value = response.data.hash
  state.value = 'loaded'
})

setInterval(async () => {
  const response = await axios.get('/slideshow/slides')

  // If Slideshow has changed, reload to get recent version
  if (response.data.hash !== slidesHash.value) {
    location.reload()
  }
}, 10 * 1000)

function component(type) {
  if (type === 'Slide') {
    return Slide
  }

  if (type === 'Klubbrekord') {
    return KlubbrekordSlide
  }

  return null
}

setInterval(() => {
  axios.post('/slideshow/log', { id: localStorage.getItem('id') })
}, 60 * 1000)

const currentSlide = ref(0)
const slideDuration = 30 * 1000
// Incremented on every slide change so the progress bar animation restarts
const slideCycle = ref(0)
let slideInterval = null

function nextSlide() {
  currentSlide.value = (currentSlide.value + 1) % slides.value.length
  restartInterval()
}

function previousSlide() {
  currentSlide.value = (currentSlide.value - 1 + slides.value.length) % slides.value.length
  restartInterval()
}

restartInterval()
function restartInterval() {
  clearInterval(slideInterval)
  slideInterval = setInterval(nextSlide, slideDuration)
  slideCycle.value++
}

document.body.addEventListener('keydown', (e) => {
  if (e.key === 'ArrowRight') {
    nextSlide()
  } else if (e.key === 'ArrowLeft') {
    previousSlide()
  }
})

// Listen for postMessage navigation from parent frame
window.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'slideshow-control') {
    if (event.data.action === 'next') {
      nextSlide()
    } else if (event.data.action === 'previous') {
      previousSlide()
    }
  }
})

// Reload page after 10 minutes. This might break the slideshow if the device is currently
// offline. That will be solved by adding a service worker for offline loading. It also looks
// like Chrome automatically tries to reloads the page while in the offline state.
setTimeout(
  () => {
    clearInterval(slideInterval)
    clearInterval(timeInterval)
    window.location.reload()
  },
  10 * 60 * 1000,
)
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

@keyframes slideshow-progress {
  from {
    transform: scaleX(0);
  }
  to {
    transform: scaleX(1);
  }
}
</style>
