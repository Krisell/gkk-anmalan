<template>
  <div class="container mx-auto max-w-3xl">
    <h1 class="text-center text-3xl font-thin mb-6">Dokument</h1>

    <div class="flex flex-col">
      <DocumentFolder
        v-for="(folder, folderIndex) in sortedFolders"
        :key="folder.id"
        :folder="folder"
        :top-folder="folderIndex === 0"
        :documents="documentsFor(folder)"
      ></DocumentFolder>
    </div>

    <GkkLink to="/" text="Tillbaka till startsidan" />
  </div>
</template>

<script>
import DocumentFolder from './DocumentFolder.vue'

export default {
  components: { DocumentFolder },
  props: ['documents', 'folders'],
  computed: {
    sortedFolders() {
      this.folders.sort((a, b) => a.order - b.order)
      return this.folders
    },
  },
  methods: {
    documentsFor(folder) {
      return this.documents.filter((document) => document.document_folder_id == folder.id)
    },
  },
}
</script>
