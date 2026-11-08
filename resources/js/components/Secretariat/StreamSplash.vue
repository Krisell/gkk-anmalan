<template>
  <!-- A full HD splash screen for the competition stream (and its thumbnail on YouTube), in the slideshow's look. It is
       drawn on a canvas, so that it can be saved as a PNG for OBS or YouTube. -->
  <canvas ref="canvas" :width="WIDTH" :height="HEIGHT" class="block h-auto w-full"></canvas>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'

const WIDTH = 1920
const HEIGHT = 1080
const FOOTER = 96

const GKK = '#314270'
const GKK_LIGHT = '#45557e'
const AMBER = '#fcd34d'
const FONT = 'ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif'

// Served by the app itself: a logo from another site would keep the canvas from being saved.
const LOGO_URL = '/img/gkk-logo.png'
let logo
function loadLogo() {
  logo ??= new Promise((resolve) => {
    const image = new Image()
    image.onload = () => resolve(image)
    image.onerror = () => resolve(null)
    image.src = LOGO_URL
  })
  return logo
}

const props = defineProps({
  kicker: { type: String, default: '' },
  title: { type: String, default: '' },
  subText: { type: String, default: '' },
  placeholder: { type: String, default: 'Din text' },
})

const canvas = ref()

// The words of a text in lines no wider than the width.
function wrap(ctx, text, width) {
  const lines = []
  let line = ''
  for (const word of text.split(/\s+/).filter(Boolean)) {
    const next = line ? `${line} ${word}` : word
    if (line && ctx.measureText(next).width > width) {
      lines.push(line)
      line = word
    } else {
      line = next
    }
  }
  if (line) {
    lines.push(line)
  }
  return lines
}

// The same number of lines, as even as can be (like CSS's text-wrap: balance), so no word is left alone on the last.
function balanced(ctx, text, width, lines) {
  let best = lines
  for (let narrower = width - 10; narrower > 0; narrower -= 10) {
    const wrapped = wrap(ctx, text, narrower)
    if (wrapped.length !== lines.length || wrapped.some((line) => ctx.measureText(line).width > narrower)) {
      break
    }
    best = wrapped
  }
  return best
}

// The largest font size (down to the smallest) at which the text fits in at most that many lines.
function fitText(ctx, text, { max, min, weight, width, lines }) {
  for (let size = max; size > min; size -= 4) {
    ctx.font = `${weight} ${size}px ${FONT}`
    const wrapped = wrap(ctx, text, width)
    if (wrapped.length <= lines && wrapped.every((line) => ctx.measureText(line).width <= width)) {
      return { size, lines: balanced(ctx, text, width, wrapped) }
    }
  }
  ctx.font = `${weight} ${min}px ${FONT}`
  return { size: min, lines: wrap(ctx, text, width) }
}

function roundRect(ctx, x, y, w, h, r) {
  ctx.beginPath()
  ctx.roundRect(x, y, w, h, r)
  ctx.fill()
}

function glow(ctx, x, y, radius, color, stop) {
  const gradient = ctx.createRadialGradient(x, y, 0, x, y, radius)
  gradient.addColorStop(0, color)
  gradient.addColorStop(stop, 'rgba(255,255,255,0)')
  ctx.fillStyle = gradient
  ctx.fillRect(0, 0, WIDTH, HEIGHT)
}

// The plate rings of the slideshow.
function rings(ctx, x, y, radius) {
  ctx.save()
  ctx.strokeStyle = 'rgba(49,66,112,0.05)'
  for (const [r, w] of [
    [0.98, 0.02],
    [0.84, 0.01],
    [0.66, 0.01],
    [0.16, 0.07],
  ]) {
    ctx.lineWidth = radius * w
    ctx.beginPath()
    ctx.arc(x, y, radius * r, 0, Math.PI * 2)
    ctx.stroke()
  }
  ctx.restore()
}

async function draw() {
  const image = await loadLogo()
  await document.fonts?.ready
  const ctx = canvas.value?.getContext('2d')
  if (!ctx) {
    return
  }
  ctx.reset?.()
  ctx.textBaseline = 'alphabetic'
  ctx.textAlign = 'center'

  // Background: the slideshow's soft light, a blue glow bottom left and a warm one top right.
  const background = ctx.createLinearGradient(0, 0, WIDTH, HEIGHT)
  background.addColorStop(0, '#f8fafc')
  background.addColorStop(0.5, '#ffffff')
  background.addColorStop(1, '#f1f5f9')
  ctx.fillStyle = background
  ctx.fillRect(0, 0, WIDTH, HEIGHT)
  glow(ctx, 0, HEIGHT, 1500, 'rgba(49,66,112,0.12)', 0.6)
  glow(ctx, WIDTH, 0, 1300, 'rgba(246,201,69,0.26)', 0.55)
  rings(ctx, 0, HEIGHT - FOOTER, 520)
  rings(ctx, WIDTH - 40, 40, 360)

  // Measure the content first, so that it can be centred.
  const center = WIDTH / 2
  const logoRadius = 120
  const pillHeight = 64
  ctx.font = `700 26px ${FONT}`
  ctx.letterSpacing = '5px'
  const kicker = props.kicker.trim().toUpperCase()
  const pillWidth = kicker ? ctx.measureText(kicker).width + 112 : 0
  ctx.letterSpacing = '0px'

  const title = fitText(ctx, props.title.trim() || props.placeholder, {
    max: 150,
    min: 72,
    weight: 800,
    width: 1600,
    lines: 2,
  })
  const titleLine = title.size * 1.02
  ctx.font = `400 46px ${FONT}`
  const sub = props.subText.trim() ? wrap(ctx, props.subText.trim(), 1400).slice(0, 2) : []
  const subLine = 60

  const gaps = { logo: 56, pill: 44, rule: 44, sub: 40 }
  const height =
    logoRadius * 2 +
    gaps.logo +
    (kicker ? pillHeight + gaps.pill : 0) +
    title.lines.length * titleLine +
    gaps.rule +
    12 +
    (sub.length ? gaps.sub + sub.length * subLine : 0)
  let y = (HEIGHT - FOOTER - height) / 2

  // The club's logo, on a white disc.
  ctx.save()
  ctx.shadowColor = 'rgba(49,66,112,0.25)'
  ctx.shadowBlur = 70
  ctx.shadowOffsetY = 24
  ctx.fillStyle = '#ffffff'
  ctx.beginPath()
  ctx.arc(center, y + logoRadius, logoRadius, 0, Math.PI * 2)
  ctx.fill()
  ctx.restore()
  ctx.strokeStyle = 'rgba(49,66,112,0.10)'
  ctx.lineWidth = 2
  ctx.stroke()
  if (image) {
    const size = (logoRadius - 10) * 2
    ctx.drawImage(image, center - size / 2, y + 10, size, size)
  }
  y += logoRadius * 2 + gaps.logo

  // The competition, in a pill with an amber dot.
  if (kicker) {
    ctx.fillStyle = GKK
    roundRect(ctx, center - pillWidth / 2, y, pillWidth, pillHeight, pillHeight / 2)
    ctx.fillStyle = AMBER
    ctx.beginPath()
    ctx.arc(center - pillWidth / 2 + 40, y + pillHeight / 2, 8, 0, Math.PI * 2)
    ctx.fill()
    ctx.fillStyle = '#ffffff'
    ctx.font = `700 26px ${FONT}`
    ctx.letterSpacing = '5px'
    ctx.textAlign = 'left'
    ctx.fillText(kicker, center - pillWidth / 2 + 68, y + pillHeight / 2 + 9)
    ctx.letterSpacing = '0px'
    ctx.textAlign = 'center'
    y += pillHeight + gaps.pill
  }

  // The headline.
  ctx.fillStyle = props.title.trim() ? GKK : 'rgba(49,66,112,0.2)'
  ctx.font = `800 ${title.size}px ${FONT}`
  for (const line of title.lines) {
    y += titleLine
    ctx.fillText(line, center, y - title.size * 0.2)
  }

  // The amber rule.
  y += gaps.rule
  ctx.fillStyle = AMBER
  roundRect(ctx, center - 70, y - 12, 140, 12, 6)

  // The line under it.
  if (sub.length) {
    y += gaps.sub
    ctx.fillStyle = GKK_LIGHT
    ctx.font = `400 46px ${FONT}`
    for (const line of sub) {
      y += subLine
      ctx.fillText(line, center, y - 14)
    }
  }

  // The slideshow's bottom bar.
  ctx.fillStyle = GKK
  ctx.fillRect(0, HEIGHT - FOOTER, WIDTH, FOOTER)
  ctx.fillStyle = 'rgba(255,255,255,0.10)'
  ctx.fillRect(0, HEIGHT - FOOTER, WIDTH, 5)
  ctx.font = `600 34px ${FONT}`
  ctx.fillStyle = '#ffffff'
  ctx.textAlign = 'left'
  ctx.fillText('Göteborg Kraftsportklubb', 64, HEIGHT - FOOTER / 2 + 12)
  ctx.font = `400 30px ${FONT}`
  ctx.fillStyle = 'rgba(255,255,255,0.65)'
  ctx.textAlign = 'right'
  ctx.fillText('goteborgkk.se', WIDTH - 64, HEIGHT - FOOTER / 2 + 11)
}

// Saves the screen as a PNG.
function download(name) {
  canvas.value.toBlob((blob) => {
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `${name}.png`
    link.click()
    setTimeout(() => URL.revokeObjectURL(url), 1000)
  }, 'image/png')
}

onMounted(draw)
watch(() => [props.kicker, props.title, props.subText], draw)

defineExpose({ download })
</script>
