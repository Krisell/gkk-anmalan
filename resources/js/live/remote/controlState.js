/**
 * The remote control's data in Firebase's Realtime Database, under live/ (one broadcast at a time):
 *
 * - control: settings that persist until changed (what is shown, texts, which automatic graphics are on),
 * - commands: one-off actions (PB stamp, skip, …), each { type, at } with the server time it was given,
 * - presence/{session}: written by each overlay in OBS ({ at, competition, showing, feed }), so the control page
 *   can show whether it is online, preview the same competition, and show what is on air.
 *
 * Everything coming from the database is normalized here, so a missing or odd value never breaks the overlay.
 */

// What the overlay shows: 'auto' (follows the competition), 'hidden' (no graphics at all), 'list' (the results list)
// or 'slate' (a full-screen message, see SLATES).
export const MODES = ['auto', 'hidden', 'list', 'slate']
export const SLATES = ['start', 'pause', 'end']
export const COMMANDS = ['pb', 'skip', 'list', 'commentators']

export const DEFAULT_CONTROL = Object.freeze({
  mode: 'auto',
  listGroup: null, // the results list of another group than the one on the platform, e.g. 'Grupp 03'
  slate: 'start',
  pauseTime: null, // when the next group starts, e.g. '14:30', for the pause slate
  title: null, // the competition's name, for the slates
  message: null, // { id, text }: a message shown until it is taken down
  commentators: [], // names for the commentator strap
  intros: true, // the big lifter card when a new lifter is called
  roundBoards: true, // the results list between rounds
})

export const MESSAGE_MAX_LENGTH = 120

// Commands older than this are not run, e.g. when an overlay is (re)loaded after they were given.
export const COMMAND_MAX_AGE_MS = 15000

const text = (value, max) => (typeof value === 'string' && value.trim() ? value.trim().slice(0, max) : null)

export function normalizeControl(raw) {
  const control = raw && typeof raw === 'object' ? raw : {}
  const message = text(control.message?.text, MESSAGE_MAX_LENGTH)

  return {
    mode: MODES.includes(control.mode) ? control.mode : DEFAULT_CONTROL.mode,
    listGroup: text(control.listGroup, 40),
    slate: SLATES.includes(control.slate) ? control.slate : DEFAULT_CONTROL.slate,
    pauseTime: /^\d{1,2}[:.]\d{2}$/.test(control.pauseTime ?? '') ? control.pauseTime.replace('.', ':') : null,
    title: text(control.title, 80),
    message: message ? { id: String(control.message.id ?? message), text: message } : null,
    commentators: parseNames(control.commentators),
    intros: control.intros !== false,
    roundBoards: control.roundBoards !== false,
  }
}

// "Anna Svensson, Erik Berg" (as typed on the control page) or a list of names.
export function parseNames(value) {
  const names = Array.isArray(value) ? value : typeof value === 'string' ? value.split(',') : []
  return names
    .map((name) => text(name, 40))
    .filter(Boolean)
    .slice(0, 4)
}

// Whether a command should still be run, given the server's current time.
export function isFreshCommand(command, serverNow) {
  return (
    COMMANDS.includes(command?.type) &&
    typeof command.at === 'number' &&
    Math.abs(serverNow - command.at) < COMMAND_MAX_AGE_MS
  )
}
