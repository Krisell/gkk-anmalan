<template>
  <div class="flex mt-4 w-full">
    <Button @click="location(to)" class="mx-auto">
      <div class="w-full text-center">
        {{ text }}
      </div>
    </Button>
  </div>
</template>

<script setup>
import Button from './Button.vue'

defineProps({
  to: String,
  text: String,
})
</script>
