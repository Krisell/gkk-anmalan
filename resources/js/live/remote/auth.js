import { connectAuthEmulator, getAuth } from 'firebase/auth'
import { emulatorHost, liveFirebaseApp } from './firebase.js'

const auths = new Map()

// Firebase Auth for a page that signs in, on its own app (SIGN_IN_APPS in firebase.js); the overlay and the slideshow
// screen only read, and need no sign-in.
export function liveAuth(name) {
  if (!auths.has(name)) {
    const auth = getAuth(liveFirebaseApp(name))
    const host = emulatorHost()
    if (host) {
      connectAuthEmulator(auth, `http://${host}:9099`, { disableWarnings: true })
    }
    auths.set(name, auth)
  }
  return auths.get(name)
}
