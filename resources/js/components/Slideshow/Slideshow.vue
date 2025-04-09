<template>
        <ImageSlide v-if="slide == 0" imageUrl="https://goteborg-kraftsportklubb.web.app/img/logo-min.png" />
        <TextSlide v-if="slide == 1" text="GKKs slideshow - kommer snart!" />
        <ImageSlide v-if="slide == 2" imageUrl="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fsta%CC%88dschema.png?alt=media&token=8d8e4060-08a2-40df-804f-b4cc6b26467a" />
        <TextSlide v-if="slide == 3" text="https://goteborgkk.se" />
</template>

<script setup>
import { ref } from 'vue'
import ImageSlide from './ImageSlide.vue'
import TextSlide from './TextSlide.vue'

const slide = ref(0)

const interval = setInterval(() => {
    slide.value = (slide.value + 1) % 4
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