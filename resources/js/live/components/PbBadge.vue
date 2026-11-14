<template>
  <div class="pb-badge" :class="{ good }">
    <Transition name="flip" mode="out-in">
      <span :key="good ? 'good' : 'attempt'" class="label">{{ good ? 'Nytt PB!' : 'PB-försök' }}</span>
    </Transition>
  </div>
</template>

<script setup>
defineProps({
  // The attempt was judged good: "NYTT PB!" in green, with a pop, instead of the slow blinking.
  good: { type: Boolean, default: false },
})
</script>

<style scoped>
/*
  "PB-försök" on the broadcast during a PB attempt: on the left, clear of the lifter in the middle, blinking slowly
  until the decision. A good lift flips it over into "NYTT PB!"; a failed one just fades it away.
*/
.pb-badge {
  --pb-ink: #f5c542;
  --pb-glow: rgba(245, 197, 66, 0.5);

  position: absolute;
  top: 300px;
  left: var(--safe-x);
  padding: 12px 34px 14px;
  border: 6px solid var(--pb-ink);
  border-radius: 14px;
  background: rgba(20, 28, 54, 0.82);
  box-shadow:
    0 0 0 3px rgba(20, 28, 54, 0.6),
    0 0 36px var(--pb-glow);
  color: var(--pb-ink);
  perspective: 600px;
  pointer-events: none;
  animation:
    badge-in 500ms var(--motion-enter) both,
    blink 1.8s ease-in-out 500ms infinite alternate;
}

.label {
  display: block;
  font-family: var(--overlay-font);
  font-size: 64px;
  font-weight: 700;
  line-height: 1;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  white-space: nowrap;
  text-shadow: 0 3px 16px rgba(0, 0, 0, 0.5);
}

.pb-badge.good {
  --pb-ink: #4fd88a;
  --pb-glow: rgba(79, 216, 138, 0.6);

  animation: pop 700ms cubic-bezier(0.2, 0.8, 0.3, 1.3) both;
}

/* Two rings bursting out from the badge when it turns green. */
.pb-badge.good::before,
.pb-badge.good::after {
  content: '';
  position: absolute;
  inset: -6px;
  border: 4px solid var(--pb-ink);
  border-radius: 14px;
  opacity: 0;
  animation: burst 900ms ease-out 150ms;
}

.pb-badge.good::after {
  animation-delay: 450ms;
}

/* The text turns over like a card. */
.flip-leave-active {
  transition:
    transform 180ms ease-in,
    opacity 180ms ease-in;
}

.flip-enter-active {
  transition:
    transform 320ms cubic-bezier(0.2, 0.8, 0.3, 1.2),
    opacity 320ms ease-out;
}

.flip-leave-to {
  opacity: 0;
  transform: rotateX(90deg);
}

.flip-enter-from {
  opacity: 0;
  transform: rotateX(-90deg);
}

/* Taken away (the decision, or a failed lift): slides out to the left. */
.pb-badge-leave-active {
  transition:
    opacity 400ms ease-in,
    transform 400ms ease-in;
}

.pb-badge-leave-to {
  opacity: 0;
  transform: translateX(-40px);
}

@keyframes badge-in {
  from {
    opacity: 0;
    transform: translateX(-60px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes blink {
  from {
    opacity: 1;
  }
  to {
    opacity: 0.5;
  }
}

@keyframes pop {
  from {
    transform: scale(1);
  }
  45% {
    transform: scale(1.22);
  }
  to {
    transform: scale(1);
  }
}

@keyframes burst {
  from {
    opacity: 0.9;
    transform: scale(1);
  }
  to {
    opacity: 0;
    transform: scale(1.5, 2);
  }
}
</style>
