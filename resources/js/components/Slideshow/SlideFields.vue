<template>
  <div class="space-y-4">
    <label class="block">
      <span class="mb-1 block text-sm font-medium text-gray-700">Rubrik</span>
      <input v-model="model.text" type="text" placeholder="T.ex. DM i helgen" :class="inputClass" />
    </label>
    <label class="block">
      <span class="mb-1 block text-sm font-medium text-gray-700">Undertext</span>
      <input v-model="model.subText" type="text" placeholder="T.ex. Kom och heja!" :class="inputClass" />
    </label>

    <div>
      <span class="mb-1 block text-sm font-medium text-gray-700">Bild</span>
      <div v-if="model.image" class="flex items-center gap-3 rounded-lg p-2 ring-1 ring-gray-200">
        <img :src="model.image" alt="" class="h-12 w-20 rounded-md bg-gray-100 object-cover" />
        <span class="flex-1 text-sm text-gray-600">Bild vald</span>
        <button
          type="button"
          class="rounded-md px-2.5 py-1.5 text-sm text-red-600 transition hover:bg-red-50"
          @click="model.image = ''"
        >
          Ta bort
        </button>
      </div>
      <label
        v-else
        class="flex cursor-pointer flex-col items-center justify-center gap-1 rounded-lg border-2 border-dashed border-gray-300 px-4 py-5 text-center transition hover:border-gkk hover:bg-gkk/5"
        :class="{ 'pointer-events-none opacity-60': uploading }"
      >
        <LoaderCircle v-if="uploading" class="size-6 animate-spin text-gkk-lightest" />
        <ImagePlus v-else class="size-6 text-gkk-lightest" />
        <span class="text-sm font-medium text-gkk">{{ uploading ? 'Laddar upp…' : 'Välj en bild' }}</span>
        <span v-if="!uploading" class="text-xs text-gray-500">Valfritt. JPG eller PNG.</span>
        <input type="file" accept="image/*" class="sr-only" @change="choose" />
      </label>
    </div>

    <p class="text-xs text-gray-500">Fyll i minst en av rubrik, undertext och bild.</p>
  </div>
</template>

<script setup>
import { ImagePlus, LoaderCircle } from 'lucide-vue-next'

// The fields of a slide (text, subText, image), for creating and editing one. The image is uploaded by the parent.
const model = defineModel({ type: Object, required: true })
defineProps({ uploading: { type: Boolean, default: false } })
const emit = defineEmits(['upload'])

const inputClass =
  'block w-full rounded-lg border-0 px-3 py-2 text-gray-900 shadow-xs ring-1 ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-gkk sm:text-sm'

function choose(event) {
  const file = event.target.files[0]
  event.target.value = ''
  if (file) {
    emit('upload', file)
  }
}
</script>
