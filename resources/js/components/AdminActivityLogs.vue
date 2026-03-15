<template>
  <div class="container mx-auto">
    <h2 class="text-2xl font-thin text-center m-4">Aktivitetslogg</h2>

    <!-- Filters -->
    <div class="flex flex-col items-center mb-6">
      <div class="flex gap-4 mb-4">
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium text-gray-700">Händelse</label>
          <select v-model="filters.action" class="rounded-md border-0 py-1.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
            <option value="">Alla händelser</option>
            <option v-for="action in availableActions" :key="action" :value="action">{{ actionLabel(action) }}</option>
          </select>
        </div>
      </div>

      <!-- Search -->
      <div class="relative rounded-md shadow-sm w-64">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
          <i class="fa fa-search text-gray-400"></i>
        </div>
        <div
          v-if="search.length > 0"
          @click="search = ''"
          class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-3"
        >
          <i class="fa fa-times text-gray-400"></i>
        </div>
        <input
          v-model="search"
          class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6"
          placeholder="Sök namn"
        />
      </div>
    </div>

    <!-- Table -->
    <div class="flex flex-col">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Datum
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Utförd av
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Händelse
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Medlem
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Detaljer
                </th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="log in filteredLogs" :key="log.id">
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm text-gray-500">
                  {{ dateTimeString(log.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm text-gray-900">
                  <span v-if="log.performer">{{ log.performer.first_name }} {{ log.performer.last_name }}</span>
                  <span v-else class="text-gray-400">-</span>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="actionClass(log.action)">
                    {{ actionLabel(log.action) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm text-gray-900">
                  <span v-if="log.user">{{ log.user.first_name }} {{ log.user.last_name }}</span>
                  <span v-else class="text-gray-400">-</span>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm text-gray-500">
                  {{ log.data || '-' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    initialLogs: {
      type: Array,
      required: true,
    },
    user: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      logs: this.initialLogs,
      search: '',
      filters: {
        action: '',
      },
    }
  },
  computed: {
    availableActions() {
      return [...new Set(this.logs.map(l => l.action))].sort()
    },
    filteredLogs() {
      let filtered = this.logs

      if (this.filters.action) {
        filtered = filtered.filter(l => l.action === this.filters.action)
      }

      if (this.search) {
        const searchLower = this.search.toLowerCase()
        filtered = filtered.filter(l => {
          const performedBy = l.performer
            ? `${l.performer.first_name} ${l.performer.last_name}`.toLowerCase()
            : ''
          const affectedUser = l.user
            ? `${l.user.first_name} ${l.user.last_name}`.toLowerCase()
            : ''
          return performedBy.includes(searchLower) || affectedUser.includes(searchLower)
        })
      }

      return filtered
    },
  },
  methods: {
    dateTimeString(date) {
      if (!date) return ''
      return date.replace('T', ' ').substring(0, 16)
    },
    actionLabel(action) {
      const labels = {
        'account-promotion': 'Befordran',
        'account-demotion': 'Degradering',
        'account-batch-creation': 'Kontoskapande',
        'explicit-registration-approval-update': 'Tävlingsundantag',
        'ren-vinnare-education-update': 'Ren Vinnare-utbildning',
        'background-check-update': 'Bakgrundskontroll',
        'event-registration': 'Evenemangsregistrering',
        'competition-registration': 'Tävlingsregistrering',
        'payment-update': 'Betalningsuppdatering',
        'result-creation': 'Resultat skapat',
        'result-deletion': 'Resultat borttaget',
      }
      return labels[action] || action
    },
    actionClass(action) {
      const classes = {
        'account-promotion': 'bg-green-100 text-green-800',
        'account-demotion': 'bg-red-100 text-red-800',
        'account-batch-creation': 'bg-blue-100 text-blue-800',
        'explicit-registration-approval-update': 'bg-purple-100 text-purple-800',
        'ren-vinnare-education-update': 'bg-yellow-100 text-yellow-800',
        'background-check-update': 'bg-yellow-100 text-yellow-800',
        'event-registration': 'bg-indigo-100 text-indigo-800',
        'competition-registration': 'bg-indigo-100 text-indigo-800',
        'payment-update': 'bg-orange-100 text-orange-800',
        'result-creation': 'bg-teal-100 text-teal-800',
        'result-deletion': 'bg-red-100 text-red-800',
      }
      return classes[action] || 'bg-gray-100 text-gray-800'
    },
  },
}
</script>
