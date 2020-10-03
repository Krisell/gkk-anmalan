<template>
  <div class="container mx-auto max-w-3xl">
    <h1 class="text-center text-3xl font-hairline mb-6">Dokument</h1>

    <div class="flex flex-col">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                >
                  Namn
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                ></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr class="event-row" v-for="document in documents" :key="document.id">
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
                  <ui-button prevent @click="open(document.url)">Visa</ui-button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <GkkLink to="/" text="Tillbaka till startsidan" />
  </div>
</template>

<script>
export default {
  props: ['documents'],
  methods: {
    open(url) {
      window.open(url)
    },
  },
}
</script>
