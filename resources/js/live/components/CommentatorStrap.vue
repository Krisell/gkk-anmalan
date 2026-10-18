<template>
  <div class="commentators">
    <div class="strap">
      <span class="eyebrow">{{ names.length > 1 ? 'Kommentatorer' : 'Kommentator' }}</span>
      <span class="names">{{ joined }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  names: { type: Array, required: true },
})

// "Anna Svensson och Erik Berg", "A, B och C"
const joined = computed(() =>
  props.names.length > 1 ? `${props.names.slice(0, -1).join(', ')} och ${props.names.at(-1)}` : (props.names[0] ?? ''),
)
</script>

<style scoped>
/* A name strap for the commentators, in the lower third's position. It slides in, stays, and slides out. */
.commentators {
  position: absolute;
  left: 0;
  right: 0;
  bottom: var(--safe-y);
  display: flex;
  justify-content: center;
}

.strap {
  display: flex;
  align-items: center;
  gap: 24px;
  height: 96px;
  padding: 0 40px;
  border-radius: 10px;
  background: var(--overlay-panel);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
  animation:
    strap-in var(--motion-base) var(--motion-enter) both,
    strap-out var(--motion-base) var(--motion-move) 9s both;
}

.eyebrow {
  font-family: var(--overlay-font);
  font-size: 20px;
  font-weight: 600;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: var(--overlay-silver);
}

.names {
  font-family: var(--overlay-font);
  font-size: 44px;
  font-weight: 700;
  text-transform: uppercase;
}

@keyframes strap-in {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes strap-out {
  to {
    opacity: 0;
    transform: translateY(30px);
  }
}
</style>
