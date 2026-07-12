<template>
  <div class="container mx-auto max-w-4xl px-4">
    <div v-if="done" class="bg-white shadow-sm sm:rounded-lg p-8 text-center max-w-lg mx-auto">
      <i class="fa fa-check-circle text-green-600 text-5xl mb-4"></i>
      <h1 class="text-2xl font-semibold mb-2">Tack, {{ signer.name }}!</h1>
      <p class="text-gray-600">Din signatur av <strong>{{ documentName }}</strong> har sparats.</p>
    </div>

    <template v-else>
      <h1 class="text-2xl font-semibold mb-2 text-gkk">Signera: {{ documentName }}</h1>
      <p class="text-gray-600 mb-6">
        Hej {{ signer.name }}! Läs igenom dokumentet nedan. Dina signaturfält är markerade.
        Skapa din signatur längst ner på sidan och klicka sedan på Signera.
      </p>

      <PdfViewer ref="viewer" :url="pdfUrl" @loaded="onViewerLoaded">
        <template #overlay="{ pageIndex }">
          <div class="absolute inset-0">
            <div
              v-for="(field, index) in fieldsOnPage(pageIndex)"
              :key="index"
              class="absolute border-2 border-gkk rounded-sm bg-gkk/10 flex items-center justify-center overflow-hidden"
              :style="{
                left: field.left + 'px',
                top: field.top + 'px',
                width: field.width + 'px',
                height: field.height + 'px',
              }"
            >
              <img v-if="preview" :src="preview" class="max-w-full max-h-full" />
              <span v-else class="text-xs text-gkk px-1">Din signatur</span>
            </div>
          </div>
        </template>
      </PdfViewer>

      <div class="bg-white shadow-sm sm:rounded-lg p-4 mt-6 max-w-xl mx-auto">
        <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Din signatur</h2>

        <SignaturePadInput ref="signatureInput" :initial-name="signer.name" />

        <div v-if="error" class="text-sm text-red-600 mt-3">{{ error }}</div>

        <div class="flex justify-center mt-4">
          <Button @click="sign" :disabled="signing">
            <i class="fa fa-pencil-square-o mr-2"></i>{{ signing ? 'Signerar...' : 'Signera dokumentet' }}
          </Button>
        </div>
        <div class="text-xs text-gray-400 text-center mt-3">
          Genom att klicka på Signera godkänner du att din signatur infogas i dokumentet.
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import Button from './ui/Button.vue'
import PdfViewer from './ui/PdfViewer.vue'
import SignaturePadInput from './ui/SignaturePadInput.vue'

export default {
  components: { Button, PdfViewer, SignaturePadInput },
  props: ['signer', 'documentName', 'pdfUrl', 'fields', 'postUrl'],
  data() {
    return {
      done: !!this.signer.signed_at,
      viewerLoaded: false,
      cssFields: [],
      preview: null,
      signing: false,
      error: null,
    }
  },
  methods: {
    onViewerLoaded() {
      this.viewerLoaded = true
      this.cssFields = this.fields.map((field) => ({
        pageIndex: field.page_index,
        ...this.$refs.viewer.pdfRectToCss(field.page_index, field),
      }))
    },
    fieldsOnPage(pageIndex) {
      return this.cssFields.filter((field) => field.pageIndex === pageIndex)
    },
    async sign() {
      this.error = null

      const signature = await this.$refs.signatureInput.getSignature()

      if (!signature) {
        this.error = 'Rita din signatur eller skriv ditt namn först.'
        return
      }

      this.preview = signature.dataUrl
      this.signing = true

      try {
        await window.axios.post(this.postUrl, {
          signature_png: signature.dataUrl,
          signature_type: signature.type,
        })
        this.done = true
      } catch (exception) {
        this.error = 'Något gick fel. Prova att ladda om sidan – om länken har hunnit gå ut kan styrelsen skicka en ny.'
      } finally {
        this.signing = false
      }
    },
  },
}
</script>
