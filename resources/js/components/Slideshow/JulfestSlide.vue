<template>
  <div class="julfest relative overflow-hidden text-white">
    <!-- Deep red, a warm glow from below and soft light from above. -->
    <div
      class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,rgba(255,255,255,0.12),transparent_55%),radial-gradient(ellipse_at_bottom,rgba(246,201,69,0.22),transparent_60%)]"
    ></div>

    <!-- A string of lights along the top, twinkling one after another. -->
    <svg
      class="absolute inset-x-0 top-0 h-[14vh] w-full"
      viewBox="0 0 1000 100"
      preserveAspectRatio="none"
      aria-hidden="true"
    >
      <path :d="WIRE" fill="none" stroke="rgba(20,40,20,0.9)" stroke-width="2.5" vector-effect="non-scaling-stroke" />
    </svg>
    <div class="absolute inset-x-0 top-0 h-[14vh]" aria-hidden="true">
      <span
        v-for="(bulb, i) in BULBS"
        :key="i"
        class="julfest-bulb absolute h-[2.6vh] w-[1.6vh] -translate-x-1/2 rounded-b-full rounded-t-[40%]"
        :style="{
          left: `${bulb.x / 10}%`,
          top: `${bulb.y}%`,
          backgroundColor: bulb.color,
          color: bulb.color,
          animationDelay: `${(i % 6) * 0.35}s`,
        }"
      ></span>
    </div>

    <!-- Falling snow. -->
    <span
      v-for="(flake, i) in FLAKES"
      :key="`flake-${i}`"
      class="julfest-flake absolute top-0 rounded-full bg-white"
      :style="flake"
      aria-hidden="true"
    ></span>

    <div class="relative flex h-full flex-col items-center justify-center px-[7vw] pt-[12vh] pb-[6vh] text-center">
      <div
        class="inline-flex items-center gap-[0.8vw] whitespace-nowrap rounded-full bg-amber-300 px-[1.4vw] py-[0.9vh] text-[min(1.3vw,2.3vh)] font-bold uppercase tracking-[0.18em] text-red-950 shadow-lg shadow-black/20"
      >
        <Sparkles class="h-[1.2em] w-[1.2em]" stroke-width="2.5" />
        GKK bjuder in
      </div>

      <h1
        class="mt-[3.5vh] text-[min(9vw,16vh)] font-extrabold leading-[0.9] tracking-tight drop-shadow-[0_0.6vh_2vh_rgba(0,0,0,0.35)]"
      >
        Julfest <span class="text-amber-300">2026</span>
      </h1>
      <p class="mt-[2.5vh] text-[min(2.6vw,4.4vh)] font-light text-white/85">Hoppas vi ses där!</p>

      <div class="mt-[6vh] grid w-full max-w-[88vw] grid-cols-[1fr_1.35fr_1fr] gap-[min(1.6vw,2.8vh)] text-left">
        <div class="julfest-card">
          <CalendarDays class="julfest-icon" stroke-width="2" />
          <div>
            <div class="julfest-label">När</div>
            <div class="julfest-value">5 december</div>
            <div class="mt-[0.6vh] text-[min(1.5vw,2.6vh)] text-white/75">Lördag</div>
          </div>
        </div>

        <div class="julfest-card">
          <MapPin class="julfest-icon" stroke-width="2" />
          <div>
            <div class="julfest-label">Var</div>
            <div class="julfest-value">GFF-gården</div>
            <div class="mt-[0.6vh] text-[min(1.5vw,2.6vh)] text-white/75">Ekedalsgatan, vid Majvallen</div>
          </div>
        </div>

        <div class="julfest-card bg-amber-300! text-red-950 ring-amber-200!">
          <Gift class="julfest-icon text-red-800!" stroke-width="2" />
          <div>
            <div class="julfest-label text-red-900/70!">Klädtema</div>
            <div class="julfest-value">Jul</div>
            <div class="mt-[0.6vh] text-[min(1.5vw,2.6vh)] font-semibold text-red-900/80">Utklädnad obligatorisk!</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { CalendarDays, Gift, MapPin, Sparkles } from 'lucide-vue-next'

// The wire sags between hooks along the top edge; the bulbs hang at the bottom of each sag and halfway up it.
const HOOKS = 8
const SPAN = 1000 / HOOKS
const WIRE = Array.from({ length: HOOKS }, (_, i) => {
  const x = i * SPAN
  return `${i === 0 ? 'M0,8' : ''} Q${x + SPAN / 2},70 ${x + SPAN},8`
}).join(' ')

const COLORS = ['#f87171', '#fcd34d', '#4ade80', '#f8fafc', '#60a5fa']
const BULBS = Array.from({ length: HOOKS }, (_, i) =>
  [0.25, 0.5, 0.75].map((t) => {
    // A point on the quadratic curve from (x, 8) via (x + SPAN / 2, 70) to (x + SPAN, 8), in percent of the strip.
    const y = (1 - t) ** 2 * 8 + 2 * (1 - t) * t * 70 + t ** 2 * 8
    return { x: i * SPAN + t * SPAN, y: y - 2 }
  }),
)
  .flat()
  .map((bulb, i) => ({ ...bulb, color: COLORS[i % COLORS.length] }))

// The same snow on every screen: a fixed pseudo-random spread of sizes, places, speeds and drifts.
function random(seed) {
  const x = Math.sin(seed * 9301 + 49297) * 233280
  return x - Math.floor(x)
}
const FLAKES = Array.from({ length: 70 }, (_, i) => {
  const size = 0.4 + random(i) * 0.9
  const duration = 9 + random(i + 100) * 10
  return {
    left: `${random(i + 200) * 100}%`,
    width: `${size}vh`,
    height: `${size}vh`,
    opacity: 0.35 + random(i + 300) * 0.55,
    animationDuration: `${duration}s`,
    animationDelay: `${-random(i + 400) * duration}s`,
    '--drift': `${(random(i + 500) - 0.5) * 8}vw`,
  }
})
</script>

<style scoped>
.julfest {
  background: linear-gradient(160deg, #7f1d1d 0%, #991b1b 45%, #450a0a 100%);
}

.julfest-card {
  display: flex;
  align-items: flex-start;
  gap: min(1.2vw, 2.1vh);
  border-radius: 2vh;
  padding: min(1.8vw, 3.2vh) min(2vw, 3.5vh);
  background: rgba(255, 255, 255, 0.1);
  box-shadow:
    inset 0 0 0 1px rgba(255, 255, 255, 0.18),
    0 2vh 4vh rgba(0, 0, 0, 0.25);
  backdrop-filter: blur(6px);
}

.julfest-icon {
  flex-shrink: 0;
  width: min(3vw, 5.2vh);
  height: min(3vw, 5.2vh);
  color: #fcd34d;
}

.julfest-label {
  font-size: min(1.15vw, 2vh);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.18em;
  color: rgba(255, 255, 255, 0.65);
}

.julfest-value {
  margin-top: 0.4vh;
  font-size: min(2.6vw, 4.5vh);
  font-weight: 800;
  line-height: 1.05;
}

.julfest-bulb {
  box-shadow: 0 0 1.6vh 0.4vh currentColor;
  animation: julfest-twinkle 2.1s ease-in-out infinite;
}

/* The socket on top of each bulb. */
.julfest-bulb::before {
  content: '';
  position: absolute;
  left: 50%;
  top: -0.7vh;
  width: 0.9vh;
  height: 0.9vh;
  transform: translateX(-50%);
  border-radius: 0.2vh;
  background: #1f2d1f;
}

@keyframes julfest-twinkle {
  0%,
  100% {
    opacity: 1;
    filter: brightness(1.1);
  }
  50% {
    opacity: 0.45;
    filter: brightness(0.7);
  }
}

.julfest-flake {
  animation-name: julfest-fall;
  animation-timing-function: linear;
  animation-iteration-count: infinite;
  filter: blur(0.5px);
}

@keyframes julfest-fall {
  from {
    transform: translate(0, -5vh);
  }
  to {
    transform: translate(var(--drift), 105vh);
  }
}
</style>
