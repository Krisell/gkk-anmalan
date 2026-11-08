import { createApp } from 'vue'
import LiveOverlay from './LiveOverlay.vue'
import HallScreen from './HallScreen.vue'
import { followCompetition } from './remote/followCompetition.js'
import './overlay.css'

const params = new URLSearchParams(window.location.search)

// The warm-up hall's TV screen (/live/hall, or ?view=hall), not a broadcast graphic.
const hall = /\/live\/hall\/?$/.test(window.location.pathname) || params.get('view') === 'hall'

// The broadcast overlay without ?competition= (and not the demo or a page of its own, like a slate) follows the
// competition chosen on the control page, and starts over on the new one when another is chosen.
const followsControl =
  !hall &&
  !params.has('competition') &&
  !params.has('demo') &&
  !['slate', 'replay', 'commentators', 'stinger'].includes(params.get('view'))

// The overlay runs unattended in OBS for hours; log errors instead of letting them break the page.
window.addEventListener('unhandledrejection', (event) => {
  console.error('[live-overlay]', event.reason)
  event.preventDefault()
})

let app = null

function mount(component, props = {}) {
  app?.unmount()
  app = createApp(component, props)
  app.config.errorHandler = (error) => console.error('[live-overlay]', error)
  app.mount('#live-overlay')
}

if (followsControl) {
  mount(LiveOverlay, { followsControl: true })
  followCompetition((competition) => mount(LiveOverlay, { competition, followsControl: true }))
} else {
  mount(hall ? HallScreen : LiveOverlay)
}
