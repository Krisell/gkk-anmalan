<template>
  <div class="container mx-auto max-w-3xl">
    <h1 class="text-center text-3xl font-thin mb-6 mt-8">Dokument</h1>

    <div class="flex flex-col">
      <DocumentFolder
        v-for="(folder, folderIndex) in sortedFolders"
        :key="folder.id"
        :folder="folder"
        :top-folder="folderIndex === 0"
        :documents="folder.documents"
      ></DocumentFolder>
    </div>

    <GkkLink to="/insidan" text="Tillbaka till insidans navigering" />
  </div>
</template>

<script>
import DocumentFolder from './DocumentFolder.vue'

export default {
  components: { DocumentFolder },
  props: ['folders'],
  computed: {
    sortedFolders() {
      this.folders.sort((a, b) => a.order - b.order)
      return this.folders
    },
  },
}
</script>
