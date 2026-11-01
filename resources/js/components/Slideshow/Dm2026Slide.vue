<template>
  <div class="relative overflow-hidden bg-gkk text-white">
    <!-- Soft light from the top left and a warm glow from the bottom right, with a faint weight plate behind. -->
    <div
      class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,rgba(255,255,255,0.16),transparent_55%),radial-gradient(ellipse_at_bottom_right,rgba(246,201,69,0.2),transparent_55%)]"
    ></div>
    <svg
      class="absolute -right-[14vh] -bottom-[22vh] h-[100vh] w-[100vh] text-white/[0.07]"
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

    <div class="relative h-full grid grid-cols-[1.15fr_1fr] gap-[4vw] px-[5vw] py-[6vh]">
      <section class="flex flex-col justify-center min-w-0">
        <div class="flex items-center gap-[1.5vw]">
          <div
            class="flex h-[13vh] w-[13vh] shrink-0 items-center justify-center rounded-full bg-white p-[0.8vh] shadow-xl shadow-black/30"
          >
            <img class="h-full w-full object-contain" :src="LOGO" alt="Göteborg Kraftsportklubb" />
          </div>
          <div
            class="inline-flex items-center gap-[0.8vw] whitespace-nowrap rounded-full bg-amber-300 text-gkk px-[1.4vw] py-[0.9vh] text-[min(1.2vw,2.1vh)] font-bold uppercase tracking-[0.14em]"
          >
            <span class="h-[0.9vh] w-[0.9vh] rounded-full bg-gkk"></span>
            Klassisk styrkelyft · Lördag 14 november
          </div>
        </div>
        <p class="mt-[3.5vh] text-[min(3vw,5vh)] font-light leading-none text-white/80">Välkommen till</p>
        <h1 class="mt-[1.5vh] text-[min(4.4vw,8vh)] font-extrabold leading-[0.95] tracking-tight">
          Distriktsmästerskapen<br />
          <span class="text-amber-300">2026</span>
        </h1>
        <p class="mt-[3vh] text-[min(1.7vw,2.9vh)] leading-snug text-white/75 max-w-[90%]">
          <i class="fa fa-map-marker mr-[0.5vw] text-amber-300"></i>
          Friskis &amp; Svettis Majorna · arrangeras av Göteborg Kraftsportklubb och Västra Götalands Styrkelyftförbund
        </p>

        <dl class="mt-[5vh] grid grid-cols-4 gap-[1.2vw]">
          <div
            v-for="stat in stats"
            :key="stat.label"
            class="flex flex-col-reverse rounded-[1.6vh] bg-white/[0.08] ring-1 ring-white/15 px-[1.2vw] py-[2vh] backdrop-blur-sm"
          >
            <dt class="mt-[0.8vh] text-[min(1.1vw,1.9vh)] font-semibold uppercase tracking-[0.15em] text-white/65">
              {{ stat.label }}
            </dt>
            <dd class="text-[min(4.4vw,7.5vh)] font-extrabold leading-none tabular-nums text-amber-300">
              {{ stat.value }}
            </dd>
          </div>
        </dl>
      </section>

      <section class="flex flex-col justify-center min-w-0">
        <h2 class="text-[min(1.4vw,2.4vh)] font-semibold uppercase tracking-[0.25em] text-white/60">Tidsschema</h2>
        <ol class="mt-[2.5vh] flex flex-col gap-[2.2vh]">
          <li
            v-for="(session, index) in SESSIONS"
            :key="session.groups"
            class="flex items-stretch overflow-hidden rounded-[1.8vh] bg-white text-gkk shadow-2xl shadow-black/25 transition-opacity duration-700"
            :class="[
              states[index] === 'done' ? 'opacity-40' : '',
              states[index] === 'live' ? 'ring-[0.5vh] ring-amber-300' : '',
            ]"
          >
            <div class="flex w-[34%] shrink-0 flex-col justify-center bg-gkk/[0.06] px-[1.5vw] py-[2vh]">
              <span class="text-[min(1vw,1.7vh)] font-semibold uppercase tracking-[0.15em] text-gkk-light">Start</span>
              <span class="text-[min(3.6vw,6.2vh)] font-extrabold leading-none tabular-nums">{{ session.start }}</span>
              <span class="mt-[0.8vh] text-[min(1.1vw,1.9vh)] text-gkk-light tabular-nums">
                Invägning {{ session.weighIn }}
              </span>
            </div>
            <div class="flex min-w-0 flex-1 flex-col justify-center px-[1.5vw] py-[2vh]">
              <div class="flex items-center gap-[0.8vw]">
                <span class="text-[min(2.2vw,3.8vh)] font-bold leading-tight">Grupp {{ session.groups }}</span>
                <span
                  v-if="states[index] === 'live'"
                  class="inline-flex items-center gap-[0.4vw] rounded-full bg-emerald-100 px-[0.8vw] py-[0.4vh] text-[min(1vw,1.7vh)] font-bold uppercase tracking-wider text-emerald-700"
                >
                  <span class="h-[0.9vh] w-[0.9vh] animate-pulse rounded-full bg-emerald-500"></span>
                  Pågår nu
                </span>
                <span
                  v-else-if="states[index] === 'weighIn'"
                  class="rounded-full bg-amber-100 px-[0.8vw] py-[0.4vh] text-[min(1vw,1.7vh)] font-bold uppercase tracking-wider text-amber-700"
                >
                  Invägning pågår
                </span>
              </div>
              <span class="mt-[0.5vh] truncate text-[min(1.4vw,2.4vh)] text-gkk-light">{{ session.description }}</span>
              <span class="mt-[0.8vh] text-[min(1.2vw,2vh)] font-semibold">{{ session.lifters.length }} lyftare</span>
            </div>
          </li>
        </ol>
      </section>
    </div>
  </div>
</template>

<script setup>
import { onBeforeUnmount, ref } from 'vue'
import { SESSIONS, sessionStates, summary } from './dm2026.js'

const LOGO = 'https://goteborg-kraftsportklubb.web.app/img/logo-min.png'

const totals = summary()
const stats = [
  { label: 'Lyftare', value: totals.lifters },
  { label: 'Klubbar', value: totals.clubs },
  { label: 'Grupper', value: totals.groups },
  { label: 'Från GKK', value: totals.homeLifters },
]

// On the competition day, which session is weighing in, lifting or done.
const states = ref(sessionStates())
const timer = setInterval(() => (states.value = sessionStates()), 30 * 1000)
onBeforeUnmount(() => clearInterval(timer))
</script>
