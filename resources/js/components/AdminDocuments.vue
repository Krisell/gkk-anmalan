<template>
  <div class="container mx-auto max-w-3xl">
    <h1 class="text-center text-3xl font-hairline mb-6">Dokument</h1>

    <div class="flex flex-col">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                >
                  Namn
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                ></th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                ></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr class="event-row" v-for="document in documents" :key="document.id">
                <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm leading-5 font-medium text-gray-900">
                        <i class="fa fa-file-o mr-4"></i>{{ document.name }}
                      </div>
                    </div>
                  </div>
                </td>

                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 flex justify-end">
                  <ui-button prevent @click="show(document)">Visa</ui-button>
                </td>

                <td class="whitespace-no-wrap border-b border-gray-200" style="width: 80px">
                  <div class="flex">
                    <svg
                      v-tooltip="'Redigera namn'"
                      class="w-6 text-gkk-light hover:text-gkk cursor-pointer"
                      @click="confirmEdit(document)"
                      fill="none"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                      ></path>
                      <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <svg
                      v-tooltip="'Radera dokument'"
                      class="w-6 ml-2 text-gkk-light hover:text-gkk cursor-pointer"
                      @click="confirmDelete(document)"
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
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="flex justify-center mt-6">
      <ui-button prevent @click="$modal.show('add-document')">Lägg till nytt dokument</ui-button>
    </div>

    <GkkLink class="mt-16" to="/" text="Tillbaka till startsidan" />

    <modal name="delete-document" :adaptive="true" height="auto">
      <div style="padding: 30px; margin-top: 20px">
        <h3 style="text-align: center">
          Är du säker på att du vill radera {{ selectedDocument && selectedDocument.name }}?
        </h3>
      </div>

      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px">
        <el-button secondary @click="$modal.hide('delete-document')">Nej</el-button>
        <el-button style="margin-left: 10px" danger primary @click="deleteDocument">Radera</el-button>
      </div>
    </modal>

    <modal name="edit-document" :adaptive="true" height="auto">
      <div style="padding: 30px; margin-top: 20px" v-if="selectedDocument">
        <h3 style="text-align: center">Redigera dokumentets namn</h3>
        <div class="mt-1 relative rounded-md shadow-sm">
          <input v-model="selectedDocument.name" class="form-input block w-full sm:text-sm sm:leading-5" />
        </div>
      </div>

      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px">
        <el-button secondary @click="$modal.hide('edit-document')">Stäng</el-button>
        <el-button style="margin-left: 10px" danger primary @click="editDocument">Uppdatera namn</el-button>
      </div>
    </modal>

    <modal name="add-document" :adaptive="true" height="auto">
      <div style="padding: 30px; margin-top: 20px">
        <div class="flex mb-6 items-center justify-center bg-grey-lighter">
          <label
            v-if="uploadStatus === ''"
            class="w-64 flex flex-col items-center px-4 py-6 bg-white text-blue rounded-lg shadow-lg tracking-wide border border-blue cursor-pointer"
          >
            <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
              <path
                d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z"
              />
            </svg>
            <span v-if="newDocument.url.length > 0" class="mt-2 text-base leading-normal">Välj annan fil</span>
            <span v-else class="mt-2 text-base leading-normal">Välj fil</span>
            <input @change="upload" ref="fileUpload" type="file" class="hidden" />
          </label>
          <p v-else>Laddar upp ...</p>
        </div>
        <h3 style="text-align: center">Ange dokumentets namn</h3>
        <div class="mt-1 relative rounded-md shadow-sm">
          <input v-model="newDocument.name" class="form-input block w-full sm:text-sm sm:leading-5" />
        </div>
      </div>

      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px">
        <el-button secondary @click="$modal.hide('edit-document')">Stäng</el-button>
        <el-button style="margin-left: 10px" danger primary @click="addDocument">Lägg till dokument</el-button>
      </div>
    </modal>
  </div>
</template>

<script>
import FirebaseFileUpload from '../modules/FirebaseFileUpload.js'

export default {
  props: ['documents', 'jwt'],
  data() {
    return {
      uploadStatus: '',
      newDocument: {
        name: '',
        url: '',
      },
      selectedDocument: null,
    }
  },
  methods: {
    addDocument() {
      if (this.newDocument.name.length === 0 || this.newDocument.url.length === 0) {
        return
      }

      window.axios
        .post(`/admin/documents`, {
          name: this.newDocument.name,
          url: this.newDocument.url,
        })
        .then(() => window.location.reload())
    },
    show(document) {
      window.open(document.url)
    },
    confirmDelete(document) {
      this.selectedDocument = JSON.parse(JSON.stringify(document))
      this.$modal.show('delete-document')
    },
    deleteDocument() {
      window.axios.delete(`/admin/documents/${this.selectedDocument.id}`).then(() => window.location.reload())
    },
    confirmEdit(document) {
      this.selectedDocument = JSON.parse(JSON.stringify(document))
      this.$modal.show('edit-document')
    },
    editDocument() {
      window.axios
        .post(`/admin/documents/${this.selectedDocument.id}`, this.selectedDocument)
        .then(() => window.location.reload())
    },
    async upload() {
      this.uploadStatus = 'pending'
      const file = this.$refs.fileUpload.files[0]

      if (!file) {
        return
      }

      this.newDocument.name = file.name
      const extension = file.name.split('.')[file.name.split('.').length - 1]

      let url = await FirebaseFileUpload.upload(this.jwt, file, extension)
      this.uploadStatus = ''
      this.newDocument.url = url
    },
  },
}
</script>
