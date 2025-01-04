<template>
  <div class="container mx-auto">
    <div v-if="ungranted.length > 0" class="flex flex-col mb-8">
      <h2 class="text-2xl font-thin text-center m-4">Väntar på godkännande</h2>
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Namn
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Datum reg.
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="account in ungranted" :key="account.id">
                <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm leading-5 font-medium text-gray-900">
                        {{ account.first_name }} {{ account.last_name }}
                      </div>
                      <div class="text-sm leading-5 text-gray-500">{{ account.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ dateString(account.created_at) }}</div>
                </td>

                <td
                  class="px-6 py-2 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                  <Button @click="grantAccount(account)">Godkänn konto</Button>
                  <Button class="ml-2" type="secondary" @click="deleteAccount(account)">Radera konto</Button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <h2 class="text-2xl font-thin text-center m-4">
      Lista med {{ sortedActiveAccounts.length }} aktiva konton
      <i style="margin-left: 20px; cursor: pointer"
        v-tooltip="'Klicka för att kopiera epostadresserna för alla ej inaktiverade konton'" @click="copyEmails"
        class="fa fa-envelope"></i>
    </h2>

    <div class="flex flex-col">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <div>
            <div class="relative my-4 w-64 ml-4 lg:mx-auto flex gap-2 text-sm font-thin">
              <ToggleButton v-model="treasurerMode"  />
              Kassörsläge
            </div>
            <div class="relative my-4 rounded-md shadow-sm w-64 ml-4 lg:mx-auto">
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
                class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6" placeholder="Sök medlem" 
              />
            </div>
          </div>
          <table class="min-w-full">
            <thead>
              <tr>
                <th @click="sortBy('first_name')"
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Namn
                </th>
                <th
                @click="sortBy('event_registrations')"
                  v-show="!treasurerMode"
                  class="cursor-pointer px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                  v-tooltip="'Bekräftad närvaro som funktionär. Inom parentes senaste 365 dagarna'"
                >
                  Bekr. funk.<br />(senaste 365 dagarna)
                </th>
                <th @click="sortBy('created_at')"
                  v-show="!treasurerMode"
                  class="w-[130px] px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Regdatum
                </th>
                <th @click="sortBy('last_visited_at')"
                  v-show="!treasurerMode"
                  class="w-[130px] px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Senaste besök
                </th>
                <th @click="sortBy('visits')"
                  v-show="!treasurerMode"
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Antal besök
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Avtal
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Licens.avg. 2025
                </th>
                <!-- <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Medl.avg. {{ getCurrentYear() + 1 }}
                </th> -->
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Medl.avg. {{ getCurrentYear() }}
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Medl.avg. {{ getCurrentYear() - 1 }}
                </th>
                <th v-show="!treasurerMode" class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="account in filteredSortedActiveAccounts" :key="account.id">
                <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm leading-5 font-medium text-gray-900">
                        {{ account.first_name }} {{ account.last_name }} 
                        <i
                        v-tooltip="{content: `${account.email}<br>Klicka för att kopiera.`, html: true}" @click="copyEmail(account.email)"
                        class="fa fa-envelope-o ml-2 cursor-pointer"></i>
                        <i v-if="account.is_honorary_member" class="fa fa-trophy text-gkk ml-2"
                        v-tooltip="'Hedersmedlem'"></i>
                        <i v-if="isJunior(account)" class="fa fa-child text-gkk ml-2"
                        v-tooltip="'Ungdom/junior'"></i>
                      </div>
                      <span v-if="account.licence_number" class="text-xs font-light">
                        {{ account.licence_number }}
                      </span>
                    </div>
                  </div>
                </td>
                <td v-show="!treasurerMode" class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">
                    {{ presentTotal(account.event_registrations) }}<br />({{
                      presentLastYear(account.event_registrations)
                    }})
                  </div>
                </td>
                <td v-show="!treasurerMode" class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ dateString(account.created_at) }}</div>
                </td>
                <td v-show="!treasurerMode" class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">
                    {{ dateString(account.last_visited_at) }}
                  </div>
                </td>
                <td
                  v-show="!treasurerMode"
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  {{ account.visits }}
                </td>
                <td
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  <i class="fa fa-check-circle text-gkk text-lg"
                    v-if="account.membership_agreement_signed_at && account.anti_doping_agreement_signed_at"></i>
                </td>
                <td
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  <ToggleButton 
                    v-if="showPaymentToggle(account, 2025, 'SSFLICENSE')" 
                    @update:modelValue="updatePayment(account, 2025, 'SSFLICENSE')"
                    :modelValue="hasPaid(account, 2025, 'SSFLICENSE')" 
                  />
                </td>
                <!-- <td
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  <span v-if="account.is_honorary_member" class="text-xs italic">Hedersmedlem</span>
                  <ToggleButton 
                    v-else-if="showPaymentToggle(account, getCurrentYear() + 1, 'MEMBERSHIP')" 
                    @update:modelValue="$event => updatePayment(account, getCurrentYear() + 1, 'MEMBERSHIP')"
                    :modelValue="hasPaid(account, getCurrentYear() + 1, 'MEMBERSHIP')" 
                  />
                </td> -->
                <td
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  <span v-if="account.is_honorary_member" class="text-xs italic">Hedersmedlem</span>
                  <ToggleButton 
                    v-else-if="showPaymentToggle(account, getCurrentYear(), 'MEMBERSHIP')" 
                    @update:modelValue="updatePayment(account, getCurrentYear(), 'MEMBERSHIP')"
                    :modelValue="hasPaid(account, getCurrentYear(), 'MEMBERSHIP')" 
                  />
                </td>
                <td
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  <span v-if="account.is_honorary_member" class="text-xs italic">Hedersmedlem</span>
                  <ToggleButton 
                    v-else-if="showPaymentToggle(account, getCurrentYear() - 1, 'MEMBERSHIP')" 
                    @update:modelValue="updatePayment(account, getCurrentYear() - 1, 'MEMBERSHIP')"
                    :modelValue="hasPaid(account, getCurrentYear() - 1, 'MEMBERSHIP')" 
                  />
                </td>
                <td
                  v-show="!treasurerMode"
                  class="px-6 py-2 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                  <div class="flex items-center gap-2">
                    <div v-if="user.role === 'superadmin'">
                      <i @click="confirmDemotion(account)" v-if="account.role === 'admin'" style="cursor: pointer"
                        class="fa fa-star" v-tooltip="'Administratör, klicka för att ta bort rollen'"></i>
                      <i v-else-if="account.role === 'superadmin'" class="fa fa-star"
                        v-tooltip="'Superadministratör'"></i>
                      <i @click="confirmPromotion(account)" v-else class="fa fa-star-o" style="cursor: pointer"
                        v-tooltip="'Gör till administratör'"></i>
                    </div>
                    <div v-else>
                      <i v-if="account.role === 'admin'" class="fa fa-star" v-tooltip="'Administratör'"></i>
                      <i v-if="account.role === 'superadmin'" class="fa fa-star" v-tooltip="'Superadministratör'"></i>
                    </div>
                    <i @click="confirmInactivation(account)" class="fa fa-ban cursor-pointer"
                      v-tooltip="'Inaktivera konto – personen kommer inte kunna logga in förrän kontot återaktiveras.'">
                    </i>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="flex flex-col mt-4 items-center justify-center">
      <h2 class="text-xl font-thin text-center m-4">Lägg till nya konton</h2>
      <div class="w-1/2 font-thin mb-4">
        Nedan kan du klistra in konton från exempelvis Excel, eller skriva manuellt. Ange dessa i formatet "Förnamn
        Efternamn Epost" där antingen tabb eller ; (semikolon) avgränsar fälten. Ange en person per rad.
      </div>
      <div class="w-1/2 font-thin mb-4">
        Epostadresser som redan finns i systemet kommer ignoreras. Tillagda konton kommer automatiskt skicka ut ett
        epost till användaren med en länk för att logga in och ange ett lösenord.
      </div>
      <textarea v-model="newAccountsString" class="mx-auto w-1/2 h-32 border rounded p-2"></textarea>
      <Button @click="createAccounts" class="mt-2">Skapa konton</Button>
    </div>

    <div v-if="inactiveAccounts.length > 0" class="flex flex-col mb-8 mt-8">
      <h2 class="text-2xl font-thin text-center m-4">{{ inactiveAccounts.length }} inaktiverade konton</h2>
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Namn
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Registreringsdatum
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="account in inactiveAccounts" :key="account.id">
                <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm leading-5 font-medium text-gray-900">
                        {{ account.first_name }} {{ account.last_name }} 
                        <i
                        v-tooltip="`${account.email}<br>Klicka för att kopiera.`" @click="copyEmail(account.email)"
                        class="fa fa-envelope-o ml-2 cursor-pointer"></i>
                      </div>
                      <span v-if="account.licence_number" class="text-xs font-light">
                        {{ account.licence_number }}
                      </span>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ dateString(account.created_at) }}</div>
                </td>

                <td
                  class="px-6 py-2 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                  <Button @click="reactivate(account)">Återaktivera konto</Button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <Modal ref="promoteModal" :title="`Är du säker på att du vill göra ${selectedAccount && selectedAccount.email} till administratör?`">
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button @click="close" type="secondary">Nej</Button>
          <Button @click="promote" type="danger">Ja, gör till administratör</Button>
        </div>
      </template>
    </Modal>

    <Modal ref="demoteModal" :title="`Är du säker på att du vill ta bort administratörsrollen för ${selectedAccount && selectedAccount.email}?`">
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button @click="close" type="secondary">Nej</Button>
          <Button @click="demote" type="danger">Ja, ta bort</Button>
        </div>
      </template>
    </Modal>

    <Modal ref="inactivateModal" :title="`Är du säker på att du vill inaktiverat kontot för ${selectedAccount && selectedAccount.email}?`">
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button @click="close" type="secondary">Nej</Button>
          <Button @click="inactivate" type="danger">Ja, inaktivera</Button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script>
import ToggleButton from './ui/ToggleButton.vue'
import Modal from './ui/Modal.vue'
import Button from './ui/Button.vue'
import Date from '../modules/Date.js'

export default {
  components: { Button, ToggleButton, Modal },
  props: {
    initialAccounts: {
      type: Array,
      required: true,
    },
    user: {
      type: Object,
      required: true,
    },
    ungranted: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      selectedAccount: null,
      grantStatus: '',
      sortKey: 'visits',
      sortOrder: -1,
      newAccountsString: '',
      accounts: this.initialAccounts,
      search: '',
      treasurerMode: false,
    }
  },
  async mounted() {
    const url = new URL(window.location.href)
    const sort = url.searchParams.get('sort')
    const order = url.searchParams.get('order')

    if (sort) {
      this.sortKey = sort
    }

    if (order) {
      this.sortOrder = parseInt(order)
    }
  },
  computed: {
    filteredSortedActiveAccounts() {
      if (this.search === '') {
        return this.sortedActiveAccounts
      }

      // Fuzzy search on full name and email string
      return this.sortedActiveAccounts.filter((account) => {
        const search = this.search.toLowerCase()
        const fullName = `${account.first_name} ${account.last_name}`.toLowerCase()
        const email = account.email.toLowerCase()

        return fullName.includes(search) || email.includes(search)
      })
    },
    sortedActiveAccounts() {
      return this.accounts
        .filter((account) => account.inactivated_at === null)
        .sort((a, b) => {
          if (this.sortKey === 'event_registrations') {
            return this.sortOrder * (this.presentLastYear(b.event_registrations) - this.presentLastYear(a.event_registrations))
          }

          return this.sortOrder *
            String(a[this.sortKey]).localeCompare(String(b[this.sortKey]), undefined, { numeric: true })
        }
        )
    },
    inactiveAccounts() {
      return this.accounts.filter((account) => account.inactivated_at !== null)
    },
  },
  methods: {
    async loadAccounts() {
      const response = await axios.get('/admin/accounts')
      this.accounts = response.data
    },
    dateString(date) {
      if (!date) {
        return ''
      }

      return date.substr(0, 10)
    },
    copyEmail(email) {
      navigator.clipboard.writeText(email)
      this.$toast.success(`Epostadress kopierad: ${email}`)
    },
    copyEmails() {
      navigator.clipboard.writeText(
        this.accounts
          .filter((account) => account.inactivated_at === null)
          .map((account) => account.email)
          .sort((a, b) => a.localeCompare(b))
          .join('; '),

        this.$toast.success('Epostadresser kopierade')
      )
    },
    sortBy(key) {
      if (this.sortKey === key) {
        this.sortOrder *= -1
      }

      this.sortKey = key

      // Update query string to reflect sort order
      const url = new URL(window.location.href)
      url.searchParams.set('sort', this.sortKey)
      url.searchParams.set('order', this.sortOrder)
      window.history.replaceState({}, '', url)
    },
    grantAccount(account) {
      if (this.grantStatus !== '') {
        return
      }

      this.grantStatus = 'pending'
      axios.post(`/admin/accounts/${account.id}/grant`).then(() => {
        window.location.reload()
      })
    },
    deleteAccount(account) {
      if (this.grantStatus !== '') {
        return
      }

      this.grantStatus = 'pending'
      axios.delete(`/admin/accounts/${account.id}/grant`).then(() => {
        window.location.reload()
      })
    },
    getCurrentYear() {
      return new window.Date().getFullYear()
    },
    presentTotal(registrations) {
      return registrations.filter((registration) => registration.presence_confirmed).length
    },
    presentLastYear(registrations) {
      return registrations.filter(
        (registration) => registration.presence_confirmed && Date.withinAYear(registration.event.date),
      ).length
    },
    confirmDemotion(account) {
      this.selectedAccount = account
      this.$refs.demoteModal.show()
    },
    demote() {
      axios.post(`/admin/accounts/demote/${this.selectedAccount.id}`).then(() => this.reload())
    },
    confirmPromotion(account) {
      this.selectedAccount = account
      this.$refs.promoteModal.show()
    },
    confirmInactivation(account) {
      this.selectedAccount = account
      this.$refs.inactivateModal.show()
    },
    promote() {
      axios.post(`/admin/accounts/promote/${this.selectedAccount.id}`).then(() => this.reload())
    },
    inactivate() {
      axios.post(`/admin/accounts/inactivate/${this.selectedAccount.id}`).then(() => this.reload())
    },
    reactivate(account) {
      axios.post(`/admin/accounts/reactivate/${account.id}`).then(() => this.reload())
    },
    async createAccounts() {
      const lines = this.newAccountsString.split(/\n+/).filter((line) => line.length > 0)

      const accounts = lines.map((line) => {
        const [firstName, lastName, email] = line.split(/[\t;]+/)
        return { firstName, lastName, email }
      })

      const response = await axios.post('/admin/accounts', { accounts })

      console.log(response)
    },
    hasPaid(user, year, paymentType) {
      return user.payments.some(payment => payment.type === paymentType && payment.year === year && payment.state === 'PAID')
    },
    showPaymentToggle(user, year, paymentType) {
      return user.payments.some(payment => payment.type === paymentType && payment.year === year)
    },
    async updatePayment(user, year, paymentType) {
      const paymentTypeMessage = paymentType == 'MEMBERSHIP' ? 'Medlemsavgiften' : 'Licens'
      const payment = user.payments.find(payment => payment.type === paymentType && payment.year === year)
      const name = `${user.first_name} ${user.last_name}`

      if (this.hasPaid(user, year, paymentType)) {
        await axios.patch(`/payments/${payment.id}`, { state: null })
        this.$toast.warning(`${paymentTypeMessage} för ${name} har markerats som obetald`)
      } else {
        await axios.patch(`/payments/${payment.id}`, { state: 'PAID' })
        this.$toast.info(`${paymentTypeMessage} för ${name} har markerats som betald`)
      }

      this.loadAccounts()
    },
    isJunior(account) {
      return new window.Date().getFullYear() - account.birth_year <= 23
    },
  },
}
</script>
