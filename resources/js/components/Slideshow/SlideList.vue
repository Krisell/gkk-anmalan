<template>
  <div class="rounded-sm shadow-sm bg-white p-4 sm:p-6">
    <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
      <div class="min-w-0 flex-1 basis-64">
        <h2 class="text-xl font-semibold mb-1">Slides</h2>
        <p v-if="!editMode" class="text-sm text-gray-600">
          Välj vilka slides som ingår i bildspelet – de visas i tur och ordning, 30 sekunder var. Med
          <span class="font-medium">Visa</span> stannar skärmen på en slide tills du släpper den, även om den inte
          ingår.
          <span v-if="current.page">
            Just nu visas en webbsida. En slide du visar hamnar ovanpå den, och webbsidan fortsätter som förut när du
            släpper sliden.
          </span>
        </p>
        <p v-else class="text-sm text-gray-600">
          Dra i handtaget för att ändra ordningen på klubbens egna slides. Vilka som ingår i bildspelet väljer du när du
          är klar. Ändringar syns på skärmen direkt.
        </p>
      </div>
      <div class="flex shrink-0 gap-2">
        <template v-if="editMode">
          <button :class="secondaryButtonClass" @click="startCreate">
            <Plus class="mr-2 size-4" />
            Ny slide
          </button>
          <button :class="buttonClass" @click="editMode = false">
            <Check class="mr-2 size-4" />
            Klar
          </button>
        </template>
        <button v-else :class="secondaryButtonClass" @click="editMode = true">
          <Pencil class="mr-2 size-4" />
          Redigera slides
        </button>
      </div>
    </div>

    <p v-if="loading" class="text-gray-600">Laddar slides…</p>
    <p v-else-if="loadFailed" class="text-red-700">Kunde inte hämta slidesen. Ladda om sidan och försök igen.</p>

    <!-- Showing: the screen's slides, the DM, Swish and GM slides first, each in or out of the rotation. -->
    <template v-else-if="!editMode">
      <div class="mb-1 flex flex-wrap items-center justify-between gap-3 rounded-sm bg-gray-50 px-3 py-2">
        <span class="text-sm text-gray-700">
          <span class="font-semibold">{{ rotatingCount }}</span> av {{ shown.length }} ingår i bildspelet
        </span>
        <div class="flex gap-2">
          <button
            type="button"
            class="rounded-sm px-3 py-1.5 text-sm font-medium text-gkk ring-1 ring-gray-300 transition hover:bg-white disabled:opacity-50"
            :disabled="!canHold"
            @click="setAll(true)"
          >
            Alla
          </button>
          <button
            type="button"
            class="rounded-sm px-3 py-1.5 text-sm font-medium text-gkk ring-1 ring-gray-300 transition hover:bg-white disabled:opacity-50"
            :disabled="!canHold"
            @click="setAll(false)"
          >
            Inga
          </button>
        </div>
      </div>
      <p v-if="!rotatingCount" class="mt-2 text-sm text-amber-800">
        Ingen slide ingår – skärmen visar klubbens logga när du inte visar en slide.
      </p>
      <ul class="divide-y divide-gray-200">
        <li v-for="slide in shown" :key="slideKey(slide)" class="flex items-center gap-3 py-3 sm:gap-4">
          <SlideThumb
            :slide="slide"
            class="w-16 transition sm:w-28"
            :class="{ 'opacity-40 grayscale': !inRotation(slide, current) }"
          />
          <div class="min-w-0 flex-1">
            <p class="line-clamp-2 font-medium break-words">{{ slideLabel(slide) }}</p>
            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
              <button
                type="button"
                role="switch"
                :aria-checked="inRotation(slide, current)"
                class="inline-flex items-center gap-2 text-sm text-gray-700 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="!canHold"
                @click="emit('rotation', { [slideKey(slide)]: !inRotation(slide, current) })"
              >
                <span
                  class="relative inline-flex h-5 w-9 shrink-0 rounded-full transition"
                  :class="inRotation(slide, current) ? 'bg-gkk' : 'bg-gray-300'"
                >
                  <span
                    class="absolute top-0.5 left-0.5 size-4 rounded-full bg-white shadow transition-transform"
                    :class="{ 'translate-x-4': inRotation(slide, current) }"
                  ></span>
                </span>
                I bildspelet
              </button>
              <span v-if="showingNow.has(slideKey(slide))" class="text-sm text-green-700">Visas nu</span>
            </div>
          </div>
          <button
            v-if="current.slide === slideKey(slide)"
            :class="activeButtonClass"
            class="shrink-0"
            :disabled="!canHold"
            @click="emit('hold', null)"
          >
            Släpp
          </button>
          <button
            v-else
            :class="secondaryButtonClass"
            class="shrink-0"
            :disabled="!canHold"
            @click="emit('hold', slideKey(slide))"
          >
            Visa
          </button>
        </li>
      </ul>
    </template>

    <!-- Editing: the built-in slides as they are, then the club's own slides to order, edit and remove. -->
    <template v-else>
      <ul class="divide-y divide-gray-200">
        <li v-for="slide in builtIn" :key="slideKey(slide)" class="flex items-center gap-3 py-3 sm:gap-4">
          <span class="w-8 shrink-0"></span>
          <SlideThumb :slide="slide" class="w-16 sm:w-28" />
          <div class="min-w-0 flex-1">
            <p class="truncate font-medium">{{ slideLabel(slide) }}</p>
            <p class="text-sm text-gray-500">Kan inte ändras här</p>
          </div>
          <span
            class="hidden shrink-0 items-center gap-1.5 rounded-full bg-gkk/10 px-2.5 py-1 text-xs font-medium text-gkk sm:inline-flex"
          >
            <Sparkles class="size-3.5" />
            Inbyggd
          </span>
        </li>
      </ul>

      <p
        v-if="!own.length"
        class="mt-3 rounded-lg border-2 border-dashed border-gray-300 p-6 text-center text-gray-500"
      >
        Inga egna slides än. Skapa den första med <span class="font-medium">Ny slide</span>.
      </p>

      <ol ref="list" class="divide-y divide-gray-200 border-t border-gray-200">
        <li v-for="slide in own" :key="slide.id" class="flex items-center gap-3 py-3 sm:gap-4">
          <button
            type="button"
            class="drag-handle flex h-12 w-8 shrink-0 cursor-grab touch-none items-center justify-center rounded-md text-gray-400 transition hover:bg-gray-100 hover:text-gkk active:cursor-grabbing"
            aria-label="Dra för att flytta"
            title="Dra för att flytta"
          >
            <GripVertical class="size-5" />
          </button>
          <button
            type="button"
            class="shrink-0 rounded-sm transition hover:ring-2 hover:ring-gkk"
            title="Redigera"
            @click="startEdit(slide)"
          >
            <SlideThumb :slide="slide" class="w-16 sm:w-28" />
          </button>
          <div class="min-w-0 flex-1">
            <p class="line-clamp-2 font-medium text-gray-900">{{ slideLabel(slide) }}</p>
            <p v-if="slide.data.text && slide.data.subText" class="truncate text-sm text-gray-500">
              {{ slide.data.subText }}
            </p>
          </div>
          <div class="flex shrink-0 items-center gap-1">
            <button :class="iconButtonClass" v-tooltip="'Redigera'" aria-label="Redigera" @click="startEdit(slide)">
              <Pencil class="size-4.5" />
            </button>
            <button
              :class="[iconButtonClass, 'hover:bg-red-50 hover:text-red-600']"
              v-tooltip="'Ta bort'"
              aria-label="Ta bort"
              @click="deleteSlide(slide)"
            >
              <Trash2 class="size-4.5" />
            </button>
          </div>
        </li>
      </ol>
    </template>

    <!-- A new slide, or one being edited. -->
    <Transition name="modal">
      <div
        v-if="editing"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4 backdrop-blur-sm"
        @click.self="editing = null"
      >
        <div class="max-h-full w-full max-w-4xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
          <div class="mb-5 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gkk">{{ editing.id ? 'Redigera slide' : 'Ny slide' }}</h2>
            <button :class="iconButtonClass" aria-label="Stäng" @click="editing = null">
              <X class="size-5" />
            </button>
          </div>
          <div class="grid gap-6 md:grid-cols-[3fr_2fr]">
            <SlidePreview v-bind="editing.data" class="self-start rounded-lg ring-1 ring-gray-200" />
            <SlideFields v-model="editing.data" :uploading="uploading" @upload="upload" />
          </div>
          <p v-if="!editing.id" class="mt-4 text-sm text-gray-500">Nya slides hamnar först, efter klubbrekorden.</p>
          <div class="mt-6 flex justify-end gap-3">
            <button :class="secondaryButtonClass" @click="editing = null">Avbryt</button>
            <button :class="buttonClass" :disabled="!hasContent(editing.data) || uploading" @click="saveEditing">
              {{ editing.id ? 'Spara' : 'Skapa slide' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import axios from 'axios'
import Sortable from 'sortablejs'
import { useToast } from 'vue-toast-notification'
import 'vue-toast-notification/dist/theme-sugar.css'
import { Check, GripVertical, Pencil, Plus, Sparkles, Trash2, X } from 'lucide-vue-next'
import FirebaseFileUpload from '../../modules/FirebaseFileUpload.js'
import SlideFields from './SlideFields.vue'
import SlidePreview from './SlidePreview.vue'
import SlideThumb from './SlideThumb.vue'
import { controlOrder, inRotation, rotation, slideKey, slideLabel } from './remote.js'
import { activeButtonClass, buttonClass, iconButtonClass, secondaryButtonClass } from './controlStyles.js'

/**
 * The slides of the screen slideshow, on the admin page (AdminSlideshow.vue): each can be put in or out of the
 * rotation and held on the screen, through the live control (`rotation` and `hold` events, saved by the page); in edit
 * mode, the club's own slides can be ordered, edited, removed and added. Saving a slide publishes a new version
 * (App\Services\SlideshowSnapshot), so the screens reload with it.
 */
const props = defineProps({
  // The live control's settings (normalizeControl), and which slides the screens show.
  current: { type: Object, required: true },
  showingNow: { type: Set, required: true },
  // Whether the live control is connected, so that a slide can be held.
  canHold: { type: Boolean, default: false },
  // The admin's Firebase sign-in, for uploading images.
  jwt: { type: String, required: true },
})
const emit = defineEmits(['hold', 'rotation'])

const $toast = useToast()
const loading = ref(true)
const loadFailed = ref(false)
const own = ref([])
// GKK's recent competition results, for the results slide (only in the rotation when there are any).
const results = ref([])
const editMode = ref(false)
const editing = ref(null)
const uploading = ref(false)
const list = ref(null)

// The slides as the screen has them, in the live control's order; and, for editing, the built-in ones.
const shown = computed(() => controlOrder(rotation(own.value, [], results.value)))
const builtIn = computed(() => controlOrder(rotation([], [], results.value)))
const rotatingCount = computed(() => shown.value.filter((slide) => inRotation(slide, props.current)).length)

// Every slide in the rotation, or none.
function setAll(on) {
  emit('rotation', Object.fromEntries(shown.value.map((slide) => [slideKey(slide), on])))
}

function emptySlide() {
  return { text: '', subText: '', image: '' }
}

function hasContent(data) {
  return Boolean(data.text || data.subText || data.image)
}

function payload(data) {
  return {
    text: data.text || undefined,
    subText: data.subText || undefined,
    image: data.image || undefined,
  }
}

function toast(type, message) {
  $toast[type](message, { duration: 2000 })
}

function startCreate() {
  editing.value = { id: null, data: emptySlide() }
}

function startEdit(slide) {
  editing.value = { id: slide.id, data: { ...emptySlide(), ...slide.data } }
}

async function upload(file) {
  const target = editing.value
  uploading.value = true
  try {
    target.data.image = await FirebaseFileUpload.upload(props.jwt, file, file.name.split('.').pop())
  } catch (error) {
    console.error('Error uploading image:', error)
    toast('error', 'Bilden kunde inte laddas upp')
  } finally {
    uploading.value = false
  }
}

async function saveEditing() {
  const { id, data } = editing.value
  try {
    if (id) {
      await axios.put(`/slideshow/slides/${id}`, { type: 'Slide', data: payload(data) })
      const slide = own.value.find((slide) => slide.id === id)
      if (slide) {
        slide.data = payload(data)
      }
      toast('success', 'Sliden har uppdaterats')
    } else {
      const response = await axios.post('/slideshow/slides', { type: 'Slide', data: payload(data), is_visible: true })
      own.value.unshift(response.data)
      toast('success', 'Sliden har skapats')
    }
    editing.value = null
  } catch (error) {
    console.error('Error saving slide:', error)
    toast('error', id ? 'Sliden kunde inte uppdateras' : 'Sliden kunde inte skapas')
  }
}

async function deleteSlide(slide) {
  if (!confirm('Är du säker på att du vill ta bort denna slide?')) return

  try {
    await axios.delete(`/slideshow/slides/${slide.id}`)
    own.value = own.value.filter((other) => other.id !== slide.id)
    toast('success', 'Sliden har tagits bort')
  } catch (error) {
    console.error('Error deleting slide:', error)
    toast('error', 'Sliden kunde inte tas bort')
  }
}

// Moves a slide to another position and saves the new order, or puts it back if saving fails.
async function move(from, to) {
  if (to < 0 || to >= own.value.length || to === from) return

  const previous = [...own.value]
  const reordered = [...own.value]
  reordered.splice(to, 0, reordered.splice(from, 1)[0])
  own.value = reordered

  try {
    await axios.post('/slideshow/order', {
      slides: reordered.map((slide, order) => ({ id: slide.id, order })),
    })
  } catch {
    own.value = previous
    toast('error', 'Ordningen kunde inte sparas')
  }
}

// Drag and drop (mouse and touch) by the handle, while editing. Sortable moves the row in the DOM; it is put back right
// away and the slides are reordered instead, so that Vue stays in charge of the DOM.
let sortable = null

watch(editMode, async (editing) => {
  sortable?.destroy()
  sortable = null
  if (!editing) {
    return
  }
  await nextTick()
  if (list.value) {
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
  }
})

function closeOnEscape(event) {
  if (event.key === 'Escape') {
    editing.value = null
  }
}

onMounted(async () => {
  window.addEventListener('keydown', closeOnEscape)
  try {
    // The screen's data: all the slides, also the hidden ones, and the recent results.
    const response = await axios.get('/slideshow/snapshot')
    own.value = response.data.slides
    results.value = response.data.results ?? []
  } catch {
    loadFailed.value = true
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
