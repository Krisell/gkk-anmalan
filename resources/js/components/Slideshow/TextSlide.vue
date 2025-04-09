<template>
    <div class="flex flex-col items-center justify-center h-screen">
        <div class="text-6xl text-center text-gkk font-thin">
            {{ text }}
        </div>
    </div>
</template>

<script setup>
const { text } = defineProps({
  text: {
    type: String,
    required: true
  }
})

</script>