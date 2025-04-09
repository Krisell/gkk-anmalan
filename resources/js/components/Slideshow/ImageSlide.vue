<template>
    <div class="flex flex-col items-center justify-center h-screen">
        <img class="max-w-[80%] max-h-[80%]" :src="imageUrl">
    </div>
</template>

<script setup>
const { imageUrl } = defineProps({
  imageUrl: {
    type: String,
    required: true
  }
})

</script>