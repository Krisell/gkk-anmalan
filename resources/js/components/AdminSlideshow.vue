<template>
  <div class="mx-auto max-w-[1600px] px-4 pb-12 lg:px-8">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
      <Breadcrumbs title="Bildspelet" />
      <a
        href="/slideshow"
        target="_blank"
        class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 font-medium text-gkk shadow-sm ring-1 ring-gray-200 transition hover:bg-gray-50"
      >
        <ExternalLink class="size-4" />
        Öppna bildspelet
      </a>
    </div>

    <p v-if="error" class="mb-6 rounded-sm bg-red-50 p-4 text-red-700">{{ error }}</p>
    <p v-if="!signedIn && !error" class="mb-6 text-gray-600">Ansluter till skärmarna…</p>

    <!-- The open screens, and everything that is on them now, each with a way to take it down. -->
    <section v-if="signedIn" class="mb-6 rounded-sm bg-white p-4 shadow-sm sm:p-6">
      <div class="flex flex-wrap items-center gap-3">
        <span
          class="inline-block size-3 shrink-0 rounded-full"
          :class="screens.length ? 'bg-green-500' : 'bg-red-500'"
        ></span>
        <h2 class="min-w-0 flex-1 basis-56 text-xl font-semibold">
          {{
            screens.length === 0
              ? 'Ingen skärm visar bildspelet'
              : screens.length === 1
                ? 'En skärm visar bildspelet'
                : `${screens.length} skärmar visar bildspelet`
          }}
        </h2>
        <button :class="secondaryButtonClass" class="shrink-0 py-2!" @click="reloadScreens">
          <i class="fa fa-refresh mr-2"></i>
          Ladda om skärmarna
        </button>
      </div>
      <ul v-if="screens.length" class="mt-2 space-y-1 text-gray-700">
        <li v-for="(screen, index) in screens" :key="index">
          Visar <span class="font-medium">{{ screen.showing || '…' }}</span>
          <span v-if="screen.held && screen.slide !== PAGE_KEY" class="text-gray-500"> · står still</span>
        </li>
      </ul>
      <p v-else class="mt-2 text-gray-600">Öppna bildspelet på skärmen för att styra det härifrån.</p>

      <div v-if="onScreen.length" class="mt-4">
        <h3 class="mb-2 text-sm font-semibold uppercase tracking-wider text-gray-500">På skärmen nu</h3>
        <ul class="grid gap-3 md:grid-cols-2 2xl:grid-cols-3">
          <li
            v-for="item in onScreen"
            :key="item.key"
            class="flex items-center gap-3 rounded-sm bg-amber-50 p-3 ring-1 ring-amber-200"
          >
            <component :is="item.icon" class="size-5 shrink-0 text-amber-700" />
            <div class="min-w-0 flex-1">
              <p class="text-xs font-semibold uppercase tracking-wider text-amber-800">{{ item.label }}</p>
              <p class="font-medium break-words text-gray-900">{{ item.text }}</p>
            </div>
            <button :class="activeButtonClass" class="shrink-0 py-2!" @click="item.clear">{{ item.action }}</button>
          </li>
        </ul>
      </div>
    </section>

    <!--
      Wide screens: the slides on the left, the controls on the right. Phones: one column, in the order the page is used
      on the day (the columns are `contents` there, so their cards are ordered together).
    -->
    <div class="flex flex-col gap-6 lg:grid lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)] lg:items-start">
      <div class="contents lg:flex lg:flex-col lg:gap-6">
        <SlideList
          class="order-4 lg:order-none"
          :current="current"
          :showing-now="showingNow"
          :can-hold="signedIn"
          :jwt="jwt"
          @hold="(key) => save({ slide: key })"
          @rotation="saveRotation"
        />
      </div>

      <div v-if="signedIn" class="contents lg:flex lg:flex-col lg:gap-6">
        <!-- Step, pause and reload. -->
        <section :class="card" class="order-1 lg:order-none">
          <h2 class="text-xl font-semibold mb-4">Bläddra</h2>
          <div class="grid grid-cols-2 gap-3">
            <button :class="buttonClass" @click="command('previous')">
              <i class="fa fa-arrow-left mr-2"></i>
              Föregående
            </button>
            <button :class="buttonClass" @click="command('next')">
              Nästa
              <i class="fa fa-arrow-right ml-2"></i>
            </button>
          </div>
          <p class="mt-3 text-sm text-gray-600">
            Bildspelet byter slide var 30:e sekund. Stega fram eller tillbaka bland de slides som ingår.
          </p>
        </section>

        <!-- A message on the screens. -->
        <section :class="card" class="order-3 lg:order-none">
          <h2 class="text-xl font-semibold mb-1">Meddelande</h2>
          <p class="mb-4 text-sm text-gray-600">
            Visas längst ner på skärmen tills du tar bort det, eller efter 30 sekunder om du väljer det.
          </p>
          <form class="flex flex-wrap gap-3" @submit.prevent="showMessage">
            <input
              v-model="draft"
              :maxlength="MESSAGE_MAX_LENGTH"
              placeholder="T.ex. Gymmet stänger kl. 21 i kväll"
              :class="inputClass"
            />
            <button :class="buttonClass" :disabled="!draft.trim()">Visa</button>
          </form>
          <label class="mt-3 flex cursor-pointer items-center gap-3">
            <input v-model="autoClear" type="checkbox" class="size-5 shrink-0 rounded-sm text-gkk focus:ring-gkk" />
            <span>Ta bort automatiskt efter 30 sekunder</span>
          </label>
        </section>

        <!-- A web page instead of the slideshow. -->
        <section :class="card" class="order-5 lg:order-none">
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
              :class="inputClass"
            />
            <button :class="buttonClass" :disabled="!pageUrl(pageDraft)">Visa</button>
          </form>
          <p v-if="pageDraft.trim() && !pageUrl(pageDraft)" class="mt-2 text-sm text-red-700">
            Det ser inte ut som en webbadress. Skriv t.ex. se.powerliftinglive.com
          </p>

          <!-- The warm-up hall screen (/live/hall) for a competition that is live on Powerlifting Live. -->
          <div class="mt-6 border-t border-gray-200 pt-4">
            <div class="flex flex-wrap items-center gap-3">
              <div class="min-w-0 flex-1 basis-56">
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
        </section>

        <!-- The bottom bar, or just the clock. -->
        <section :class="card" class="order-6 lg:order-none">
          <h2 class="text-xl font-semibold mb-4">Nederkant</h2>
          <div class="space-y-4">
            <label class="flex cursor-pointer items-center justify-between gap-4">
              <span>
                <span class="block font-medium">Visa nederkanten</span>
                <span class="block text-sm text-gray-600">Datum, vecka, klocka och vilken slide som visas.</span>
              </span>
              <input
                type="checkbox"
                class="size-6 shrink-0 rounded-sm text-gkk focus:ring-gkk"
                :checked="current.footer"
                @change="save({ footer: $event.target.checked })"
              />
            </label>
            <label
              class="flex items-center justify-between gap-4"
              :class="current.footer ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'"
            >
              <span>
                <span class="block font-medium">Visa klockan nere till höger</span>
                <span class="block text-sm text-gray-600">När nederkanten är dold. Visas över allt annat.</span>
              </span>
              <input
                type="checkbox"
                class="size-6 shrink-0 rounded-sm text-gkk focus:ring-gkk disabled:cursor-not-allowed"
                :checked="current.clock"
                :disabled="current.footer"
                @change="save({ clock: $event.target.checked })"
              />
            </label>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import axios from 'axios'
import { signInWithCustomToken } from 'firebase/auth'
import { onValue, push, ref as databaseRef, serverTimestamp, update } from 'firebase/database'
import { ExternalLink, Globe, MessageSquare, Pause, Pin } from 'lucide-vue-next'
import Breadcrumbs from './ui/Breadcrumbs.vue'
import SlideList from './Slideshow/SlideList.vue'
import { liveAuth } from '../live/remote/auth.js'
import { liveDatabase } from '../live/remote/firebase.js'
import { watchServerClock } from '../live/remote/serverClock.js'
import {
  MESSAGE_AUTO_CLEAR_MS,
  MESSAGE_MAX_LENGTH,
  PAGE_KEY,
  PAGE_URL_MAX_LENGTH,
  messageShown,
  normalizeControl,
  onlineScreens,
  pageUrl,
} from './Slideshow/remote.js'
import { activeButtonClass, buttonClass, secondaryButtonClass } from './Slideshow/controlStyles.js'

/**
 * The screen slideshow's admin page: what the open screens show and live control of them (through Firebase's Realtime
 * Database, slideshow/), and the slides themselves (SlideList.vue, with an edit mode for ordering and editing them).
 */
const { jwt } = defineProps({
  // The admin's Firebase sign-in (App\Firebase::makeAdminJwt): database.rules.json lets it control the slideshow, and
  // it uploads the slides' images.
  jwt: {
    type: String,
    required: true,
  },
})

const card = 'rounded-sm bg-white p-4 shadow-sm sm:p-6'
const inputClass =
  'min-w-0 flex-1 basis-56 appearance-none rounded-sm px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-hidden focus:border-blue-300'

const database = liveDatabase()
// The server's time, for when a message that clears by itself is taken down.
const serverClock = watchServerClock(database)
const signedIn = ref(false)
const error = ref(null)
const current = ref(normalizeControl(null))
const presence = ref({})
const now = ref(serverClock.now())
const draft = ref('')
const autoClear = ref(false)
const pageDraft = ref('')
const hall = ref({ state: 'idle', competitions: [] })

let unsubscribes = []
let ticker = null
let messageTimer = null

onMounted(async () => {
  ticker = setInterval(() => (now.value = serverClock.now()), 5000)

  try {
    await signInWithCustomToken(liveAuth(), jwt)
  } catch {
    error.value = 'Kunde inte ansluta till skärmarna. Ladda om sidan och försök igen.'
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
  clearTimeout(messageTimer)
  serverClock.stop()
  unsubscribes.forEach((unsubscribe) => unsubscribe())
})

// A message that clears by itself is taken down from the database when its time is up (the screens hide it then on
// their own, also when this page is closed), and off the list below at once.
watch(
  () => current.value.message,
  (message) => {
    clearTimeout(messageTimer)
    if (message?.until) {
      messageTimer = setTimeout(
        () => {
          now.value = serverClock.now()
          if (current.value.message?.id === message.id) {
            void save({ message: null })
          }
        },
        Math.max(0, message.until - serverClock.now()),
      )
    }
  },
)

const screens = computed(() => onlineScreens(presence.value, now.value))
const showingNow = computed(() => new Set(screens.value.map((screen) => screen.slide)))

// Everything put on the screens from here that is still there, at the top of the page.
const onScreen = computed(() => {
  const items = []
  const control = current.value
  if (messageShown(control.message, now.value)) {
    const clear = () => save({ message: null })
    items.push({
      key: 'message',
      icon: MessageSquare,
      label: control.message.until ? 'Meddelande · tas bort efter 30 s' : 'Meddelande',
      text: control.message.text,
      action: 'Ta bort',
      clear,
    })
  }
  if (control.slide) {
    const name = screens.value.find((screen) => screen.slide === control.slide)?.showing ?? 'En slide'
    items.push({
      key: 'slide',
      icon: Pin,
      label: 'Slide',
      text: name,
      action: 'Släpp',
      clear: () => save({ slide: null }),
    })
  }
  if (control.page) {
    const clear = () => save({ page: null })
    items.push({ key: 'page', icon: Globe, label: 'Webbsida', text: control.page.url, action: 'Tillbaka', clear })
  }
  if (control.paused && !control.slide && !control.page) {
    const clear = () => save({ paused: false })
    items.push({
      key: 'paused',
      icon: Pause,
      label: 'Pausat',
      text: 'Bildspelet står still',
      action: 'Fortsätt',
      clear,
    })
  }
  return items
})

// Slides put in or out of the rotation, by key.
function saveRotation(choice) {
  void save(Object.fromEntries(Object.entries(choice).map(([key, on]) => [`rotation/${key}`, on])))
}

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

function showMessage() {
  const text = draft.value.trim().slice(0, MESSAGE_MAX_LENGTH)
  if (text) {
    const message = { id: Date.now().toString(36), text }
    void save({
      message: autoClear.value ? { ...message, at: serverTimestamp(), duration: MESSAGE_AUTO_CLEAR_MS } : message,
    })
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
