<template>
  <div>
    <div class="flex gap-2 mb-3">
      <button
        type="button"
        class="px-4 py-2 rounded-md text-sm border"
        :class="mode === 'drawn' ? 'bg-gkk text-white border-gkk' : 'bg-white text-gray-700 border-gray-300'"
        @click="setMode('drawn')"
      >
        <i class="fa fa-pencil mr-2"></i>Rita
      </button>
      <button
        type="button"
        class="px-4 py-2 rounded-md text-sm border"
        :class="mode === 'typed' ? 'bg-gkk text-white border-gkk' : 'bg-white text-gray-700 border-gray-300'"
        @click="setMode('typed')"
      >
        <i class="fa fa-keyboard-o mr-2"></i>Skriv
      </button>
    </div>

    <div v-show="mode === 'drawn'">
      <div class="border border-gray-300 rounded-md bg-white relative" style="touch-action: none">
        <canvas ref="canvas" class="w-full block rounded-md" style="height: 200px"></canvas>
        <button
          type="button"
          class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 text-sm"
          @click="clear"
        >
          <i class="fa fa-eraser mr-1"></i>Rensa
        </button>
      </div>
      <div class="text-sm text-gray-500 mt-1">Rita din signatur i rutan ovan.</div>
    </div>

    <div v-show="mode === 'typed'">
      <input
        v-model="typedName"
        type="text"
        placeholder="Skriv ditt namn"
        class="form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md p-2 border"
      />
      <div
        class="border border-gray-300 rounded-md bg-white mt-2 flex items-center justify-center overflow-hidden"
        style="height: 100px"
      >
        <span v-if="typedName" :style="{ fontFamily: `'Dancing Script', cursive`, fontSize: '40px', color: '#1e3a5f' }">
          {{ typedName }}
        </span>
        <span v-else class="text-gray-400 text-sm">Förhandsvisning</span>
      </div>
    </div>
  </div>
</template>

<script>
import SignaturePad from 'signature_pad'
import SignatureImage from '../../modules/SignatureImage.js'

export default {
  props: ['initialName'],
  data() {
    return {
      mode: 'drawn',
      typedName: this.initialName ?? '',
      pad: null,
    }
  },
  mounted() {
    const canvas = this.$refs.canvas
    const dpr = window.devicePixelRatio || 1

    canvas.width = canvas.offsetWidth * dpr
    canvas.height = canvas.offsetHeight * dpr
    canvas.getContext('2d').scale(dpr, dpr)

    this.pad = new SignaturePad(canvas, { penColor: '#1e3a5f' })
  },
  methods: {
    setMode(mode) {
      this.mode = mode
    },
    clear() {
      this.pad.clear()
    },
    async getSignature() {
      if (this.mode === 'typed') {
        const dataUrl = await SignatureImage.renderTypedSignature(this.typedName)
        return dataUrl ? { dataUrl, type: 'typed' } : null
      }

      if (this.pad.isEmpty()) {
        return null
      }

      const dataUrl = SignatureImage.trimCanvas(this.$refs.canvas)
      return dataUrl ? { dataUrl, type: 'drawn' } : null
    },
  },
}
</script>
