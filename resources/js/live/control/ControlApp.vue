<template>
  <main class="control">
    <header class="top">
      <div class="brand">
        <img :src="logo" alt="" class="logo" />
        <div>
          <div class="eyebrow">Livegrafik</div>
          <div class="heading">{{ draft.title || 'Styr grafiken' }}</div>
        </div>
      </div>
      <div v-if="signedIn" class="status">
        <span class="dot" :class="overlayStatus.kind" />
        <span>{{ overlayStatus.text }}</span>
      </div>
    </header>

    <!-- Sign in with the PIN. -->
    <form v-if="!signedIn" class="card sign-in" @submit.prevent="signIn">
      <h1>Logga in</h1>
      <p class="hint">Ange PIN-koden för livegrafiken.</p>
      <label class="field">
        <span>PIN-kod</span>
        <input
          v-model.trim="pin"
          inputmode="numeric"
          pattern="[0-9]*"
          autocomplete="one-time-code"
          maxlength="12"
          required
        />
      </label>
      <p v-if="error" class="error">{{ error }}</p>
      <button class="button primary" :disabled="busy">{{ busy ? 'Loggar in…' : 'Logga in' }}</button>
    </form>

    <template v-else>
      <section class="preview">
        <div ref="previewBox" class="preview-box">
          <iframe
            v-if="testVideoUrl"
            :src="testVideoUrl"
            title="Testvideo"
            class="preview-frame video"
            allow="autoplay; encrypted-media; fullscreen"
            :style="{ transform: `scale(${previewScale})` }"
          />
          <p v-if="!previewUrl" class="preview-empty">Förhandsvisningen syns när grafiken i OBS är igång.</p>
          <iframe
            v-else
            :src="previewUrl"
            title="Förhandsvisning"
            class="preview-frame"
            :style="{ transform: `scale(${previewScale})` }"
          />
        </div>
      </section>

      <section class="card">
        <h2>Visas nu</h2>
        <div class="choices">
          <button
            v-for="choice in SHOWN"
            :key="choice.key"
            class="button choice"
            :class="{ active: shown === choice.key }"
            @click="show(choice)"
          >
            {{ choice.label }}
          </button>
        </div>
        <label class="field inline">
          <span>Nästa grupp startar</span>
          <input v-model="draft.pauseTime" type="time" @change="save({ pauseTime: draft.pauseTime || null })" />
        </label>
      </section>

      <section v-if="groups.length" class="card">
        <h2>Resultat per grupp</h2>
        <p class="hint">Visar gruppens resultatlista i sändningen tills du väljer något annat.</p>
        <div class="choices">
          <button
            v-for="group in groups"
            :key="group.name"
            class="button choice group"
            :class="{ active: current.mode === 'list' && current.listGroup === group.name }"
            @click="save({ mode: 'list', listGroup: group.name })"
          >
            <span>{{ group.name }}</span>
            <span class="group-status" :class="group.status">{{ GROUP_STATUS[group.status] }}</span>
          </button>
        </div>
      </section>

      <section class="card">
        <h2>Snabbknappar</h2>
        <div class="choices">
          <button class="button action" @click="command('pb')">PB-stämpel</button>
          <button class="button action" @click="command('list')">Resultatlistan (10 s)</button>
          <button class="button action" @click="command('skip')">Hoppa över</button>
        </div>
      </section>

      <section class="card">
        <h2>Meddelande</h2>
        <p class="hint">Visas uppe till vänster tills du tar bort det.</p>
        <div class="message-row">
          <input
            v-model="draft.message"
            class="grow"
            :maxlength="MESSAGE_MAX_LENGTH"
            placeholder="T.ex. Rekordförsök!"
            @keydown.enter.prevent="showMessage(draft.message)"
          />
          <button class="button primary" :disabled="!draft.message.trim()" @click="showMessage(draft.message)">
            Visa
          </button>
          <button class="button" :disabled="!current.message" @click="save({ message: null })">Ta bort</button>
        </div>
        <div class="presets">
          <button v-for="preset in PRESETS" :key="preset" class="chip" @click="showMessage(preset)">
            {{ preset }}
          </button>
        </div>
        <p v-if="current.message" class="on-air">I sändning: ”{{ current.message.text }}”</p>
      </section>

      <section class="card">
        <h2>Inställningar</h2>
        <label class="field">
          <span>Tävlingens namn (på skyltarna)</span>
          <input v-model="draft.title" maxlength="80" @change="save({ title: draft.title.trim() || null })" />
        </label>
        <label class="toggle">
          <input type="checkbox" :checked="current.intros" @change="save({ intros: $event.target.checked })" />
          <span>Stort lyftarkort när en ny lyftare ropas upp</span>
        </label>
        <label class="toggle">
          <input
            type="checkbox"
            :checked="current.roundBoards"
            @change="save({ roundBoards: $event.target.checked })"
          />
          <span>Resultatlista mellan omgångarna</span>
        </label>
      </section>

      <section class="card">
        <h2>Testa mot en videoström</h2>
        <p class="hint">
          Bara för test, och bara på den här enheten: spelar en annan sändning (t.ex. SM) bakom förhandsvisningen.
          Fördröjningen håller förhandsvisningens grafik tillbaka så att den hamnar i takt med videon.
        </p>
        <label class="field">
          <span>Länk till sändningen (YouTube eller annan sida)</span>
          <input v-model.trim="testVideo" type="url" placeholder="https://…" />
        </label>
        <label class="field inline">
          <span>Fördröjning (sekunder)</span>
          <input v-model.number="testDelay" type="number" min="0" max="120" step="1" class="short" />
        </label>
      </section>

      <p v-if="error" class="error">{{ error }}</p>
      <footer class="footer">
        <button class="link" @click="signOutHere">Logga ut</button>
      </footer>
    </template>
  </main>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue'
import { onAuthStateChanged, signInWithCustomToken, signOut } from 'firebase/auth'
import { onValue, push, ref as databaseRef, serverTimestamp, update } from 'firebase/database'
import { liveAuth } from '../remote/auth.js'
import { emulatorHost, liveDatabase } from '../remote/firebase.js'
import { MESSAGE_MAX_LENGTH, normalizeControl } from '../remote/controlState.js'
import { normalizeResults } from '../normalize.js'
import { youTubeId } from '../sources/delayedSource.js'
import { BROADCASTER_LOGO as logo } from '../clubs.js'

// "Visas nu": the overlay's mode, with the slates as separate choices.
const SHOWN = [
  { key: 'auto', label: 'Automatik', mode: 'auto' },
  { key: 'list', label: 'Resultatlista', mode: 'list' },
  { key: 'start', label: 'Startar snart', mode: 'slate', slate: 'start' },
  { key: 'pause', label: 'Paus', mode: 'slate', slate: 'pause' },
  { key: 'end', label: 'Tack för idag', mode: 'slate', slate: 'end' },
  { key: 'hidden', label: 'Ingen grafik', mode: 'hidden' },
]

const GROUP_STATUS = { upcoming: 'Ej startad', ongoing: 'Pågår', done: 'Klar' }

const PRESETS = ['Rekordförsök!', 'Juryn överväger', 'Prisutdelning efter gruppen', 'Tack till våra funktionärer!']

// An overlay that hasn't reported for this long is considered gone (it reports at least every 30 s).
const PRESENCE_TIMEOUT_MS = 75000

const pin = ref('')
const busy = ref(false)
const error = ref(null)
const signedIn = ref(false)

const auth = liveAuth()
const database = liveDatabase()

const current = ref(normalizeControl(null))
const presence = ref({})
const now = ref(Date.now())
const draft = reactive({ title: '', pauseTime: '', message: '' })

let unsubscribes = []

onAuthStateChanged(auth, async (user) => {
  signedIn.value = Boolean(user && (await user.getIdTokenResult()).claims.liveControl === true)
  listen()
})

async function signIn() {
  busy.value = true
  error.value = null
  try {
    const response = await fetch('/live/control/token', {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
      },
      body: JSON.stringify({ pin: pin.value }),
    })
    if (response.status === 429) {
      throw new Error('För många försök. Vänta en minut och försök igen.')
    }
    if (!response.ok) {
      throw new Error(response.status === 403 ? 'Fel PIN-kod.' : 'Kunde inte logga in.')
    }
    await signInWithCustomToken(auth, (await response.json()).token)
    pin.value = ''
  } catch (e) {
    error.value = e.message
  } finally {
    busy.value = false
  }
}

async function signOutHere() {
  await signOut(auth)
}

function listen() {
  unsubscribes.forEach((unsubscribe) => unsubscribe())
  unsubscribes = []
  if (!signedIn.value) {
    return
  }

  const path = 'live'
  unsubscribes = [
    onValue(databaseRef(database, `${path}/control`), (snapshot) => {
      const raw = snapshot.val() ?? {}
      current.value = normalizeControl(raw)
      // Fill the fields from what is saved, without overwriting what is being typed.
      const focused = document.activeElement
      if (!(focused instanceof HTMLInputElement) || focused.type === 'checkbox') {
        draft.title = current.value.title ?? ''
        draft.pauseTime = current.value.pauseTime ?? ''
      }
    }),
    onValue(databaseRef(database, `${path}/presence`), (snapshot) => (presence.value = snapshot.val() ?? {})),
  ]
}

async function save(patch) {
  error.value = null
  try {
    await update(databaseRef(database, 'live/control'), patch)
  } catch {
    error.value = 'Kunde inte spara. Kontrollera uppkopplingen, eller logga in igen.'
  }
}

async function command(type) {
  error.value = null
  try {
    await push(databaseRef(database, 'live/commands'), { type, at: serverTimestamp() })
  } catch {
    error.value = 'Kunde inte skicka. Kontrollera uppkopplingen, eller logga in igen.'
  }
}

function show(choice) {
  // "Resultatlista" is the group on the platform; other groups are chosen under "Resultat per grupp".
  void save(choice.slate ? { mode: choice.mode, slate: choice.slate } : { mode: choice.mode, listGroup: null })
}

function showMessage(text) {
  const trimmed = text.trim().slice(0, MESSAGE_MAX_LENGTH)
  if (trimmed) {
    void save({ message: { id: Date.now().toString(36), text: trimmed } })
    draft.message = ''
  }
}

const shown = computed(() => {
  if (current.value.mode === 'list' && current.value.listGroup) {
    return null // shown under "Resultat per grupp"
  }
  return current.value.mode === 'slate' ? current.value.slate : current.value.mode
})

// The overlay in OBS: online if one has reported recently (the latest, if there are several).
const onAir = computed(
  () =>
    Object.values(presence.value)
      .filter((entry) => now.value - (entry?.at ?? 0) < PRESENCE_TIMEOUT_MS)
      .sort((a, b) => b.at - a.at)[0] ?? null,
)

const overlayStatus = computed(() => {
  const latest = onAir.value
  if (!latest) {
    return { kind: 'offline', text: 'Grafiken i OBS är inte igång' }
  }
  const feed =
    latest.feed === 'live' ? '' : latest.feed === 'stale' ? ' · tävlingsdata släpar' : ' · ingen tävlingsdata'
  return { kind: latest.feed === 'live' ? 'online' : 'warning', text: `I sändning: ${latest.showing}${feed}` }
})

// The preview: the overlay itself, for the competition the overlay in OBS shows, following the control like that one,
// scaled to fit.
const overlayUrl = document.body.dataset.overlayUrl
const previewCompetition = computed(() => onAir.value?.competition ?? null)

// Testing against someone else's stream (e.g. SM): a video behind the preview, and the preview's graphics delayed to
// match it. Remembered on this device only.
const TEST_VIDEO_KEY = 'live-control-test-video'
const TEST_DELAY_KEY = 'live-control-test-delay'
const testVideo = ref(remembered(TEST_VIDEO_KEY) ?? '')
const testDelay = ref(Number(remembered(TEST_DELAY_KEY)) || 0)
watch(testVideo, (value) => remember(TEST_VIDEO_KEY, value))
watch(testDelay, (value) => remember(TEST_DELAY_KEY, String(value || 0)))

const testVideoUrl = computed(() => {
  if (!testVideo.value.startsWith('https://')) {
    return null
  }
  const id = youTubeId(testVideo.value)
  return id ? `https://www.youtube.com/embed/${id}?autoplay=1&mute=1&playsinline=1` : testVideo.value
})

const previewUrl = computed(() => {
  if (!previewCompetition.value) {
    return null
  }
  const url = new URL(overlayUrl, window.location.href)
  url.searchParams.set('competition', previewCompetition.value)
  const delay = Math.max(0, Math.min(120, Math.round(testDelay.value || 0)))
  if (testVideoUrl.value && delay) {
    url.searchParams.set('delay', String(delay))
  }
  url.searchParams.set('preview', '1')
  const host = emulatorHost()
  if (host) {
    url.searchParams.set('firebaseEmulator', host)
  }
  return url.toString()
})
const previewBox = ref(null)
const previewScale = ref(0.3)
let observer = null

watch(previewBox, (box) => {
  observer?.disconnect()
  if (box) {
    observer = new ResizeObserver(([entry]) => (previewScale.value = entry.contentRect.width / 1920))
    observer.observe(box)
  }
})

function remembered(key) {
  try {
    return localStorage.getItem(key)
  } catch {
    return null
  }
}

function remember(key, value) {
  try {
    if (value) {
      localStorage.setItem(key, value)
    } else {
      localStorage.removeItem(key)
    }
  } catch {
    // Private browsing: just not remembered.
  }
}

// The competition's groups, for showing the results of another group (e.g. an earlier one), refreshed every minute.
const groups = ref([])
let groupsTimer = null

async function loadGroups() {
  clearTimeout(groupsTimer)
  if (previewCompetition.value) {
    try {
      const response = await fetch(`/live/results/${encodeURIComponent(previewCompetition.value)}`, {
        headers: { Accept: 'application/json' },
      })
      if (response.ok) {
        groups.value = normalizeResults(await response.json()).map(({ name, status }) => ({ name, status }))
      }
    } catch {
      // Keep the list we have.
    }
  }
  groupsTimer = setTimeout(loadGroups, 60000)
}

watch(previewCompetition, (competition, previous) => {
  if (competition !== previous) {
    groups.value = []
    void loadGroups()
  }
})

let ticker = null
onMounted(() => (ticker = setInterval(() => (now.value = Date.now()), 5000)))
onBeforeUnmount(() => {
  clearInterval(ticker)
  clearTimeout(groupsTimer)
  observer?.disconnect()
  unsubscribes.forEach((unsubscribe) => unsubscribe())
})
</script>

<style>
:root {
  color-scheme: dark;
}

html,
body {
  margin: 0;
  background: #161f3b;
  color: #ffffff;
  font-family: 'Barlow', 'Helvetica Neue', Arial, sans-serif;
  -webkit-text-size-adjust: 100%;
}
</style>

<style scoped>
.control {
  --navy: #314270;
  --navy-light: #45557e;
  --navy-deep: #222f57;
  --silver: #a6acb9;
  --good: #2f9a5c;
  --failed: #c7453d;

  max-width: 1100px;
  margin: 0 auto;
  padding: max(16px, env(safe-area-inset-top)) 16px 40px;
}

.top {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 12px 24px;
  margin-bottom: 16px;
}

.brand {
  display: flex;
  align-items: center;
  gap: 14px;
}

.logo {
  width: 52px;
  height: 52px;
  padding: 2px;
  border-radius: 50%;
  background: #ffffff;
}

.eyebrow {
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: var(--silver);
}

.heading {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 28px;
  font-weight: 700;
  line-height: 1.1;
}

.status {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 16px;
  border-radius: 999px;
  background: var(--navy-deep);
  font-size: 16px;
}

.dot {
  flex: none;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: var(--failed);
}

.dot.online {
  background: var(--good);
}

.dot.warning {
  background: #d9a23a;
}

.preview {
  margin-bottom: 16px;
}

.preview-box {
  position: relative;
  aspect-ratio: 16 / 9;
  overflow: hidden;
  border-radius: 12px;
  background:
    linear-gradient(135deg, rgba(255, 255, 255, 0.04) 25%, transparent 25%) 0 0 / 40px 40px,
    #0d1430;
}

.preview-frame.video {
  pointer-events: auto;
}

input.short {
  width: 110px;
}

.preview-empty {
  position: absolute;
  inset: 0;
  display: grid;
  place-items: center;
  margin: 0;
  padding: 20px;
  color: var(--silver);
  text-align: center;
}

.preview-frame {
  position: absolute;
  top: 0;
  left: 0;
  width: 1920px;
  height: 1080px;
  border: 0;
  transform-origin: 0 0;
  pointer-events: none;
  /* The same color scheme as the overlay page, or the browser paints the frame opaque instead of transparent. */
  color-scheme: normal;
}

.card {
  margin-bottom: 16px;
  padding: 20px;
  border-radius: 14px;
  background: var(--navy);
}

h1,
h2 {
  margin: 0 0 14px;
  font-family: 'Barlow Condensed', sans-serif;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

h1 {
  font-size: 30px;
}

h2 {
  font-size: 22px;
}

.hint {
  margin: -6px 0 14px;
  color: var(--silver);
}

.choices {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 10px;
}

.button {
  min-height: 58px;
  padding: 0 18px;
  border: 2px solid transparent;
  border-radius: 10px;
  background: var(--navy-light);
  color: #ffffff;
  font:
    600 18px 'Barlow',
    sans-serif;
  cursor: pointer;
  touch-action: manipulation;
}

.button:active {
  transform: scale(0.98);
}

.button:disabled {
  opacity: 0.45;
  cursor: default;
}

.button.active {
  border-color: #ffffff;
  background: #ffffff;
  color: var(--navy-deep);
}

.button.group {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2px;
}

.group-status {
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--silver);
}

.group-status.ongoing {
  color: #9be3b5;
}

.button.active .group-status {
  color: var(--navy-light);
}

.button.primary {
  background: var(--good);
}

.button.action {
  background: var(--navy-deep);
}

.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 14px;
  font-size: 15px;
  color: var(--silver);
}

.field.inline {
  flex-direction: row;
  align-items: center;
  gap: 14px;
  margin: 16px 0 0;
}

input:not([type='checkbox']) {
  min-height: 52px;
  padding: 0 14px;
  border: 2px solid transparent;
  border-radius: 10px;
  background: #ffffff;
  color: #111827;
  font:
    500 18px 'Barlow',
    sans-serif;
}

input:focus {
  outline: none;
  border-color: #9fb4ff;
}

.message-row {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.grow {
  flex: 1 1 260px;
}

.presets {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 12px;
}

.chip {
  min-height: 44px;
  padding: 0 16px;
  border: 0;
  border-radius: 999px;
  background: var(--navy-deep);
  color: #ffffff;
  font:
    500 16px 'Barlow',
    sans-serif;
  cursor: pointer;
}

.on-air {
  margin: 14px 0 0;
  color: var(--silver);
}

.toggle {
  display: flex;
  align-items: center;
  gap: 14px;
  min-height: 52px;
  font-size: 17px;
}

.toggle input {
  width: 26px;
  height: 26px;
  accent-color: var(--good);
}

.sign-in {
  max-width: 460px;
  margin: 40px auto;
}

.sign-in .button {
  width: 100%;
}

.error {
  color: #ffb4ad;
  font-weight: 600;
}

.footer {
  display: flex;
  justify-content: center;
}

.link {
  min-height: 44px;
  border: 0;
  background: none;
  color: var(--silver);
  font:
    500 16px 'Barlow',
    sans-serif;
  text-decoration: underline;
  cursor: pointer;
}
</style>
