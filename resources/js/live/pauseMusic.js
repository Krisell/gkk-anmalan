import { onBeforeUnmount, watch } from 'vue'

/**
 * Pause music: every audio file in assets/music/ (see the README there), played by the overlay in OBS when it is
 * turned on from the control page, in a shuffled order, over and over. It fades in when turned on and out when
 * turned off, and continues where it was when turned on again. Not tied to what is shown: it is on until turned off.
 *
 * Only royalty-free music (e.g. YouTube's Audio Library), or YouTube may interrupt the stream.
 */
export const MUSIC_TRACKS = Object.values(
  import.meta.glob('./assets/music/*.{mp3,m4a,ogg,wav}', { eager: true, query: '?url', import: 'default' }),
).sort((a, b) => a.localeCompare(b))

const FADE_MS = 2000
const FADE_STEP_MS = 50
// After this many tracks failing in a row (e.g. a broken file each), stop trying until turned on again.
const MAX_FAILURES = 3

/**
 * Plays the music while `settings().music` is true, at `settings().musicVolume`. Does nothing without tracks or when
 * `enabled` is false (e.g. the control page's preview, which mustn't play on the phone too).
 */
export function usePauseMusic(settings, { enabled = true, tracks = MUSIC_TRACKS } = {}) {
  if (!enabled || !tracks.length || typeof Audio === 'undefined') {
    return
  }

  const audio = new Audio()
  audio.preload = 'auto'
  audio.volume = 0
  // In the page, so that it can be inspected while testing.
  audio.dataset.pauseMusic = ''
  audio.hidden = true
  document.body.append(audio)

  let order = shuffled(tracks)
  let index = 0
  let target = 0
  let failures = 0
  let fadeTimer = null

  function load(i) {
    index = i
    audio.src = order[index]
  }

  function next() {
    if (index + 1 >= order.length) {
      order = shuffled(tracks, order[index])
      load(0)
    } else {
      load(index + 1)
    }
    if (target > 0) {
      void play()
    }
  }

  async function play() {
    try {
      await audio.play()
      failures = 0
    } catch (error) {
      // Autoplay refused (a browser without OBS's permission) or a file that can't be played.
      if (error?.name !== 'NotAllowedError' && ++failures < MAX_FAILURES) {
        next()
      }
    }
  }

  audio.addEventListener('ended', next)
  audio.addEventListener('error', () => {
    if (target > 0 && ++failures < MAX_FAILURES) {
      next()
    }
  })

  // Steps the volume towards the target; pauses once faded out.
  function fade() {
    clearInterval(fadeTimer)
    const step = FADE_STEP_MS / FADE_MS
    fadeTimer = setInterval(() => {
      const difference = target - audio.volume
      if (Math.abs(difference) <= step) {
        audio.volume = target
        clearInterval(fadeTimer)
        if (target === 0) {
          audio.pause()
        }
      } else {
        audio.volume = Math.min(1, Math.max(0, audio.volume + Math.sign(difference) * step))
      }
    }, FADE_STEP_MS)
  }

  load(0)

  const stop = watch(
    () => [settings().music, settings().musicVolume],
    ([on, volume]) => {
      target = on ? volume : 0
      if (on && audio.paused) {
        failures = 0
        void play()
      }
      fade()
    },
    { immediate: true },
  )

  onBeforeUnmount(() => {
    stop()
    clearInterval(fadeTimer)
    audio.pause()
    audio.remove()
  })
}

// The tracks in a random order, not starting with `last` (the one just played) when there are several.
export function shuffled(tracks, last = null) {
  const order = [...tracks]
  for (let i = order.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1))
    ;[order[i], order[j]] = [order[j], order[i]]
  }
  if (order.length > 1 && order[0] === last) {
    ;[order[0], order[1]] = [order[1], order[0]]
  }
  return order
}
