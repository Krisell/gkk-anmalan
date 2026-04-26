<template>
  <div class="container mx-auto max-w-3xl">
    <div class="flex items-start justify-between gap-4 flex-wrap mb-6 mt-8">
      <h1 class="text-2xl font-semibold">
        <a href="/insidan" class="inline-flex items-center gap-2 text-gray-400 hover:text-gkk transition-colors group">
          <i class="fa fa-angle-left"></i>
          <span class="underline underline-offset-4 decoration-gray-300 group-hover:decoration-gkk">Start</span>
        </a>
        <span class="text-gray-300 mx-2">/</span>
        <span class="text-gkk">Dokument</span>
      </h1>
      <a
        v-if="isAdmin"
        href="/admin/documents"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gkk text-white font-medium text-sm hover:bg-gkk-light transition-colors"
      >
        <i class="fa fa-lock"></i>
        <span>Klicka för att administrera</span>
      </a>
    </div>

    <div class="flex flex-col">
      <DocumentFolder
        v-for="(folder, folderIndex) in sortedFolders"
        :key="folder.id"
        :folder="folder"
        :top-folder="folderIndex === 0"
        :documents="folder.documents"
      ></DocumentFolder>
    </div>
  </div>
</template>

<script>
import DocumentFolder from './DocumentFolder.vue'

export default {
  components: { DocumentFolder },
  props: ['folders', 'user'],
  computed: {
    sortedFolders() {
      this.folders.sort((a, b) => a.order - b.order)
      return this.folders
    },
    isAdmin() {
      return this.user && ['admin', 'superadmin'].includes(this.user.role)
    },
  },
}
</script>
