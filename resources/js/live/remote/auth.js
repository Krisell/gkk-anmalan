import { connectAuthEmulator, getAuth } from 'firebase/auth'
import { emulatorHost, liveFirebaseApp } from './firebase.js'

let auth = null

// Firebase Auth for the control page (the overlay itself only reads, and needs no sign-in).
export function liveAuth() {
  if (!auth) {
    auth = getAuth(liveFirebaseApp())
    const host = emulatorHost()
    if (host) {
      connectAuthEmulator(auth, `http://${host}:9099`, { disableWarnings: true })
    }
  }
  return auth
}
