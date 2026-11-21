<template>
  <div class="container mx-auto max-w-3xl px-4 pb-16 lg:px-0">
    <div class="flex flex-wrap items-start justify-between gap-4">
      <Breadcrumbs :items="[{ name: 'Dokument', href: '/member-documents' }]" title="Hantera dokument" />
      <button
        type="button"
        class="inline-flex items-center gap-2 rounded-full bg-gkk px-4 py-2 text-sm font-semibold text-white shadow-xs transition hover:bg-gkk-light"
        @click="openNewFolder"
      >
        <FolderPlus class="size-4" stroke-width="2.25" />
        Ny mapp
      </button>
    </div>
    <p class="mt-4 text-gray-500">
      Lägg till, byt namn på och ordna klubbens dokument. Mappar markerade
      <span class="font-medium text-gray-700">Endast administratörer</span> syns inte för medlemmar.
    </p>

    <label class="relative mt-6 block">
      <span class="sr-only">Sök dokument</span>
      <Search class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-gray-400" />
      <input
        v-model="query"
        type="search"
        :placeholder="`Sök bland ${documentCount} dokument`"
        class="block w-full rounded-full border-0 bg-white py-3 pr-4 pl-12 text-gray-900 shadow-xs ring-1 ring-gray-900/10 placeholder:text-gray-400 focus:ring-2 focus:ring-gkk focus:outline-hidden"
      />
    </label>

    <div ref="folderList" class="mt-6 space-y-3">
      <section
        v-for="{ folder, documents } in visibleFolders"
        :key="folder.id"
        class="overflow-hidden rounded-2xl bg-white shadow-xs ring-1 ring-gray-900/5"
      >
        <div class="flex items-center gap-1 pr-2 sm:pr-3">
          <!-- Drag to reorder. Hidden while searching, as only some folders are shown then. -->
          <button
            v-if="!searching"
            type="button"
            class="drag-handle ml-1.5 flex h-12 w-7 shrink-0 cursor-grab touch-none items-center justify-center rounded-md text-gray-300 transition hover:bg-gray-100 hover:text-gkk active:cursor-grabbing sm:ml-2"
            title="Dra för att flytta mappen"
            aria-label="Dra för att flytta mappen"
          >
            <GripVertical class="size-5" />
          </button>
          <button
            type="button"
            class="flex min-w-0 flex-1 items-center gap-3 py-3.5 text-left"
            :class="searching ? 'pl-4 sm:pl-5' : 'pl-1'"
            :aria-expanded="isOpen(folder)"
            @click="toggle(folder)"
          >
            <span class="hidden size-10 shrink-0 items-center justify-center rounded-xl bg-gkk/5 text-gkk sm:flex">
              <FolderOpen v-if="isOpen(folder)" class="size-5" stroke-width="2" />
              <Folder v-else class="size-5" stroke-width="2" />
            </span>
            <span class="min-w-0 flex-1">
              <span class="block font-semibold break-words text-gray-900">{{ folder.name }}</span>
              <span class="flex flex-wrap items-center gap-x-2 text-sm text-gray-500">
                {{ folder.documents.length }} dokument
                <span
                  v-if="folder.only_administrators"
                  class="inline-flex items-center gap-1 text-xs font-semibold text-gkk"
                >
                  <Lock class="size-3" stroke-width="2.5" />
                  Endast administratörer
                </span>
              </span>
            </span>
            <ChevronDown
              class="size-5 shrink-0 text-gray-400 transition-transform duration-200"
              :class="{ 'rotate-180': isOpen(folder) }"
            />
          </button>

          <div class="flex shrink-0 items-center border-l border-gray-100 pl-1 sm:pl-2">
            <button
              type="button"
              :class="iconButton"
              title="Redigera mappen"
              aria-label="Redigera mappen"
              @click="editFolder(folder)"
            >
              <Pencil class="size-4" />
            </button>
            <button
              type="button"
              :class="[iconButton, 'hover:bg-red-50 hover:text-red-600']"
              :disabled="folder.documents.length > 0"
              :title="folder.documents.length > 0 ? 'Bara tomma mappar kan raderas' : 'Radera mappen'"
              aria-label="Radera mappen"
              @click="deleteFolder(folder)"
            >
              <Trash2 class="size-4" />
            </button>
          </div>
        </div>

        <div v-show="isOpen(folder)" class="border-t border-gray-100">
          <ul class="divide-y divide-gray-100">
            <li
              v-for="document in documents"
              :key="document.id"
              class="flex items-center gap-3 py-2.5 pr-2 pl-4 sm:pr-3 sm:pl-5"
            >
              <span
                class="flex size-9 shrink-0 items-center justify-center rounded-lg"
                :class="documentType(document.url).classes"
              >
                <component :is="documentType(document.url).icon" class="size-[18px]" stroke-width="2" />
              </span>
              <a
                :href="document.url"
                target="_blank"
                rel="noopener"
                class="group min-w-0 flex-1"
                :title="`Öppna ${document.name}`"
              >
                <span class="flex items-center gap-1.5 font-medium break-words text-gray-900 group-hover:text-gkk">
                  <span class="min-w-0">{{ document.name }}</span>
                  <ExternalLink class="size-3.5 shrink-0 text-gray-300 group-hover:text-gkk" />
                </span>
                <span class="block text-xs text-gray-500">{{ documentType(document.url).label }}</span>
              </a>
              <div class="flex shrink-0 items-center">
                <button
                  type="button"
                  :class="iconButton"
                  title="Redigera dokumentet"
                  aria-label="Redigera dokumentet"
                  @click="confirmEdit(document)"
                >
                  <Pencil class="size-4" />
                </button>
                <button
                  type="button"
                  :class="[iconButton, 'hover:bg-red-50 hover:text-red-600']"
                  title="Radera dokumentet"
                  aria-label="Radera dokumentet"
                  @click="confirmDelete(document)"
                >
                  <Trash2 class="size-4" />
                </button>
              </div>
            </li>
          </ul>

          <p v-if="!folder.documents.length" class="px-5 pt-5 text-center text-sm text-gray-500">Mappen är tom.</p>

          <div class="p-3">
            <button
              type="button"
              class="flex w-full items-center justify-center gap-2 rounded-xl border border-dashed border-gray-300 py-2.5 text-sm font-semibold text-gkk transition hover:border-gkk/40 hover:bg-gkk/5"
              @click="addDocumentToFolder(folder)"
            >
              <Plus class="size-4" stroke-width="2.5" />
              Lägg till dokument
            </button>
          </div>
        </div>
      </section>
    </div>

    <div
      v-if="!visibleFolders.length"
      class="mt-6 rounded-2xl bg-white px-6 py-12 text-center text-gray-500 ring-1 ring-gray-900/5"
    >
      <template v-if="searching">Inga dokument matchar ”{{ query.trim() }}”.</template>
      <template v-else>Det finns inga mappar ännu. Skapa en för att komma igång.</template>
    </div>

    <Modal
      ref="deleteDocumentModal"
      :title="`Är du säker på att du vill radera ${selectedDocument && selectedDocument.name}?`"
    >
      <template #footer="{ close }">
        <div class="mt-4 flex items-center justify-center gap-2">
          <Button type="secondary" @click="close">Avbryt</Button>
          <Button type="danger" @click="deleteDocument">Radera</Button>
        </div>
      </template>
    </Modal>

    <Modal ref="editDocumentModal" title="Redigera dokumentet" size="lg">
      <div v-if="selectedDocument" class="space-y-4 text-left">
        <div>
          <label for="edit-document-name" :class="labelClass">Namn</label>
          <input id="edit-document-name" v-model="selectedDocument.name" type="text" :class="inputClass" />
        </div>
        <div>
          <label for="edit-document-url" :class="labelClass">Länk</label>
          <input id="edit-document-url" v-model="selectedDocument.url" type="text" :class="inputClass" />
        </div>
      </div>

      <template #footer="{ close }">
        <div class="mt-6 flex items-center justify-center gap-2">
          <Button type="secondary" @click="close">Avbryt</Button>
          <Button
            class="disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!selectedDocument || !selectedDocument.name.trim() || !selectedDocument.url.trim()"
            @click="editDocument"
          >
            Spara
          </Button>
        </div>
      </template>
    </Modal>

    <Modal ref="addDocumentModal" :title="`Nytt dokument i ${chosenFolder ? chosenFolder.name : 'mappen'}`" size="lg">
      <div class="space-y-4 text-left">
        <label
          class="flex cursor-pointer flex-col items-center gap-2 rounded-xl border-2 border-dashed px-4 py-6 text-center transition"
          :class="
            newDocument.url && uploadStatus !== 'pending'
              ? 'border-green-300 bg-green-50'
              : 'border-gray-300 hover:border-gkk/40 hover:bg-gkk/5'
          "
        >
          <LoaderCircle v-if="uploadStatus === 'pending'" class="size-8 animate-spin text-gkk" />
          <CloudUpload v-else class="size-8" :class="newDocument.url ? 'text-green-600' : 'text-gkk'" />
          <span v-if="uploadStatus === 'pending'" class="text-sm font-medium text-gray-700">Laddar upp…</span>
          <span v-else-if="uploadStatus === 'done'" class="text-sm font-medium text-green-700">
            Filen är uppladdad · välj en annan
          </span>
          <span v-else class="text-sm font-medium text-gray-700">Välj en fil att ladda upp</span>
          <span v-if="uploadStatus === 'failed'" class="text-sm text-red-600">Uppladdningen misslyckades.</span>
          <input ref="fileUpload" type="file" class="hidden" @change="upload" />
        </label>

        <div class="flex items-center gap-3 text-xs font-medium tracking-wider text-gray-400 uppercase">
          <span class="h-px flex-1 bg-gray-200"></span>
          eller ange en länk
          <span class="h-px flex-1 bg-gray-200"></span>
        </div>

        <div>
          <label for="new-document-name" :class="labelClass">Namn</label>
          <input
            id="new-document-name"
            v-model="newDocument.name"
            type="text"
            placeholder="Till exempel Protokoll årsmöte 2026"
            :class="inputClass"
          />
        </div>
        <div>
          <label for="new-document-url" :class="labelClass">Länk</label>
          <input
            id="new-document-url"
            v-model="newDocument.url"
            type="text"
            placeholder="https://"
            :class="inputClass"
          />
        </div>
      </div>

      <template #footer="{ close }">
        <div class="mt-6 flex items-center justify-center gap-2">
          <Button type="secondary" @click="close">Avbryt</Button>
          <Button
            class="disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!newDocument.name.trim() || !newDocument.url.trim() || uploadStatus === 'pending'"
            @click="addDocument"
          >
            Lägg till dokument
          </Button>
        </div>
      </template>
    </Modal>

    <Modal ref="editFolderModal" title="Redigera mappen" size="lg">
      <div v-if="selectedFolder" class="space-y-4 text-left">
        <div>
          <label for="edit-folder-name" :class="labelClass">Mappnamn</label>
          <input id="edit-folder-name" v-model="selectedFolder.name" type="text" :class="inputClass" />
        </div>
        <label :class="checkboxCard">
          <input
            v-model="selectedFolder.only_administrators"
            type="checkbox"
            class="mt-0.5 size-4 shrink-0 rounded border-gray-300 text-gkk focus:ring-gkk"
          />
          <span>
            <span class="block text-sm font-medium text-gray-800">Endast administratörer</span>
            <span class="block text-xs text-gray-500">Mappen och dess dokument visas inte för medlemmar.</span>
          </span>
        </label>
      </div>

      <template #footer="{ close }">
        <div class="mt-6 flex items-center justify-center gap-2">
          <Button type="secondary" @click="close">Avbryt</Button>
          <Button
            class="disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!selectedFolder || !selectedFolder.name.trim()"
            @click="editFolderConfirm"
          >
            Spara
          </Button>
        </div>
      </template>
    </Modal>

    <Modal ref="newFolderModal" title="Ny mapp" size="lg">
      <div class="space-y-4 text-left">
        <p class="text-sm text-gray-500">Skapa en mapp för att samla relaterade dokument. Den läggs sist i listan.</p>
        <div>
          <label for="new-folder-name" :class="labelClass">Mappnamn</label>
          <input
            id="new-folder-name"
            v-model="newFolderName"
            type="text"
            placeholder="Till exempel Protokoll 2027"
            :class="inputClass"
          />
        </div>
        <label :class="checkboxCard">
          <input
            v-model="newFolderOnlyAdministrators"
            type="checkbox"
            class="mt-0.5 size-4 shrink-0 rounded border-gray-300 text-gkk focus:ring-gkk"
          />
          <span>
            <span class="block text-sm font-medium text-gray-800">Endast administratörer</span>
            <span class="block text-xs text-gray-500">Mappen och dess dokument visas inte för medlemmar.</span>
          </span>
        </label>
      </div>

      <template #footer="{ close }">
        <div class="mt-6 flex items-center justify-center gap-2">
          <Button type="secondary" @click="close">Avbryt</Button>
          <Button
            class="disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!newFolderName.trim()"
            @click="newFolder"
          >
            Skapa mapp
          </Button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script>
import Sortable from 'sortablejs'
import { useToast } from 'vue-toast-notification'
import 'vue-toast-notification/dist/theme-sugar.css'
import {
  ChevronDown,
  CloudUpload,
  ExternalLink,
  Folder,
  FolderOpen,
  FolderPlus,
  GripVertical,
  LoaderCircle,
  Lock,
  Pencil,
  Plus,
  Search,
  Trash2,
} from 'lucide-vue-next'
import Breadcrumbs from './ui/Breadcrumbs.vue'
import FirebaseFileUpload from '../modules/FirebaseFileUpload.js'
import Button from './ui/Button.vue'
import Modal from './ui/Modal.vue'
import { documentType, matchesSearch } from '../modules/DocumentType.js'

export default {
  components: {
    Breadcrumbs,
    Button,
    ChevronDown,
    CloudUpload,
    ExternalLink,
    Folder,
    FolderOpen,
    FolderPlus,
    GripVertical,
    LoaderCircle,
    Lock,
    Modal,
    Pencil,
    Plus,
    Search,
    Trash2,
  },
  props: ['jwt', 'folders'],
  data() {
    // A copy of the folders in their set order, that dragging rearranges.
    const folderList = [...this.folders].sort((a, b) => a.order - b.order)
    const first = folderList[0]

    return {
      folderList,
      query: '',
      // The first folder starts open, as that's usually where new documents go.
      expanded: first ? { [first.id]: true } : {},
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
      iconButton:
        'flex size-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gkk/5 hover:text-gkk disabled:pointer-events-none disabled:opacity-30',
      labelClass: 'mb-1 block text-sm font-medium text-gray-700',
      inputClass:
        'block w-full rounded-lg border-0 px-3 py-2 text-gray-900 ring-1 ring-gray-300 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-gkk sm:text-sm',
      checkboxCard:
        'flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3 text-left transition-colors hover:border-gkk/40',
    }
  },
  computed: {
    searching() {
      return this.query.trim().length > 0
    },
    documentCount() {
      return this.folderList.reduce((sum, folder) => sum + folder.documents.length, 0)
    },
    // Every folder with the documents matching the search (all of them when the folder's own name matches). While
    // searching, folders without a match are hidden.
    visibleFolders() {
      const query = this.query.trim()

      return this.folderList
        .map((folder) => ({
          folder,
          documents:
            !query || matchesSearch(folder.name, query)
              ? folder.documents
              : folder.documents.filter((document) => matchesSearch(document.name, query)),
        }))
        .filter(({ folder, documents }) => !query || documents.length > 0 || matchesSearch(folder.name, query))
    },
  },
  watch: {
    // Only the full list can be reordered, so dragging is off while searching.
    searching(searching) {
      this.sortable?.option('disabled', searching)
    },
  },
  mounted() {
    // Sortable moves the folder in the DOM; it is put back right away and the list is reordered instead, so that Vue
    // stays in charge of the DOM.
    this.sortable = Sortable.create(this.$refs.folderList, {
      handle: '.drag-handle',
      // Sortable's own dragging rather than the browser's, so the card itself follows the pointer.
      forceFallback: true,
      fallbackClass: 'folder-dragging',
      animation: 150,
      ghostClass: 'folder-ghost',
      chosenClass: 'folder-chosen',
      onEnd: ({ item, from, oldIndex, newIndex }) => {
        item.remove()
        from.insertBefore(item, from.children[oldIndex] ?? null)
        void this.move(oldIndex, newIndex)
      },
    })
  },
  beforeUnmount() {
    this.sortable?.destroy()
  },
  methods: {
    documentType,
    isOpen(folder) {
      return this.searching || !!this.expanded[folder.id]
    },
    toggle(folder) {
      this.expanded = { ...this.expanded, [folder.id]: !this.expanded[folder.id] }
    },
    // Moves a folder in the list right away, then saves the new order of the folders whose place changed. If saving
    // fails, the list goes back to how it was.
    async move(oldIndex, newIndex) {
      if (oldIndex === newIndex) {
        return
      }

      const previous = this.folderList.map((folder) => ({ folder, order: folder.order }))
      const list = [...this.folderList]
      list.splice(newIndex, 0, ...list.splice(oldIndex, 1))
      this.folderList = list

      const changed = list.filter((folder, index) => +folder.order !== index + 1)
      list.forEach((folder, index) => (folder.order = index + 1))

      try {
        await Promise.all(
          changed.map((folder) =>
            window.axios.post(`/admin/document-folders/${folder.id}`, {
              name: folder.name,
              order: folder.order,
              only_administrators: folder.only_administrators,
            }),
          ),
        )
      } catch {
        previous.forEach(({ folder, order }) => (folder.order = order))
        this.folderList = previous.map(({ folder }) => folder)
        useToast().error('Det gick inte att spara mapparnas ordning. Försök igen.', { position: 'top' })
      }
    },
    openNewFolder() {
      this.newFolderName = ''
      this.newFolderOnlyAdministrators = false
      this.$refs.newFolderModal.show()
    },
    async newFolder() {
      if (!this.newFolderName.trim()) {
        return
      }

      await window.axios.post('/admin/document-folders', {
        name: this.newFolderName.trim(),
        order: Math.max(0, ...this.folderList.map((folder) => +folder.order)) + 1,
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
      this.newDocument = { name: '', url: '' }
      this.uploadStatus = ''
      this.$refs.addDocumentModal.show()
    },
    addDocument() {
      if (!this.newDocument.name.trim() || !this.newDocument.url.trim()) {
        return
      }

      window.axios
        .post(`/admin/documents`, {
          name: this.newDocument.name.trim(),
          url: this.newDocument.url.trim(),
          document_folder_id: this.chosenFolder.id,
        })
        .then(() => window.location.reload())
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
      const file = this.$refs.fileUpload.files[0]
      if (!file) {
        return
      }

      this.uploadStatus = 'pending'
      try {
        const extension = file.name.split('.').pop()
        this.newDocument.url = await FirebaseFileUpload.upload(this.jwt, file, extension)
        // Suggest the file name, without its extension, unless a name is already written.
        if (!this.newDocument.name.trim()) {
          this.newDocument.name = file.name.replace(/\.[^.]+$/, '')
        }
        this.uploadStatus = 'done'
      } catch {
        this.uploadStatus = 'failed'
      }
    },
  },
}
</script>

<style scoped>
.folder-ghost {
  opacity: 0.4;
  outline: 2px dashed var(--color-gkk);
  outline-offset: 2px;
}

.folder-chosen {
  box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.15);
}

/* The copy of the card that follows the pointer. */
:global(.folder-dragging) {
  opacity: 0.95 !important;
  box-shadow: 0 20px 40px -10px rgb(0 0 0 / 0.25);
  cursor: grabbing;
}
</style>
