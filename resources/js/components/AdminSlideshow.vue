<template>
  <div class="container mx-auto pl-6 -mt-12">
    <p class="text-left -mt-6 text-3xl mb-4">Administrera Slideshow</p>

    <!-- Create new slide section -->
    <div class="rounded shadow bg-white p-6 mb-6">
      <h2 class="text-xl font-semibold mb-4">Skapa ny slide</h2>
      <div class="flex flex-col gap-4">
        <input
          v-model="newSlide.text"
          type="text"
          placeholder="Text (valfritt)"
          class="appearance-none rounded relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
        />
        <input
          v-model="newSlide.subText"
          type="text"
          placeholder="Undertext (valfritt)"
          class="appearance-none rounded relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
        />
        <div class="flex flex-col gap-2">
          <label class="text-sm text-gray-700">Bild (valfritt)</label>
          <input
            ref="imageInput"
            type="file"
            accept="image/*"
            @change="handleImageUpload"
            class="appearance-none rounded relative block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          />
          <div v-if="uploadProgress > 0 && uploadProgress < 100" class="w-full bg-gray-200 rounded">
            <div class="bg-gkk text-xs leading-none py-1 text-center text-white rounded" :style="{ width: uploadProgress + '%' }">{{ uploadProgress }}%</div>
          </div>
          <img v-if="newSlide.image" :src="newSlide.image" alt="Preview" class="mt-2 rounded w-64">
        </div>
        <button
          @click="createSlide"
          :disabled="!canCreateSlide"
          class="px-4 py-2 bg-gkk text-white rounded hover:bg-gkk-dark disabled:bg-gray-300 disabled:cursor-not-allowed"
        >
          Skapa slide
        </button>
      </div>
    </div>

    <!-- Edit slide modal -->
    <div v-if="editingSlide" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="cancelEdit">
      <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
        <h2 class="text-xl font-semibold mb-4">Redigera slide</h2>
        <div class="flex flex-col gap-4">
          <input
            v-model="editingSlide.data.text"
            type="text"
            placeholder="Text (valfritt)"
            class="appearance-none rounded relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          />
          <input
            v-model="editingSlide.data.subText"
            type="text"
            placeholder="Undertext (valfritt)"
            class="appearance-none rounded relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
          />
          <div class="flex flex-col gap-2">
            <label class="text-sm text-gray-700">Bild (valfritt)</label>
            <input
              ref="editImageInput"
              type="file"
              accept="image/*"
              @change="handleEditImageUpload"
              class="appearance-none rounded relative block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
            />
            <div v-if="editUploadProgress > 0 && editUploadProgress < 100" class="w-full bg-gray-200 rounded">
              <div class="bg-gkk text-xs leading-none py-1 text-center text-white rounded" :style="{ width: editUploadProgress + '%' }">{{ editUploadProgress }}%</div>
            </div>
            <div v-if="editingSlide.data.image" class="flex items-center gap-4">
              <img :src="editingSlide.data.image" alt="Preview" class="mt-2 rounded w-64">
              <button @click="removeEditImage" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                Ta bort bild
              </button>
            </div>
          </div>
          <div class="flex gap-2 justify-end">
            <button
              @click="cancelEdit"
              class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
            >
              Avbryt
            </button>
            <button
              @click="saveEdit"
              class="px-4 py-2 bg-gkk text-white rounded hover:bg-gkk-dark"
            >
              Spara
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="flex flex-col justify-center mx-auto gap-4 text-center">
      <TransitionGroup name="list" tag="div" class="flex flex-col gap-4">
        <div v-for="(slide, index) in slides" :key="slide.id" class="relative flex gap-6">
          <div class="flex flex-col items-center justify-center gap-3 text-2xl text-gkk rounded shadow bg-white p-4">
            <i
              class="fa fa-arrow-up hover:scale-110 transition-all"
              :class="{ 'text-gray-400': index === 0, 'cursor-pointer': index !== 0 }"
              v-tooltip="'Flytta upp'"
              @click="moveByIndex(index, -1)"
            ></i>
            <i
              class="cursor-pointer fa fa-angle-double-up hover:scale-110 transition-all"
              :class="{ 'text-gray-400': index === 0 }"
              v-tooltip="'Skicka till toppen'"
              @click="moveToTop(index)"
            ></i>
            <i
              class="cursor-pointer fa fa-pencil hover:scale-110 transition-all"
              v-tooltip="'Redigera'"
              @click="startEdit(slide)"
            ></i>
            <i
            class="cursor-pointer fa fa-eye-slash hover:scale-110 transition-all"
            v-tooltip="'Visa'"
            v-if="!slide.is_visible"
            @click="setVisibility(slide.id, true)"
            ></i>
            <i
              class="cursor-pointer fa fa-eye hover:scale-110 transition-all"
              v-tooltip="'Göm'"
              v-if="slide.is_visible"
              @click="setVisibility(slide.id, false)"
              ></i>
            <i
              class="cursor-pointer fa fa-trash hover:scale-110 transition-all text-red-500"
              v-tooltip="'Ta bort'"
              @click="deleteSlide(slide.id)"
            ></i>
            <i
              class="cursor-pointer fa fa-angle-double-down hover:scale-110 transition-all"
              :class="{ 'text-gray-400': index === slides.length - 1 }"
              v-tooltip="'Skicka till botten'"
              @click="moveToBottom(index)"
            ></i>
            <i
              class="fa fa-arrow-down hover:scale-110 transition-all"
              :class="{ 'text-gray-400': index === slides.length - 1, 'cursor-pointer': index !== slides.length - 1 }"
              v-tooltip="'Flytta ner'"
              @click="moveByIndex(index, 1)"
            ></i>
          </div>
          <div class="rounded shadow bg-white p-4 flex flex-col items-center justify-center">
            <p v-if="slide.data.text">{{ slide.data.text }}</p>
            <p v-if="slide.data.subText" class="text-sm text-gray-500">{{  slide.data.subText }}</p>
            <a v-if="slide.data.image" :href="slide.data.image" target="_blank" class="block mt-2 text-blue-500 hover:underline">
              <img :src="slide.data.image" alt="Slide Image" class="mt-2 rounded w-64 mx-auto">
            </a>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </div>
</template>

<script setup>
import { useToast } from 'vue-toast-notification'
import 'vue-toast-notification/dist/theme-sugar.css'
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import FirebaseFileUpload from '../modules/FirebaseFileUpload.js'

const props = defineProps({
  jwt: {
    type: String,
    required: true
  }
})

const $toast = useToast();
const slides = ref([])
const newSlide = ref({
  text: '',
  subText: '',
  image: ''
})
const uploadProgress = ref(0)
const editUploadProgress = ref(0)
const imageInput = ref(null)
const editImageInput = ref(null)
const editingSlide = ref(null)

const canCreateSlide = computed(() => {
  return newSlide.value.text || newSlide.value.subText || newSlide.value.image
})

async function handleImageUpload(event) {
  const file = event.target.files[0]
  if (!file) return

  try {
    uploadProgress.value = 10
    const extension = file.name.split('.').pop()

    // Simulate progress
    const progressInterval = setInterval(() => {
      if (uploadProgress.value < 90) {
        uploadProgress.value += 10
      }
    }, 100)

    const url = await FirebaseFileUpload.upload(props.jwt, file, extension)

    clearInterval(progressInterval)
    uploadProgress.value = 100
    newSlide.value.image = url

    setTimeout(() => {
      uploadProgress.value = 0
    }, 1000)

    $toast.success('Bilden har laddats upp', {
      duration: 2000,
      type: 'success'
    })
  } catch (error) {
    console.error('Error uploading image:', error)
    uploadProgress.value = 0
    $toast.error('Bilden kunde inte laddas upp', {
      duration: 2000,
      type: 'error'
    })
  }
}

async function handleEditImageUpload(event) {
  const file = event.target.files[0]
  if (!file) return

  try {
    editUploadProgress.value = 10
    const extension = file.name.split('.').pop()

    // Simulate progress
    const progressInterval = setInterval(() => {
      if (editUploadProgress.value < 90) {
        editUploadProgress.value += 10
      }
    }, 100)

    const url = await FirebaseFileUpload.upload(props.jwt, file, extension)

    clearInterval(progressInterval)
    editUploadProgress.value = 100
    editingSlide.value.data.image = url

    setTimeout(() => {
      editUploadProgress.value = 0
    }, 1000)

    $toast.success('Bilden har laddats upp', {
      duration: 2000,
      type: 'success'
    })
  } catch (error) {
    console.error('Error uploading image:', error)
    editUploadProgress.value = 0
    $toast.error('Bilden kunde inte laddas upp', {
      duration: 2000,
      type: 'error'
    })
  }
}

async function createSlide() {
  if (!canCreateSlide.value) return

  try {
    const response = await axios.post('/slideshow/slides', {
      type: 'Slide',
      data: {
        text: newSlide.value.text || undefined,
        subText: newSlide.value.subText || undefined,
        image: newSlide.value.image || undefined
      },
      is_visible: true
    })

    // Add new slide to the beginning of the array
    slides.value.unshift(response.data)

    // Reset form
    newSlide.value = {
      text: '',
      subText: '',
      image: ''
    }
    if (imageInput.value) {
      imageInput.value.value = ''
    }

    $toast.success('Sliden har skapats', {
      duration: 2000,
      type: 'success'
    })
  } catch (error) {
    console.error('Error creating slide:', error)
    $toast.error('Sliden kunde inte skapas', {
      duration: 2000,
      type: 'error'
    })
  }
}

function startEdit(slide) {
  editingSlide.value = JSON.parse(JSON.stringify(slide))
}

function cancelEdit() {
  editingSlide.value = null
  editUploadProgress.value = 0
  if (editImageInput.value) {
    editImageInput.value.value = ''
  }
}

function removeEditImage() {
  if (editingSlide.value) {
    editingSlide.value.data.image = null
  }
}

async function saveEdit() {
  if (!editingSlide.value) return

  try {
    await axios.put(`/slideshow/slides/${editingSlide.value.id}`, {
      type: editingSlide.value.type,
      data: {
        text: editingSlide.value.data.text || undefined,
        subText: editingSlide.value.data.subText || undefined,
        image: editingSlide.value.data.image || undefined
      }
    })

    const index = slides.value.findIndex(s => s.id === editingSlide.value.id)
    if (index !== -1) {
      slides.value[index] = editingSlide.value
    }

    editingSlide.value = null

    $toast.success('Sliden har uppdaterats', {
      duration: 2000,
      type: 'success'
    })
  } catch (error) {
    console.error('Error updating slide:', error)
    $toast.error('Sliden kunde inte uppdateras', {
      duration: 2000,
      type: 'error'
    })
  }
}

async function deleteSlide(id) {
  if (!confirm('Är du säker på att du vill ta bort denna slide?')) return

  try {
    await axios.delete(`/slideshow/slides/${id}`)

    const index = slides.value.findIndex(s => s.id === id)
    if (index !== -1) {
      slides.value.splice(index, 1)
    }

    $toast.success('Sliden har tagits bort', {
      duration: 2000,
      type: 'success'
    })
  } catch (error) {
    console.error('Error deleting slide:', error)
    $toast.error('Sliden kunde inte tas bort', {
      duration: 2000,
      type: 'error'
    })
  }
}

async function setVisibility(id, isVisible) {
  try {
    await axios.patch(`/slideshow/slides/${id}`, { is_visible: isVisible })

    const slide = slides.value.find(slide => slide.id === id)
    if (slide) {
      slide.is_visible = isVisible
    }

    $toast.success(`Sliden är nu ${isVisible ? 'synlig' : 'gömd'}`, {
      duration: 2000,
      type: 'success'
    })
  } catch (error) {
    console.error('Error updating slide visibility:', error)
    $toast.error('Sliden kunde inte ändras', {
      duration: 2000,
      type: 'error'
    })
  }
}

async function moveByIndex(index, direction) {
  const newIndex = index + direction
  if (newIndex < 0 || newIndex >= slides.value.length) return

  const temp = slides.value[index]
  slides.value[index] = slides.value[newIndex]
  slides.value[newIndex] = temp

  try {
    await axios.post('/slideshow/order', {
      slides: slides.value.map((slide, index) => ({
        id: slide.id,
        order: index
      }))
    })

    $toast.success(`Flyttade slide ${index + 1} ${direction > 0 ? 'ner' : 'upp'}`, {
      duration: 2000,
      type: 'success'
    })
  } catch (error) {
    $toast.error('Sliden kunde inte flyttas', {
      duration: 2000,
      type: 'error'
    })
    // Revert the changes if the request fails
    slides.value[newIndex] = slides.value[index]
    slides.value[index] = temp
    return
  }
}

async function moveToTop(index) {
  if (index === 0) return

  const slide = slides.value.splice(index, 1)[0]
  slides.value.unshift(slide)

  try {
    await axios.post('/slideshow/order', {
      slides: slides.value.map((slide, index) => ({
        id: slide.id,
        order: index
      }))
    })

    $toast.success('Sliden har skickats till toppen', {
      duration: 2000,
      type: 'success'
    })
  } catch (error) {
    $toast.error('Sliden kunde inte flyttas', {
      duration: 2000,
      type: 'error'
    })
    // Revert the changes if the request fails
    slides.value.splice(0, 1)
    slides.value.splice(index, 0, slide)
    return
  }
}

async function moveToBottom(index) {
  if (index === slides.value.length - 1) return

  const slide = slides.value.splice(index, 1)[0]
  slides.value.push(slide)

  try {
    await axios.post('/slideshow/order', {
      slides: slides.value.map((slide, index) => ({
        id: slide.id,
        order: index
      }))
    })

    $toast.success('Sliden har skickats till botten', {
      duration: 2000,
      type: 'success'
    })
  } catch (error) {
    $toast.error('Sliden kunde inte flyttas', {
      duration: 2000,
      type: 'error'
    })
    // Revert the changes if the request fails
    slides.value.splice(slides.value.length - 1, 1)
    slides.value.splice(index, 0, slide)
    return
  }
}

onMounted(async () => {
  const response = await axios.get('/slideshow/slides')
  slides.value = response.data.slides
})
</script>

<style scoped>
.list-move, /* apply transition to moving elements */
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

/* ensure leaving items are taken out of layout flow so that moving
   animations can be calculated correctly. */
.list-leave-active {
  position: absolute;
}
</style>
