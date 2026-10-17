<template>
  <div class="container mx-auto max-w-3xl">
    <div class="flex items-start justify-between gap-4 flex-wrap mb-6 mt-8">
      <Breadcrumbs title="Dokument" />
      <a
        v-if="isAdmin"
        href="/admin/documents"
        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gkk/10 text-gkk text-[10px] font-semibold uppercase tracking-wider hover:bg-gkk/20"
      >
        <i class="fa fa-lock"></i>
        Admin
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
import Breadcrumbs from './ui/Breadcrumbs.vue'
import DocumentFolder from './DocumentFolder.vue'

export default {
  components: { Breadcrumbs, DocumentFolder },
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
