<template>
    <div class="flex flex-col items-center justify-center h-screen gap-4 max-h-screen">
      <div v-if="text" class="text-6xl text-center text-gkk font-thin mt-4">
            {{ text }}
      </div> 
      <div v-if="subText" class="text-2xl text-center text-gkk font-thin">
            {{ subText }}
      </div>
      <img v-if="image" class="max-w-[97%] max-h-[97%] overflow-auto mb-4" :src="image">
    </div>
</template>

<script setup>
const { image, text } = defineProps({
  image: {
    type: String,
    required: false
  },
  text: {
    type: String,
    required: false
  },
  subText: {
    type: String,
    required: false
  }
})

</script>