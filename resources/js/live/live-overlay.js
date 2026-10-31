import { createApp } from 'vue'
import LiveOverlay from './LiveOverlay.vue'
import HallScreen from './HallScreen.vue'
import './overlay.css'

// The warm-up hall's TV screen (/live/hall, or ?view=hall), not a broadcast graphic.
const hall =
  /\/live\/hall\/?$/.test(window.location.pathname) ||
  new URLSearchParams(window.location.search).get('view') === 'hall'
const app = createApp(hall ? HallScreen : LiveOverlay)

// The overlay runs unattended in OBS for hours; log errors instead of letting them break the page.
app.config.errorHandler = (error) => console.error('[live-overlay]', error)
window.addEventListener('unhandledrejection', (event) => {
  console.error('[live-overlay]', event.reason)
  event.preventDefault()
})

app.mount('#live-overlay')
