<template>
  <div class="flex flex-col items-center justify-center h-full gap-[2.5vh] px-[4vw] py-[4vh]">
    <div v-if="text" class="flex flex-col items-center gap-[1.5vh] shrink-0">
      <div class="h-[0.6vh] min-h-1 w-[6vw] rounded-full bg-gkk/80"></div>
      <h1
        class="text-center text-gkk font-semibold tracking-tight leading-tight text-balance"
        :class="image ? 'text-[min(4.5vw,7.5vh)]' : 'text-[min(6.5vw,11vh)] max-w-[85%]'"
      >
        {{ text }}
      </h1>
    </div>
    <p
      v-if="subText"
      class="shrink-0 text-center text-gkk-light font-light text-balance"
      :class="image ? 'text-[min(2.2vw,3.8vh)]' : 'text-[min(3vw,5vh)] max-w-[75%]'"
    >
      {{ subText }}
    </p>
    <div v-if="image" class="flex-1 min-h-0 w-full flex items-center justify-center">
      <img class="max-w-full max-h-full object-contain rounded-xl drop-shadow-xl" :src="image" alt="" />
    </div>
  </div>
</template>

<script setup>
const { image, text } = defineProps({
  image: {
    type: String,
    required: false,
  },
  text: {
    type: String,
    required: false,
  },
  subText: {
    type: String,
    required: false,
  },
})
</script>
