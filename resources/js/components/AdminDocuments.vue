<template>
  <div class="container mx-auto max-w-3xl">
    <h1 class="text-2xl font-semibold mb-6">
      <a href="/insidan" class="inline-flex items-center gap-2 text-gray-400 hover:text-gkk transition-colors group">
        <i class="fa fa-angle-left"></i>
        <span class="underline underline-offset-4 decoration-gray-300 group-hover:decoration-gkk">Start</span>
      </a>
      <span class="text-gray-300 mx-2">/</span>
      <span class="text-gkk">Admin - Dokument</span>
    </h1>

    <div class="flex flex-col">
      <div
        v-for="(folder, folderIndex) in sortedFolders"
        :key="folder.id"
        class="py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8"
      >
        <div
          class="align-middle inline-block min-w-full shadow-sm overflow-hidden sm:rounded-lg border-b border-gray-200"
        >
          <table class="min-w-full">
            <thead>
              <tr>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                >
                  {{ folder.name }}
                  <span
                    v-if="folder.only_administrators"
                    class="ml-2 inline-flex items-center rounded-full bg-gkk/10 px-2 py-0.5 text-[10px] font-semibold normal-case tracking-normal text-gkk"
                  >
                    Endast administratörer
                  </span>
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                ></th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                >
                  <div class="flex justify-end">
                    <svg
                      v-if="folderIndex < folders.length - 1"
                      @click="moveDown(folderIndex)"
                      v-tooltip.left="'Flytta ner mapp'"
                      class="w-6 ml-2 text-gkk-light hover:text-gkk cursor-pointer"
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="#000"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <polyline points="6 9 12 15 18 9" />
                    </svg>

                    <svg
                      v-if="folderIndex > 0"
                      @click="moveUp(folderIndex)"
                      v-tooltip.left="'Flytta upp mapp'"
                      class="w-6 ml-2 text-gkk-light hover:text-gkk cursor-pointer"
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="#000"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <polyline points="18 15 12 9 6 15" />
                    </svg>

                    <svg
                      v-tooltip.left="'Redigera mappnamn'"
                      class="w-6 ml-2 text-gkk-light hover:text-gkk cursor-pointer"
                      @click="editFolder(folder)"
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
                      v-tooltip.left="'Radera mapp'"
                      v-if="folder.documents.length === 0"
                      class="w-6 ml-2 text-gkk-light hover:text-gkk cursor-pointer"
                      @click="deleteFolder(folder)"
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
                </th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="document in folder.documents" :key="document.id">
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
                  <Button @click="show(document)">Visa</Button>
                </td>

                <td class="px-6 whitespace-no-wrap border-b border-gray-200" style="width: 80px">
                  <div class="flex justify-end">
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
        <div class="flex justify-center mt-6">
          <Button @click="addDocumentToFolder(folder)"
            ><i class="fa fa-plus mr-2"></i>Nytt dokument i mappen {{ folder.name }}</Button
          >
        </div>
      </div>

      <div class="mt-10 rounded-xl border border-gray-200 bg-gray-50 p-5 sm:p-6">
        <div class="mb-5 text-center">
          <h2 class="text-lg font-semibold text-gray-900">Ny mapp</h2>
          <p class="mt-1 text-sm text-gray-500">Skapa en mapp för att samla relaterade dokument.</p>
        </div>

        <div class="mx-auto max-w-md space-y-4">
          <div>
            <label for="new-folder-name" class="mb-1 block text-sm font-medium text-gray-700">Mappnamn</label>
            <input
              id="new-folder-name"
              v-model="newFolderName"
              name="name"
              placeholder="Till exempel styrelseprotokoll"
              class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-gkk focus:outline-hidden focus:ring-1 focus:ring-gkk sm:text-sm"
            />
          </div>

          <label
            class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white p-3 text-left transition-colors hover:border-gkk/40"
          >
            <input
              v-model="newFolderOnlyAdministrators"
              type="checkbox"
              class="mt-0.5 h-4 w-4 shrink-0 rounded border-gray-300 text-gkk focus:ring-gkk"
            />
            <span>
              <span class="block text-sm font-medium text-gray-800">Endast administratörer</span>
              <span class="block text-xs text-gray-500">Mappen och dess dokument visas inte för medlemmar.</span>
            </span>
          </label>

          <div class="flex justify-center pt-1">
            <Button @click="newFolder"><i class="fa fa-plus mr-2"></i>Skapa ny mapp</Button>
          </div>
        </div>
      </div>
    </div>

    <Modal
      ref="deleteDocumentModal"
      :title="`Är du säker på att du vill radera ${selectedDocument && selectedDocument.name}?`"
    >
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button type="secondary" @click="close">Nej</Button>
          <Button type="danger" @click="deleteDocument">Radera</Button>
        </div>
      </template>
    </Modal>

    <Modal ref="editDocumentModal" title="Redigera dokumentets namn">
      <div v-if="selectedDocument">
        <div class="mt-1 relative rounded-md shadow-xs">
          <input
            type="text"
            v-model="selectedDocument.name"
            class="form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md p-2 border"
          />
        </div>
      </div>

      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button type="secondary" @click="close">Stäng</Button>
          <Button type="danger" @click="editDocument">Uppdatera namn</Button>
        </div>
      </template>
    </Modal>

    <Modal ref="addDocumentModal" title="Lägg till dokument/länk">
      <div class="flex mb-6 items-center justify-center bg-grey-lighter">
        <h1 class="p-10 text-center">Ladda upp en fil, eller ange valfri URL till en fil eller webbsida.</h1>
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
            <span v-if="newDocument.url.length > 0" class="mt-2 text-base leading-normal">Välj annan fil</span>
            <span v-else class="mt-2 text-base leading-normal">Ladda upp fil</span>
          </div>
          <input @change="upload" ref="fileUpload" type="file" class="hidden" />
        </label>
      </div>
      <div class="mt-1 relative rounded-md shadow-xs">
        <input
          type="text"
          v-model="newDocument.name"
          placeholder="Namn"
          class="form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md p-2 border"
        />
      </div>
      <div class="mt-1 relative rounded-md shadow-xs">
        <input
          type="text"
          v-model="newDocument.url"
          placeholder="URL"
          class="form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md p-2 border"
        />
      </div>

      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button type="secondary" @click="close">Stäng</Button>
          <Button type="danger" @click="addDocument">Lägg till dokument</Button>
        </div>
      </template>
    </Modal>

    <Modal ref="editFolderModal" title="Redigera mappens namn">
      <div v-if="selectedFolder">
        <div class="mt-1 relative rounded-md shadow-xs">
          <input
            v-model="selectedFolder.name"
            class="form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md p-2 border"
          />
        </div>
        <label
          class="mt-4 flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700"
        >
          <input
            v-model="selectedFolder.only_administrators"
            type="checkbox"
            class="mt-0.5 h-4 w-4 shrink-0 rounded border-gray-300 text-gkk focus:ring-gkk"
          />
          <span>
            <span class="block font-medium text-gray-800">Endast administratörer</span>
            <span class="block text-xs text-gray-500">Mappen och dess dokument visas inte för medlemmar.</span>
          </span>
        </label>
      </div>

      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button type="secondary" @click="close">Stäng</Button>
          <Button type="danger" @click="editFolderConfirm">Uppdatera namn</Button>
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
  props: ['jwt', 'folders'],
  data() {
    return {
      newFolderName: '',
      newFolderOnlyAdministrators: false,
      uploadStatus: '',
      newDocument: {
        name: '',
        url: '',
      },
      selectedDocument: null,
      selectedFolder: null,
      chosenFolder: null,
    }
  },
  computed: {
    sortedFolders() {
      this.folders.sort((a, b) => a.order - b.order)
      return this.folders
    },
  },
  methods: {
    async moveUp(index) {
      const newFolderOrder = [
        ...this.folders.slice(0, index - 1),
        this.folders[index],
        this.folders[index - 1],
        ...this.folders.slice(index + 1),
      ]

      await Promise.all(
        newFolderOrder.map((folder, index) =>
          axios.post(`/admin/document-folders/${folder.id}`, {
            name: folder.name,
            order: index + 1,
            only_administrators: folder.only_administrators,
          }),
        ),
      )

      window.location.reload()
    },
    async moveDown(index) {
      const newFolderOrder = [
        ...this.folders.slice(0, index),
        this.folders[index + 1],
        this.folders[index],
        ...this.folders.slice(index + 2),
      ]

      await Promise.all(
        newFolderOrder.map((folder, index) =>
          axios.post(`/admin/document-folders/${folder.id}`, {
            name: folder.name,
            order: index + 1,
            only_administrators: folder.only_administrators,
          }),
        ),
      )

      window.location.reload()
    },
    async newFolder() {
      await window.axios.post('/admin/document-folders', {
        name: this.newFolderName,
        order: +this.folders.sort((a, b) => b.order - a.order)[0].order + 1,
        only_administrators: this.newFolderOnlyAdministrators,
      })
      window.location.reload()
    },
    async deleteFolder(folder) {
      await window.axios.delete(`/admin/document-folders/${folder.id}`)
      window.location.reload()
    },
    addDocumentToFolder(folder) {
      this.chosenFolder = folder
      this.$refs.addDocumentModal.show()
    },
    addDocument() {
      if (this.newDocument.name.length === 0 || this.newDocument.url.length === 0) {
        return
      }

      window.axios
        .post(`/admin/documents`, {
          name: this.newDocument.name,
          url: this.newDocument.url,
          document_folder_id: this.chosenFolder.id,
        })
        .then(() => window.location.reload())
    },
    show(document) {
      window.open(document.url)
    },
    confirmDelete(document) {
      this.selectedDocument = JSON.parse(JSON.stringify(document))
      this.$refs.deleteDocumentModal.show()
    },
    deleteDocument() {
      window.axios.delete(`/admin/documents/${this.selectedDocument.id}`).then(() => window.location.reload())
    },
    confirmEdit(document) {
      this.selectedDocument = JSON.parse(JSON.stringify(document))
      this.$refs.editDocumentModal.show()
    },
    editDocument() {
      window.axios
        .post(`/admin/documents/${this.selectedDocument.id}`, this.selectedDocument)
        .then(() => window.location.reload())
    },
    editFolder(folder) {
      this.selectedFolder = JSON.parse(JSON.stringify(folder))
      this.$refs.editFolderModal.show()
    },
    async editFolderConfirm() {
      await window.axios.post(`/admin/document-folders/${this.selectedFolder.id}`, {
        name: this.selectedFolder.name,
        order: this.selectedFolder.order,
        only_administrators: this.selectedFolder.only_administrators,
      })
      window.location.reload()
    },
    async upload() {
      this.uploadStatus = 'pending'
      const file = this.$refs.fileUpload.files[0]

      if (!file) {
        return
      }

      const extension = file.name.split('.')[file.name.split('.').length - 1]
      let url = await FirebaseFileUpload.upload(this.jwt, file, extension)
      this.uploadStatus = ''
      this.newDocument.name = file.name
      this.newDocument.url = url
    },
  },
}
</script>
