import { describe, expect, it } from 'vite-plus/test'
import { DEMO_STEPS, demoFeed } from '../../../resources/js/live/sources/demoSource.js'
import { normalizeFeed } from '../../../resources/js/live/normalize.js'
import { initialOverlayState, reduceOverlayState } from '../../../resources/js/live/overlayState.js'

describe('demo source', () => {
  it('walks through a complete scenario', () => {
    let state = initialOverlayState()
    const timeline = DEMO_STEPS.map((step, index) => {
      const snapshot = normalizeFeed(demoFeed(index))
      state = reduceOverlayState(state, snapshot, index * 4000)
      return { snapshot, result: state.result }
    })

    expect(timeline[0].snapshot.current).toBeNull()
    expect(timeline[1].snapshot.current).toMatchObject({
      id: 'd1:SQ2',
      name: 'Johanna Hassel',
      weight: 130,
      attempt: 2,
    })
    expect(timeline[2].result).toMatchObject({ id: 'd1:SQ2', outcome: 'good' })
    expect(timeline[3].snapshot.current).toMatchObject({ id: 'd2:SQ2', weight: 200 })
    expect(timeline[4].snapshot.current).toMatchObject({ id: 'd2:SQ2', weight: 202.5 })
    expect(timeline[5].result).toMatchObject({ id: 'd2:SQ2', outcome: 'failed' })
    expect(timeline[7].result).toMatchObject({ id: 'd3:SQ2', outcome: 'good', decisions: null })
    expect(timeline[8].snapshot.current).toMatchObject({ id: 'd1:BP2', discipline: 'BP', attempt: 2 })
    expect(timeline[9].result).toMatchObject({ id: 'd1:BP2', outcome: 'good' })
    expect(timeline[10].snapshot.current).toMatchObject({ id: 'd2:DL3', discipline: 'DL', weight: 265 })
    expect(timeline[11].result).toMatchObject({ id: 'd2:DL3', outcome: 'failed' })
    expect(timeline.at(-1).snapshot.current).toBeNull()
  })
})
