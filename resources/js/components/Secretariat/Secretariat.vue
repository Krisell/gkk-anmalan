<template>
  <div class="relative min-h-screen overflow-hidden bg-linear-to-br from-slate-50 via-white to-slate-100">
    <!-- The slideshow's soft light: a blue glow bottom left and a warm one top right, and the plate rings. -->
    <div
      class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,rgba(49,66,112,0.10),transparent_60%),radial-gradient(ellipse_at_top_right,rgba(246,201,69,0.22),transparent_55%)]"
    ></div>
    <svg
      class="pointer-events-none absolute -right-48 -top-48 h-[36rem] w-[36rem] text-gkk/[0.05]"
      viewBox="0 0 200 200"
      fill="none"
      stroke="currentColor"
      aria-hidden="true"
    >
      <circle cx="100" cy="100" r="98" stroke-width="2" />
      <circle cx="100" cy="100" r="84" stroke-width="1" />
      <circle cx="100" cy="100" r="66" stroke-width="1" />
      <circle cx="100" cy="100" r="16" stroke-width="7" />
    </svg>

    <div class="relative mx-auto max-w-6xl px-4 py-10 sm:px-8 sm:py-16">
      <header class="flex flex-col items-start gap-6 sm:flex-row sm:items-center sm:gap-8">
        <a
          href="/"
          class="flex h-24 w-24 shrink-0 items-center justify-center rounded-full bg-white p-1.5 shadow-2xl shadow-gkk/20 ring-1 ring-gkk/10 sm:h-28 sm:w-28"
        >
          <img class="h-full w-full object-contain" :src="LOGO" alt="Göteborg Kraftsportklubb" />
        </a>
        <div class="min-w-0">
          <div
            class="inline-flex items-center gap-2 rounded-full bg-gkk px-4 py-1.5 text-xs font-bold uppercase tracking-[0.18em] text-white"
          >
            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
            Tävlingsdag
          </div>
          <h1 class="mt-3 text-5xl font-extrabold leading-[0.95] tracking-tight text-gkk sm:text-6xl">Sekretariatet</h1>
          <div class="mt-4 h-1.5 w-20 rounded-full bg-amber-300"></div>
        </div>
      </header>
      <p class="mt-6 max-w-2xl text-lg leading-snug text-gkk-light text-balance">
        Länkar och verktyg för tävlingssekreterare – allt som behövs vid sekretariatsbordet på ett ställe.
      </p>

      <!-- Links -->
      <section class="mt-12">
        <h2 class="text-sm font-bold uppercase tracking-[0.18em] text-gkk-light">Länkar</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
          <a
            v-for="link in LINKS"
            :key="link.href"
            :href="link.href"
            target="_blank"
            rel="noopener"
            class="group flex items-start gap-4 rounded-2xl bg-white/90 p-5 shadow-xl shadow-gkk/10 ring-1 ring-gkk/10 transition hover:-translate-y-0.5 hover:shadow-2xl hover:shadow-gkk/20 hover:ring-gkk/25"
          >
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gkk text-amber-300">
              <component :is="link.icon" class="h-7 w-7" :stroke-width="1.75" />
            </div>
            <div class="min-w-0 flex-1">
              <p class="flex items-center gap-1.5 text-lg font-bold leading-tight text-gkk">
                {{ link.title }}
                <ExternalLink
                  class="h-4 w-4 shrink-0 text-gkk-lightest transition group-hover:text-gkk"
                  :stroke-width="2"
                />
              </p>
              <p class="mt-1 text-sm leading-snug text-gkk-light">{{ link.text }}</p>
              <p class="mt-2 truncate text-xs font-medium text-gkk-lightest">{{ link.host }}</p>
            </div>
          </a>
        </div>
      </section>

      <!-- Signs -->
      <section class="mt-14">
        <div class="flex flex-wrap items-end justify-between gap-2">
          <div>
            <h2 class="text-sm font-bold uppercase tracking-[0.18em] text-gkk-light">Platslappar</h2>
            <p class="mt-1 text-gkk-light">
              A4 liggande, svartvitt med klubbens logga. Klicka på en lapp för att skriva ut den.
            </p>
          </div>
        </div>

        <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          <button
            v-for="sign in SIGNS"
            :key="sign"
            type="button"
            class="group rounded-2xl bg-white/90 p-3 text-left shadow-xl shadow-gkk/10 ring-1 ring-gkk/10 transition hover:-translate-y-0.5 hover:shadow-2xl hover:shadow-gkk/20 hover:ring-gkk/25 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gkk"
            @click="print(sign)"
          >
            <div class="overflow-hidden rounded-lg ring-1 ring-black/10">
              <Platslapp :text="sign" preview />
            </div>
            <div class="mt-3 flex items-center justify-between px-1">
              <span class="font-bold text-gkk">{{ sign }}</span>
              <span
                class="inline-flex items-center gap-1.5 rounded-full bg-gkk/5 px-3 py-1 text-sm font-semibold text-gkk transition group-hover:bg-gkk group-hover:text-white"
              >
                <Printer class="h-4 w-4" :stroke-width="2" />
                Skriv ut
              </span>
            </div>
          </button>

          <!-- A sign of one's own -->
          <form
            class="rounded-2xl bg-gkk p-3 text-left shadow-xl shadow-gkk/20 ring-1 ring-gkk/10"
            @submit.prevent="print(custom.trim())"
          >
            <div class="overflow-hidden rounded-lg ring-1 ring-black/10">
              <Platslapp :text="custom.trim()" placeholder="Egen text" preview />
            </div>
            <div class="mt-3 flex items-center gap-2">
              <label class="sr-only" for="custom-sign">Egen platslapp</label>
              <input
                id="custom-sign"
                v-model="custom"
                type="text"
                maxlength="60"
                placeholder="Skriv egen text…"
                class="min-w-0 flex-1 rounded-full border-0 bg-white/10 px-4 py-1.5 text-white placeholder:text-white/50 focus:bg-white/15 focus:ring-2 focus:ring-amber-300"
              />
              <button
                type="submit"
                :disabled="!custom.trim()"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-amber-300 px-3 py-1.5 text-sm font-bold text-gkk transition hover:bg-amber-200 disabled:cursor-not-allowed disabled:opacity-40"
              >
                <Printer class="h-4 w-4" :stroke-width="2" />
                Skriv ut
              </button>
            </div>
          </form>
        </div>
      </section>
      <!-- Signpost -->
      <section class="mt-14">
        <h2 class="text-sm font-bold uppercase tracking-[0.18em] text-gkk-light">Vägvisare</h2>
        <p class="mt-1 text-gkk-light">
          A4 liggande, svartvitt – välj åt vilket håll varje plats ligger från där skylten sitter. Platserna ordnas
          efter riktning, och mitten döljer en plats.
        </p>

        <form
          class="mt-5 grid gap-6 rounded-2xl bg-white/90 p-5 shadow-xl shadow-gkk/10 ring-1 ring-gkk/10 lg:grid-cols-[1.4fr_1fr]"
          @submit.prevent="printSignpost"
        >
          <div class="self-start overflow-hidden rounded-lg ring-1 ring-black/10">
            <Vagvisare :items="signpost" :kicker="stream.competition.trim()" preview />
          </div>
          <div class="flex flex-col gap-5">
            <div class="grid grid-cols-2 gap-x-4 gap-y-5">
              <fieldset v-for="item in signpost" :key="item.label">
                <legend class="text-xs font-bold uppercase tracking-[0.14em] text-gkk-light">{{ item.label }}</legend>
                <div class="mt-2 grid w-max grid-cols-3 gap-1">
                  <button
                    v-for="choice in COMPASS"
                    :key="choice.direction"
                    type="button"
                    :aria-label="`${item.label}: ${choice.name}`"
                    :aria-pressed="item.direction === choice.direction"
                    class="flex h-9 w-9 items-center justify-center rounded-lg ring-1 transition"
                    :class="
                      item.direction === choice.direction
                        ? 'bg-gkk text-amber-300 ring-gkk'
                        : 'bg-white text-gkk-light ring-gkk/15 hover:bg-gkk/5 hover:text-gkk'
                    "
                    @click="item.direction = choice.direction"
                  >
                    <component :is="choice.icon" class="h-5 w-5" :stroke-width="2.25" />
                  </button>
                </div>
              </fieldset>
            </div>
            <div class="mt-auto flex items-center justify-between gap-3">
              <button type="button" class="text-sm font-medium text-gkk-light hover:text-gkk" @click="resetSignpost">
                Återställ
              </button>
              <button
                type="submit"
                :disabled="!signpost.some((item) => item.direction)"
                class="inline-flex items-center gap-2 rounded-full bg-gkk px-5 py-2.5 font-bold text-white shadow-lg shadow-gkk/20 transition hover:bg-gkk-light disabled:cursor-not-allowed disabled:opacity-40"
              >
                <Printer class="h-4 w-4 text-amber-300" :stroke-width="2" />
                Skriv ut
              </button>
            </div>
          </div>
        </form>
      </section>

      <!-- Welcome sign -->
      <section class="mt-14">
        <h2 class="text-sm font-bold uppercase tracking-[0.18em] text-gkk-light">Välkomstskylt</h2>
        <p class="mt-1 text-gkk-light">A4 liggande, svartvitt – till entrén eller tävlingsflaket.</p>

        <form
          class="mt-5 grid gap-6 rounded-2xl bg-white/90 p-5 shadow-xl shadow-gkk/10 ring-1 ring-gkk/10 lg:grid-cols-[1.4fr_1fr]"
          @submit.prevent="print(welcome.title.trim(), welcome.from.trim())"
        >
          <div class="overflow-hidden rounded-lg ring-1 ring-black/10">
            <Platslapp :text="welcome.title.trim()" :sub-text="welcome.from.trim()" placeholder="Välkomna" preview />
          </div>
          <div class="flex flex-col gap-4">
            <label class="block">
              <span class="text-xs font-bold uppercase tracking-[0.14em] text-gkk-light">Rubrik</span>
              <textarea
                v-model="welcome.title"
                rows="3"
                class="mt-1.5 resize-none w-full rounded-xl border-0 bg-white px-4 py-2.5 text-gkk shadow-sm ring-1 ring-gkk/15 placeholder:text-gkk-lightest focus:ring-2 focus:ring-gkk"
              ></textarea>
            </label>
            <label class="block">
              <span class="text-xs font-bold uppercase tracking-[0.14em] text-gkk-light">Avsändare</span>
              <textarea
                v-model="welcome.from"
                rows="3"
                class="mt-1.5 resize-none w-full rounded-xl border-0 bg-white px-4 py-2.5 text-gkk shadow-sm ring-1 ring-gkk/15 placeholder:text-gkk-lightest focus:ring-2 focus:ring-gkk"
              ></textarea>
            </label>
            <div class="mt-auto flex items-center justify-between gap-3">
              <button type="button" class="text-sm font-medium text-gkk-light hover:text-gkk" @click="resetWelcome">
                Återställ
              </button>
              <button
                type="submit"
                :disabled="!welcome.title.trim()"
                class="inline-flex items-center gap-2 rounded-full bg-gkk px-5 py-2.5 font-bold text-white shadow-lg shadow-gkk/20 transition hover:bg-gkk-light disabled:cursor-not-allowed disabled:opacity-40"
              >
                <Printer class="h-4 w-4 text-amber-300" :stroke-width="2" />
                Skriv ut
              </button>
            </div>
          </div>
        </form>
      </section>

      <!-- Stream splash screens -->
      <section class="mt-14">
        <div class="flex flex-wrap items-start gap-2 sm:items-center">
          <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gkk text-amber-300">
            <Youtube class="h-5 w-5" :stroke-width="1.75" />
          </div>
          <div>
            <h2 class="text-sm font-bold uppercase tracking-[0.18em] text-gkk-light">Bilder till sändningen</h2>
            <p class="text-gkk-light">
              1920×1080 PNG – som bild i OBS före start och i pauser, eller som miniatyr på YouTube.
            </p>
          </div>
        </div>

        <div
          class="mt-5 grid gap-4 rounded-2xl bg-white/90 p-5 shadow-xl shadow-gkk/10 ring-1 ring-gkk/10 md:grid-cols-[1fr_1fr_auto] md:items-end"
        >
          <label class="block">
            <span class="text-xs font-bold uppercase tracking-[0.14em] text-gkk-light">Tävling</span>
            <input
              v-model="stream.competition"
              type="text"
              class="mt-1.5 w-full rounded-xl border-0 bg-white px-4 py-2.5 text-gkk shadow-sm ring-1 ring-gkk/15 placeholder:text-gkk-lightest focus:ring-2 focus:ring-gkk"
            />
          </label>
          <label class="block">
            <span class="text-xs font-bold uppercase tracking-[0.14em] text-gkk-light">Datum och plats</span>
            <input
              v-model="stream.info"
              type="text"
              class="mt-1.5 w-full rounded-xl border-0 bg-white px-4 py-2.5 text-gkk shadow-sm ring-1 ring-gkk/15 placeholder:text-gkk-lightest focus:ring-2 focus:ring-gkk"
            />
          </label>
          <button type="button" class="py-2.5 text-sm font-medium text-gkk-light hover:text-gkk" @click="resetStream">
            Återställ
          </button>
        </div>

        <div class="mt-5 grid gap-5 md:grid-cols-2">
          <div
            v-for="(screen, index) in screens"
            :key="index"
            class="rounded-2xl p-3 shadow-xl ring-1 ring-gkk/10"
            :class="screen.custom ? 'bg-gkk shadow-gkk/20' : 'bg-white/90 shadow-gkk/10'"
          >
            <div class="overflow-hidden rounded-lg ring-1 ring-black/10">
              <StreamSplash
                :ref="(el) => (splashes[index] = el)"
                :kicker="stream.competition"
                :title="screen.custom ? customSplash : screen.title"
                :sub-text="stream.info"
                placeholder="Egen text"
              />
            </div>
            <div class="mt-3 flex items-center gap-2 px-1">
              <span v-if="!screen.custom" class="min-w-0 flex-1 truncate font-bold text-gkk">{{ screen.title }}</span>
              <input
                v-else
                v-model="customSplash"
                type="text"
                maxlength="60"
                placeholder="Skriv egen text…"
                aria-label="Egen bild till sändningen"
                class="min-w-0 flex-1 rounded-full border-0 bg-white/10 px-4 py-1.5 text-white placeholder:text-white/50 focus:bg-white/15 focus:ring-2 focus:ring-amber-300"
              />
              <button
                type="button"
                :disabled="screen.custom && !customSplash.trim()"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-40"
                :class="
                  screen.custom
                    ? 'bg-amber-300 text-gkk hover:bg-amber-200'
                    : 'bg-gkk/5 text-gkk hover:bg-gkk hover:text-white'
                "
                @click="splashes[index].download(fileName(screen.custom ? customSplash : screen.title))"
              >
                <Download class="h-4 w-4" :stroke-width="2" />
                Ladda ner
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- The sheet that is printed: off screen, and alone on the page when printing (see resources/views/sek.blade.php). -->
    <Teleport to="body">
      <div class="sign-print" aria-hidden="true">
        <Vagvisare
          v-if="printing === 'signpost'"
          ref="printSheet"
          :items="signpost"
          :kicker="stream.competition.trim()"
        />
        <Platslapp v-else ref="printSheet" :text="printText" :sub-text="printSubText" />
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { nextTick, reactive, ref, watch } from 'vue'
import {
  ArrowDown,
  ArrowDownLeft,
  ArrowDownRight,
  ArrowLeft,
  ArrowRight,
  ArrowUp,
  ArrowUpLeft,
  ArrowUpRight,
  Database,
  Download,
  ExternalLink,
  EyeOff,
  MonitorPlay,
  Presentation,
  Printer,
  Ticket,
  Youtube,
} from 'lucide-vue-next'
import Platslapp from './Platslapp.vue'
import StreamSplash from './StreamSplash.vue'
import Vagvisare from './Vagvisare.vue'
import { STREAM_IMAGES } from '../../live/streamImages.js'

const LOGO = 'https://goteborg-kraftsportklubb.web.app/img/logo-min.png'

const LINKS = [
  {
    icon: MonitorPlay,
    title: 'Powerlifting Live',
    text: 'Tävlingssystemet – lyftordning, försök och domarsignaler.',
    href: 'https://se.powerliftinglive.com',
    host: 'se.powerliftinglive.com',
  },
  {
    icon: Database,
    title: 'Styrkelyft Data',
    text: 'Anmälningar till tävlingen och rapportering av resultat.',
    href: 'https://data.styrkelyft.se',
    host: 'data.styrkelyft.se',
  },
  {
    icon: Ticket,
    title: 'Höjningskort',
    text: 'PDF att skriva ut när höjningskorten tar slut.',
    href: 'https://www.styrkelyft.se/wp-content/uploads/2026/06/Tavling-Hojningslapp-pdf.pdf',
    host: 'styrkelyft.se',
  },
  {
    icon: Presentation,
    title: 'Slideshow',
    text: 'Bildspelet för hallens skärm – resultat, rekord och meddelanden.',
    href: '/slides-for-screen',
    host: '/slides-for-screen',
  },
]

const SIGNS = ['Pant', 'Skräp', 'Invägning', 'Ställningshöjder']

const custom = ref('')
const printText = ref('')
const printSubText = ref('')
const printSheet = ref()
// Which sign is printed: a Platslapp or the signpost.
const printing = ref('')

async function printSheetAs(kind) {
  printing.value = kind
  await nextTick()
  await printSheet.value.fit()
  window.print()
}

async function print(text, subText = '') {
  if (!text) {
    return
  }
  printText.value = text
  printSubText.value = subText
  await printSheetAs('platslapp')
}

// The signpost: where each place is from where the sign is posted, which differs from sign to sign. In the order of
// the compass on the page, with no direction in the middle.
const COMPASS = [
  { direction: 'nw', name: 'snett framåt vänster', icon: ArrowUpLeft },
  { direction: 'n', name: 'rakt fram', icon: ArrowUp },
  { direction: 'ne', name: 'snett framåt höger', icon: ArrowUpRight },
  { direction: 'w', name: 'vänster', icon: ArrowLeft },
  { direction: '', name: 'visas inte', icon: EyeOff },
  { direction: 'e', name: 'höger', icon: ArrowRight },
  { direction: 'sw', name: 'snett bakåt vänster', icon: ArrowDownLeft },
  { direction: 's', name: 'bakåt', icon: ArrowDown },
  { direction: 'se', name: 'snett bakåt höger', icon: ArrowDownRight },
]
const SIGNPOST = [
  { label: 'Tävling', direction: 'n' },
  { label: 'Uppvärmning', direction: 'e' },
  { label: 'Invägning', direction: 'w' },
  { label: 'Omklädning', direction: 's' },
]
const signpost = reactive(SIGNPOST.map((item) => ({ ...item })))
const resetSignpost = () => SIGNPOST.forEach((item, index) => Object.assign(signpost[index], item))
const printSignpost = () => printSheetAs('signpost')

// The texts of the welcome sign and the stream's splash screens are remembered in this browser until the next
// competition, so they only need to be typed once.
function remembered(key, defaults) {
  let saved = {}
  try {
    saved = JSON.parse(localStorage.getItem(key) ?? '{}') ?? {}
  } catch {
    // Not remembered (e.g. a private window): the defaults.
  }
  const state = reactive({ ...defaults, ...saved })
  watch(state, () => {
    try {
      localStorage.setItem(key, JSON.stringify(state))
    } catch {
      // Not remembered, but still used on this page.
    }
  })
  const reset = () => Object.assign(state, defaults)
  return [state, reset]
}

const [welcome, resetWelcome] = remembered('sek.welcome', {
  title: 'Välkomna till Distriktsmästerskapen i Klassisk Styrkelyft',
  from: 'Göteborg Kraftsportklubb och Västra Götalands Styrkelyftförbund',
})

const [stream, resetStream] = remembered('sek.stream', {
  competition: 'DM i klassisk styrkelyft 2026',
  info: 'Lördag 14 november · Friskis & Svettis Majorna, Göteborg',
})

// The same images can be shown in the stream's overlay, from the control page (/live/control).
const screens = [...Object.values(STREAM_IMAGES).map((title) => ({ title })), { custom: true }]
const customSplash = ref('')
const splashes = []

function fileName(title) {
  return [stream.competition, title]
    .join(' ')
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '')
}
</script>
