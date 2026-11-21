<template>
  <section class="overflow-hidden rounded-2xl bg-white shadow-xs ring-1 ring-gray-900/5">
    <h2>
      <button
        type="button"
        class="flex w-full items-center gap-3 px-4 py-3.5 text-left transition-colors hover:bg-gray-50 sm:px-5"
        :aria-expanded="isOpen"
        @click="expanded = !expanded"
      >
        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-gkk/5 text-gkk">
          <FolderOpen v-if="isOpen" class="size-5" stroke-width="2" />
          <Folder v-else class="size-5" stroke-width="2" />
        </span>
        <span class="min-w-0 flex-1">
          <span class="block font-semibold break-words text-gray-900">{{ folder.name }}</span>
          <span class="block text-sm text-gray-500">{{ documents.length }} dokument</span>
        </span>
        <span
          v-if="folder.only_administrators"
          class="hidden items-center gap-1 rounded-full bg-gkk/5 px-2.5 py-1 text-xs font-semibold text-gkk ring-1 ring-inset ring-gkk/10 sm:inline-flex"
        >
          <Lock class="size-3" stroke-width="2.5" />
          Endast administratörer
        </span>
        <ChevronDown
          class="size-5 shrink-0 text-gray-400 transition-transform duration-200"
          :class="{ 'rotate-180': isOpen }"
        />
      </button>
    </h2>

    <ul v-show="isOpen" class="divide-y divide-gray-100 border-t border-gray-100">
      <li v-for="document in documents" :key="document.id">
        <a
          :href="document.url"
          target="_blank"
          rel="noopener"
          class="group flex items-center gap-3 px-4 py-3 transition-colors hover:bg-gkk/[0.03] sm:px-5"
        >
          <span
            class="flex size-9 shrink-0 items-center justify-center rounded-lg"
            :class="documentType(document.url).classes"
          >
            <component :is="documentType(document.url).icon" class="size-[18px]" stroke-width="2" />
          </span>
          <span class="min-w-0 flex-1">
            <span class="block font-medium break-words text-gray-900 group-hover:text-gkk">{{ document.name }}</span>
            <span class="block text-xs text-gray-500">{{ documentType(document.url).label }}</span>
          </span>
          <span
            class="hidden shrink-0 items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-semibold text-gkk ring-1 ring-inset ring-gkk/15 transition-colors group-hover:bg-gkk group-hover:text-white sm:inline-flex"
          >
            Visa
            <ExternalLink class="size-3.5" stroke-width="2.5" />
          </span>
          <ExternalLink class="size-4 shrink-0 text-gray-400 sm:hidden" />
        </a>
      </li>
    </ul>
  </section>
</template>

<script>
import { ChevronDown, ExternalLink, Folder, FolderOpen, Lock } from 'lucide-vue-next'
import { documentType } from '../modules/DocumentType.js'

export default {
  components: { ChevronDown, ExternalLink, Folder, FolderOpen, Lock },
  props: {
    folder: { type: Object, required: true },
    documents: { type: Array, required: true },
    openByDefault: Boolean,
    // While searching, every folder with a match is shown open.
    forceOpen: Boolean,
  },
  data() {
    return {
      expanded: this.openByDefault,
    }
  },
  computed: {
    isOpen() {
      return this.forceOpen || this.expanded
    },
  },
  methods: {
    documentType,
  },
}
</script>
