<template>
  <div class="replay">
    <span class="mark">◀◀</span>
    <span class="label">Repris</span>
  </div>
</template>

<style scoped>
/* Marks the picture as a replay, as Swedish TV always does. Put it in the OBS scene that plays the replay. */
.replay {
  position: absolute;
  top: var(--safe-y);
  left: var(--safe-x);
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 10px 24px;
  border-radius: 8px;
  background: var(--overlay-panel-deep);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
  animation: replay-in var(--motion-base) var(--motion-enter) both;
}

.mark {
  font-size: 24px;
  color: var(--overlay-failed);
  animation: blink 1.2s steps(2, jump-none) infinite;
}

.label {
  font-family: var(--overlay-font);
  font-size: 34px;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

@keyframes replay-in {
  from {
    opacity: 0;
    transform: translateX(-30px);
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
    opacity: 0.3;
  }
}
</style>
