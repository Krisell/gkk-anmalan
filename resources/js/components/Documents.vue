<template>
  <div class="container mx-auto max-w-3xl px-4 pb-16 lg:px-0">
    <div class="mt-8 flex flex-wrap items-start justify-between gap-4">
      <Breadcrumbs title="Dokument" />
      <a
        v-if="isAdmin"
        href="/admin/documents"
        class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-gkk shadow-xs ring-1 ring-gray-900/5 transition hover:bg-gkk/5"
      >
        <Settings2 class="size-4" stroke-width="2.25" />
        Hantera dokument
      </a>
    </div>
    <p class="mt-4 text-gray-500">Protokoll, årsmöteshandlingar och andra dokument från klubben.</p>

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

    <div class="mt-6 space-y-3">
      <DocumentFolder
        v-for="(folder, index) in visibleFolders"
        :key="folder.id"
        :folder="folder"
        :documents="folder.matchingDocuments"
        :open-by-default="index === 0"
        :force-open="searching"
      />
    </div>

    <div
      v-if="!visibleFolders.length"
      class="mt-6 rounded-2xl bg-white px-6 py-12 text-center text-gray-500 ring-1 ring-gray-900/5"
    >
      <template v-if="searching">Inga dokument matchar ”{{ query.trim() }}”.</template>
      <template v-else>Det finns inga dokument ännu.</template>
    </div>
  </div>
</template>

<script>
import { Search, Settings2 } from 'lucide-vue-next'
import Breadcrumbs from './ui/Breadcrumbs.vue'
import DocumentFolder from './DocumentFolder.vue'
import { matchesSearch } from '../modules/DocumentType.js'

export default {
  components: { Breadcrumbs, DocumentFolder, Search, Settings2 },
  props: ['folders', 'user'],
  data() {
    return {
      query: '',
    }
  },
  computed: {
    isAdmin() {
      return this.user && ['admin', 'superadmin'].includes(this.user.role)
    },
    searching() {
      return this.query.trim().length > 0
    },
    documentCount() {
      return this.folders.reduce((sum, folder) => sum + folder.documents.length, 0)
    },
    // Folders in their set order, each with the documents that match the search (all of them when the folder's own
    // name matches). Empty folders are left out, as there is nothing in them to open.
    visibleFolders() {
      const query = this.query.trim()

      return [...this.folders]
        .sort((a, b) => a.order - b.order)
        .map((folder) => ({
          ...folder,
          matchingDocuments:
            !query || matchesSearch(folder.name, query)
              ? folder.documents
              : folder.documents.filter((document) => matchesSearch(document.name, query)),
        }))
        .filter((folder) => folder.matchingDocuments.length > 0)
    },
  },
}
</script>
