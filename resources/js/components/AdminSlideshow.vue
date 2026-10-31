<template>
  <div class="mx-auto max-w-[1600px] px-4 pb-12 lg:px-8">
    <Breadcrumbs class="mb-4" title="Bildspelet" />

    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
      <p class="max-w-2xl text-gray-600">
        Slides som visas på skärmen i lokalen, i den här ordningen, 30 sekunder var. Dra i handtaget till vänster för
        att ändra ordningen.
      </p>
      <div class="flex flex-wrap gap-3">
        <a
          href="/admin/slideshow/live"
          class="inline-flex items-center gap-2 rounded-lg bg-gkk px-4 py-2.5 font-medium text-white shadow-sm transition hover:bg-gkk-light"
        >
          <SlidersHorizontal class="size-4" />
          Styr bildspelet
        </a>
        <a
          href="/slides-for-screen"
          target="_blank"
          class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 font-medium text-gkk shadow-sm ring-1 ring-gray-200 transition hover:bg-gray-50"
        >
          <ExternalLink class="size-4" />
          Öppna bildspelet
        </a>
      </div>
    </div>

    <div class="grid items-start gap-8 xl:grid-cols-[minmax(0,1fr)_26rem]">
      <!-- The slides, in the screen's order. -->
      <section class="min-w-0">
        <div class="mb-3 flex items-baseline justify-between">
          <h2 class="text-lg font-semibold text-gkk">Slides</h2>
          <span class="text-sm text-gray-500">{{ visibleCount }} av {{ slides.length + 1 }} visas</span>
        </div>

        <!-- The club records are always first, and automatic. -->
        <div class="mb-3 flex items-center gap-3 rounded-xl bg-white p-3 shadow-sm sm:gap-4 ring-1 ring-gray-200/70">
          <div class="flex w-8 shrink-0 justify-center text-gray-300" title="Kan inte flyttas">
            <GripVertical class="size-5 opacity-0" />
          </div>
          <span class="hidden w-5 shrink-0 sm:block text-center text-sm font-semibold text-gray-400">1</span>
          <SlidePreview records class="w-28 shrink-0 rounded-lg ring-1 ring-gray-200 sm:w-56" />
          <div class="min-w-0 flex-1">
            <p class="font-medium text-gray-900">Senaste klubbrekorden</p>
            <p class="text-sm text-gray-500">Visas alltid först och uppdateras av sig själv från klubbrekorden.</p>
          </div>
          <span
            class="hidden shrink-0 items-center gap-1.5 rounded-full bg-gkk/10 px-2.5 py-1 text-xs font-medium text-gkk sm:inline-flex"
          >
            <Sparkles class="size-3.5" />
            Automatisk
          </span>
        </div>

        <p v-if="loading" class="rounded-xl bg-white p-6 text-center text-gray-500 shadow-sm ring-1 ring-gray-200/70">
          Laddar slides…
        </p>
        <p
          v-else-if="!slides.length"
          class="rounded-xl border-2 border-dashed border-gray-300 p-8 text-center text-gray-500"
        >
          Inga egna slides än. Skapa den första med formuläret.
        </p>

        <ol ref="list" class="space-y-3">
          <li
            v-for="slide in slides"
            :key="slide.id"
            class="flex items-center gap-3 rounded-xl bg-white p-3 shadow-sm sm:gap-4 ring-1 ring-gray-200/70"
            :class="{ 'bg-gray-50/80': !slide.is_visible }"
          >
            <button
              type="button"
              class="drag-handle flex h-16 w-8 shrink-0 cursor-grab touch-none items-center justify-center rounded-md text-gray-400 transition hover:bg-gray-100 hover:text-gkk active:cursor-grabbing"
              aria-label="Dra för att flytta"
              title="Dra för att flytta"
            >
              <GripVertical class="size-5" />
            </button>

            <span class="hidden w-5 shrink-0 sm:block text-center text-sm font-semibold text-gray-400">
              {{ slide.is_visible ? positionOf(slide) : '–' }}
            </span>

            <button
              type="button"
              class="w-28 shrink-0 overflow-hidden rounded-lg ring-1 ring-gray-200 transition hover:ring-2 hover:ring-gkk sm:w-56"
              :class="{ 'opacity-40 grayscale': !slide.is_visible }"
              title="Redigera"
              @click="startEdit(slide)"
            >
              <SlidePreview v-bind="slide.data" />
            </button>

            <div class="flex min-w-0 flex-1 flex-col gap-1 sm:flex-row sm:items-center sm:gap-4">
              <div class="min-w-0 flex-1">
                <p class="line-clamp-2 font-medium text-gray-900" :class="{ 'text-gray-500': !slide.is_visible }">
                  {{ slide.data.text || slide.data.subText || (slide.data.image ? 'Bild' : 'Tom slide') }}
                </p>
                <p v-if="slide.data.text && slide.data.subText" class="truncate text-sm text-gray-500">
                  {{ slide.data.subText }}
                </p>
                <span
                  v-if="!slide.is_visible"
                  class="mt-1.5 inline-flex items-center gap-1 rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-600"
                >
                  <EyeOff class="size-3" />
                  Dold
                </span>
              </div>

              <div class="-ml-2 flex shrink-0 items-center gap-1 sm:ml-0">
                <button :class="iconButton" v-tooltip="'Redigera'" aria-label="Redigera" @click="startEdit(slide)">
                  <Pencil class="size-4.5" />
                </button>
                <button
                  :class="iconButton"
                  v-tooltip="slide.is_visible ? 'Dölj' : 'Visa'"
                  :aria-label="slide.is_visible ? 'Dölj' : 'Visa'"
                  @click="setVisibility(slide, !slide.is_visible)"
                >
                  <Eye v-if="slide.is_visible" class="size-4.5" />
                  <EyeOff v-else class="size-4.5" />
                </button>
                <button
                  :class="[iconButton, 'hover:bg-red-50 hover:text-red-600']"
                  v-tooltip="'Ta bort'"
                  aria-label="Ta bort"
                  @click="deleteSlide(slide)"
                >
                  <Trash2 class="size-4.5" />
                </button>
              </div>
            </div>
          </li>
        </ol>
      </section>

      <!-- A new slide. -->
      <aside class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200/70 xl:sticky xl:top-24">
        <h2 class="mb-4 text-lg font-semibold text-gkk">Ny slide</h2>
        <SlidePreview v-bind="newSlide" class="mb-5 rounded-lg ring-1 ring-gray-200" />
        <SlideFields v-model="newSlide" :uploading="uploading === 'new'" @upload="upload($event, newSlide)" />
        <button
          class="mt-5 w-full rounded-lg bg-gkk px-4 py-2.5 font-medium text-white shadow-sm transition hover:bg-gkk-light disabled:cursor-not-allowed disabled:bg-gray-300 disabled:shadow-none"
          :disabled="!hasContent(newSlide) || uploading === 'new'"
          @click="createSlide"
        >
          Skapa slide
        </button>
        <p class="mt-2 text-center text-xs text-gray-500">Nya slides hamnar först, efter klubbrekorden.</p>
      </aside>
    </div>

    <!-- Edit a slide. -->
    <Transition name="modal">
      <div
        v-if="editing"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4 backdrop-blur-sm"
        @click.self="editing = null"
      >
        <div class="max-h-full w-full max-w-4xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
          <div class="mb-5 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gkk">Redigera slide</h2>
            <button :class="iconButton" aria-label="Stäng" @click="editing = null">
              <X class="size-5" />
            </button>
          </div>
          <div class="grid gap-6 md:grid-cols-[3fr_2fr]">
            <SlidePreview v-bind="editing.data" class="self-start rounded-lg ring-1 ring-gray-200" />
            <SlideFields
              v-model="editing.data"
              :uploading="uploading === 'edit'"
              @upload="upload($event, editing.data, 'edit')"
            />
          </div>
          <div class="mt-6 flex justify-end gap-3">
            <button
              class="rounded-lg px-4 py-2.5 font-medium text-gray-700 ring-1 ring-gray-200 transition hover:bg-gray-50"
              @click="editing = null"
            >
              Avbryt
            </button>
            <button
              class="rounded-lg bg-gkk px-5 py-2.5 font-medium text-white shadow-sm transition hover:bg-gkk-light disabled:cursor-not-allowed disabled:bg-gray-300"
              :disabled="!hasContent(editing.data) || uploading === 'edit'"
              @click="saveEdit"
            >
              Spara
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { useToast } from 'vue-toast-notification'
import 'vue-toast-notification/dist/theme-sugar.css'
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import axios from 'axios'
import Sortable from 'sortablejs'
import {
  ExternalLink,
  Eye,
  EyeOff,
  GripVertical,
  Pencil,
  SlidersHorizontal,
  Sparkles,
  Trash2,
  X,
} from 'lucide-vue-next'
import FirebaseFileUpload from '../modules/FirebaseFileUpload.js'
import Breadcrumbs from './ui/Breadcrumbs.vue'
import SlidePreview from './Slideshow/SlidePreview.vue'
import SlideFields from './Slideshow/SlideFields.vue'

const props = defineProps({
  jwt: {
    type: String,
    required: true,
  },
})

const iconButton =
  'inline-flex size-9 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-100 hover:text-gkk'

const $toast = useToast()
const loading = ref(true)
const slides = ref([])
const newSlide = ref(emptySlide())
const editing = ref(null)
const uploading = ref(null)
const list = ref(null)

// The club records slide is always shown, so it counts.
const visibleCount = computed(() => slides.value.filter((slide) => slide.is_visible).length + 1)

function emptySlide() {
  return { text: '', subText: '', image: '' }
}

function hasContent(data) {
  return Boolean(data.text || data.subText || data.image)
}

// The position on the screen: after the club records, counting only the visible slides.
function positionOf(slide) {
  return slides.value.filter((other) => other.is_visible).indexOf(slide) + 2
}

function toast(type, message) {
  $toast[type](message, { duration: 2000 })
}

function payload(data) {
  return {
    text: data.text || undefined,
    subText: data.subText || undefined,
    image: data.image || undefined,
  }
}

async function upload(file, target, which = 'new') {
  uploading.value = which
  try {
    target.image = await FirebaseFileUpload.upload(props.jwt, file, file.name.split('.').pop())
  } catch (error) {
    console.error('Error uploading image:', error)
    toast('error', 'Bilden kunde inte laddas upp')
  } finally {
    uploading.value = null
  }
}

async function createSlide() {
  try {
    const response = await axios.post('/slideshow/slides', {
      type: 'Slide',
      data: payload(newSlide.value),
      is_visible: true,
    })
    slides.value.unshift(response.data)
    newSlide.value = emptySlide()
    toast('success', 'Sliden har skapats')
  } catch (error) {
    console.error('Error creating slide:', error)
    toast('error', 'Sliden kunde inte skapas')
  }
}

function startEdit(slide) {
  editing.value = { id: slide.id, type: slide.type, data: { ...emptySlide(), ...slide.data } }
}

async function saveEdit() {
  const { id, type, data } = editing.value
  try {
    await axios.put(`/slideshow/slides/${id}`, { type, data: payload(data) })
    const slide = slides.value.find((slide) => slide.id === id)
    if (slide) {
      slide.data = payload(data)
    }
    editing.value = null
    toast('success', 'Sliden har uppdaterats')
  } catch (error) {
    console.error('Error updating slide:', error)
    toast('error', 'Sliden kunde inte uppdateras')
  }
}

async function deleteSlide(slide) {
  if (!confirm('Är du säker på att du vill ta bort denna slide?')) return

  try {
    await axios.delete(`/slideshow/slides/${slide.id}`)
    slides.value = slides.value.filter((other) => other.id !== slide.id)
    toast('success', 'Sliden har tagits bort')
  } catch (error) {
    console.error('Error deleting slide:', error)
    toast('error', 'Sliden kunde inte tas bort')
  }
}

async function setVisibility(slide, isVisible) {
  try {
    await axios.patch(`/slideshow/slides/${slide.id}`, { is_visible: isVisible })
    slide.is_visible = isVisible
    toast('success', isVisible ? 'Sliden visas nu' : 'Sliden är dold')
  } catch (error) {
    console.error('Error updating slide visibility:', error)
    toast('error', 'Sliden kunde inte ändras')
  }
}

// Moves a slide to another position and saves the new order, or puts it back if saving fails.
async function move(from, to) {
  if (to < 0 || to >= slides.value.length || to === from) return

  const previous = [...slides.value]
  const reordered = [...slides.value]
  reordered.splice(to, 0, reordered.splice(from, 1)[0])
  slides.value = reordered

  try {
    await axios.post('/slideshow/order', {
      slides: reordered.map((slide, order) => ({ id: slide.id, order })),
    })
  } catch {
    slides.value = previous
    toast('error', 'Ordningen kunde inte sparas')
  }
}

// Drag and drop (mouse and touch) by the handle. Sortable moves the row in the DOM; it is put back right away and the
// slides array is reordered instead, so that Vue stays in charge of the DOM.
let sortable = null

function closeOnEscape(event) {
  if (event.key === 'Escape') {
    editing.value = null
  }
}

onMounted(async () => {
  window.addEventListener('keydown', closeOnEscape)

  sortable = Sortable.create(list.value, {
    handle: '.drag-handle',
    animation: 150,
    ghostClass: 'slide-ghost',
    chosenClass: 'slide-chosen',
    onEnd({ item, from, oldIndex, newIndex }) {
      item.remove()
      from.insertBefore(item, from.children[oldIndex] ?? null)
      void move(oldIndex, newIndex)
    },
  })

  try {
    const response = await axios.get('/slideshow/slides')
    slides.value = response.data.slides
  } finally {
    loading.value = false
  }
})

onBeforeUnmount(() => {
  window.removeEventListener('keydown', closeOnEscape)
  sortable?.destroy()
})
</script>

<style scoped>
/* The row's place while it is dragged, and the row being dragged. */
.slide-ghost {
  opacity: 0.4;
  outline: 2px dashed var(--color-gkk);
  outline-offset: 2px;
}

.slide-chosen {
  box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.15);
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
