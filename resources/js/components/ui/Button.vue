<template>
  <button
    class="inline-flex items-center px-4 py-2 border leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out"
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
    return 'bg-white text-gkk border-gkk hover:bg-gray-200 text-sm focus:border-indigo-600 active:bg-gkk'
  }

  if (type === 'danger') {
    return 'bg-[#f8868a] text-white border-[#f8868a] hover:bg-[#c7453d] text-2xs focus:border-[#c7453d] active:bg-[#c7453d]'
  }

  return 'bg-gkk hover:bg-gkk-light text-white border-transparent text-sm focus:border-indigo-600 active:bg-gkk'
})
</script>
