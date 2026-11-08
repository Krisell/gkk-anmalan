import { getApp, getApps, initializeApp } from 'firebase/app'
import { connectDatabaseEmulator, getDatabase } from 'firebase/database'
import { firebaseConfig } from '../../firebaseConfig.js'

/**
 * The club's Firebase app, for the live overlay's remote control (see controlState.js).
 *
 * For local testing, ?firebaseEmulator=<host> (only on a local site) uses the Firebase emulators instead
 * (`firebase emulators:start --only auth,database` in the project root).
 */
// The default app is for pages that only read (the overlay, the slideshow screen). A page that signs in uses an app of
// its own (see SIGN_IN_APPS), because Firebase keeps one signed-in user per app, shared by all tabs: otherwise signing
// in to the slideshow's control in one tab would sign the live control out in another, and the other way round.
export const SIGN_IN_APPS = Object.freeze({ liveControl: 'live-control', slideshowAdmin: 'slideshow-admin' })

const DEFAULT_APP = '[DEFAULT]'

export function liveFirebaseApp(name = DEFAULT_APP) {
  return getApps().some((app) => app.name === name) ? getApp(name) : initializeApp(firebaseConfig, name)
}

const LOCAL_HOSTS = /(^localhost$|^127\.0\.0\.1$|\.test$)/

export function emulatorHost() {
  const host = new URLSearchParams(window.location.search).get('firebaseEmulator')
  return host && LOCAL_HOSTS.test(window.location.hostname) ? host : null
}

const databases = new Map()

export function liveDatabase(name = DEFAULT_APP) {
  if (!databases.has(name)) {
    const database = getDatabase(liveFirebaseApp(name))
    const host = emulatorHost()
    if (host) {
      connectDatabaseEmulator(database, host, 9000)
    }
    databases.set(name, database)
  }
  return databases.get(name)
}
