// Renders the OBS stinger (the scene transition in Stinger.vue) to a WebM video with transparency.
//
// Usage (with the site running and the frontend built):
//   node resources/js/live/tools/render-stinger.mjs https://gkk.test/live/overlay?view=stinger
//
// Each frame is captured with a transparent background by pausing the CSS animations and stepping them, then the
// frames are encoded with ffmpeg as VP9 with alpha, which OBS's Stinger transition supports.
import { chromium } from '@playwright/test'
import { execFileSync } from 'node:child_process'
import { mkdtempSync, rmSync } from 'node:fs'
import { tmpdir } from 'node:os'
import { join } from 'node:path'

const [url, output = 'resources/js/live/obs/gkk-stinger.webm'] = process.argv.slice(2)
if (!url) {
  console.error(
    'Usage: node resources/js/live/tools/render-stinger.mjs <url of /live/overlay?view=stinger> [output.webm]',
  )
  process.exit(1)
}

// The animation is 1.2 s; a little extra makes sure the last frame is fully transparent.
const FPS = 60
const DURATION_MS = 1300

const browser = await chromium.launch()
const page = await browser.newPage({ viewport: { width: 1920, height: 1080 }, ignoreHTTPSErrors: true })
await page.goto(url)
await page.waitForSelector('.stinger')
await page.evaluate(() => document.fonts.ready)
await page.waitForFunction(() => [...document.images].every((image) => image.complete))
// Only the overlay may end up in the video, not e.g. a local dev server's badge (Laravel Herd injects one, and
// may do so late or outside <body>), so everything else is hidden.
await page.addStyleTag({
  content: `
    body > *:not(#live-overlay),
    html > *:not(head):not(body) {
      display: none !important;
    }
  `,
})
await page.evaluate(() => document.getAnimations().forEach((animation) => animation.pause()))

const frames = mkdtempSync(join(tmpdir(), 'stinger-'))
try {
  for (let index = 0; (index * 1000) / FPS <= DURATION_MS; index++) {
    const time = (index * 1000) / FPS
    await page.evaluate((t) => document.getAnimations().forEach((animation) => (animation.currentTime = t)), time)
    await page.screenshot({ path: join(frames, `frame-${String(index).padStart(4, '0')}.png`), omitBackground: true })
  }
  await browser.close()

  execFileSync('ffmpeg', [
    '-y',
    '-loglevel',
    'error',
    '-framerate',
    String(FPS),
    '-i',
    join(frames, 'frame-%04d.png'),
    '-c:v',
    'libvpx-vp9',
    '-pix_fmt',
    'yuva420p',
    '-b:v',
    '0',
    '-crf',
    '30',
    '-auto-alt-ref',
    '0',
    output,
  ])
  console.log(`Wrote ${output}`)
} finally {
  rmSync(frames, { recursive: true, force: true })
}
