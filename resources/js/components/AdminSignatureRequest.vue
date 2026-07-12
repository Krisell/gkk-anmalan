<template>
  <div class="container mx-auto max-w-4xl px-4">
    <h1 class="text-2xl font-semibold mb-6">
      <a
        href="/admin/signature-requests"
        class="inline-flex items-center gap-2 text-gray-400 hover:text-gkk transition-colors group"
      >
        <i class="fa fa-angle-left"></i>
        <span class="underline underline-offset-4 decoration-gray-300 group-hover:decoration-gkk">Signering</span>
      </a>
      <span class="text-gray-300 mx-2">/</span>
      <span class="text-gkk">{{ request.name }}</span>
    </h1>

    <!-- Signers -->
    <div class="bg-white shadow-sm sm:rounded-lg p-4 mb-6">
      <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Signerare</h2>

      <table v-if="request.sent_at" class="min-w-full">
        <tbody>
          <tr v-for="signer in request.signers" :key="signer.id" class="border-b border-gray-100 last:border-0">
            <td class="py-2 text-sm text-gray-900">
              <span
                class="inline-block w-3 h-3 rounded-full mr-2"
                :style="{ backgroundColor: signerColor(signer.user_id) }"
              ></span>
              {{ signerName(signer.user_id) }}
            </td>
            <td class="py-2 text-sm">
              <span v-if="signer.signed_at" class="text-green-700"
                ><i class="fa fa-check-circle mr-1"></i>Signerade {{ formatDate(signer.signed_at) }}</span
              >
              <span v-else class="text-gray-500">Väntar på signatur</span>
            </td>
            <td class="py-2 text-right">
              <Button
                v-if="!signer.signed_at"
                type="secondary"
                v-tooltip="'Kopiera personlig signeringslänk att dela via t.ex. Discord'"
                @click="copyLink(signer)"
              >
                <i class="fa fa-link mr-2"></i>Kopiera länk
              </Button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-else>
        <div v-for="userId in signerUserIds" :key="userId" class="flex items-center gap-2 py-1 text-sm">
          <span class="inline-block w-3 h-3 rounded-full" :style="{ backgroundColor: signerColor(userId) }"></span>
          <span class="grow">{{ signerName(userId) }}</span>
          <button class="text-gray-400 hover:text-red-600" @click="removeSigner(userId)">
            <i class="fa fa-times"></i>
          </button>
        </div>

        <div class="flex gap-2 mt-3">
          <select
            v-model="selectedUserId"
            class="form-select block w-64 sm:text-sm sm:leading-5 border-gray-300 rounded-md p-2 border"
          >
            <option :value="null" disabled>Välj medlem...</option>
            <option v-for="user in availableUsers" :key="user.id" :value="user.id">
              {{ user.first_name }} {{ user.last_name }}
            </option>
          </select>
          <Button type="secondary" @click="addSigner"><i class="fa fa-plus mr-2"></i>Lägg till</Button>
        </div>
      </div>
    </div>

    <!-- Placement instructions / actions -->
    <div v-if="!request.sent_at" class="bg-white shadow-sm sm:rounded-lg p-4 mb-6">
      <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Placera signaturfält</h2>
      <div v-if="signerUserIds.length === 0" class="text-sm text-gray-500">Lägg till minst en signerare först.</div>
      <div v-else>
        <div class="text-sm text-gray-600 mb-2">
          Välj en signerare och klicka i dokumentet för att placera ett fält. Dra för att flytta, dra i hörnet för att
          ändra storlek.
        </div>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="userId in signerUserIds"
            :key="userId"
            class="px-3 py-1 rounded-full text-sm border"
            :style="
              activeUserId === userId
                ? { backgroundColor: signerColor(userId), color: 'white', borderColor: signerColor(userId) }
                : { borderColor: signerColor(userId), color: signerColor(userId) }
            "
            @click="activeUserId = activeUserId === userId ? null : userId"
          >
            {{ signerName(userId) }}
          </button>
        </div>
      </div>

      <div class="flex gap-2 justify-end mt-4">
        <Button type="secondary" @click="save" :disabled="saving">{{ saving ? 'Sparar...' : 'Spara utkast' }}</Button>
        <Button @click="confirmSend" :disabled="saving || signerUserIds.length === 0 || cssFields.length === 0">
          <i class="fa fa-unlock-alt mr-2"></i>Aktivera signering
        </Button>
      </div>
    </div>

    <!-- Completed actions -->
    <div v-if="request.sent_at" class="bg-white shadow-sm sm:rounded-lg p-4 mb-6">
      <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Signerat dokument</h2>

      <div v-if="!allSigned" class="text-sm text-gray-500">
        När alla har signerat kan du ladda ner dokumentet med alla signaturer på plats.
      </div>

      <div v-else class="flex flex-wrap gap-2 items-center">
        <Button @click="composeAndDownload" :disabled="composing">
          <i class="fa fa-download mr-2"></i>{{ composing ? 'Skapar PDF...' : 'Ladda ner signerad PDF' }}
        </Button>

        <template v-if="request.completed_pdf_url">
          <Button type="secondary" @click="loadURL(request.completed_pdf_url, true)">Öppna sparad PDF</Button>
          <select
            v-model="selectedFolderId"
            class="form-select block w-48 sm:text-sm sm:leading-5 border-gray-300 rounded-md p-2 border"
          >
            <option :value="null" disabled>Välj mapp...</option>
            <option v-for="folder in folders" :key="folder.id" :value="folder.id">{{ folder.name }}</option>
          </select>
          <Button type="secondary" @click="addToDocuments" :disabled="!selectedFolderId"
            >Lägg till i medlemsdokument</Button
          >
        </template>
      </div>
    </div>

    <!-- Document -->
    <PdfViewer ref="viewer" :url="request.pdf_url" @loaded="onViewerLoaded">
      <template #overlay="{ pageIndex }">
        <div
          class="absolute inset-0"
          :class="{ 'cursor-crosshair': !request.sent_at && activeUserId }"
          @pointerdown.self="placeField(pageIndex, $event)"
        >
          <div
            v-for="field in fieldsOnPage(pageIndex)"
            :key="field.key"
            class="absolute border-2 rounded-sm flex items-center justify-center overflow-hidden select-none"
            :class="{ 'cursor-move': !request.sent_at }"
            :style="{
              left: field.left + 'px',
              top: field.top + 'px',
              width: field.width + 'px',
              height: field.height + 'px',
              borderColor: signerColor(field.user_id),
              backgroundColor: signerColor(field.user_id) + '18',
            }"
            @pointerdown.stop="startDrag(field, 'move', $event)"
          >
            <img
              v-if="signatureFor(field.user_id)"
              :src="signatureFor(field.user_id)"
              class="max-w-full max-h-full pointer-events-none"
            />
            <span
              v-else
              class="text-xs pointer-events-none px-1 truncate"
              :style="{ color: signerColor(field.user_id) }"
            >
              {{ signerName(field.user_id) }}
            </span>

            <template v-if="!request.sent_at">
              <button
                class="absolute top-0 right-0 w-5 h-5 text-xs flex items-center justify-center text-white rounded-bl"
                :style="{ backgroundColor: signerColor(field.user_id) }"
                @click.stop="removeField(field)"
                @pointerdown.stop
              >
                <i class="fa fa-times"></i>
              </button>
              <div
                class="absolute bottom-0 right-0 w-4 h-4 cursor-nwse-resize"
                :style="{ backgroundColor: signerColor(field.user_id) }"
                @pointerdown.stop="startDrag(field, 'resize', $event)"
              ></div>
            </template>
          </div>
        </div>
      </template>
    </PdfViewer>

    <Modal ref="sendModal" title="Aktivera signering?">
      <div class="text-sm text-gray-600 text-center">
        Varje signerare får en personlig länk som du kopierar och delar hur du vill (t.ex. Discord). Efter aktiveringen
        kan dokumentet och fälten inte ändras.
      </div>
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button type="secondary" @click="close">Avbryt</Button>
          <Button type="danger" @click="send" :disabled="sending">{{ sending ? 'Aktiverar...' : 'Aktivera' }}</Button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script>
import { PDFDocument } from 'pdf-lib'
import FirebaseFileUpload from '../modules/FirebaseFileUpload.js'
import Button from './ui/Button.vue'
import Modal from './ui/Modal.vue'
import PdfViewer from './ui/PdfViewer.vue'

const SIGNER_COLORS = ['#1d4ed8', '#b91c1c', '#047857', '#7c3aed', '#b45309', '#0e7490', '#be185d', '#4d7c0f']

export default {
  components: { Button, Modal, PdfViewer },
  props: ['initialRequest', 'signUrls', 'users', 'folders', 'jwt'],
  data() {
    return {
      request: JSON.parse(JSON.stringify(this.initialRequest)),
      signerUserIds: this.initialRequest.signers.map((signer) => signer.user_id),
      selectedUserId: null,
      activeUserId: null,
      cssFields: [],
      nextFieldKey: 1,
      viewerLoaded: false,
      saving: false,
      sending: false,
      composing: false,
      selectedFolderId: null,
      dragState: null,
    }
  },
  computed: {
    availableUsers() {
      return this.users.filter((user) => !this.signerUserIds.includes(user.id))
    },
    allSigned() {
      return this.request.signers.length > 0 && this.request.signers.every((signer) => signer.signed_at)
    },
  },
  mounted() {
    window.addEventListener('pointermove', this.onDragMove)
    window.addEventListener('pointerup', this.onDragEnd)
  },
  beforeUnmount() {
    window.removeEventListener('pointermove', this.onDragMove)
    window.removeEventListener('pointerup', this.onDragEnd)
  },
  methods: {
    signerName(userId) {
      const user = this.users.find((user) => user.id === userId)
      return user ? `${user.first_name} ${user.last_name}` : 'Okänd'
    },
    signerColor(userId) {
      const index = this.signerUserIds.indexOf(userId)
      return SIGNER_COLORS[index === -1 ? 0 : index % SIGNER_COLORS.length]
    },
    signatureFor(userId) {
      return this.request.signers.find((signer) => signer.user_id === userId)?.signature_png
    },
    formatDate(timestamp) {
      return timestamp.substring(0, 10)
    },
    onViewerLoaded() {
      this.viewerLoaded = true

      const signersById = Object.fromEntries(this.request.signers.map((signer) => [signer.id, signer]))

      this.cssFields = this.request.fields.map((field) => ({
        key: this.nextFieldKey++,
        user_id: signersById[field.signature_request_signer_id].user_id,
        pageIndex: field.page_index,
        ...this.$refs.viewer.pdfRectToCss(field.page_index, field),
      }))
    },
    fieldsOnPage(pageIndex) {
      return this.cssFields.filter((field) => field.pageIndex === pageIndex)
    },
    addSigner() {
      if (this.selectedUserId) {
        this.signerUserIds.push(this.selectedUserId)
        this.activeUserId = this.selectedUserId
        this.selectedUserId = null
      }
    },
    removeSigner(userId) {
      this.signerUserIds = this.signerUserIds.filter((id) => id !== userId)
      this.cssFields = this.cssFields.filter((field) => field.user_id !== userId)
      if (this.activeUserId === userId) {
        this.activeUserId = null
      }
    },
    placeField(pageIndex, event) {
      if (this.request.sent_at || !this.activeUserId || !this.viewerLoaded) {
        return
      }

      const overlay = event.currentTarget
      const width = 150
      const height = 50

      this.cssFields.push({
        key: this.nextFieldKey++,
        user_id: this.activeUserId,
        pageIndex,
        left: Math.min(Math.max(event.offsetX - width / 2, 0), overlay.clientWidth - width),
        top: Math.min(Math.max(event.offsetY - height / 2, 0), overlay.clientHeight - height),
        width,
        height,
      })
    },
    removeField(field) {
      this.cssFields = this.cssFields.filter((existing) => existing.key !== field.key)
    },
    startDrag(field, mode, event) {
      if (this.request.sent_at) {
        return
      }

      const overlay = event.currentTarget.closest('.absolute.inset-0')

      this.dragState = {
        field,
        mode,
        startX: event.clientX,
        startY: event.clientY,
        origLeft: field.left,
        origTop: field.top,
        origWidth: field.width,
        origHeight: field.height,
        maxWidth: overlay.clientWidth,
        maxHeight: overlay.clientHeight,
      }
    },
    onDragMove(event) {
      if (!this.dragState) {
        return
      }

      const { field, mode, startX, startY, origLeft, origTop, origWidth, origHeight, maxWidth, maxHeight } =
        this.dragState
      const deltaX = event.clientX - startX
      const deltaY = event.clientY - startY

      if (mode === 'move') {
        field.left = Math.min(Math.max(origLeft + deltaX, 0), maxWidth - field.width)
        field.top = Math.min(Math.max(origTop + deltaY, 0), maxHeight - field.height)
      } else {
        field.width = Math.min(Math.max(origWidth + deltaX, 40), maxWidth - field.left)
        field.height = Math.min(Math.max(origHeight + deltaY, 20), maxHeight - field.top)
      }
    },
    onDragEnd() {
      this.dragState = null
    },
    fieldsForSave() {
      return this.cssFields.map((field) => ({
        user_id: field.user_id,
        page_index: field.pageIndex,
        ...this.$refs.viewer.cssRectToPdf(field.pageIndex, field),
      }))
    },
    async save() {
      if (this.signerUserIds.length === 0) {
        this.$toast.warning('Lägg till minst en signerare.')
        return
      }

      this.saving = true

      try {
        const { data } = await window.axios.post(`/admin/signature-requests/${this.request.id}`, {
          name: this.request.name,
          signers: this.signerUserIds,
          fields: this.fieldsForSave(),
        })
        this.request.signers = data.signers
        this.request.fields = data.fields
        this.$toast.success('Sparat')
      } finally {
        this.saving = false
      }
    },
    confirmSend() {
      const missingFields = this.signerUserIds.filter(
        (userId) => !this.cssFields.some((field) => field.user_id === userId),
      )

      if (missingFields.length > 0) {
        this.$toast.warning(`Signaturfält saknas för: ${missingFields.map((id) => this.signerName(id)).join(', ')}`)
        return
      }

      this.$refs.sendModal.show()
    },
    async send() {
      this.sending = true

      try {
        await this.save()
        await window.axios.post(`/admin/signature-requests/${this.request.id}/activate`)
        window.location.reload()
      } finally {
        this.sending = false
      }
    },
    async copyLink(signer) {
      await navigator.clipboard.writeText(this.signUrls[signer.id])
      this.$toast.success(`Länk för ${this.signerName(signer.user_id)} kopierad. Giltig i 14 dagar.`)
    },
    async composeAndDownload() {
      this.composing = true

      try {
        const response = await fetch(this.request.pdf_url)
        const pdfDoc = await PDFDocument.load(await response.arrayBuffer())

        const signersById = Object.fromEntries(this.request.signers.map((signer) => [signer.id, signer]))
        const embeddedBySignerId = {}
        let rotationWarning = false

        for (const field of this.request.fields) {
          const signer = signersById[field.signature_request_signer_id]

          if (!signer?.signature_png) {
            continue
          }

          embeddedBySignerId[signer.id] ??= await pdfDoc.embedPng(signer.signature_png)
          const png = embeddedBySignerId[signer.id]
          const page = pdfDoc.getPage(field.page_index)

          if (page.getRotation().angle !== 0) {
            rotationWarning = true
          }

          const fieldWidth = +field.width
          const fieldHeight = +field.height
          const scale = Math.min(fieldWidth / png.width, fieldHeight / png.height)
          const width = png.width * scale
          const height = png.height * scale

          page.drawImage(png, {
            x: +field.x + (fieldWidth - width) / 2,
            y: +field.y + (fieldHeight - height) / 2,
            width,
            height,
          })
        }

        if (rotationWarning) {
          this.$toast.warning('Dokumentet innehåller roterade sidor – kontrollera att signaturerna hamnade rätt.')
        }

        const bytes = await pdfDoc.save()
        const blob = new Blob([bytes], { type: 'application/pdf' })

        const link = document.createElement('a')
        link.href = URL.createObjectURL(blob)
        link.download = `${this.request.name} (signerad).pdf`
        link.click()

        if (!this.request.completed_pdf_url) {
          const url = await FirebaseFileUpload.upload(this.jwt, blob, 'pdf')
          await window.axios.post(`/admin/signature-requests/${this.request.id}/complete`, { completed_pdf_url: url })
          this.request.completed_pdf_url = url
        }
      } finally {
        this.composing = false
      }
    },
    async addToDocuments() {
      await window.axios.post('/admin/documents', {
        name: `${this.request.name} (signerad)`,
        url: this.request.completed_pdf_url,
        document_folder_id: this.selectedFolderId,
      })
      this.$toast.success('Dokumentet har lagts till i medlemsdokumenten')
    },
    loadURL(url, newTab = false) {
      if (newTab) {
        return window.open(url)
      }
      window.location = url
    },
  },
}
</script>
