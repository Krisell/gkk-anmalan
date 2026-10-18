<template>
  <div class="scoreboard">
    <div
      ref="board"
      class="board"
      :style="{ '--row-height': `${rowHeight}px`, '--columns': gridColumns, marginTop: `${top}px` }"
    >
      <div class="heading">
        <div class="brand"><img :src="logo" alt="" class="logo" /></div>
        <div class="titles">
          <div class="eyebrow">{{ [group, disciplineTitle].filter(Boolean).join(' · ') }}</div>
          <div class="title">Resultatlista – Pågående tävling</div>
        </div>
      </div>

      <div class="body">
        <div class="body-inner">
          <div class="row columns">
            <div class="place">Plac.</div>
            <div>Lyftare</div>
            <div
              v-for="column in columns"
              :key="column.key"
              class="cell"
              :class="{ current: column.kind === 'attempt' }"
            >
              {{ column.label }}
            </div>
            <div class="cell total">Total</div>
            <div class="cell points">IPF-poäng</div>
          </div>

          <template v-for="(row, index) in rows" :key="row.key">
            <div v-if="row.header" class="row group" :style="{ '--index': index }">{{ row.header }}</div>
            <div
              v-else
              class="row lifter"
              :class="{ active: row.lifter.id === currentLifterId }"
              :style="{ '--index': index }"
            >
              <div class="place">{{ row.lifter.place ?? '–' }}</div>
              <div class="who">
                <span class="name">{{ row.lifter.name ?? 'Okänd lyftare' }}</span>
                <span v-if="row.lifter.team" class="team">{{ row.lifter.team }}</span>
              </div>
              <div v-for="column in columns" :key="column.key" class="cell" :class="cell(row.lifter, column).state">
                {{ cell(row.lifter, column).label }}
              </div>
              <div class="cell total">{{ row.lifter.total ? formatWeight(row.lifter.total) : '–' }}</div>
              <div class="cell points">{{ formatPoints(row.lifter.points) }}</div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, ref, watch } from 'vue'
import { BROADCASTER_LOGO as logo } from '../clubs.js'
import { disciplineName, formatPoints, formatWeight, formatWeightClass } from '../format.js'

const props = defineProps({
  // The normalized Scoreboard (see normalize.js).
  scoreboard: { type: Object, required: true },
  discipline: { type: String, default: null },
  group: { type: String, default: null },
  currentLifterId: { type: String, default: null },
})

const SHORT_NAMES = { SQ: 'Knäböj', BP: 'Bänk', DL: 'Mark' }

const disciplineTitle = computed(() => disciplineName(props.discipline))

// Finished disciplines get one column (the best lift), the current discipline one column per attempt, and
// disciplines still to come an empty column, so a full competition always has five result columns.
const columns = computed(() => {
  const order = props.scoreboard.disciplines
  const currentIndex = order.indexOf(props.discipline)

  return order.flatMap((d, index) =>
    index === currentIndex
      ? [1, 2, 3].map((n) => ({
          key: `${d}${n}`,
          discipline: d,
          attempt: n,
          kind: 'attempt',
          label: `${SHORT_NAMES[d]} ${n}`,
        }))
      : [
          {
            key: d,
            discipline: d,
            kind: currentIndex === -1 || index < currentIndex ? 'done' : 'todo',
            label: SHORT_NAMES[d],
          },
        ],
  )
})

const gridColumns = computed(() => `64px minmax(0, 1fr) repeat(${columns.value.length}, 112px) 116px 136px`)

// Lifters in Powerlifting Live's order, with a header whenever the weight class or category changes.
const rows = computed(() => {
  const result = []
  let previous = null

  for (const lifter of props.scoreboard.lifters) {
    const groupKey = `${lifter.weightClass}|${lifter.category}`
    if (groupKey !== previous) {
      const header = [formatWeightClass(lifter.weightClass), lifter.category].filter(Boolean).join(' · ')
      if (header) {
        result.push({ key: `header-${groupKey}-${result.length}`, header })
      }
      previous = groupKey
    }
    result.push({ key: lifter.id ?? `lifter-${result.length}`, lifter })
  }

  return result
})

// Rows shrink when the group is large, so that the whole list always fits between the demo toolbar and the bottom.
const rowHeight = computed(() => Math.max(26, Math.min(54, Math.floor(620 / Math.max(rows.value.length, 1)))))

// The list is placed where it ends up once fully folded down (centered on the stage), so that the heading slides
// in at its final position and only the list grows below it. Measured from the content, which is laid out even
// while folded.
const board = ref(null)
const top = ref(0)

function placeBoard() {
  const heading = board.value?.querySelector('.heading')
  const list = board.value?.querySelector('.body-inner')
  if (heading && list) {
    top.value = Math.max(40, Math.round((1080 - heading.offsetHeight - list.scrollHeight) / 2))
  }
}

onMounted(placeBoard)
watch(rows, () => nextTick(placeBoard))

function cell(lifter, column) {
  const attempts = lifter.lifts[column.discipline]

  if (column.kind === 'attempt') {
    const { weight, outcome } = attempts[column.attempt - 1]
    return {
      label: weight == null ? '' : formatWeight(weight),
      state: outcome ?? (weight == null ? 'empty' : 'declared'),
    }
  }

  if (column.kind === 'todo') {
    return { label: '', state: 'empty' }
  }

  const good = attempts.filter((attempt) => attempt.outcome === 'good').map((attempt) => attempt.weight)
  if (good.length) {
    return { label: formatWeight(Math.max(...good)), state: 'good' }
  }

  return attempts.some((attempt) => attempt.outcome) ? { label: '–', state: 'failed' } : { label: '', state: 'empty' }
}
</script>

<style scoped>
.scoreboard {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: flex-start;
  justify-content: center;
}

/*
  Entrance in two steps: the heading slides in from the left, then the list folds down below it. The board paints
  nothing itself, so only the revealed parts (and their shadow) show.
*/
.board {
  width: 1560px;
  filter: drop-shadow(0 20px 36px rgba(0, 0, 0, 0.45));
  animation: board-slide-in 650ms var(--motion-enter) both;
}

.heading {
  display: flex;
  align-items: center;
  gap: 4px;
  border-radius: 14px 14px 0 0;
  background: var(--overlay-panel-deep);
}

.brand {
  flex: none;
  padding: 18px 32px;
}

.body {
  display: grid;
  border-radius: 0 0 14px 14px;
  background: var(--overlay-panel);
  animation: body-fold-down 700ms var(--motion-move) 650ms both;
}

.body-inner {
  min-height: 0;
  overflow: hidden;
}

/* Spacing below the last row, inside the clipped area so the folded list is really 0 px tall. */
.body-inner::after {
  content: '';
  display: block;
  height: 10px;
}

.logo {
  width: 72px;
  height: 72px;
  padding: 3px;
  border-radius: 50%;
  background: #ffffff;
}

.eyebrow {
  font-family: var(--overlay-font);
  font-size: 20px;
  font-weight: 600;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: var(--overlay-silver);
}

.title {
  font-family: var(--overlay-font);
  font-size: 48px;
  font-weight: 700;
  line-height: 1;
  text-transform: uppercase;
}

.row {
  display: grid;
  grid-template-columns: var(--columns);
  align-items: center;
  gap: 8px;
  min-height: var(--row-height);
  padding: 0 32px;
}

.columns {
  min-height: 46px;
  font-family: var(--overlay-font);
  font-size: 18px;
  font-weight: 600;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: var(--overlay-silver);
  border-bottom: 1px solid var(--overlay-divider);
}

.columns .current {
  color: #ffffff;
}

.group {
  display: block;
  margin-top: 6px;
  background: var(--overlay-panel-deep);
  box-shadow: inset 6px 0 0 var(--overlay-silver);
  font-family: var(--overlay-font);
  font-size: 22px;
  font-weight: 700;
  letter-spacing: 0.14em;
  line-height: calc(var(--row-height) * 0.85);
  text-transform: uppercase;
  color: #ffffff;
  animation: row-in 450ms var(--motion-enter) both;
  animation-delay: calc(800ms + var(--index) * 35ms);
}

.lifter {
  border-top: 1px solid rgba(166, 172, 185, 0.12);
  font-size: calc(var(--row-height) * 0.44);
  animation: row-in 450ms var(--motion-enter) both;
  animation-delay: calc(800ms + var(--index) * 35ms);
}

.lifter.active {
  background: rgba(255, 255, 255, 0.1);
  box-shadow: inset 6px 0 0 #ffffff;
}

.place {
  font-family: var(--overlay-font);
  font-weight: 700;
  text-align: center;
}

.who {
  display: flex;
  align-items: baseline;
  gap: 14px;
  min-width: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.name {
  font-family: var(--overlay-font);
  font-size: 1.25em;
  font-weight: 700;
  text-transform: uppercase;
}

.team {
  color: var(--overlay-silver);
}

.cell {
  text-align: center;
}

.lifter .cell {
  height: calc(var(--row-height) * 0.72);
  border-radius: 4px;
  font-family: var(--overlay-font);
  font-size: 1.15em;
  font-weight: 600;
  line-height: calc(var(--row-height) * 0.72);
  font-variant-numeric: tabular-nums;
}

.lifter .cell.good {
  background: var(--overlay-good);
}

.lifter .cell.failed {
  background: var(--overlay-failed);
}

.lifter .cell.failed:not(:empty) {
  text-decoration: line-through;
  text-decoration-thickness: 2px;
}

.lifter .cell.declared {
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.35);
  color: var(--overlay-silver);
}

.lifter .cell.empty {
  background: rgba(255, 255, 255, 0.04);
}

.lifter .cell.points {
  color: var(--overlay-silver);
}

.lifter .cell.total {
  font-size: 1.3em;
  font-weight: 700;
}

@keyframes board-slide-in {
  from {
    opacity: 0;
    transform: translateX(-1800px);
  }
  30% {
    opacity: 1;
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes body-fold-down {
  from {
    grid-template-rows: 0fr;
  }
  to {
    grid-template-rows: 1fr;
  }
}

@keyframes row-in {
  from {
    opacity: 0;
    transform: translateX(-24px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
</style>
