<template>
  <div class="demo">
    <div class="title">Demo · steg {{ index + 1 }}/{{ steps.length }}</div>
    <div class="label">{{ steps[index].label }}</div>
    <div class="buttons">
      <button type="button" @click="emit('go', index - 1)">◀ Föregående</button>
      <button type="button" @click="emit('toggle')">{{ playing ? '❚❚ Pausa' : '▶ Spela' }}</button>
      <button type="button" @click="emit('go', index + 1)">Nästa ▶</button>
    </div>
    <div class="hint">Tangenter: ← → byt steg, mellanslag spela/pausa</div>
  </div>
</template>

<script setup>
defineProps({
  steps: { type: Array, required: true },
  index: { type: Number, required: true },
  playing: { type: Boolean, required: true },
})

const emit = defineEmits(['go', 'toggle'])
</script>

<style scoped>
.demo {
  position: absolute;
  top: 24px;
  left: 24px;
  width: 560px;
  padding: 16px 20px;
  border-radius: 8px;
  background: rgba(0, 0, 0, 0.75);
  font-size: 20px;
}

.title {
  font-size: 16px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--overlay-text-muted);
}

.label {
  margin: 6px 0 12px;
  font-weight: 600;
}

.buttons {
  display: flex;
  gap: 10px;
}

button {
  padding: 8px 14px;
  border: 0;
  border-radius: 5px;
  background: rgba(255, 255, 255, 0.15);
  color: inherit;
  font: inherit;
  font-size: 18px;
  cursor: pointer;
}

button:hover {
  background: rgba(255, 255, 255, 0.25);
}

.hint {
  margin-top: 10px;
  font-size: 15px;
  color: var(--overlay-text-muted);
}
</style>
