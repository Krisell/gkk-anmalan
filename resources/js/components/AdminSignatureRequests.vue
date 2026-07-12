<template>
  <div class="container mx-auto max-w-3xl">
    <h1 class="text-2xl font-semibold mb-6">
      <a href="/insidan" class="inline-flex items-center gap-2 text-gray-400 hover:text-gkk transition-colors group">
        <i class="fa fa-angle-left"></i>
        <span class="underline underline-offset-4 decoration-gray-300 group-hover:decoration-gkk">Start</span>
      </a>
      <span class="text-gray-300 mx-2">/</span>
      <span class="text-gkk">Admin - Signering</span>
    </h1>

    <div class="py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
      <div class="align-middle inline-block min-w-full shadow-sm overflow-hidden sm:rounded-lg border-b border-gray-200">
        <table class="min-w-full">
          <thead>
            <tr>
              <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                Dokument
              </th>
              <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"></th>
              <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
            </tr>
          </thead>
          <tbody class="bg-white">
            <tr v-if="requests.length === 0">
              <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500">
                Inga signaturförfrågningar ännu.
              </td>
            </tr>
            <tr v-for="request in requests" :key="request.id">
              <td class="px-6 py-3 whitespace-no-wrap border-b border-gray-200">
                <div class="text-sm leading-5 font-medium text-gray-900">
                  <i class="fa fa-file-text-o mr-3"></i>{{ request.name }}
                </div>
                <div class="text-xs text-gray-500 mt-1">Skapad {{ request.created_at.substring(0, 10) }}</div>
              </td>
              <td class="px-6 py-3 whitespace-no-wrap border-b border-gray-200 text-sm">
                <span v-if="!request.sent_at" class="text-gray-500"><i class="fa fa-pencil mr-1"></i>Utkast</span>
                <span v-else-if="signedCount(request) === request.signers.length" class="text-green-700">
                  <i class="fa fa-check-circle mr-1"></i>Alla har signerat
                </span>
                <span v-else class="text-amber-600">
                  <i class="fa fa-clock-o mr-1"></i>{{ signedCount(request) }} av {{ request.signers.length }} har signerat
                </span>
              </td>
              <td class="px-6 py-3 whitespace-no-wrap border-b border-gray-200">
                <div class="flex justify-end">
                  <Button @click="location(`/admin/signature-requests/${request.id}`)">Öppna</Button>
                </div>
              </td>
              <td class="px-6 whitespace-no-wrap border-b border-gray-200" style="width: 50px">
                <svg
                  v-tooltip="'Radera'"
                  class="w-6 text-gkk-light hover:text-gkk cursor-pointer"
                  @click="confirmDelete(request)"
                  fill="none"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                  ></path>
                </svg>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="flex justify-center mt-6">
      <Button @click="$refs.createModal.show()"><i class="fa fa-plus mr-2"></i>Ny signaturförfrågan</Button>
    </div>

    <Modal ref="createModal" title="Ny signaturförfrågan">
      <div class="flex mb-6 items-center justify-center bg-grey-lighter">
        <label
          class="w-64 flex flex-col items-center px-4 py-6 bg-white text-blue rounded-lg shadow-lg tracking-wide border border-blue cursor-pointer"
        >
          <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path
              d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z"
            />
          </svg>
          <div v-if="uploadStatus === 'pending'">Laddar upp ...</div>
          <div v-else>
            <span v-if="newRequest.pdf_url.length > 0" class="mt-2 text-base leading-normal">Välj annan PDF</span>
            <span v-else class="mt-2 text-base leading-normal">Ladda upp PDF</span>
          </div>
          <input @change="upload" ref="fileUpload" type="file" accept="application/pdf" class="hidden" />
        </label>
      </div>
      <div class="mt-1 relative rounded-md shadow-xs">
        <input
          type="text"
          v-model="newRequest.name"
          placeholder="Namn, t.ex. Årsmötesprotokoll 2026"
          class="form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md p-2 border"
        />
      </div>

      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button type="secondary" @click="close">Stäng</Button>
          <Button type="danger" @click="create">Skapa</Button>
        </div>
      </template>
    </Modal>

    <Modal ref="deleteModal" :title="`Är du säker på att du vill radera ${selectedRequest && selectedRequest.name}?`">
      <div class="text-sm text-gray-600 text-center">Alla signaturer och utskickade länkar slutar fungera.</div>
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button type="secondary" @click="close">Nej</Button>
          <Button type="danger" @click="deleteRequest">Radera</Button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script>
import FirebaseFileUpload from '../modules/FirebaseFileUpload.js'
import Button from './ui/Button.vue'
import Modal from './ui/Modal.vue'

export default {
  components: { Button, Modal },
  props: ['jwt', 'requests', 'users'],
  data() {
    return {
      uploadStatus: '',
      newRequest: {
        name: '',
        pdf_url: '',
      },
      selectedRequest: null,
    }
  },
  methods: {
    signedCount(request) {
      return request.signers.filter((signer) => signer.signed_at).length
    },
    async upload() {
      const file = this.$refs.fileUpload.files[0]

      if (!file) {
        return
      }

      this.uploadStatus = 'pending'
      this.newRequest.pdf_url = await FirebaseFileUpload.upload(this.jwt, file, 'pdf')
      this.uploadStatus = ''

      if (this.newRequest.name.length === 0) {
        this.newRequest.name = file.name.replace(/\.pdf$/i, '')
      }
    },
    async create() {
      if (this.newRequest.name.length === 0 || this.newRequest.pdf_url.length === 0) {
        return
      }

      const { data } = await window.axios.post('/admin/signature-requests', this.newRequest)
      window.location = `/admin/signature-requests/${data.id}`
    },
    confirmDelete(request) {
      this.selectedRequest = request
      this.$refs.deleteModal.show()
    },
    deleteRequest() {
      window.axios.delete(`/admin/signature-requests/${this.selectedRequest.id}`).then(() => window.location.reload())
    },
  },
}
</script>
