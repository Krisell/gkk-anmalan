<template>
  <div class="container mx-auto">
    <h2 class="text-2xl font-thin text-center m-4">Betalningsverktyg</h2>

    <!-- Generate Membership Payments -->
    <ToolCard
      title="Generera medlemsavgifter"
      description="Skapar betalningsposter för alla aktiva medlemmar som saknar medlemsavgift."
    >
      <div class="flex gap-4 items-end mb-4">
        <div class="flex flex-col gap-1">
          <label class="text-sm font-medium text-gray-700">År</label>
          <select v-model="memberships.year" class="input-field">
            <option v-for="y in yearOptions" :key="y" :value="y">{{ y }}</option>
          </select>
        </div>
        <button @click="preview('memberships')" :disabled="memberships.loading" class="btn-secondary">
          <i v-if="memberships.loading" class="fa fa-spinner fa-spin mr-1"></i>
          Förhandsgranska
        </button>
      </div>
      <PreviewResult :tool="memberships" @execute="execute('memberships')" />
    </ToolCard>

    <!-- Generate License Payments -->
    <ToolCard
      title="Generera licensavgifter"
      description="Skapar SSF-licensbetalningar för tävlingsregistrerade användare."
    >
      <div class="flex gap-4 items-end mb-4">
        <div class="flex flex-col gap-1">
          <label class="text-sm font-medium text-gray-700">År</label>
          <select v-model="licenses.year" class="input-field">
            <option v-for="y in yearOptions" :key="y" :value="y">{{ y }}</option>
          </select>
        </div>
        <button @click="preview('licenses')" :disabled="licenses.loading" class="btn-secondary">
          <i v-if="licenses.loading" class="fa fa-spinner fa-spin mr-1"></i>
          Förhandsgranska
        </button>
      </div>
      <PreviewResult :tool="licenses" @execute="execute('licenses')" />
    </ToolCard>

    <!-- Create Competition Payments -->
    <ToolCard
      title="Skapa tävlingsavgifter"
      description="Skapar betalningsposter för registrerade tävlingsdeltagare."
    >
      <div class="flex flex-wrap gap-4 items-end mb-4">
        <div class="flex flex-col gap-1">
          <label class="text-sm font-medium text-gray-700">Tävling</label>
          <select v-model="competitionPayments.competition_id" class="input-field">
            <option value="">Välj tävling...</option>
            <option v-for="c in competitions" :key="c.id" :value="c.id">
              {{ c.name }}{{ c.date ? ` (${c.date})` : '' }}
            </option>
          </select>
        </div>
        <div class="flex flex-col gap-1">
          <label class="text-sm font-medium text-gray-700">Senioravgift (SEK)</label>
          <input v-model="competitionPayments.senior_amount" type="number" class="input-field w-32" placeholder="0" />
        </div>
        <div class="flex flex-col gap-1">
          <label class="text-sm font-medium text-gray-700">Junioravgift (SEK)</label>
          <input v-model="competitionPayments.junior_amount" type="number" class="input-field w-32" placeholder="0" />
        </div>
        <button
          @click="preview('competitionPayments')"
          :disabled="competitionPayments.loading || !competitionPayments.competition_id || !competitionPayments.senior_amount || !competitionPayments.junior_amount"
          class="btn-secondary"
        >
          <i v-if="competitionPayments.loading" class="fa fa-spinner fa-spin mr-1"></i>
          Förhandsgranska
        </button>
      </div>
      <PreviewResult :tool="competitionPayments" @execute="execute('competitionPayments')" />
    </ToolCard>

    <!-- Sync Fortnox Customers -->
    <ToolCard
      title="Synka Fortnox-kunder"
      description="Skapar kundposter i Fortnox för aktiva användare som saknar kundnummer."
    >
      <div class="flex gap-4 items-end mb-4">
        <button @click="preview('syncCustomers')" :disabled="syncCustomers.loading" class="btn-secondary">
          <i v-if="syncCustomers.loading" class="fa fa-spinner fa-spin mr-1"></i>
          Förhandsgranska
        </button>
      </div>
      <PreviewResult :tool="syncCustomers" @execute="executeStream('syncCustomers')" :streaming="true" />
      <StreamProgress :tool="syncCustomers" />
    </ToolCard>

    <!-- Create Fortnox Invoices -->
    <ToolCard
      title="Skapa Fortnox-fakturor"
      description="Skapar fakturor i Fortnox från betalningsposter."
    >
      <div class="flex gap-4 items-end mb-4">
        <div class="flex flex-col gap-1">
          <label class="text-sm font-medium text-gray-700">Betalningstyp</label>
          <select v-model="createInvoices.type" class="input-field">
            <option value="MEMBERSHIP">Medlemsavgift</option>
            <option value="SSFLICENSE">Tävlingslicens</option>
            <option value="COMPETITION">Tävlingsavgift</option>
            <option value="OTHER">Övrig avgift</option>
          </select>
        </div>
        <div class="flex flex-col gap-1">
          <label class="text-sm font-medium text-gray-700">År</label>
          <select v-model="createInvoices.year" class="input-field">
            <option v-for="y in yearOptions" :key="y" :value="y">{{ y }}</option>
          </select>
        </div>
        <button @click="preview('createInvoices')" :disabled="createInvoices.loading" class="btn-secondary">
          <i v-if="createInvoices.loading" class="fa fa-spinner fa-spin mr-1"></i>
          Förhandsgranska
        </button>
      </div>
      <PreviewResult :tool="createInvoices" @execute="executeStream('createInvoices')" :streaming="true" />
      <StreamProgress :tool="createInvoices" />
    </ToolCard>

    <!-- Email Invoices -->
    <ToolCard
      title="Skicka fakturor via email"
      description="Skickar skapade fakturor som inte har emailats via Fortnox."
    >
      <div class="flex gap-4 items-end mb-4">
        <button @click="preview('emailInvoices')" :disabled="emailInvoices.loading" class="btn-secondary">
          <i v-if="emailInvoices.loading" class="fa fa-spinner fa-spin mr-1"></i>
          Förhandsgranska
        </button>
      </div>
      <PreviewResult :tool="emailInvoices" @execute="executeStream('emailInvoices')" :streaming="true" />
      <StreamProgress :tool="emailInvoices" />
    </ToolCard>

    <!-- Verify Payments -->
    <ToolCard
      title="Verifiera betalningar"
      description="Kontrollerar Fortnox för betalda fakturor och uppdaterar status."
    >
      <div class="flex gap-4 items-end mb-4">
        <button @click="preview('verifyPayments')" :disabled="verifyPayments.loading" class="btn-secondary">
          <i v-if="verifyPayments.loading" class="fa fa-spinner fa-spin mr-1"></i>
          Förhandsgranska
        </button>
      </div>
      <PreviewResult :tool="verifyPayments" @execute="executeStream('verifyPayments')" :streaming="true" />
      <StreamProgress :tool="verifyPayments" />
    </ToolCard>

    <!-- Remind Unpaid Fees -->
    <ToolCard
      title="Påminn om obetalda avgifter"
      description="Skickar påminnelsemail för fakturor som skickats för mer än 30 dagar sedan."
    >
      <div class="flex gap-4 items-end mb-4">
        <div class="flex flex-col gap-1">
          <label class="text-sm font-medium text-gray-700">Betalningstyp</label>
          <select v-model="remindUnpaid.type" class="input-field">
            <option value="ALL">Alla typer</option>
            <option value="MEMBERSHIP">Medlemsavgift</option>
            <option value="SSFLICENSE">Tävlingslicens</option>
            <option value="COMPETITION">Tävlingsavgift</option>
            <option value="OTHER">Övrig avgift</option>
          </select>
        </div>
        <button @click="preview('remindUnpaid')" :disabled="remindUnpaid.loading" class="btn-secondary">
          <i v-if="remindUnpaid.loading" class="fa fa-spinner fa-spin mr-1"></i>
          Förhandsgranska
        </button>
      </div>
      <PreviewResult :tool="remindUnpaid" @execute="execute('remindUnpaid')" />
      <div v-if="remindUnpaid.previewData && remindUnpaid.previewData.items && remindUnpaid.previewData.items.length > 0" class="mt-4">
        <div class="max-h-64 overflow-y-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Namn</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Typ</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">År</th>
                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Belopp</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, i) in remindUnpaid.previewData.items" :key="i" class="border-t border-gray-200">
                <td class="px-3 py-2">{{ item.user }}</td>
                <td class="px-3 py-2 text-gray-500">{{ item.email }}</td>
                <td class="px-3 py-2">{{ item.type }}</td>
                <td class="px-3 py-2">{{ item.year }}</td>
                <td class="px-3 py-2 text-right">{{ item.amount }} kr</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </ToolCard>
  </div>
</template>

<script>
const defaultToolState = () => ({
  loading: false,
  executing: false,
  previewData: null,
  result: null,
  error: null,
  progress: null,
})

const ToolCard = {
  props: ['title', 'description'],
  template: `
    <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
      <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 cursor-pointer" @click="open = !open">
        <div class="flex justify-between items-center">
          <div>
            <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ description }}</p>
          </div>
          <i class="fa text-gray-400" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
        </div>
      </div>
      <div v-show="open" class="px-6 py-4">
        <slot></slot>
      </div>
    </div>
  `,
  data() {
    return { open: false }
  },
}

const PreviewResult = {
  props: ['tool', 'streaming'],
  emits: ['execute'],
  template: `
    <div v-if="tool.previewData && !tool.result && !tool.executing" class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
      <p class="text-sm text-blue-800">{{ tool.previewData.message }}</p>
      <button
        v-if="tool.previewData.count > 0"
        @click="$emit('execute')"
        class="mt-3 btn-primary"
      >
        Kör
      </button>
    </div>
    <div v-if="tool.result && !tool.executing" class="bg-green-50 border border-green-200 rounded-md p-4 mb-4">
      <p class="text-sm text-green-800"><i class="fa fa-check mr-1"></i> {{ tool.result.message }}</p>
    </div>
    <div v-if="tool.error" class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
      <p class="text-sm text-red-800"><i class="fa fa-exclamation-triangle mr-1"></i> {{ tool.error }}</p>
    </div>
  `,
}

const StreamProgress = {
  props: ['tool'],
  template: `
    <div v-if="tool.executing || (tool.progress && !tool.result)" class="mb-4">
      <div v-if="tool.progress" class="mb-2">
        <div class="flex justify-between text-sm text-gray-600 mb-1">
          <span>{{ tool.progress.message }}</span>
          <span>{{ tool.progress.current }} / {{ tool.progress.total }}</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
          <div
            class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
            :style="{ width: (tool.progress.current / tool.progress.total * 100) + '%' }"
          ></div>
        </div>
      </div>
      <div v-else class="flex items-center gap-2 text-sm text-gray-500">
        <i class="fa fa-spinner fa-spin"></i> Startar...
      </div>
      <div v-if="tool.streamLog && tool.streamLog.length > 0" class="mt-3 max-h-40 overflow-y-auto bg-gray-50 rounded p-2">
        <div v-for="(entry, i) in tool.streamLog" :key="i" class="text-xs py-0.5" :class="entry.status === 'error' ? 'text-red-600' : 'text-gray-600'">
          {{ entry.message }}
        </div>
      </div>
    </div>
  `,
}

export default {
  components: { ToolCard, PreviewResult, StreamProgress },
  props: {
    competitions: { type: Array, required: true },
  },
  data() {
    const currentYear = new Date().getFullYear()
    return {
      memberships: { ...defaultToolState(), year: currentYear },
      licenses: { ...defaultToolState(), year: currentYear },
      competitionPayments: { ...defaultToolState(), competition_id: '', senior_amount: '', junior_amount: '' },
      syncCustomers: { ...defaultToolState(), streamLog: [] },
      createInvoices: { ...defaultToolState(), type: 'MEMBERSHIP', year: currentYear, streamLog: [] },
      emailInvoices: { ...defaultToolState(), streamLog: [] },
      verifyPayments: { ...defaultToolState(), streamLog: [] },
      remindUnpaid: { ...defaultToolState(), type: 'ALL' },
    }
  },
  computed: {
    yearOptions() {
      const y = new Date().getFullYear()
      return [y + 1, y, y - 1]
    },
    csrfToken() {
      const el = document.querySelector('meta[name="csrf-token"]')
      return el ? el.getAttribute('content') : ''
    },
  },
  methods: {
    resetTool(name) {
      const tool = this[name]
      tool.previewData = null
      tool.result = null
      tool.error = null
      tool.progress = null
      if (tool.streamLog) tool.streamLog = []
    },

    async preview(name) {
      const tool = this[name]
      this.resetTool(name)
      tool.loading = true

      const endpoints = {
        memberships: '/admin/payment-tools/generate-memberships/preview',
        licenses: '/admin/payment-tools/generate-licenses/preview',
        competitionPayments: '/admin/payment-tools/competition-payments/preview',
        syncCustomers: '/admin/payment-tools/sync-customers/preview',
        createInvoices: '/admin/payment-tools/create-invoices/preview',
        emailInvoices: '/admin/payment-tools/email-invoices/preview',
        verifyPayments: '/admin/payment-tools/verify-payments/preview',
        remindUnpaid: '/admin/payment-tools/remind-unpaid/preview',
      }

      const params = this.getParams(name)

      try {
        const response = await fetch(endpoints[name], {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
          body: JSON.stringify(params),
        })
        const data = await response.json()

        if (!response.ok) {
          tool.error = data.error || 'Ett fel uppstod.'
        } else {
          tool.previewData = data
        }
      } catch (e) {
        tool.error = 'Kunde inte ansluta till servern.'
      } finally {
        tool.loading = false
      }
    },

    async execute(name) {
      const tool = this[name]
      tool.executing = true
      tool.result = null
      tool.error = null

      const endpoints = {
        memberships: '/admin/payment-tools/generate-memberships/execute',
        licenses: '/admin/payment-tools/generate-licenses/execute',
        competitionPayments: '/admin/payment-tools/competition-payments/execute',
        remindUnpaid: '/admin/payment-tools/remind-unpaid/execute',
      }

      const params = this.getParams(name)

      try {
        const response = await fetch(endpoints[name], {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
          body: JSON.stringify(params),
        })
        const data = await response.json()

        if (!response.ok) {
          tool.error = data.error || 'Ett fel uppstod.'
        } else {
          tool.result = data
          tool.previewData = null
        }
      } catch (e) {
        tool.error = 'Kunde inte ansluta till servern.'
      } finally {
        tool.executing = false
      }
    },

    async executeStream(name) {
      const tool = this[name]
      tool.executing = true
      tool.result = null
      tool.error = null
      tool.progress = null
      tool.streamLog = []

      const endpoints = {
        syncCustomers: '/admin/payment-tools/sync-customers/execute',
        createInvoices: '/admin/payment-tools/create-invoices/execute',
        emailInvoices: '/admin/payment-tools/email-invoices/execute',
        verifyPayments: '/admin/payment-tools/verify-payments/execute',
      }

      const params = this.getParams(name)

      try {
        const response = await fetch(endpoints[name], {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
          body: JSON.stringify(params),
        })

        if (!response.ok) {
          const data = await response.json()
          tool.error = data.error || 'Ett fel uppstod.'
          tool.executing = false
          return
        }

        const reader = response.body.getReader()
        const decoder = new TextDecoder()
        let buffer = ''

        while (true) {
          const { done, value } = await reader.read()
          if (done) break

          buffer += decoder.decode(value, { stream: true })
          const lines = buffer.split('\n')
          buffer = lines.pop()

          for (const line of lines) {
            if (!line.trim()) continue
            try {
              const data = JSON.parse(line)
              if (data.done) {
                tool.result = { message: `Klart! ${data.processed} poster behandlade.${data.paid !== undefined ? ` ${data.paid} markerade som betalda.` : ''}` }
                tool.previewData = null
              } else {
                tool.progress = data
                tool.streamLog.push(data)
              }
            } catch (e) {
              // skip malformed lines
            }
          }
        }
      } catch (e) {
        tool.error = 'Anslutningen avbröts.'
      } finally {
        tool.executing = false
      }
    },

    getParams(name) {
      switch (name) {
        case 'memberships': return { year: this.memberships.year }
        case 'licenses': return { year: this.licenses.year }
        case 'competitionPayments': return {
          competition_id: this.competitionPayments.competition_id,
          senior_amount: this.competitionPayments.senior_amount,
          junior_amount: this.competitionPayments.junior_amount,
        }
        case 'createInvoices': return { type: this.createInvoices.type, year: this.createInvoices.year }
        case 'remindUnpaid': return { type: this.remindUnpaid.type }
        default: return {}
      }
    },
  },
}
</script>

<style scoped>
.input-field {
  @apply rounded-md border-0 py-1.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 text-sm;
}
.btn-secondary {
  @apply px-4 py-2 bg-gray-600 hover:bg-gray-700 disabled:bg-gray-400 text-white text-sm font-medium rounded-md transition-colors duration-200;
}
.btn-primary {
  @apply px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white text-sm font-medium rounded-md transition-colors duration-200;
}
</style>
