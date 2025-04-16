<template>
  <div class="container mx-auto pl-6 -mt-12">
    <p class="text-left -mt-6 text-3xl mb-4">Administrera Slideshow</p>
    <div class="flex flex-col justify-center mx-auto gap-4 text-center">
      <TransitionGroup name="list" tag="div" class="flex flex-col gap-4">
        <div v-for="(slide, index) in slides" :key="slide.data" class="relative flex gap-6">
          <div class="flex flex-col items-center justify-center gap-3 text-2xl text-gkk rounded shadow bg-white p-4">
            <i 
              class="fa fa-arrow-up hover:scale-110 transition-all"
              :class="{ 'text-gray-400': index === 0, 'cursor-pointer': index !== 0 }"
              v-tooltip="'Flytta upp'"
              @click="moveByIndex(index, -1)"
            ></i>
            <i 
              class="cursor-pointer fa fa-pencil hover:scale-110 transition-all"
              v-tooltip="'Redigera'"
            ></i>
            <i 
              class="fa fa-arrow-down hover:scale-110 transition-all" 
              :class="{ 'text-gray-400': index === slides.length - 1, 'cursor-pointer': index !== slides.length - 1 }"
              v-tooltip="'Flytta ner'"
              @click="moveByIndex(index, 1)"
            ></i>
          </div>
          <div class="rounded shadow bg-white p-4 flex flex-col items-center justify-center">
            <p v-if="slide.data.text">{{ slide.data.text }}</p>
            <p v-if="slide.data.subText" class="text-sm text-gray-500">{{  slide.data.subText }}</p>
            <a v-if="slide.data.image" :href="slide.data.image" target="_blank" class="block mt-2 text-blue-500 hover:underline">
              <img :src="slide.data.image" alt="Slide Image" class="mt-2 rounded w-64 mx-auto">
            </a>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </div>
</template>

<script setup>
import { useToast } from 'vue-toast-notification'
import 'vue-toast-notification/dist/theme-sugar.css'
import { ref, onMounted } from 'vue'
import axios from 'axios'

const $toast = useToast();
const slides = ref([])

async function moveByIndex(index, direction) {
  const newIndex = index + direction
  if (newIndex < 0 || newIndex >= slides.value.length) return

  const temp = slides.value[index]
  slides.value[index] = slides.value[newIndex]
  slides.value[newIndex] = temp

  try {
    await axios.post('/slideshow/order', {
      slides: slides.value.map((slide, index) => ({
        id: slide.id,
        order: index
      }))
    })

    $toast.success(`Flyttade slide ${index + 1} ${direction > 0 ? 'ner' : 'upp'}`, {
      duration: 2000,
      type: 'success'
    })
  } catch (error) {
    $toast.error('Sliden kunde inte flyttas', {
      duration: 2000,
      type: 'error'
    })
    // Revert the changes if the request fails
    slides.value[newIndex] = slides.value[index]
    slides.value[index] = temp
    return
  }
}

onMounted(async () => {
  const response = await axios.get('/slideshow/slides')
  slides.value = response.data.slides
})
</script>

<style scoped>
.list-move, /* apply transition to moving elements */
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

/* ensure leaving items are taken out of layout flow so that moving
   animations can be calculated correctly. */
.list-leave-active {
  position: absolute;
}
</style>
