<template>
  <div class="container mx-auto max-w-4xl px-4">
    <Breadcrumbs class="mb-6" :items="[{ name: 'Slideshow', href: '/admin/slideshow' }]" title="Styr bildspelet" />

    <p v-if="!signedIn && !error" class="text-gray-600">Ansluter…</p>

    <template v-if="signedIn">
      <!-- The open screens. -->
      <div class="rounded-sm shadow-sm bg-white p-6 mb-6">
        <div class="flex items-center gap-3">
          <span
            class="inline-block h-3 w-3 rounded-full"
            :class="screens.length ? 'bg-green-500' : 'bg-red-500'"
          ></span>
          <h2 class="text-xl font-semibold">
            {{
              screens.length === 0
                ? 'Ingen skärm visar bildspelet'
                : screens.length === 1
                  ? 'En skärm visar bildspelet'
                  : `${screens.length} skärmar visar bildspelet`
            }}
          </h2>
        </div>
        <ul v-if="screens.length" class="mt-3 space-y-1 text-gray-700">
          <li v-for="(screen, index) in screens" :key="index">
            Visar <span class="font-medium">{{ screen.showing || '…' }}</span>
            <span v-if="screen.held && screen.slide !== PAGE_KEY" class="text-gray-500"> · står still</span>
          </li>
        </ul>
        <p v-else class="mt-2 text-gray-600">Öppna bildspelet på skärmen för att styra det härifrån.</p>
      </div>

      <!-- Step, pause and reload. -->
      <div class="rounded-sm shadow-sm bg-white p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Bläddra</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <button :class="buttonClass" @click="command('previous')">
            <i class="fa fa-arrow-left mr-2"></i>
            Föregående
          </button>
          <button :class="buttonClass" @click="command('next')">
            Nästa
            <i class="fa fa-arrow-right ml-2"></i>
          </button>
          <button
            :class="current.paused ? activeButtonClass : buttonClass"
            :disabled="!!current.slide || !!current.page"
            @click="save({ paused: !current.paused })"
          >
            <i class="fa mr-2" :class="current.paused ? 'fa-play' : 'fa-pause'"></i>
            {{ current.paused ? 'Fortsätt' : 'Pausa' }}
          </button>
          <button :class="secondaryButtonClass" @click="reloadScreens">
            <i class="fa fa-refresh mr-2"></i>
            Ladda om
          </button>
        </div>
        <p class="mt-3 text-sm text-gray-600">
          Bildspelet byter slide var 30:e sekund. Pausa för att stanna på den slide som visas.
        </p>
      </div>

      <!-- The bottom bar, or just the clock. -->
      <div class="rounded-sm shadow-sm bg-white p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Nederkant</h2>
        <div class="space-y-4">
          <label class="flex items-center justify-between gap-4 cursor-pointer">
            <span>
              <span class="block font-medium">Visa nederkanten</span>
              <span class="block text-sm text-gray-600">Datum, vecka, klocka och vilken slide som visas.</span>
            </span>
            <input
              type="checkbox"
              class="h-6 w-6 shrink-0 rounded-sm text-gkk focus:ring-gkk"
              :checked="current.footer"
              @change="save({ footer: $event.target.checked })"
            />
          </label>
          <label
            class="flex items-center justify-between gap-4"
            :class="current.footer ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'"
          >
            <span>
              <span class="block font-medium">Visa klockan nere till höger</span>
              <span class="block text-sm text-gray-600">När nederkanten är dold. Visas även över en webbsida.</span>
            </span>
            <input
              type="checkbox"
              class="h-6 w-6 shrink-0 rounded-sm text-gkk focus:ring-gkk disabled:cursor-not-allowed"
              :checked="current.clock"
              :disabled="current.footer"
              @change="save({ clock: $event.target.checked })"
            />
          </label>
        </div>
      </div>

      <!-- Hold a slide. -->
      <div class="rounded-sm shadow-sm bg-white p-6 mb-6">
        <h2 class="text-xl font-semibold mb-1">Visa en slide</h2>
        <p class="mb-4 text-sm text-gray-600">
          Skärmen stannar på sliden tills du släpper den.
          <span v-if="current.page">Just nu visas en webbsida i stället, se nedan.</span>
        </p>
        <p v-if="!slides.length" class="text-gray-600">Laddar slides…</p>
        <ul class="divide-y divide-gray-200">
          <li v-for="slide in slides" :key="slideKey(slide)" class="flex items-center gap-4 py-3">
            <img
              v-if="slide.data?.image"
              :src="slide.data.image"
              alt=""
              class="h-12 w-20 shrink-0 rounded-sm object-cover bg-gray-100"
            />
            <div
              v-else
              class="h-12 w-20 shrink-0 rounded-sm bg-gray-100 flex items-center justify-center text-gray-400"
            >
              <i class="fa" :class="SLIDE_ICONS[slide.type] ?? 'fa-font'"></i>
            </div>
            <div class="min-w-0 flex-1">
              <p class="truncate font-medium">{{ slideLabel(slide) }}</p>
              <p v-if="showingNow.has(slideKey(slide))" class="text-sm text-green-700">Visas nu</p>
              <p v-else-if="slide.manual" class="text-sm text-gray-500">Ingår inte i bildspelet – visas bara här</p>
            </div>
            <button
              v-if="current.slide === slideKey(slide)"
              :class="activeButtonClass"
              class="shrink-0"
              @click="save({ slide: null })"
            >
              Släpp
            </button>
            <button v-else :class="secondaryButtonClass" class="shrink-0" @click="save({ slide: slideKey(slide) })">
              Visa
            </button>
          </li>
        </ul>
      </div>

      <!-- A flash: over the whole screen, then in the message bar for 30 s, then gone. -->
      <div class="rounded-sm shadow-sm bg-white p-6 mb-6">
        <h2 class="text-xl font-semibold mb-1">Kort meddelande</h2>
        <p class="mb-4 text-sm text-gray-600">
          Visas först över hela skärmen – även över en webbsida – och glider sedan ner längst ner i 30 sekunder innan
          det försvinner.
        </p>
        <form class="flex flex-wrap gap-3" @submit.prevent="showFlash">
          <input
            v-model="flashDraft"
            :maxlength="MESSAGE_MAX_LENGTH"
            placeholder="T.ex. Grupp 3 – invägningen börjar nu!"
            class="min-w-0 flex-1 basis-64 appearance-none rounded-sm px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-hidden focus:border-blue-300"
          />
          <button :class="buttonClass" :disabled="!flashDraft.trim()">
            <i class="fa fa-bolt mr-2"></i>
            Visa
          </button>
          <button type="button" :class="secondaryButtonClass" :disabled="!flashShown" @click="clearFlash">
            Ta bort
          </button>
        </form>
        <p v-if="flashShown" class="mt-3 text-gray-700">På skärmen: ”{{ flashShown }}”</p>
      </div>

      <!-- A message on the screens. -->
      <div class="rounded-sm shadow-sm bg-white p-6 mb-6">
        <h2 class="text-xl font-semibold mb-1">Meddelande</h2>
        <p class="mb-4 text-sm text-gray-600">Visas längst ner på skärmen tills du tar bort det.</p>
        <form class="flex flex-wrap gap-3" @submit.prevent="showMessage">
          <input
            v-model="draft"
            :maxlength="MESSAGE_MAX_LENGTH"
            placeholder="T.ex. Gymmet stänger kl. 21 i kväll"
            class="min-w-0 flex-1 basis-64 appearance-none rounded-sm px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-hidden focus:border-blue-300"
          />
          <button :class="buttonClass" :disabled="!draft.trim()">Visa</button>
          <button
            type="button"
            :class="secondaryButtonClass"
            :disabled="!current.message"
            @click="save({ message: null })"
          >
            Ta bort
          </button>
        </form>
        <p v-if="current.message" class="mt-3 text-gray-700">På skärmen: ”{{ current.message.text }}”</p>
      </div>

      <!-- A web page instead of the slideshow. -->
      <div class="rounded-sm shadow-sm bg-white p-6 mb-6">
        <h2 class="text-xl font-semibold mb-1">Visa en webbsida</h2>
        <p class="mb-4 text-sm text-gray-600">
          Visas i stället för bildspelet tills du går tillbaka. Datum, klocka och ett ev. meddelande visas fortfarande
          längst ner. Vissa webbplatser tillåter inte att visas inuti en annan sida och blir då tomma – prova sidan
          innan den behövs.
        </p>
        <form class="flex flex-wrap gap-3" @submit.prevent="showPage">
          <input
            v-model="pageDraft"
            type="text"
            inputmode="url"
            autocomplete="off"
            autocapitalize="none"
            spellcheck="false"
            :maxlength="PAGE_URL_MAX_LENGTH"
            placeholder="se.powerliftinglive.com/…"
            class="min-w-0 flex-1 basis-64 appearance-none rounded-sm px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-hidden focus:border-blue-300"
          />
          <button :class="buttonClass" :disabled="!pageUrl(pageDraft)">Visa</button>
        </form>
        <p v-if="pageDraft.trim() && !pageUrl(pageDraft)" class="mt-2 text-sm text-red-700">
          Det ser inte ut som en webbadress. Skriv t.ex. se.powerliftinglive.com
        </p>

        <!-- The warm-up hall screen (/live/hall) for a competition that is live on Powerlifting Live. -->
        <div class="mt-6 border-t border-gray-200 pt-4">
          <div class="flex flex-wrap items-center gap-3">
            <div class="min-w-0 flex-1 basis-64">
              <h3 class="font-semibold">Uppvärmningsskärm</h3>
              <p class="text-sm text-gray-600">
                Lyftordning, resultat och nästa grupp för en tävling som är live på Powerlifting Live.
              </p>
            </div>
            <button
              :class="secondaryButtonClass"
              class="shrink-0"
              :disabled="hall.state === 'loading'"
              @click="loadCompetitions"
            >
              <i class="fa fa-refresh mr-2" :class="{ 'fa-spin': hall.state === 'loading' }"></i>
              {{ hall.state === 'idle' ? 'Hämta tävlingar' : 'Uppdatera' }}
            </button>
          </div>
          <p v-if="hall.state === 'error'" class="mt-3 text-sm text-red-700">
            Kunde inte hämta tävlingarna från Powerlifting Live. Försök igen om en stund.
          </p>
          <p v-else-if="hall.state === 'loaded' && !hall.competitions.length" class="mt-3 text-sm text-gray-600">
            Inga tävlingar live just nu.
          </p>
          <ul v-if="hall.competitions.length" class="mt-3 divide-y divide-gray-200">
            <li v-for="competition in hall.competitions" :key="competition.id" class="flex items-center gap-4 py-3">
              <div class="min-w-0 flex-1">
                <p class="truncate font-medium">{{ competition.name }}</p>
                <p v-if="competition.date" class="text-sm text-gray-600">{{ competition.date }}</p>
              </div>
              <span v-if="current.page?.url === hallUrl(competition.id)" class="shrink-0 text-sm text-green-700">
                Visas nu
              </span>
              <button
                v-else
                :class="secondaryButtonClass"
                class="shrink-0"
                @click="save({ page: { id: Date.now().toString(36), url: hallUrl(competition.id) } })"
              >
                Visa
              </button>
            </li>
          </ul>
        </div>
        <div v-if="current.page" class="mt-4 flex flex-wrap items-center gap-3">
          <p class="min-w-0 flex-1 basis-64 text-gray-700">
            På skärmen:
            <a :href="current.page.url" target="_blank" rel="noopener" class="text-gkk break-all hover:underline">
              {{ current.page.url }}
            </a>
          </p>
          <button :class="activeButtonClass" class="shrink-0" @click="save({ page: null })">
            <i class="fa fa-undo mr-2"></i>
            Tillbaka till bildspelet
          </button>
        </div>
      </div>
    </template>

    <p v-if="error" class="mb-6 rounded-sm bg-red-50 p-4 text-red-700">{{ error }}</p>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import axios from 'axios'
import Breadcrumbs from './ui/Breadcrumbs.vue'
import { signInWithCustomToken } from 'firebase/auth'
import { onValue, push, ref as databaseRef, serverTimestamp, update } from 'firebase/database'
import { liveAuth } from '../live/remote/auth.js'
import { liveDatabase } from '../live/remote/firebase.js'
import {
  FLASH_BAR_MS,
  FLASH_FULL_MS,
  MESSAGE_MAX_LENGTH,
  PAGE_KEY,
  PAGE_URL_MAX_LENGTH,
  normalizeControl,
  onlineScreens,
  pageUrl,
  rotation,
  slideKey,
  slideLabel,
} from './Slideshow/remote.js'

const { jwt } = defineProps({
  // The admin's Firebase sign-in (App\Firebase::makeAdminJwt), which database.rules.json lets control the slideshow.
  jwt: {
    type: String,
    required: true,
  },
})

const buttonClass =
  'inline-flex items-center justify-center px-4 py-3 bg-gkk text-white rounded-sm hover:bg-gkk-dark disabled:bg-gray-300 disabled:cursor-not-allowed'
const activeButtonClass =
  'inline-flex items-center justify-center px-4 py-3 bg-amber-400 text-gkk font-medium rounded-sm hover:bg-amber-300 disabled:bg-gray-300 disabled:text-white disabled:cursor-not-allowed'
const secondaryButtonClass =
  'inline-flex items-center justify-center px-4 py-3 bg-gray-200 text-gray-800 rounded-sm hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed'

const database = liveDatabase()
const signedIn = ref(false)
const error = ref(null)
const current = ref(normalizeControl(null))
const presence = ref({})
const now = ref(Date.now())
const slides = ref([])
const draft = ref('')
const pageDraft = ref('')
const flashDraft = ref('')
// The flash sent from here, while the screens show it.
const flashShown = ref(null)
let flashTimer = null

const SLIDE_ICONS = { Klubbrekord: 'fa-trophy', Dm2026: 'fa-flag-checkered', Swish: 'fa-qrcode' }
const hall = ref({ state: 'idle', competitions: [] })

let unsubscribes = []
let ticker = null

onMounted(async () => {
  ticker = setInterval(() => (now.value = Date.now()), 5000)

  axios
    .get('/slideshow/snapshot')
    .then(({ data }) => {
      // The screen's slides, with the manual ones (only shown from here) first.
      const all = rotation(data.slides)
      slides.value = [...all.filter((slide) => slide.manual), ...all.filter((slide) => !slide.manual)]
    })
    .catch(() => (error.value = 'Kunde inte hämta bildspelets slides.'))

  try {
    await signInWithCustomToken(liveAuth(), jwt)
  } catch {
    error.value = 'Kunde inte ansluta. Ladda om sidan och försök igen.'
    return
  }

  signedIn.value = true
  unsubscribes = [
    onValue(databaseRef(database, 'slideshow/control'), (snapshot) => {
      current.value = normalizeControl(snapshot.val())
    }),
    onValue(databaseRef(database, 'slideshow/presence'), (snapshot) => (presence.value = snapshot.val() ?? {})),
  ]
})

onBeforeUnmount(() => {
  clearInterval(ticker)
  clearTimeout(flashTimer)
  unsubscribes.forEach((unsubscribe) => unsubscribe())
})

const screens = computed(() => onlineScreens(presence.value, now.value))
const showingNow = computed(() => new Set(screens.value.map((screen) => screen.slide)))

async function save(patch) {
  error.value = null
  try {
    await update(databaseRef(database, 'slideshow/control'), patch)
  } catch {
    error.value = 'Kunde inte spara. Kontrollera uppkopplingen, eller ladda om sidan.'
  }
}

// Whether the command was sent.
async function command(type, data = {}) {
  error.value = null
  try {
    await push(databaseRef(database, 'slideshow/commands'), { ...data, type, at: serverTimestamp() })
    return true
  } catch {
    error.value = 'Kunde inte skicka. Kontrollera uppkopplingen, eller ladda om sidan.'
    return false
  }
}

function reloadScreens() {
  if (window.confirm('Ladda om bildspelet på alla skärmar?')) {
    void command('reload')
  }
}

async function showFlash() {
  const text = flashDraft.value.trim().slice(0, MESSAGE_MAX_LENGTH)
  if (!text) {
    return
  }
  flashDraft.value = ''
  if (await command('flash', { text })) {
    flashShown.value = text
    clearTimeout(flashTimer)
    flashTimer = setTimeout(() => (flashShown.value = null), FLASH_FULL_MS + FLASH_BAR_MS)
  }
}

async function clearFlash() {
  if (await command('clearFlash')) {
    clearTimeout(flashTimer)
    flashShown.value = null
  }
}

function showMessage() {
  const text = draft.value.trim().slice(0, MESSAGE_MAX_LENGTH)
  if (text) {
    void save({ message: { id: Date.now().toString(36), text } })
    draft.value = ''
  }
}

// The competitions live on Powerlifting Live now (LiveOverlayController@competitions), for the hall screen.
async function loadCompetitions() {
  hall.value = { ...hall.value, state: 'loading' }
  try {
    const { data } = await axios.get('/live/competitions')
    hall.value = { state: 'loaded', competitions: Array.isArray(data.competitions) ? data.competitions : [] }
  } catch {
    hall.value = { state: 'error', competitions: [] }
  }
}

function hallUrl(id) {
  return pageUrl(`${window.location.origin}/live/hall?competition=${encodeURIComponent(id)}`)
}

function showPage() {
  const url = pageUrl(pageDraft.value)
  if (url) {
    void save({ page: { id: Date.now().toString(36), url } })
    pageDraft.value = ''
  }
}
</script>
