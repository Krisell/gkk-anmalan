<template>
    <div class="cursor-none">
        <ImageSlide v-if="slide == 0" imageUrl="https://goteborg-kraftsportklubb.web.app/img/logo-min.png" />
        <TextSlide v-if="slide == 1" text="GKKs slideshow - kommer snart!" />
        <ImageSlide v-if="slide == 2" imageUrl="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fsta%CC%88dschema.png?alt=media&token=8d8e4060-08a2-40df-804f-b4cc6b26467a" />
        <TextSlide v-if="slide == 3" text="goteborgkk.se" />
        <ImageSlide v-if="slide == 4" imageUrl="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FLokalregler.png?alt=media&token=f6eacaf7-01cb-4983-81d8-5549679592e4" />
        <ImageSlide v-if="slide == 5" imageUrl="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FVa%CC%88lkommen.png?alt=media&token=448520ae-092b-43d6-b72e-4092cc55e435" />

        <!-- Add loading bar at the bottom, covering the full width -->
        <div class="fixed bottom-0 left-0 w-full h-2 bg-gray-300">
            <div class="h-full bg-gkk transition-all duration-500" :style="{ width: (slide + 1) * (1/6) * 100 + '%' }"></div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import ImageSlide from './ImageSlide.vue'
import TextSlide from './TextSlide.vue'
import axios from 'axios'

setInterval(() => {
    axios.post('/slideshow/log', { id: localStorage.getItem('id') })
}, 60 * 1000)

const slide = ref(0)

const interval = setInterval(() => {
    slide.value = (slide.value + 1) % 6
}, 5000)

// Reload page after 30 minutes. This might break the slideshow if the device is currently
// offline. That will be solved by adding a service worker for offline loading.
setTimeout(() => {
    clearInterval(interval)
    window.location.reload()
}, 30 * 60 * 1000)

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