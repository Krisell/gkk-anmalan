<template>
  <div ref="container" class="flex flex-col items-center gap-6">
    <div v-if="loading" class="py-12 text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i>Laddar dokument...</div>
    <div v-if="error" class="py-12 text-red-600">Dokumentet kunde inte laddas.</div>

    <div
      v-for="page in pages"
      :key="page.index"
      class="relative shadow-sm border border-gray-300 bg-white"
      :style="{ width: page.cssWidth + 'px', height: page.cssHeight + 'px' }"
    >
      <canvas :ref="(el) => (canvases[page.index] = el)"></canvas>
      <div class="absolute inset-0">
        <slot name="overlay" :page-index="page.index"></slot>
      </div>
    </div>
  </div>
</template>

<script>
import PdfCoordinates from '../../modules/PdfCoordinates.js'

// pdfjs-dist is heavy — load it lazily so only pages that render PDFs pay for it.
async function loadPdfjs() {
  const [pdfjs, worker] = await Promise.all([import('pdfjs-dist'), import('pdfjs-dist/build/pdf.worker.min.mjs?url')])
  pdfjs.GlobalWorkerOptions.workerSrc = worker.default
  return pdfjs
}

export default {
  props: ['url'],
  emits: ['loaded'],
  data() {
    return {
      loading: true,
      error: false,
      pages: [],
      canvases: {},
      viewports: {},
    }
  },
  async mounted() {
    try {
      const pdfjs = await loadPdfjs()
      const pdf = await pdfjs.getDocument({ url: this.url }).promise
      const containerWidth = Math.min(this.$refs.container.clientWidth, 800)

      const pageData = []
      for (let number = 1; number <= pdf.numPages; number++) {
        const page = await pdf.getPage(number)
        const scale = containerWidth / page.getViewport({ scale: 1 }).width
        const viewport = page.getViewport({ scale })

        this.viewports[number - 1] = viewport
        pageData.push({ page, viewport, index: number - 1 })
        this.pages.push({
          index: number - 1,
          cssWidth: Math.floor(viewport.width),
          cssHeight: Math.floor(viewport.height),
        })
      }

      this.loading = false
      await this.$nextTick()

      // Only the canvas backing store is scaled by devicePixelRatio — all
      // overlay/field math must stay in CSS pixels.
      const dpr = window.devicePixelRatio || 1
      for (const { page, viewport, index } of pageData) {
        const canvas = this.canvases[index]
        canvas.width = Math.floor(viewport.width * dpr)
        canvas.height = Math.floor(viewport.height * dpr)
        canvas.style.width = `${Math.floor(viewport.width)}px`
        canvas.style.height = `${Math.floor(viewport.height)}px`

        await page.render({
          canvasContext: canvas.getContext('2d'),
          viewport,
          transform: dpr !== 1 ? [dpr, 0, 0, dpr, 0, 0] : null,
        }).promise
      }

      this.$emit('loaded')
    } catch (error) {
      this.loading = false
      this.error = true
      this.log(error)
    }
  },
  methods: {
    cssRectToPdf(pageIndex, rect) {
      return PdfCoordinates.cssRectToPdf(this.viewports[pageIndex], rect)
    },
    pdfRectToCss(pageIndex, field) {
      return PdfCoordinates.pdfRectToCss(this.viewports[pageIndex], field)
    },
  },
}
</script>
