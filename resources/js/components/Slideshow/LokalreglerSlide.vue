<template>
  <div class="relative overflow-hidden">
    <!-- The same soft light as the Swish slide: a blue glow bottom left and a warm one top right. -->
    <div
      class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,rgba(49,66,112,0.10),transparent_60%),radial-gradient(ellipse_at_top_right,rgba(246,201,69,0.22),transparent_55%)]"
    ></div>
    <svg
      class="absolute -bottom-[45vh] -left-[45vh] h-[90vh] w-[90vh] text-gkk/[0.05]"
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

    <div class="relative grid h-full grid-cols-[0.8fr_1.2fr] items-center gap-[5vw] px-[7vw] py-[6vh]">
      <section class="min-w-0">
        <div
          class="flex h-[20vh] w-[20vh] items-center justify-center rounded-full bg-white p-[1vh] shadow-2xl shadow-gkk/20 ring-1 ring-gkk/10"
        >
          <img class="h-full w-full object-contain" :src="LOGO" alt="Göteborg Kraftsportklubb" />
        </div>
        <div
          class="mt-[5vh] inline-flex items-center gap-[0.8vw] whitespace-nowrap rounded-full bg-gkk px-[1.4vw] py-[0.9vh] text-[min(1.3vw,2.3vh)] font-bold uppercase tracking-[0.18em] text-white"
        >
          <span class="h-[0.9vh] w-[0.9vh] rounded-full bg-amber-300"></span>
          I hallen
        </div>
        <h1 class="mt-[3vh] text-[min(6vw,11vh)] font-extrabold leading-[0.95] tracking-tight text-gkk">Lokalregler</h1>
        <div class="mt-[3.5vh] h-[0.6vh] min-h-1 w-[6vw] rounded-full bg-amber-300"></div>
        <p class="mt-[3.5vh] max-w-[95%] text-[min(2vw,3.4vh)] leading-snug text-gkk-light text-balance">
          Så blir hallen en trivsam plats att träna på – för alla.
        </p>
      </section>

      <ol class="flex min-w-0 flex-col gap-[2vh]">
        <li
          v-for="(rule, index) in RULES"
          :key="rule.title"
          class="flex items-center gap-[1.6vw] rounded-[2vh] bg-white/90 px-[1.6vw] py-[1.8vh] shadow-xl shadow-gkk/10 ring-1 ring-gkk/10"
        >
          <div
            class="relative flex h-[9vh] w-[9vh] shrink-0 items-center justify-center rounded-[1.6vh] bg-gkk text-amber-300"
          >
            <component :is="rule.icon" class="h-[55%] w-[55%]" :stroke-width="1.75" />
            <span
              class="absolute -right-[1vh] -top-[1vh] flex h-[3.6vh] w-[3.6vh] items-center justify-center rounded-full bg-amber-300 text-[min(1.2vw,2.1vh)] font-extrabold text-gkk ring-[0.4vh] ring-white"
            >
              {{ index + 1 }}
            </span>
          </div>
          <div class="min-w-0">
            <p class="text-[min(2.5vw,4.3vh)] font-bold leading-tight text-gkk">{{ rule.title }}</p>
            <p class="mt-[0.4vh] text-[min(1.7vw,2.9vh)] leading-snug text-gkk-light">{{ rule.text }}</p>
          </div>
        </li>
      </ol>
    </div>
  </div>
</template>

<script setup>
import { Backpack, Dumbbell, HeartHandshake, LightbulbOff, Users } from 'lucide-vue-next'

const LOGO = 'https://goteborg-kraftsportklubb.web.app/img/logo-min.png'

const RULES = [
  { icon: HeartHandshake, title: 'Håll en god och respektfull ton', text: 'Visa respekt och var snälla mot varandra!' },
  { icon: Dumbbell, title: 'Håll ordning!', text: 'Viktskivor och redskap ska vara på plats.' },
  { icon: Users, title: 'Dela ställning och stång', text: 'Om det är mycket folk som tränar samtidigt.' },
  { icon: Backpack, title: 'Väskor i hyllan eller i korridoren', text: 'Inte på golvet!' },
  { icon: LightbulbOff, title: 'Sista person', text: 'Släck och stäng fönster.' },
]
</script>
