<template>
  <label :for="id" class="inline-flex relative items-center cursor-pointer">
    <input type="checkbox" v-model="enabled" :disabled :id="id" class="sr-only peer" />
    <div
      class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-gkk"
    ></div>
    <slot></slot>
  </label>
</template>

<script setup>
import { useId } from 'vue'

const id = useId()

const { disabled = false } = defineProps({
  disabled: Boolean,
})

const enabled = defineModel()
</script>
