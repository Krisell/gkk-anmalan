<template>
    <div class="cursor-none">
        <div class="fixed bottom-0 right-0 p-4 text-gkk text-3xl opacity-70">
            {{ currentSlide + 1 }} / {{ slides.length }}
        </div>

        <template v-for="(slide, index) in slides">
            <Component :is="slide.type" v-bind="slide.props" v-if="currentSlide == index" :key="index" class="w-full h-full" />
        </template>

        <div 
            class="fixed bottom-0 left-0 h-3 bg-gkk opacity-60 transition-all"
            :style="{ width: `${progress}%` }">
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import Slide from './Slide.vue'
import axios from 'axios'

const slides = [
    { id: 0, type: Slide, props: { image: 'https://goteborg-kraftsportklubb.web.app/img/logo-min.png' } },
    { id: 1, type: Slide, props: { text: 'GKKs slideshow - kommer snart!' } },
    { id: 1, type: Slide, props: { text: 'Glöm inte anmäla er som funktionär till serie 2!' } },
    { id: 2, type: Slide, props: { text: 'Städschema', image: 'https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fsta%CC%88dschema.png?alt=media&token=8d8e4060-08a2-40df-804f-b4cc6b26467a' } },
    { id: 4, type: Slide, props: { image: 'https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FLokalregler.png?alt=media&token=f6eacaf7-01cb-4983-81d8-5549679592e4' } },
    { id: 6, type: Slide, props: { text: 'Veckans meme', image: 'https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2F27FE51DA-89FC-43DE-BB99-087315ADBC52.jpg?alt=media&token=e94405b0-5483-4705-b0d6-9c86f37d6a09' } },
    { id: 7, type: Slide, props: { text: 'Klubblokalen januari 2019', subText: 'Före golvet gjordes om', image: 'https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FlokalenJanuari2019.png?alt=media&token=499ede71-8975-4c52-a4cf-fb3bff2ec810' } },
]

setInterval(() => {
    axios.post('/slideshow/log', { id: localStorage.getItem('id') })
}, 60 * 1000)

const currentSlide = ref(0)
let startTime = Date.now()
let slideInterval = null
let progressInterval = null
let progress = ref(0)

function nextSlide() {
    currentSlide.value = (currentSlide.value + 1) % slides.length
    startTime = Date.now()
    restartInterval()
}

function previousSlide() {
    currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length
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

// Reload page after 10 minutes. This might break the slideshow if the device is currently
// offline. That will be solved by adding a service worker for offline loading. It also looks
// like Chrome automatically reloads the page in the offline state.
setTimeout(() => {
    clearInterval(interval)
    window.location.reload()
}, 10 * 60 * 1000)

</script>

<style scoped>
/* we will explain what these classes do next! */
.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}</style>