<template>
  <div class="container mx-auto max-w-3xl">
    <Breadcrumbs class="mb-6" title="Admin - Protokoll">
      <Button @click="location('/admin/minutes/new')"><i class="fa fa-plus mr-2"></i>Nytt protokoll</Button>
    </Breadcrumbs>

    <div class="shadow-sm overflow-hidden sm:rounded-lg border-b border-gray-200 bg-white">
      <div v-if="items.length === 0" class="px-6 py-6 text-center text-sm text-gray-500">Inga protokoll ännu.</div>
      <div
        v-for="item in items"
        :key="item.id"
        class="flex flex-wrap items-center gap-3 px-6 py-3 border-b border-gray-200 last:border-b-0"
      >
        <a :href="`/admin/minutes/${item.id}`" class="flex-1 min-w-0">
          <div class="text-sm font-medium text-gray-900">
            <i class="fa fa-book mr-3"></i>{{ item.meeting_type }} {{ item.number }}, {{ item.year }}
          </div>
          <div class="text-xs text-gray-500 mt-1">
            {{ item.date }}
            <span class="ml-2 rounded-full px-2 py-0.5 font-medium" :class="statuses[item.status].class">
              {{ statuses[item.status].label }}
            </span>
          </div>
        </a>
        <Button type="secondary" @click="window.open(`/admin/minutes/${item.id}/print`, '_blank')">
          <i class="fa fa-print mr-2"></i>Skriv ut
        </Button>
        <Button type="secondary" @click="location(`/admin/minutes/new?copy=${item.id}`)">
          <i class="fa fa-files-o mr-2"></i>Kopiera
        </Button>
        <Button @click="location(`/admin/minutes/${item.id}`)">Redigera</Button>
        <button
          v-tooltip="'Radera'"
          class="text-gkk-light hover:text-gkk cursor-pointer"
          aria-label="Radera"
          @click="confirmDelete(item)"
        >
          <i class="fa fa-trash-o text-lg"></i>
        </button>
      </div>
    </div>

    <Modal ref="deleteModal" :title="`Radera ${selected && selected.meeting_type} ${selected && selected.number}?`">
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button type="secondary" @click="close">Nej</Button>
          <Button type="danger" @click="destroy(close)">Ja, radera</Button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script>
import axios from 'axios'
import Breadcrumbs from '../ui/Breadcrumbs.vue'
import Button from '../ui/Button.vue'
import Modal from '../ui/Modal.vue'

export default {
  components: { Breadcrumbs, Button, Modal },
  props: ['minutes'],
  data() {
    return {
      items: [...this.minutes],
      selected: null,
      statuses: {
        draft: { label: 'Utkast', class: 'bg-gray-100 text-gray-600' },
        review: { label: 'Väntar på justering', class: 'bg-blue-50 text-blue-700' },
        changes_requested: { label: 'Ändringar önskas', class: 'bg-amber-50 text-amber-800' },
        approved: { label: 'Justerat', class: 'bg-green-50 text-green-700' },
        published: { label: 'Publicerat', class: 'bg-green-100 text-green-800' },
      },
      window,
    }
  },
  methods: {
    confirmDelete(item) {
      this.selected = item
      this.$refs.deleteModal.show()
    },
    destroy(close) {
      axios.delete(`/admin/minutes/${this.selected.id}`).then(() => {
        this.items = this.items.filter((item) => item.id !== this.selected.id)
        close()
      })
    },
  },
}
</script>
