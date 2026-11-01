<template>
  <div class="relative overflow-hidden bg-gkk text-white">
    <!-- The same backdrop as the DM welcome: soft light, a warm glow and a faint weight plate on each side. -->
    <div
      class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,rgba(255,255,255,0.16),transparent_60%),radial-gradient(ellipse_at_bottom,rgba(246,201,69,0.2),transparent_55%)]"
    ></div>
    <svg
      v-for="side in ['-left-[30vh]', '-right-[30vh]']"
      :key="side"
      class="absolute top-1/2 h-[90vh] w-[90vh] -translate-y-1/2 text-white/[0.07]"
      :class="side"
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

    <div class="relative flex h-full flex-col items-center justify-center px-[8vw] py-[6vh] text-center">
      <div
        class="flex h-[18vh] w-[18vh] items-center justify-center rounded-full bg-white p-[1vh] shadow-2xl shadow-black/30"
      >
        <img class="h-full w-full object-contain" :src="LOGO" alt="Göteborg Kraftsportklubb" />
      </div>

      <div
        class="mt-[4vh] inline-flex items-center gap-[0.8vw] whitespace-nowrap rounded-full bg-amber-300 px-[1.4vw] py-[0.9vh] text-[min(1.3vw,2.3vh)] font-bold uppercase tracking-[0.18em] text-gkk"
      >
        <span class="h-[0.9vh] w-[0.9vh] rounded-full bg-gkk"></span>
        Nästa år hos oss
      </div>

      <p class="mt-[4vh] text-[min(3vw,5vh)] font-light leading-none text-white/80">GKK arrangerar</p>
      <h1 class="mt-[1.5vh] text-[min(6.4vw,11vh)] font-extrabold leading-[0.95] tracking-tight">
        Götalandsmästerskapen <span class="text-amber-300">2027</span>
      </h1>

      <div class="mt-[4.5vh] h-[0.6vh] min-h-1 w-[8vw] rounded-full bg-amber-300"></div>
      <p class="mt-[4.5vh] text-[min(3.4vw,6vh)] font-semibold leading-tight text-balance">
        Varmt välkomna tillbaka då!
      </p>
    </div>
  </div>
</template>

<script setup>
const LOGO = 'https://goteborg-kraftsportklubb.web.app/img/logo-min.png'
</script>
