<template>
  <div class="py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
    <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
      <div>
        <table class="min-w-full">
          <thead>
            <tr>
              <th
                @click="expanded = !expanded"
                class="cursor-pointer px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
              >
                <i v-if="!expanded" class="fa fa-chevron-right -ml-4 mr-2"></i>
                <i v-if="expanded" class="fa fa-chevron-down -ml-4 mr-2"></i>
                {{ folder.name }} ({{ documents.length }} dokument)
              </th>
              <th
                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
              ></th>
            </tr>
          </thead>
          <tbody class="bg-white">
            <tr class="event-row" v-show="expanded" v-for="document in documents" :key="document.id">
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
                <Button @click="open(document.url)">Visa</Button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import Button from './ui/Button.vue'

export default {
  components: { Button },
  props: ['folder', 'documents', 'top-folder'],
  data() {
    return {
      expanded: false,
    }
  },
  mounted() {
    this.expanded = this.topFolder
  },
  methods: {
    open(url) {
      window.open(url)
    },
  },
}
</script>

<style></style>
