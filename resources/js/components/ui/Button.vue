<template>
  <button
    class="inline-flex items-center px-4 py-2 border leading-5 font-medium rounded-md focus:outline-none  transition duration-150 ease-in-out"
    :class="classes"
  >
    <slot></slot>
  </button>
</template>

<script setup>
import { computed } from 'vue'

const { type = 'primary' } = defineProps({
  type: String,
})

const classes = computed(() => {
  if (type === 'secondary') {
    return 'bg-white text-gkk border-gkk hover:bg-gray-200 text-sm active:bg-gkk'
  }

  if (type === 'danger') {
    return 'bg-[#f8868a] text-white border-[#f8868a] hover:bg-[#c7453d] text-2xs focus:border-[#c7453d] active:bg-[#c7453d]'
  }

  if (type === 'warning') {
    return 'bg-[#f8c68a] text-white border-[#f8c68a] hover:bg-[#c7a53d] text-2xs focus:border-[#c7a53d] active:bg-[#c7a53d]'
  }

  return 'bg-gkk hover:bg-gkk-light text-white border-transparent text-sm active:bg-gkk'
})
</script>
