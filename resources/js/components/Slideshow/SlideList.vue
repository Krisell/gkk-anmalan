<template>
  <div class="rounded-sm shadow-sm bg-white p-4 sm:p-6">
    <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
      <div class="min-w-0 flex-1 basis-64">
        <h2 class="text-xl font-semibold mb-1">Slides</h2>
        <p v-if="!editMode" class="text-sm text-gray-600">
          Visa en slide så stannar skärmen på den tills du släpper den.
          <span v-if="current.page">
            Just nu visas en webbsida. En slide du visar hamnar ovanpå den, och webbsidan fortsätter som förut när du
            släpper sliden.
          </span>
        </p>
        <p v-else class="text-sm text-gray-600">
          Bildspelet visar klubbrekorden och sedan de synliga slidesen i den här ordningen, 30 sekunder var. Dra i
          handtaget för att ändra ordningen. Ändringar syns på skärmen direkt.
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

    <!-- Showing: the screen's slides, the DM, Swish and GM slides first. -->
    <ul v-else-if="!editMode" class="divide-y divide-gray-200">
      <li v-for="slide in shown" :key="slideKey(slide)" class="flex items-center gap-4 py-3">
        <SlideThumb :slide="slide" class="w-16 sm:w-28" />
        <div class="min-w-0 flex-1">
          <p class="line-clamp-2 font-medium">{{ slideLabel(slide) }}</p>
          <p v-if="showingNow.has(slideKey(slide))" class="text-sm text-green-700">Visas nu</p>
          <p v-else-if="slide.manual" class="text-sm text-gray-500">Ingår inte i bildspelet – visas bara här</p>
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

    <!-- Editing: the built-in slides as they are, then the club's own slides to order, hide, edit and remove. -->
    <template v-else>
      <ul class="divide-y divide-gray-200">
        <li v-for="slide in builtIn" :key="slideKey(slide)" class="flex items-center gap-3 py-3 sm:gap-4">
          <span class="w-8 shrink-0"></span>
          <SlideThumb :slide="slide" class="w-16 sm:w-28" />
          <div class="min-w-0 flex-1">
            <p class="truncate font-medium">{{ slideLabel(slide) }}</p>
            <p class="text-sm text-gray-500">
              {{
                slide.manual ? 'Visas bara när du väljer den' : 'Visas först i bildspelet och uppdateras av sig själv'
              }}
            </p>
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
        <li
          v-for="slide in own"
          :key="slide.id"
          class="flex items-center gap-3 py-3 sm:gap-4"
          :class="{ 'bg-gray-50/80': !slide.is_visible }"
        >
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
            :class="{ 'opacity-40 grayscale': !slide.is_visible }"
            title="Redigera"
            @click="startEdit(slide)"
          >
            <SlideThumb :slide="slide" class="w-16 sm:w-28" />
          </button>
          <div class="min-w-0 flex-1">
            <p class="line-clamp-2 font-medium" :class="slide.is_visible ? 'text-gray-900' : 'text-gray-500'">
              {{ slideLabel(slide) }}
            </p>
            <p v-if="slide.data.text && slide.data.subText" class="truncate text-sm text-gray-500">
              {{ slide.data.subText }}
            </p>
            <span
              v-if="!slide.is_visible"
              class="mt-1 inline-flex items-center gap-1 rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-600"
            >
              <EyeOff class="size-3" />
              Dold
            </span>
          </div>
          <div class="flex shrink-0 items-center gap-1">
            <button :class="iconButtonClass" v-tooltip="'Redigera'" aria-label="Redigera" @click="startEdit(slide)">
              <Pencil class="size-4.5" />
            </button>
            <button
              :class="iconButtonClass"
              v-tooltip="slide.is_visible ? 'Dölj' : 'Visa i bildspelet'"
              :aria-label="slide.is_visible ? 'Dölj' : 'Visa i bildspelet'"
              @click="setVisibility(slide, !slide.is_visible)"
            >
              <Eye v-if="slide.is_visible" class="size-4.5" />
              <EyeOff v-else class="size-4.5" />
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
import { Check, Eye, EyeOff, GripVertical, Pencil, Plus, Sparkles, Trash2, X } from 'lucide-vue-next'
import FirebaseFileUpload from '../../modules/FirebaseFileUpload.js'
import SlideFields from './SlideFields.vue'
import SlidePreview from './SlidePreview.vue'
import SlideThumb from './SlideThumb.vue'
import { controlOrder, rotation, slideKey, slideLabel } from './remote.js'
import { activeButtonClass, buttonClass, iconButtonClass, secondaryButtonClass } from './controlStyles.js'

/**
 * The slides of the screen slideshow, on the admin page (AdminSlideshow.vue): shown, each can be held on the screen
 * from the live control; in edit mode, the club's own slides can be ordered, hidden, edited, removed and added.
 * Saving a slide publishes a new version (App\Services\SlideshowSnapshot), so the screens reload with it.
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
const emit = defineEmits(['hold'])

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

async function setVisibility(slide, isVisible) {
  try {
    await axios.patch(`/slideshow/slides/${slide.id}`, { is_visible: isVisible })
    slide.is_visible = isVisible
    toast('success', isVisible ? 'Sliden visas i bildspelet' : 'Sliden är dold')
  } catch (error) {
    console.error('Error updating slide visibility:', error)
    toast('error', 'Sliden kunde inte ändras')
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
