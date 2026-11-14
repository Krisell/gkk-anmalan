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

      <!-- Until a competition is chosen this comes first; after that it moves to the bottom, so that what is used during
           the broadcast is at the top (e.g. on a phone). -->
      <Teleport defer to="#competition-at-bottom" :disabled="!current.competition">
        <section class="card">
          <h2>Tävling</h2>
          <p v-if="current.competition" class="on-air chosen">
            Grafiken följer: {{ competitionLabel(current.competition) }}
          </p>
          <p v-else class="hint">Välj vilken tävling grafiken i OBS ska följa.</p>
          <p v-if="fixedInObs" class="error chosen">
            Grafiken i OBS visar tävling {{ onAir.competition }}, eftersom den står i OBS-adressen. Ta bort
            <code>?competition=…</code> ur adressen i OBS för att styra tävlingen härifrån.
          </p>

          <div v-if="pendingCompetition" class="confirm">
            <span>Byta grafiken till {{ competitionLabel(pendingCompetition) }}?</span>
            <button class="button primary" @click="chooseCompetition(pendingCompetition)">Byt tävling</button>
            <button class="button" @click="pendingCompetition = null">Avbryt</button>
          </div>

          <p v-if="liveCompetitionsState === 'loading'" class="hint">Hämtar tävlingar live på Powerlifting Live…</p>
          <p v-else-if="liveCompetitionsState === 'error'" class="hint">
            Kunde inte hämta tävlingarna från Powerlifting Live. Ange tävlingens id nedan.
          </p>
          <p v-else-if="!liveCompetitions.length" class="hint">Inga tävlingar live på Powerlifting Live just nu.</p>
          <div v-else class="choices competitions">
            <button
              v-for="competition in liveCompetitions"
              :key="competition.id"
              class="button choice group"
              :class="{ active: current.competition === String(competition.id) }"
              @click="pickCompetition(String(competition.id))"
            >
              <span>{{ competition.name }}</span>
              <span class="group-status">{{ competition.date ?? competition.id }}</span>
            </button>
          </div>

          <div class="message-row competition-id">
            <input
              v-model.trim="draft.competition"
              class="grow"
              inputmode="numeric"
              pattern="[0-9]*"
              maxlength="9"
              placeholder="Tävlingens id i Powerlifting Live, t.ex. 3001"
              @keydown.enter.prevent="pickTyped"
            />
            <button class="button" :disabled="!normalizeCompetition(draft.competition)" @click="pickTyped">Välj</button>
            <button class="button" @click="loadLiveCompetitions">Uppdatera listan</button>
          </div>
        </section>
      </Teleport>

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
        <p class="hint">
          Bildspel visar klubbens bildspel, samma som på hallens skärm. Vad som visas – t.ex. tidsschemat eller ett
          meddelande – styrs från <a href="/admin/slideshow" target="_blank" rel="noopener">/admin/slideshow</a>.
        </p>

        <h3>Bilder</h3>
        <p class="hint">
          Samma bilder som på <a href="/sek" target="_blank" rel="noopener">/sek</a>, över hela bilden. Paus visar när
          nästa grupp startar; annars står datum och plats från Inställningar under rubriken. Tidsschema är bildspelets
          DM-bild, bara i sändningen (hallens skärmar påverkas inte).
        </p>
        <div class="choices">
          <button
            v-for="choice in IMAGE_CHOICES"
            :key="choice.key"
            class="button choice"
            :class="{ active: shown === choice.key }"
            @click="save({ mode: 'image', image: choice.image })"
          >
            {{ choice.label }}
          </button>
          <button class="button choice" :class="{ active: shown === 'schedule' }" @click="save({ mode: 'schedule' })">
            Tidsschema
          </button>
        </div>
        <div class="message-row image-row">
          <input
            v-model="draft.imageText"
            class="grow"
            :maxlength="IMAGE_TEXT_MAX_LENGTH"
            placeholder="Egen bild, t.ex. Prisutdelning"
            @keydown.enter.prevent="showImageText"
          />
          <button
            class="button"
            :class="{ active: shown === 'image-custom' }"
            :disabled="!draft.imageText.trim()"
            @click="showImageText"
          >
            Visa egen bild
          </button>
        </div>
      </section>

      <section v-if="groups.length" class="card">
        <h2>Resultat per grupp</h2>
        <p class="hint">Visar gruppens resultatlista i sändningen tills du väljer något annat.</p>
        <label class="toggle">
          <input type="checkbox" :checked="current.instant" @change="save({ instant: $event.target.checked })" />
          <span>Utan animation – byten direkt, t.ex. under prisutdelningen</span>
        </label>
        <div class="choices">
          <button class="button choice" :class="{ active: shown === 'hidden' }" @click="save({ mode: 'hidden' })">
            Ingen grafik
          </button>
        </div>
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

        <h3>Pall{{ podiumGroup ? ` – ${podiumGroup.name}` : '' }}</h3>
        <p class="hint">
          Guld, silver och brons i en klass, nederst i bild. Uppifrån och ned efter placering, så det spelar ingen roll
          hur lyftarna står på pallen.
        </p>
        <div v-if="podiumGroup" class="choices">
          <button
            v-for="podiumClass in podiumGroup.classes"
            :key="podiumClass.key"
            class="button choice"
            :class="{
              active:
                current.mode === 'podium' &&
                current.podiumGroup === podiumGroup.name &&
                current.podiumClass === podiumClass.key,
            }"
            @click="save({ mode: 'podium', podiumGroup: podiumGroup.name, podiumClass: podiumClass.key })"
          >
            {{ podiumClass.label }}
          </button>
        </div>
        <p v-else class="hint">Välj en grupp ovan, så visas dess klasser här.</p>
      </section>

      <section v-if="platform" class="card">
        <h2>Lyftaren på tur</h2>
        <p class="on-air chosen">
          {{ platform.name }} · {{ disciplineName(platform.discipline) }} försök {{ platform.attempt }} ·
          {{ formatWeight(platform.weight) }} kg
        </p>
        <p class="hint">
          Bästa registrerade resultat (klassiskt) i förbundets resultatdatabas, data.styrkelyft.se. Dagens tävling är
          inte med. Hämtas av sig självt för alla i gruppen och sparas i den här webbläsaren över dagen
          <template v-if="bestsProgress.total">({{ bestsProgress.done }} av {{ bestsProgress.total }} hämtade)</template
          >.
        </p>
        <label class="toggle">
          <input type="checkbox" :checked="current.autoPb" @change="save({ autoPb: $event.target.checked })" />
          <span>
            Automatisk PB-stämpel – i sändningen och på hallens skärmar, några sekunder efter lyftarkortet och när
            klockan startar, om försöket slår lyftarens bästa registrerade resultat i grenen (inte totalen)
          </span>
        </label>
        <div class="choices">
          <button class="button action" :disabled="bestsState.loading" @click="refreshPlatformBests">
            {{ bestsState.loading ? 'Hämtar…' : platformBests ? 'Hämta igen' : 'Kolla PB' }}
          </button>
        </div>
        <p v-if="bestsState.error" class="error">{{ bestsState.error }}</p>
        <template v-else-if="platformBests">
          <p v-if="!platformBests.found" class="hint">Finns inte i resultatdatabasen (ingen licens hittad).</p>
          <template v-else>
            <ul class="bests">
              <li v-for="row in platformComparison" :key="row.key" :class="row.verdict">
                <span>{{ row.label }} {{ formatWeight(row.weight) }} kg</span>
                <strong>{{ VERDICTS[row.verdict] }}</strong>
                <span v-if="row.best" class="best-from">
                  Bästa {{ formatWeight(row.best.value) }} kg · {{ row.best.competition }} {{ row.best.date }}
                </span>
              </li>
            </ul>
            <p class="hint">
              Alla bästa: knäböj {{ bestText('squat') }} · bänk {{ bestText('bench') }} · mark
              {{ bestText('deadlift') }} · total {{ bestText('total') }} ({{ platformBests.competitions }} tävlingar)
            </p>
          </template>
        </template>
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
        <h2>Pausmusik</h2>
        <template v-if="MUSIC_TRACKS.length">
          <div class="choices">
            <button class="button choice" :class="{ active: !current.music }" @click="save({ music: false })">
              Av
            </button>
            <button class="button choice" :class="{ active: current.music }" @click="save({ music: true })">På</button>
          </div>
          <label class="field inline volume">
            <span>Volym</span>
            <input
              v-model.number="draft.musicVolume"
              type="range"
              min="0"
              max="1"
              step="0.05"
              @change="save({ musicVolume: draft.musicVolume })"
            />
            <span class="volume-value">{{ Math.round(draft.musicVolume * 100) }} %</span>
          </label>
          <p class="hint">
            Spelas i sändningen tills du stänger av den, oavsett vad som visas. Tonas in och ut. Höj eller sänk ljudet i
            OBS mixer om nivån inte stämmer mot mikrofonen.
          </p>
        </template>
        <p v-else class="hint">Ingen musik är tillagd ännu (se resources/js/live/assets/music/).</p>
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
        <label class="field">
          <span>Datum och plats (på bilderna)</span>
          <input
            v-model="draft.imageInfo"
            maxlength="120"
            placeholder="T.ex. Lördag 14 november · Friskis & Svettis Majorna, Göteborg"
            @change="save({ imageInfo: draft.imageInfo.trim() || null })"
          />
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

      <div id="competition-at-bottom" />

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
import { SIGN_IN_APPS, emulatorHost, liveDatabase } from '../remote/firebase.js'
import { DEFAULT_CONTROL, MESSAGE_MAX_LENGTH, normalizeCompetition, normalizeControl } from '../remote/controlState.js'
import { MUSIC_TRACKS } from '../pauseMusic.js'
import { IMAGE_TEXT_MAX_LENGTH } from '../streamImages.js'
import { normalizeFeed, normalizeResults } from '../normalize.js'
import { podiumClasses } from '../podium.js'
import { youTubeId } from '../sources/delayedSource.js'
import { BROADCASTER_LOGO as logo } from '../clubs.js'
import { compareWithBests } from './personalBest.js'
import { useLifterBests } from '../useLifterBests.js'
import { disciplineName, formatWeight } from '../format.js'

// "Visas nu": the overlay's mode, with the slates as separate choices.
const SHOWN = [
  { key: 'auto', label: 'Automatik', mode: 'auto' },
  { key: 'list', label: 'Resultatlista', mode: 'list' },
  { key: 'start', label: 'Startar snart', mode: 'slate', slate: 'start' },
  { key: 'pause', label: 'Paus', mode: 'slate', slate: 'pause' },
  { key: 'end', label: 'Tack för idag', mode: 'slate', slate: 'end' },
  { key: 'slideshow', label: 'Bildspel', mode: 'slideshow' },
  { key: 'hidden', label: 'Ingen grafik', mode: 'hidden' },
]

// The stream's images from /sek (the custom one has its own field).
const IMAGE_CHOICES = [
  { key: 'image-start', label: 'Börjar snart', image: 'start' },
  { key: 'image-pause', label: 'Paus', image: 'pause' },
  { key: 'image-end', label: 'Tack för att ni tittade', image: 'end' },
]

const GROUP_STATUS = { upcoming: 'Ej startad', ongoing: 'Pågår', done: 'Klar' }

const PRESETS = ['Rekordförsök!', 'Juryn överväger', 'Prisutdelning efter gruppen', 'Tack till våra funktionärer!']

// An overlay that hasn't reported for this long is considered gone (it reports at least every 30 s).
const PRESENCE_TIMEOUT_MS = 75000

const pin = ref('')
const busy = ref(false)
const error = ref(null)
const signedIn = ref(false)

const auth = liveAuth(SIGN_IN_APPS.liveControl)
const database = liveDatabase(SIGN_IN_APPS.liveControl)

const current = ref(normalizeControl(null))
const presence = ref({})
const now = ref(Date.now())
const draft = reactive({
  musicVolume: DEFAULT_CONTROL.musicVolume,
  title: '',
  pauseTime: '',
  message: '',
  competition: '',
  imageText: '',
  imageInfo: '',
})

let unsubscribes = []

onAuthStateChanged(auth, async (user) => {
  signedIn.value = Boolean(user && (await user.getIdTokenResult()).claims.liveControl === true)
  listen()
  if (signedIn.value && liveCompetitionsState.value !== 'loaded') {
    void loadLiveCompetitions()
  }
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
        draft.imageText = current.value.imageText ?? ''
        draft.imageInfo = current.value.imageInfo ?? ''
        draft.musicVolume = current.value.musicVolume
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

function showImageText() {
  const text = draft.imageText.trim().slice(0, IMAGE_TEXT_MAX_LENGTH)
  if (text) {
    void save({ mode: 'image', image: 'custom', imageText: text })
  }
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
  if (current.value.mode === 'image') {
    return `image-${current.value.image}`
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

// The competition: the overlay in OBS follows the one chosen here, unless its address in OBS has ?competition=.
const liveCompetitions = ref([])
const liveCompetitionsState = ref('idle')
const pendingCompetition = ref(null)

async function loadLiveCompetitions() {
  liveCompetitionsState.value = 'loading'
  try {
    const response = await fetch('/live/competitions', { headers: { Accept: 'application/json' } })
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`)
    }
    liveCompetitions.value = (await response.json()).competitions ?? []
    liveCompetitionsState.value = 'loaded'
  } catch {
    liveCompetitionsState.value = 'error'
  }
}

function competitionLabel(id) {
  const known = liveCompetitions.value.find((competition) => String(competition.id) === id)
  return known ? `${known.name} (${id})` : `tävling ${id}`
}

// Switching away from a chosen competition is confirmed first, since it changes what is on air.
function pickCompetition(id) {
  if (id === current.value.competition) {
    pendingCompetition.value = null
  } else if (current.value.competition) {
    pendingCompetition.value = id
  } else {
    void chooseCompetition(id)
  }
}

function pickTyped() {
  const id = normalizeCompetition(draft.competition)
  if (id) {
    pickCompetition(id)
    draft.competition = ''
  }
}

async function chooseCompetition(id) {
  pendingCompetition.value = null
  const name = liveCompetitions.value.find((competition) => String(competition.id) === id)?.name
  // The competition's name on the slates, if none has been given yet.
  const title = !current.value.title && name ? { title: name.slice(0, 80) } : {}
  await save({ competition: id, ...title })
}

const fixedInObs = computed(
  () =>
    Boolean(onAir.value?.competition && current.value.competition) &&
    onAir.value.competition !== current.value.competition,
)

// The preview: the overlay itself, for the competition the overlay in OBS shows (or the one chosen here, while the
// overlay isn't running), following the control like that one, scaled to fit.
const overlayUrl = document.body.dataset.overlayUrl
const previewCompetition = computed(() => onAir.value?.competition ?? current.value.competition ?? null)

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

// The competition's groups, for showing the results of another group (e.g. an earlier one), and their classes, for the
// podiums; refreshed every minute.
const groups = ref([])
// The group whose podiums are offered: the one whose podium or results list is shown, or was last (so that they stay
// while switching to "Ingen grafik" and back during the ceremony).
const lastPodiumGroup = ref(null)
watch(
  () =>
    current.value.mode === 'podium'
      ? current.value.podiumGroup
      : current.value.mode === 'list'
        ? current.value.listGroup
        : null,
  (name) => name && (lastPodiumGroup.value = name),
  { immediate: true },
)
const podiumGroup = computed(() => groups.value.find((group) => group.name === lastPodiumGroup.value) ?? null)
let groupsTimer = null

async function loadGroups() {
  clearTimeout(groupsTimer)
  if (previewCompetition.value) {
    try {
      const response = await fetch(`/live/results/${encodeURIComponent(previewCompetition.value)}`, {
        headers: { Accept: 'application/json' },
      })
      if (response.ok) {
        groups.value = normalizeResults(await response.json()).map(({ name, status, scoreboard }) => ({
          name,
          status,
          classes: podiumClasses(scoreboard),
        }))
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

// The lifter on the platform, from the feed (polled while signed in), and the best registered results of everyone in
// the group, looked up one at a time as they appear (the lifter on the platform first) and kept in this browser for
// the day (see useLifterBests.js); "Hämta igen" looks the lifter on the platform up again.
const VERDICTS = { pb: 'Nytt PB', equal: 'Tangerar PB', below: 'Under PB', first: 'Första registrerade' }
const platform = ref(null)
const groupLifterIds = ref([])
const lifterBests = useLifterBests(() => previewCompetition.value)
const bests = lifterBests.bests
const bestsState = reactive({ loading: false, error: null })
let platformTimer = null

async function loadPlatform() {
  clearTimeout(platformTimer)
  if (signedIn.value && previewCompetition.value) {
    try {
      const response = await fetch(`/live/feed/${encodeURIComponent(previewCompetition.value)}`, {
        headers: { Accept: 'application/json' },
      })
      if (response.ok) {
        const snapshot = normalizeFeed(await response.json())
        platform.value = snapshot.current
        groupLifterIds.value = [
          ...new Set([
            ...snapshot.order.map((entry) => entry.lifterId),
            ...snapshot.scoreboard.lifters.map((lifter) => lifter.id),
          ]),
        ].filter(Boolean)
        lifterBests.want([platform.value?.lifterId, ...groupLifterIds.value])
      }
    } catch {
      // Keep the lifters we have.
    }
  }
  platformTimer = setTimeout(loadPlatform, 5000)
}

async function refreshPlatformBests() {
  bestsState.loading = true
  bestsState.error = null
  if (!(await lifterBests.refresh(platform.value?.lifterId))) {
    bestsState.error = 'Kunde inte hämta från resultatdatabasen. Försök igen.'
  }
  bestsState.loading = false
}

const platformBests = computed(() => (platform.value ? (bests.value[platform.value.lifterId] ?? null) : null))
const platformComparison = computed(() => compareWithBests(platform.value, platformBests.value?.bests))
const bestsProgress = computed(() => {
  const ids = groupLifterIds.value
  return { done: ids.filter((id) => bests.value[id]).length, total: ids.length }
})

function bestText(lift) {
  const best = platformBests.value?.bests?.[lift]
  return best ? `${formatWeight(best.value)} kg` : '–'
}

watch(
  () => platform.value?.lifterId,
  () => (bestsState.error = null),
)

watch(previewCompetition, () => {
  platform.value = null
  groupLifterIds.value = []
})

let ticker = null
onMounted(() => {
  ticker = setInterval(() => (now.value = Date.now()), 5000)
  void loadPlatform()
})
onBeforeUnmount(() => {
  clearInterval(ticker)
  clearTimeout(groupsTimer)
  clearTimeout(platformTimer)
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

h3 {
  margin: 26px 0 14px;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 18px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--silver);
}

.bests {
  display: grid;
  gap: 8px;
  margin: 14px 0 0;
  padding: 0;
  list-style: none;
}

.bests li {
  display: flex;
  flex-wrap: wrap;
  align-items: baseline;
  gap: 4px 14px;
  padding: 10px 14px;
  border-left: 4px solid var(--silver);
  border-radius: 8px;
  background: var(--navy-deep);
  font-size: 18px;
}

.bests li.pb,
.bests li.first {
  border-left-color: var(--good);
}

.bests li.pb strong {
  color: #6fd59a;
}

.bests li.below strong {
  color: var(--silver);
}

.best-from {
  flex-basis: 100%;
  font-size: 14px;
  color: var(--silver);
}

.image-row {
  margin-top: 10px;
}

.hint {
  margin: -6px 0 14px;
  color: var(--silver);
}

.choices {
  display: grid;
  /* Two per row on a phone, so that the controls used during the broadcast fit near the top. */
  grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
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
  /* The hint under it pulls itself up (it usually follows a heading), so this keeps them apart. */
  margin: 16px 0 20px;
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

.chosen {
  margin: 0 0 14px;
}

.competitions {
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
}

.competition-id {
  margin-top: 14px;
}

.volume input[type='range'] {
  flex: 1 1 160px;
  min-height: 44px;
  padding: 0;
  border: 0;
  background: transparent;
  accent-color: #f6c945;
}

.volume-value {
  min-width: 3.5em;
  text-align: right;
  font-variant-numeric: tabular-nums;
}

.confirm {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px;
  margin-bottom: 14px;
  padding: 12px 14px;
  border-radius: 10px;
  background: var(--navy-deep);
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
