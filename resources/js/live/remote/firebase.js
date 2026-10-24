import { getApp, getApps, initializeApp } from 'firebase/app'
import { connectDatabaseEmulator, getDatabase } from 'firebase/database'
import { firebaseConfig } from '../../firebaseConfig.js'

/**
 * The club's Firebase app, for the live overlay's remote control (see controlState.js).
 *
 * For local testing, ?firebaseEmulator=<host> (only on a local site) uses the Firebase emulators instead
 * (`firebase emulators:start --only auth,database` in the project root).
 */
export function liveFirebaseApp() {
  return getApps().length ? getApp() : initializeApp(firebaseConfig)
}

const LOCAL_HOSTS = /(^localhost$|^127\.0\.0\.1$|\.test$)/

export function emulatorHost() {
  const host = new URLSearchParams(window.location.search).get('firebaseEmulator')
  return host && LOCAL_HOSTS.test(window.location.hostname) ? host : null
}

let database = null

export function liveDatabase() {
  if (!database) {
    database = getDatabase(liveFirebaseApp())
    const host = emulatorHost()
    if (host) {
      connectDatabaseEmulator(database, host, 9000)
    }
  }
  return database
}
