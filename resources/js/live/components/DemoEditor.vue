<template>
  <!-- A toolbar along the top edge, clear of the lifter card in the middle and the lower third at the bottom. -->
  <form class="editor" :class="{ folded }" @submit.prevent>
    <button type="button" class="toggle" :aria-expanded="!folded" @click="setFolded(!folded)">
      Demo <span class="caret">▾</span>
    </button>

    <div class="body" :inert="folded">
      <div class="rows">
        <div class="row">
          <div class="group">
            <span class="label">Läge</span>
            <div class="segmented">
              <button
                v-for="option in MODES"
                :key="option.value"
                type="button"
                :class="{ active: state.mode === option.value }"
                :disabled="busy"
                @click="state.mode = option.value"
              >
                {{ option.label }}
              </button>
            </div>
            <select v-model="state.slate" :disabled="state.mode !== 'slate'" class="slate-kind" title="Vilken skylt">
              <option v-for="option in SLATES" :key="option.value" :value="option.value">{{ option.label }}</option>
            </select>
          </div>
          <div class="group">
            <span class="label">Gren</span>
            <div class="segmented">
              <button
                v-for="option in DISCIPLINES"
                :key="option.value"
                type="button"
                :class="{ active: state.discipline === option.value }"
                @click="change('discipline', option.value)"
              >
                {{ option.label }}
              </button>
            </div>
          </div>
          <div class="group">
            <span class="label">Försök</span>
            <div class="segmented">
              <button
                v-for="n in [1, 2, 3]"
                :key="n"
                type="button"
                :class="{ active: state.attempt === n }"
                @click="change('attempt', n)"
              >
                {{ n }}
              </button>
            </div>
          </div>
          <label class="group">
            <span class="label">Vikt</span>
            <input
              :value="state.weight"
              type="number"
              min="0"
              step="2.5"
              class="number"
              @change="change('weight', Number($event.target.value))"
            />
          </label>
          <label class="group">
            <span class="label">Resultat</span>
            <select :value="state.result ?? ''" @change="chooseResult($event.target.value)">
              <option value="">Inget</option>
              <option v-for="option in RESULTS" :key="option.value" :value="option.value">{{ option.label }}</option>
            </select>
          </label>
        </div>

        <div class="row">
          <label class="group">
            <span class="label">Tävling</span>
            <input v-model="state.title" type="text" class="text" />
          </label>
          <label class="group">
            <span class="label">Namn</span>
            <input v-model="state.name" type="text" class="text" />
          </label>
          <label class="group">
            <span class="label">Klubb</span>
            <input v-model="state.team" type="text" class="text" />
          </label>
          <label class="group">
            <span class="label">Viktklass</span>
            <input v-model="state.weightClass" type="text" class="number" />
          </label>
          <label class="group">
            <span class="label">Därefter</span>
            <input v-model="state.nextName" type="text" class="text" placeholder="(ingen)" />
            <input v-model="state.nextWeight" type="number" min="0" step="2.5" class="number" />
          </label>
          <button
            type="button"
            class="secondary push"
            :class="{ active: state.lastInRound }"
            title="Ett resultat avslutar omgången och visar resultatlistan"
            @click="state.lastInRound = !state.lastInRound"
          >
            Sista i omgången
          </button>
          <button type="button" class="secondary" :class="{ active: clockRunning }" @click="toggleClock">
            {{ clockRunning ? 'Stoppa klocka' : 'Starta klocka' }}
          </button>
          <button type="button" class="secondary" @click="reroll">Slumpa om</button>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { computed, onBeforeUnmount, ref } from 'vue'
import { CLOCK_SECONDS, randomHistory } from '../sources/editorSource.js'

const props = defineProps({
  // The reactive editor state (see defaultEditorState); edited in place.
  state: { type: Object, required: true },
  // True while an animation between the card and the lower third is running.
  busy: { type: Boolean, default: false },
})

const emit = defineEmits(['clear-result'])

// Folding is a per-browser preference, remembered across reloads where storage is available.
const FOLDED_KEY = 'live-overlay-editor-folded'
const folded = ref(readFolded())

function readFolded() {
  try {
    return window.localStorage.getItem(FOLDED_KEY) === '1'
  } catch {
    return false
  }
}

function setFolded(value) {
  folded.value = value
  try {
    window.localStorage.setItem(FOLDED_KEY, value ? '1' : '0')
  } catch {
    // Storage unavailable (e.g. private window): folding still works for this page load.
  }
}

const MODES = [
  { value: 'card', label: 'Stort kort' },
  { value: 'bar', label: 'Nederkant' },
  { value: 'list', label: 'Resultatlista' },
  { value: 'slate', label: 'Skylt' },
]

const SLATES = [
  { value: 'start', label: 'Startar snart' },
  { value: 'pause', label: 'Paus' },
  { value: 'end', label: 'Tack för idag' },
]

const DISCIPLINES = [
  { value: 'SQ', label: 'Knäböj' },
  { value: 'BP', label: 'Bänkpress' },
  { value: 'DL', label: 'Marklyft' },
]

const RESULTS = [
  { value: 'www', label: '3 vita – godkänt' },
  { value: 'wwr', label: '2 vita – godkänt' },
  { value: 'wrr', label: '1 vit – underkänt' },
  { value: 'rrr', label: '3 röda – underkänt' },
]

// A different attempt clears the result, since the result belongs to the attempt that was judged.
function change(field, value) {
  if (props.state[field] !== value) {
    props.state[field] = value
    props.state.result = null
    props.state.clockStartedAt = null
  }
}

// The demo's attempt clock runs for 60 seconds; a result or another attempt stops it.
const now = ref(Date.now())
const ticker = setInterval(() => (now.value = Date.now()), 500)
onBeforeUnmount(() => clearInterval(ticker))
const clockRunning = computed(
  () => Boolean(props.state.clockStartedAt) && now.value - props.state.clockStartedAt < CLOCK_SECONDS * 1000,
)

function toggleClock() {
  props.state.clockStartedAt = clockRunning.value ? null : Date.now()
}

// Each chosen result is a new referee decision (so it animates); "Inget" removes it.
function chooseResult(lights) {
  if (!lights) {
    emit('clear-result')
    return
  }

  props.state.result = lights
  props.state.decisions++
  props.state.clockStartedAt = null
}

function reroll() {
  props.state.history = randomHistory()
}
</script>

<style scoped>
.editor {
  position: absolute;
  /* Above the broadcast graphics, including the corner logo. */
  z-index: 10;
  top: 20px;
  left: 20px;
  display: flex;
  align-items: flex-start;
  gap: 18px;
  max-width: calc(100% - 40px);
  padding: 14px 18px;
  border-radius: 10px;
  background: rgba(10, 14, 28, 0.85);
  font-size: 17px;
  transition:
    gap var(--motion-fast) var(--motion-move),
    padding var(--motion-fast) var(--motion-move);
}

.editor.folded {
  gap: 0;
  padding: 6px 8px;
}

/* Folding animates the grid's single column and row between their full size (1fr) and nothing (0fr). */
.body {
  display: grid;
  grid-template-columns: 1fr;
  grid-template-rows: 1fr;
  transition:
    grid-template-columns var(--motion-fast) var(--motion-move),
    grid-template-rows var(--motion-fast) var(--motion-move),
    opacity 200ms ease;
}

.editor.folded .body {
  grid-template-columns: 0fr;
  grid-template-rows: 0fr;
  opacity: 0;
}

.rows {
  display: flex;
  flex-direction: column;
  gap: 10px;
  min-width: 0;
  min-height: 0;
  overflow: hidden;
}

.row {
  display: flex;
  align-items: center;
  gap: 20px;
  /*
    The full stage width minus the margins, padding and the Demo toggle, so the toolbar spans the screen. Set on
    the rows rather than their container, which must be able to shrink to nothing when folded.
  */
  width: 1725px;
  white-space: nowrap;
}

.toggle {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: none;
  padding: 6px 8px;
  background: none;
  font-size: 15px;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--overlay-silver);
}

.toggle:hover {
  background: rgba(255, 255, 255, 0.1);
}

.caret {
  display: inline-block;
  transition: transform var(--motion-fast) var(--motion-move);
}

.editor.folded .caret {
  transform: rotate(-90deg);
}

.group {
  display: flex;
  align-items: center;
  gap: 10px;
}

.label {
  color: var(--overlay-silver);
}

input,
select {
  padding: 6px 10px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 5px;
  background: rgba(255, 255, 255, 0.08);
  color: inherit;
  font: inherit;
}

input.text {
  width: 180px;
}

input.number {
  width: 80px;
}

select {
  min-width: 200px;
}

select.slate-kind {
  min-width: 150px;
}

option {
  background: #1b2444;
}

input:focus,
select:focus {
  border-color: rgba(255, 255, 255, 0.6);
}

.segmented {
  display: flex;
  gap: 6px;
}

button {
  padding: 5px 11px;
  border: 0;
  border-radius: 5px;
  background: rgba(255, 255, 255, 0.12);
  color: inherit;
  font: inherit;
  cursor: pointer;
}

button:hover {
  background: rgba(255, 255, 255, 0.22);
}

button.active {
  background: var(--overlay-navy-light);
  box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.7);
}

button:disabled {
  opacity: 0.5;
  cursor: default;
}

button.secondary {
  font-size: 16px;
}

button.push {
  margin-left: auto;
}
</style>
