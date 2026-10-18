<template>
  <div class="slate">
    <div class="board">
      <div class="heading">
        <div class="brand"><img :src="logo" alt="Göteborg Kraftsportklubb" class="logo" /></div>
        <div class="titles">
          <div v-if="title" class="eyebrow">{{ title }}</div>
          <div class="headline">{{ content.headline }}</div>
        </div>
      </div>
      <div class="body">
        <div class="body-inner">
          <div class="message">{{ content.message }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { BROADCASTER_LOGO as logo } from '../clubs.js'

const props = defineProps({
  // 'start' (before the broadcast), 'pause' (between groups) or 'end'.
  kind: { type: String, default: 'start' },
  // The competition's name (?title=…).
  title: { type: String, default: null },
  // When the next group starts, e.g. '14:30' (pause only).
  time: { type: String, default: null },
})

const today = new Intl.DateTimeFormat('sv-SE', { weekday: 'long', day: 'numeric', month: 'long' }).format(new Date())

const content = computed(
  () =>
    ({
      start: { headline: 'Sändningen startar snart', message: today.charAt(0).toUpperCase() + today.slice(1) },
      pause: {
        headline: 'Paus',
        message: props.time ? `Nästa grupp startar ${props.time}` : 'Tävlingen fortsätter snart',
      },
      end: { headline: 'Tack för idag!', message: 'Fullständiga resultat finns på se.powerliftinglive.com' },
    })[props.kind] ?? { headline: props.kind, message: '' },
)
</script>

<style scoped>
/*
  A full-screen message between the lifts, with the same look and entrance as the results list. The background is
  the stinger's blue, slightly transparent, so the hall still shows through.
*/
.slate {
  position: absolute;
  inset: 0;
  display: grid;
  place-items: center;
  background: rgba(49, 66, 112, 0.85);
}

.board {
  width: 1400px;
  filter: drop-shadow(0 20px 36px rgba(0, 0, 0, 0.45));
  animation: slide-in var(--motion-slow) var(--motion-enter) both;
}

.heading {
  display: flex;
  align-items: center;
  border-radius: 14px 14px 0 0;
  background: var(--overlay-panel-deep);
}

.brand {
  flex: none;
  padding: 28px 40px;
}

.logo {
  width: 140px;
  height: 140px;
  padding: 5px;
  border-radius: 50%;
  background: #ffffff;
}

.eyebrow {
  font-family: var(--overlay-font);
  font-size: 26px;
  font-weight: 600;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: var(--overlay-silver);
}

.headline {
  font-family: var(--overlay-font);
  font-size: 96px;
  font-weight: 700;
  line-height: 1;
  text-transform: uppercase;
}

.body {
  display: grid;
  border-radius: 0 0 14px 14px;
  background: var(--overlay-panel);
  animation: fold-down var(--motion-slow) var(--motion-move) var(--motion-slow) both;
}

.body-inner {
  min-height: 0;
  overflow: hidden;
}

.message {
  padding: 28px 40px 32px;
  font-family: var(--overlay-font);
  font-size: 40px;
  font-weight: 600;
  letter-spacing: 0.04em;
}

@keyframes slide-in {
  from {
    opacity: 0;
    transform: translateX(-1600px);
  }
  30% {
    opacity: 1;
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes fold-down {
  from {
    grid-template-rows: 0fr;
  }
  to {
    grid-template-rows: 1fr;
  }
}
</style>
