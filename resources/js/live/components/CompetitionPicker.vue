<template>
  <div class="picker">
    <h1 class="title">Välj tävling</h1>
    <p class="subtitle">Tävlingar live på Powerlifting Live just nu</p>

    <p v-if="state === 'loading'" class="message">Hämtar tävlingar…</p>
    <p v-else-if="state === 'error'" class="message">
      Kunde inte hämta tävlingarna från Powerlifting Live. Ladda om sidan för att försöka igen.
    </p>
    <p v-else-if="!competitions.length" class="message">Inga tävlingar live just nu.</p>

    <ul v-else class="list">
      <li v-for="competition in competitions" :key="competition.id">
        <a :href="linkTo(competition.id)" class="competition">
          <span class="name">{{ competition.name }}</span>
          <span v-if="competition.date" class="date">{{ competition.date }}</span>
          <span class="address">{{ screenAddress(competition.id) }}</span>
        </a>
      </li>
    </ul>

    <p class="hint">
      Visa en tävling på klubbens skärm genom att klistra in dess adress under <em>Visa en webbsida</em> i bildspelets
      styrning. Tävlingar som inte listas här kan anges med <code>?competition=</code> och tävlingens id i Powerlifting
      Live.
    </p>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'

/**
 * The warm-up hall screen without a competition (/live/hall): the competitions Powerlifting Live lists as live now
 * (LiveOverlayController@competitions), each linking to its hall screen.
 */
const state = ref('loading')
const competitions = ref([])

onMounted(async () => {
  try {
    const response = await fetch(new URL('competitions', window.location.href), {
      headers: { Accept: 'application/json' },
    })
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`)
    }
    competitions.value = (await response.json()).competitions ?? []
    state.value = 'loaded'
  } catch {
    state.value = 'error'
  }
})

// This page with the competition chosen (keeping ?view=hall when the page was opened as /live/overlay).
function linkTo(id) {
  const url = new URL(window.location.href)
  url.searchParams.set('competition', id)
  return url.toString()
}

// The short address, for the slideshow's live control.
function screenAddress(id) {
  return `${window.location.origin}/live/hall?competition=${id}`
}
</script>

<style scoped>
.picker {
  position: absolute;
  inset: 0;
  box-sizing: border-box;
  padding: 80px 160px;
  overflow: hidden;
}

.title {
  margin: 0;
  font-family: var(--overlay-font);
  font-size: 72px;
  font-weight: 700;
  text-transform: uppercase;
}

.subtitle {
  margin: 4px 0 48px;
  font-size: 28px;
  color: var(--overlay-silver);
}

.message {
  font-size: 32px;
  color: var(--overlay-silver);
}

.list {
  display: grid;
  gap: 16px;
  margin: 0;
  padding: 0;
  list-style: none;
}

.competition {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  gap: 4px 32px;
  padding: 24px 32px;
  border-radius: 12px;
  background: var(--overlay-panel-deep);
  box-shadow: inset 0 0 0 2px rgba(166, 172, 185, 0.25);
  color: inherit;
  text-decoration: none;
  transition: box-shadow var(--motion-fast) var(--motion-move);
}

.competition:hover,
.competition:focus-visible {
  box-shadow: inset 0 0 0 3px #f6c945;
  outline: none;
}

.name {
  font-family: var(--overlay-font);
  font-size: 44px;
  font-weight: 700;
  text-transform: uppercase;
}

.date {
  align-self: center;
  font-family: var(--overlay-font);
  font-size: 36px;
  font-weight: 600;
  color: var(--overlay-silver);
}

.address {
  grid-column: 1 / -1;
  font-size: 24px;
  color: var(--overlay-silver);
  user-select: all;
}

.hint {
  max-width: 1300px;
  margin-top: 48px;
  font-size: 24px;
  line-height: 1.5;
  color: var(--overlay-silver);
}

code {
  color: #ffffff;
}
</style>
