import { SESSIONS } from '../../components/Slideshow/dm2026.js'

/**
 * The speaker's tabs: the competition's groups by start time. Powerlifting Live has no start times, so they come from
 * the day's schedule (the DM 2026 slide's SESSIONS, groups written '1–2'), matched on the group's number ('Grupp 1' and
 * 'Grupp 01' are both group 1). Groups the schedule doesn't cover get a tab each, after the scheduled ones.
 *
 * Tab: { key, label, start: 'HH:MM' | null, groups: [group name, …] }, the groups in group order.
 */
export const SCHEDULE = SESSIONS.map((session) => ({ start: session.start, groups: groupNumbers(session.groups) }))

export function speakerSessions(groupNames, schedule = SCHEDULE) {
  const names = [...groupNames].sort((a, b) => a.localeCompare(b, 'sv', { numeric: true }))
  const scheduled = new Set()
  const tabs = schedule
    .map((session) => {
      const groups = names.filter((name) => session.groups.includes(groupNumber(name)))
      groups.forEach((name) => scheduled.add(name))
      return { key: `start-${session.start}`, label: `Start ${session.start}`, start: session.start, groups }
    })
    .filter((tab) => tab.groups.length)
  const others = names
    .filter((name) => !scheduled.has(name))
    .map((name) => ({ key: `group-${name}`, label: name, start: null, groups: [name] }))
  return [...tabs, ...others]
}

// The tab to open with: the last one that has started by `time` ('HH:MM'), or the first.
export function currentSession(tabs, time) {
  const started = tabs.filter((tab) => tab.start && tab.start <= time)
  return (started.at(-1) ?? tabs[0])?.key ?? null
}

// '1–2' (or '1-2', '1, 2', '3') -> [1, 2].
export function groupNumbers(text) {
  const range = /^\s*(\d+)\s*[–-]\s*(\d+)\s*$/.exec(text ?? '')
  if (range) {
    const [from, to] = [Number(range[1]), Number(range[2])]
    return Array.from({ length: Math.max(0, to - from + 1) }, (_, i) => from + i)
  }
  return String(text ?? '')
    .split(/[,\s]+/)
    .filter(Boolean)
    .map(Number)
    .filter(Number.isInteger)
}

// 'Grupp 01' -> 1; null without a number.
export function groupNumber(name) {
  const match = /(\d+)\s*$/.exec(name ?? '')
  return match ? Number(match[1]) : null
}
