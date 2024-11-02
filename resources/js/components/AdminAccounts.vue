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
      {{ accounts.length }} medlemmar har registrerat ett konto
      <i style="margin-left: 20px; cursor: pointer"
        v-tooltip="'Klicka för att kopiera epostadresserna för alla ej inaktiverade konton'" @click="copyEmails"
        class="fa fa-envelope"></i>
    </h2>

    <div class="flex flex-col">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th @click="sortBy('first_name')"
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Namn
                </th>
                <th
                @click="sortBy('event_registrations')"
                  class="cursor-pointer px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                  v-tooltip="'Bekräftad närvaro som funktionär. Inom parentes senaste 365 dagarna'"
                >
                  Bekr. funk.<br />(senaste 365 dagarna)
                </th>
                <th @click="sortBy('created_at')"
                  class="w-[130px] px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Regdatum
                </th>
                <th @click="sortBy('last_visited_at')"
                  class="w-[130px] px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Senaste besök
                </th>
                <th @click="sortBy('visits')"
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Antal besök
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Godkänt avtalen
                </th>
                <th v-if="getCurrentYear() > 2024"
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Licens.avg. {{ getCurrentYear() }}
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Medl.avg. {{ getCurrentYear() }}
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Medl.avg. {{ getCurrentYear() - 1 }}
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Medl.avg. {{ getCurrentYear() - 2 }}
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="account in sortedActiveAccounts" :key="account.id">
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
                  <div class="text-sm leading-5 text-gray-500 text-center">
                    {{ presentTotal(account.event_registrations) }}<br />({{
                      presentLastYear(account.event_registrations)
                    }})
                  </div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ dateString(account.created_at) }}</div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">
                    {{ dateString(account.last_visited_at) }}
                  </div>
                </td>
                <td
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  {{ account.visits }}
                </td>
                <td
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  <i class="fa fa-check-circle text-gkk text-lg"
                    v-if="account.membership_agreement_signed_at && account.anti_doping_agreement_signed_at"></i>
                </td>
                <td v-if="getCurrentYear() > 2024"
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  <ToggleButton @input="updateMembershipPayment(account, getCurrentYear(), 'SSFLICENSE')"
                    :value="hasPaid(account, getCurrentYear(), 'SSFLICENSE')" color="#314270" />
                </td>
                <td
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  <ToggleButton @input="updateMembershipPayment(account, getCurrentYear(), 'MEMBERSHIP')"
                    :value="hasPaid(account, getCurrentYear(), 'MEMBERSHIP')" color="#314270" />

                </td>
                <td
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  <ToggleButton @input="updateMembershipPayment(account, getCurrentYear() - 1, 'MEMBERSHIP')"
                    :value="hasPaid(account, getCurrentYear() - 1, 'MEMBERSHIP')" color="#314270" />
                </td>
                <td
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  <ToggleButton @input="updateMembershipPayment(account, getCurrentYear() - 2, 'MEMBERSHIP')"
                    :value="hasPaid(account, getCurrentYear() - 2, 'MEMBERSHIP')" color="#314270" />
                </td>
                <td
                  class="px-6 py-2 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
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
                </td>
                <td
                  class="px-6 py-2 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                  <i @click="confirmInactivation(account)" class="fa fa-ban cursor-pointer"
                    v-tooltip="'Inaktivera konto – personen kommer inte kunna logga in förrän kontot återaktiveras.'"></i>
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
      <textarea v-model="newAccountsString" class="mx-auto w-1/2 h-32"></textarea>
      <Button @click="createAccounts" class="mt-2">Skapa konton</Button>
    </div>

    <div v-if="inactiveAccounts.length > 0" class="flex flex-col mb-8 mt-8">
      <h2 class="text-2xl font-thin text-center m-4">Inaktiverade konton</h2>
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

    <modal name="promote" :adaptive="true" height="auto">
      <div style="padding: 30px; margin-top: 20px">
        <h3 style="text-align: center">
          Är du säker på att du vill göra {{ selectedAccount && selectedAccount.email }} till administratör?
        </h3>
      </div>

      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px">
        <Button @click="$modal.hide('promote')" type="secondary" class="mx-4">Nej</Button>
        <Button @click="promotion" type="danger">Ja, gör till administratör</Button>
      </div>
    </modal>

    <modal name="demote" :adaptive="true" height="auto">
      <div style="padding: 30px; margin-top: 20px">
        <h3 style="text-align: center">
          Är du säker på att du vill ta bort administratörsrollen för {{ selectedAccount && selectedAccount.email }}?
        </h3>
      </div>

      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px">
        <Button @click="$modal.hide('demote')" type="secondary" class="mx-4">Nej</Button>
        <Button @click="demotion" type="danger">Ja, ta bort</Button>
      </div>
    </modal>

    <modal name="inactivate" :adaptive="true" height="auto">
      <div style="padding: 30px; margin-top: 20px">
        <h3 style="text-align: center">
          <i class="fa fa-ban text-3xl mb-8"></i>
        </h3>
        <h3 style="text-align: center">
          Är du säker på att du vill inaktiverat kontot för {{ selectedAccount && selectedAccount.email }}?
        </h3>
      </div>

      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px">
        <Button @click="$modal.hide('inactivate')" type="secondary" class="mx-4">Nej</Button>
        <Button @click="inactivate" type="danger">Ja, inaktivera</Button>
      </div>
    </modal>
  </div>
</template>

<script>
import ToggleButton from './ui/ToggleButton.vue'
import Button from './ui/Button.vue'
import Date from '../modules/Date.js'

export default {
  components: { Button, ToggleButton },
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
      accounts: this.initialAccounts
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
      var currentDate = new window.Date();
      console.log(currentDate);
      return currentDate.getFullYear();
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
      this.$modal.show('demote')
    },
    demotion() {
      axios.post(`/admin/accounts/demote/${this.selectedAccount.id}`).then(() => this.reload())
    },
    confirmPromotion(account) {
      this.selectedAccount = account
      this.$modal.show('promote')
    },
    confirmInactivation(account) {
      this.selectedAccount = account
      this.$modal.show('inactivate')
    },
    promotion() {
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
      return user.payments.some(payment => payment.type === paymentType && payment.year === year)
    },
    async updateMembershipPayment(user, year, paymentType) {
      const name = user.first_name + ' ' + user.last_name
      const paymentTypeMessage = paymentType == 'MEMBERSHIP' ? 'Medlemsavgiften' : 'Licens'
      if (this.hasPaid(user, year, paymentType)) {
        const payment = user.payments.find(payment => payment.type === paymentType && payment.year === year)
        await axios.delete(`/admin/accounts/payment/${payment.id}`)

        this.$toast.warning(`${paymentTypeMessage} för ${name} har markerats som obetald`);

        this.loadAccounts()
        return
      }

      await axios.post(`/admin/accounts/payment/${user.id}`, { type: paymentType, year })

      this.$toast.info(`${paymentTypeMessage} för ${name} har markerats som betald`);

      this.loadAccounts()
    },
  },
}
</script>
