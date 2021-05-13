<template>
  <div class="container mx-auto">
    <div v-if="ungranted.length > 0" class="flex flex-col mb-8">
      <h2 class="text-2xl font-hairline text-center m-4">Väntar på godkännande</h2>
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th
                  class="
                    px-6
                    py-3
                    border-b border-gray-200
                    bg-gray-50
                    text-left text-xs
                    leading-4
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >
                  Namn
                </th>
                <th
                  class="
                    px-6
                    py-3
                    border-b border-gray-200
                    bg-gray-50
                    text-center text-xs
                    leading-4
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >
                  Registreringsdatum
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
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ account.created_at | dateString }}</div>
                </td>

                <td
                  class="px-6 py-2 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium"
                >
                  <ui-button @click="grantAccount(account)">Godkänn konto</ui-button>
                  <ui-button class="ml-2" type="secondary" @click="deleteAccount(account)">Radera konto</ui-button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <h2 class="text-2xl font-hairline text-center m-4">{{ accounts.length }} medlemmar har registrerat ett konto</h2>

    <div class="flex flex-col">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th
                  class="
                    px-6
                    py-3
                    border-b border-gray-200
                    bg-gray-50
                    text-left text-xs
                    leading-4
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >
                  Namn
                </th>
                <th
                  class="
                    px-6
                    py-3
                    border-b border-gray-200
                    bg-gray-50
                    text-center text-xs
                    leading-4
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >
                  Bekräftad närvaro som funktionär<br />(senaste 365 dagarna)
                </th>
                <th
                  class="
                    px-6
                    py-3
                    border-b border-gray-200
                    bg-gray-50
                    text-center text-xs
                    leading-4
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >
                  Registreringsdatum
                </th>
                <th
                  class="
                    px-6
                    py-3
                    border-b border-gray-200
                    bg-gray-50
                    text-center text-xs
                    leading-4
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >
                  Senaste besök
                </th>
                <th
                  class="
                    px-6
                    py-3
                    border-b border-gray-200
                    bg-gray-50
                    text-center text-xs
                    leading-4
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >
                  Antal besök
                </th>
                <th
                  class="
                    px-6
                    py-3
                    border-b border-gray-200
                    bg-gray-50
                    text-center text-xs
                    leading-4
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >
                  Godkänt avtalen
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="account in accounts" :key="account.id">
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
                  <div class="text-sm leading-5 text-gray-500 text-center">
                    {{ presentTotal(account.event_registrations) }}<br />({{
                      presentLastYear(account.event_registrations)
                    }})
                  </div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ account.created_at | dateString }}</div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">
                    {{ account.last_visited_at | dateString }}
                  </div>
                </td>
                <td
                  class="
                    px-6
                    py-2
                    whitespace-no-wrap
                    border-b border-gray-200
                    text-sm
                    leading-5
                    text-gray-500 text-center
                  "
                >
                  {{ account.visits }}
                </td>
                <td
                  class="
                    px-6
                    py-2
                    whitespace-no-wrap
                    border-b border-gray-200
                    text-sm
                    leading-5
                    text-gray-500 text-center
                  "
                >
                  <i
                    class="fa fa-check-circle text-gkk text-lg"
                    v-if="account.membership_agreement_signed_at && account.anti_doping_agreement_signed_at"
                  ></i>
                </td>
                <td
                  class="px-6 py-2 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium"
                >
                  <div v-if="user.role === 'superadmin'">
                    <i
                      @click="confirmDemotion(account)"
                      v-if="account.role === 'admin'"
                      style="cursor: pointer"
                      class="fa fa-star"
                      v-tooltip="'Administratör, klicka för att ta bort rollen'"
                    ></i>
                    <i
                      v-else-if="account.role === 'superadmin'"
                      class="fa fa-star"
                      v-tooltip="'Superadministratör'"
                    ></i>
                    <i
                      @click="confirmPromotion(account)"
                      v-else
                      class="fa fa-star-o"
                      style="cursor: pointer"
                      v-tooltip="'Gör till administratör'"
                    ></i>
                  </div>
                  <div v-else>
                    <i v-if="account.role === 'admin'" class="fa fa-star" v-tooltip="'Administratör'"></i>
                    <i v-if="account.role === 'superadmin'" class="fa fa-star" v-tooltip="'Superadministratör'"></i>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <GkkLink to="/" text="Tillbaka till startsidan" />

    <modal name="promote" :adaptive="true" height="auto">
      <div style="padding: 30px; margin-top: 20px">
        <h3 style="text-align: center">
          Är du säker på att du vill göra {{ selectedAccount && selectedAccount.email }} till administratör?
        </h3>
      </div>

      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px">
        <el-button secondary @click="$modal.hide('promote')">Nej</el-button>
        <el-button @click="promotion" danger primary style="margin-left: 20px">Ja, gör till administratör</el-button>
      </div>
    </modal>

    <modal name="demote" :adaptive="true" height="auto">
      <div style="padding: 30px; margin-top: 20px">
        <h3 style="text-align: center">
          Är du säker på att du vill ta bort administratörsrollen för {{ selectedAccount && selectedAccount.email }}?
        </h3>
      </div>

      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px">
        <el-button secondary @click="$modal.hide('demote')">Nej</el-button>
        <el-button @click="demotion" danger primary style="margin-left: 20px">Ja, ta bort</el-button>
      </div>
    </modal>
  </div>
</template>

<script>
import Date from '../modules/Date.js'

export default {
  props: ['accounts', 'user', 'ungranted'],
  data() {
    return {
      selectedAccount: null,
      grantStatus: '',
    }
  },
  filters: {
    dateString(date) {
      if (!date) {
        return ''
      }

      return date.substr(0, 10)
    },
  },
  methods: {
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
    promotion() {
      axios.post(`/admin/accounts/promote/${this.selectedAccount.id}`).then(() => this.reload())
    },
  },
}
</script>
