<template>
    <div v-if="state == 'loading'" :class="{ 'cursor-default': inIframe, 'cursor-none': !inIframe }">
        <div class="flex flex-col items-center justify-center h-screen gap-4 max-h-screen">
            <img class="max-w-[97%] max-h-[97%] overflow-auto" src="https://goteborg-kraftsportklubb.web.app/img/logo-min.png" />
        </div>
    </div>
    <div :class="{ 'cursor-default': inIframe, 'cursor-none': !inIframe }">
        <div class="fixed bottom-3 left-0 p-2">
            <div class="bg-white p-2 rounded-lg shadow-md text-2xl text-center text-gkk">
                <div class="min-w-32">{{ day }} {{ month }}</div>
                <div class="text-xl">vecka {{ weekNumber }}</div>
            </div>
        </div>
        
        <div class="fixed bottom-3 right-0 p-2">
            <div class="bg-white p-2 rounded-lg shadow-md text-2xl text-center text-gkk">
                <div class="min-w-32">{{ time }}</div>
                <div>{{ currentSlide + 1 }} / {{ slides.length }}</div>
            </div>
        </div>

        <template v-for="(slide, index) in slides" :key="index">
            <Component :is="component(slide.type)" v-bind="slide.data" v-show="currentSlide == index" class="w-full h-full" />
        </template>

        <div 
            class="fixed bottom-0 left-0 h-3 bg-gkk opacity-60 transition-all"
            :style="{ width: `${progress}%` }">
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Slide from './Slide.vue'
import axios from 'axios'
import { getISOWeek, getSwedishMonthName } from '../../utils/date'

let state = ref('loading')
let slides = ref([])
let slidesHash = ref('')
let time = ref('')
let day = ref('')
let month = ref('')
let weekNumber = ref('')
let inIframe = window.self !== window.top

function updateDateTime() {
    const now = new Date()
    // Format time explicitly as 24h format with hours, minutes, and seconds
    const hours = String(now.getHours()).padStart(2, '0')
    const minutes = String(now.getMinutes()).padStart(2, '0')
    const seconds = String(now.getSeconds()).padStart(2, '0')

    day.value = String(now.getDate())
    month.value = getSwedishMonthName(now.getMonth())
    weekNumber.value = getISOWeek(now)
    
    time.value = `${hours}:${minutes}:${seconds}`
}

// Update time immediately and then every second
updateDateTime();
const timeInterval = setInterval(updateDateTime, 1000);

onMounted(async () => {
    const response = await axios.get('/slideshow/slides')
    slides.value = response.data.slides.filter(slide => slide.is_visible)
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

    return null
}

setInterval(() => {
    axios.post('/slideshow/log', { id: localStorage.getItem('id') })
}, 60 * 1000)

const currentSlide = ref(0)
let startTime = Date.now()
let slideInterval = null
let progressInterval = null
let progress = ref(0)

function nextSlide() {
    currentSlide.value = (currentSlide.value + 1) % slides.value.length
    startTime = Date.now()
    restartInterval()
}

function previousSlide() {
    currentSlide.value = (currentSlide.value - 1 + slides.value.length) % slides.value.length
    startTime = Date.now()
    restartInterval()
}

restartInterval()
function restartInterval() {
    clearInterval(slideInterval)
    clearInterval(progressInterval)
    slideInterval = setInterval(nextSlide, 30 * 1000)

    progress.value = 0
    progressInterval = setInterval(() => {
        progress.value += 1/6
    }, 50)
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
setTimeout(() => {
    clearInterval(slideInterval)
    clearInterval(progressInterval)
    clearInterval(timeInterval)
    window.location.reload()
}, 10 * 60 * 1000)

</script>
