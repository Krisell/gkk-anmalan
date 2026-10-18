import { createApp } from 'vue'
import LiveOverlay from './LiveOverlay.vue'
import './overlay.css'

const app = createApp(LiveOverlay)

// The overlay runs unattended in OBS for hours; log errors instead of letting them break the page.
app.config.errorHandler = (error) => console.error('[live-overlay]', error)
window.addEventListener('unhandledrejection', (event) => {
  console.error('[live-overlay]', event.reason)
  event.preventDefault()
})

app.mount('#live-overlay')
