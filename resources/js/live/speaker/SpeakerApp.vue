<template>
  <div class="speaker">
    <header class="top">
      <div class="title-row">
        <h1>Resultat</h1>
        <span class="updated">
          <template v-if="error">Kunde inte uppdatera{{ updatedAt ? ` · senast ${updatedAt}` : '' }}</template>
          <template v-else-if="updatedAt">Uppdaterad {{ updatedAt }}</template>
          <template v-else-if="competition">Hämtar…</template>
        </span>
        <button v-if="competition" class="refresh" :disabled="loading" @click="load">Uppdatera</button>
      </div>
      <nav v-if="tabs.length" class="tabs">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          class="tab"
          :class="{ active: tab.key === activeKey }"
          @click="choose(tab.key)"
        >
          {{ tab.label }}
          <small>{{ tab.groups.join(', ') }}</small>
        </button>
      </nav>
    </header>

    <main>
      <form v-if="!competition" class="pick" @submit.prevent="pickTyped">
        <p>Välj tävling på kontrollsidan (/live/control), eller ange tävlingens id i Powerlifting Live:</p>
        <input v-model="typed" inputmode="numeric" pattern="[0-9]*" placeholder="T.ex. 3261" />
        <button class="refresh" type="submit">Visa</button>
      </form>

      <p v-else-if="!groups.length && !loading" class="empty">Inga resultat ännu.</p>

      <section v-for="group in activeGroups" :key="group.name" class="group">
        <h2>
          {{ group.name }}
          <span class="status" :class="group.status">{{ GROUP_STATUS[group.status] }}</span>
        </h2>
        <div v-for="block in classesOf(group)" :key="block.key" class="class">
          <h3>{{ block.label }}</h3>
          <table>
            <thead>
              <tr>
                <th class="place">Plac.</th>
                <th>Lyftare</th>
                <th class="num wide">Kroppsvikt</th>
                <th v-for="discipline in group.scoreboard.disciplines" :key="discipline" class="num wide">
                  {{ disciplineName(discipline) }}
                </th>
                <th class="num" :class="{ main: !block.coefficient }">Total</th>
                <th class="num" :class="{ main: block.coefficient, wide: !block.coefficient }">IPF-poäng</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="lifter in block.lifters" :key="lifter.id" :class="MEDALS[lifter.place]">
                <td class="place">
                  <span class="medal">{{ lifter.place ?? '–' }}</span>
                </td>
                <td class="who">
                  <span class="name">{{ lifter.name ?? 'Okänd lyftare' }}</span>
                  <span v-if="lifter.team" class="team">{{ lifter.team }}</span>
                </td>
                <td class="num wide">{{ formatBodyWeight(lifter.bodyWeight) }}</td>
                <td v-for="discipline in group.scoreboard.disciplines" :key="discipline" class="num wide">
                  {{ formatWeight(bestLift(lifter, discipline)) }}
                </td>
                <td class="num" :class="{ main: !block.coefficient }">
                  {{ lifter.total ? formatWeight(lifter.total) : '–' }}
                </td>
                <td class="num" :class="{ main: block.coefficient, wide: !block.coefficient }">
                  {{ formatPoints(lifter.points) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, ref, shallowRef } from 'vue'
import { normalizeResults } from '../normalize.js'
import {
  competitionClass,
  disciplineName,
  formatBodyWeight,
  formatPoints,
  formatWeight,
  isCoefficientClass,
} from '../format.js'
import { normalizeCompetition } from '../remote/controlState.js'
import { followCompetition } from '../remote/followCompetition.js'
import { currentSession, speakerSessions } from './sessions.js'

/**
 * The speaker's results (/live/speaker), e.g. for reading out the prize ceremony: every group of a start time, class
 * by class, by place. Not affected by what the control page shows on the stream; only the competition is taken from
 * it (unless the address has ?competition=). Refreshed every 30 s.
 */
const REFRESH_MS = 30000
const GROUP_STATUS = { upcoming: 'Ej startad', ongoing: 'Pågår', done: 'Klar' }
const MEDALS = { 1: 'gold', 2: 'silver', 3: 'bronze' }

const params = new URLSearchParams(window.location.search)
const competition = ref(normalizeCompetition(params.get('competition')))
const typed = ref('')
if (!competition.value) {
  followCompetition((id) => {
    competition.value = id
    void load()
  })
}

function pickTyped() {
  const id = normalizeCompetition(typed.value)
  if (id) {
    competition.value = id
    params.set('competition', id)
    history.replaceState(null, '', `?${params}`)
    void load()
  }
}

const groups = shallowRef([])
const loading = ref(false)
const error = ref(false)
const updatedAt = ref(null)
let timer = null

async function load() {
  clearTimeout(timer)
  if (!competition.value) {
    return
  }
  loading.value = true
  try {
    const response = await fetch(`/live/results/${competition.value}`, { headers: { Accept: 'application/json' } })
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`)
    }
    groups.value = normalizeResults(await response.json())
    error.value = false
    updatedAt.value = new Date().toLocaleTimeString('sv-SE', { hour: '2-digit', minute: '2-digit' })
  } catch {
    error.value = true
  } finally {
    loading.value = false
    timer = setTimeout(load, REFRESH_MS)
  }
}
void load()
onBeforeUnmount(() => clearTimeout(timer))

const tabs = computed(() => speakerSessions(groups.value.map((group) => group.name)))
// The tab chosen here, or else the session under way (the last one started).
const chosen = ref(null)
const now = () => new Date().toLocaleTimeString('sv-SE', { hour: '2-digit', minute: '2-digit' })
const activeKey = computed(() =>
  tabs.value.some((tab) => tab.key === chosen.value) ? chosen.value : currentSession(tabs.value, now()),
)
const activeGroups = computed(() => {
  const names = tabs.value.find((tab) => tab.key === activeKey.value)?.groups ?? []
  return groups.value.filter((group) => names.includes(group.name))
})

function choose(key) {
  chosen.value = key
  window.scrollTo({ top: 0 })
}

// A group's lifters by class (in the results list's order), each class by place.
function classesOf(group) {
  const classes = new Map()
  for (const lifter of group.scoreboard.lifters) {
    const { key, label } = competitionClass(lifter)
    if (!classes.has(key)) {
      classes.set(key, {
        key,
        label: label ?? 'Okänd klass',
        coefficient: isCoefficientClass(lifter.category),
        lifters: [],
      })
    }
    classes.get(key).lifters.push(lifter)
  }
  return [...classes.values()]
}

// The heaviest good lift of a discipline, or null.
function bestLift(lifter, discipline) {
  const good = (lifter.lifts[discipline] ?? []).filter((attempt) => attempt.outcome === 'good' && attempt.weight)
  return good.length ? Math.max(...good.map((attempt) => attempt.weight)) : null
}
</script>

<style>
html,
body {
  margin: 0;
  background: #f3f5f9;
}
</style>

<style scoped>
.speaker {
  --navy: #314270;
  --navy-light: #45557e;
  --muted: #6b7489;
  --line: #dde1ea;
  min-height: 100vh;
  font-family: 'Barlow', 'Helvetica Neue', Arial, sans-serif;
  color: var(--navy);
  -webkit-font-smoothing: antialiased;
}

.top {
  position: sticky;
  top: 0;
  z-index: 2;
  padding: 12px 16px 0;
  background: var(--navy);
  color: #ffffff;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.18);
}

.title-row {
  display: flex;
  align-items: center;
  gap: 12px;
  max-width: 1200px;
  margin: 0 auto;
}

h1 {
  margin: 0;
  font-family: 'Barlow Condensed', 'Arial Narrow', sans-serif;
  font-size: 28px;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

.updated {
  flex: 1;
  font-size: 14px;
  color: #c9cfdc;
}

.refresh {
  padding: 8px 14px;
  border: 0;
  border-radius: 8px;
  background: #f6c945;
  color: var(--navy);
  font:
    600 15px 'Barlow',
    sans-serif;
  cursor: pointer;
}

.refresh:disabled {
  opacity: 0.6;
}

.tabs {
  display: flex;
  gap: 6px;
  max-width: 1200px;
  margin: 10px auto 0;
  overflow-x: auto;
}

.tab {
  flex: none;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  padding: 8px 16px 10px;
  border: 0;
  border-radius: 10px 10px 0 0;
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff;
  font:
    700 20px 'Barlow Condensed',
    'Arial Narrow',
    sans-serif;
  text-transform: uppercase;
  cursor: pointer;
}

.tab small {
  font:
    500 13px 'Barlow',
    sans-serif;
  text-transform: none;
  opacity: 0.8;
}

.tab.active {
  background: #f3f5f9;
  color: var(--navy);
}

main {
  max-width: 1200px;
  margin: 0 auto;
  padding: 8px 16px 48px;
}

.pick,
.empty {
  margin-top: 24px;
  font-size: 18px;
}

.pick input {
  margin-right: 8px;
  padding: 8px 12px;
  border: 1px solid var(--line);
  border-radius: 8px;
  font-size: 18px;
}

.group h2 {
  display: flex;
  align-items: baseline;
  gap: 12px;
  margin: 28px 0 4px;
  font-family: 'Barlow Condensed', 'Arial Narrow', sans-serif;
  font-size: 34px;
  font-weight: 700;
  text-transform: uppercase;
}

.status {
  font-family: 'Barlow', sans-serif;
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.08em;
  color: var(--muted);
}

.status.ongoing {
  color: #2f9a5c;
}

.class {
  margin-top: 14px;
  overflow: hidden;
  border-radius: 12px;
  background: #ffffff;
  box-shadow: 0 2px 8px rgba(49, 66, 112, 0.08);
}

h3 {
  margin: 0;
  padding: 10px 16px;
  background: var(--navy-light);
  color: #ffffff;
  font-family: 'Barlow Condensed', 'Arial Narrow', sans-serif;
  font-size: 22px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
}

/* Fixed columns, so that they line up from class to class. */
table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

th.num {
  width: 112px;
}

th {
  padding: 8px 10px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-align: left;
  text-transform: uppercase;
  color: var(--muted);
}

td {
  padding: 10px;
  border-top: 1px solid var(--line);
  font-size: 18px;
}

.num {
  text-align: right;
  white-space: nowrap;
  font-variant-numeric: tabular-nums;
}

td.main {
  font-size: 22px;
  font-weight: 700;
}

.place {
  width: 56px;
  text-align: center;
}

.medal {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  font:
    700 20px 'Barlow Condensed',
    sans-serif;
}

.gold .medal {
  background: radial-gradient(circle at 32% 28%, #ffe58a, #d9b54a 55%, #9c7a1c);
}

.silver .medal {
  background: radial-gradient(circle at 32% 28%, #f1f4f8, #b9c0cc 55%, #6f7785);
}

.bronze .medal {
  background: radial-gradient(circle at 32% 28%, #f0b98a, #c27c45 55%, #7d4720);
}

.who {
  display: flex;
  flex-direction: column;
}

.name {
  font-family: 'Barlow Condensed', 'Arial Narrow', sans-serif;
  font-size: 24px;
  font-weight: 700;
  text-transform: uppercase;
}

.team {
  font-size: 15px;
  color: var(--muted);
}

/* A phone: name, total and points only. */
@media (max-width: 640px) {
  .wide {
    display: none;
  }
}
</style>
