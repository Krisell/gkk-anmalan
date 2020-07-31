<template>
  <div class="container mx-auto">
    <h2 class="text-2xl font-hairline text-center m-4">{{ accounts.length }} medlemmar har registrerat ett konto</h2>

    <div class="flex flex-col">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Namn
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Registreringsdatum
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Senaste besök
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Antal besök
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="account in accounts" :key="account.id">
                <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm leading-5 font-medium text-gray-900">{{ account.first_name }} {{ account.last_name }}</div>
                      <div class="text-sm leading-5 text-gray-500">{{ account.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ account.created_at | dateString }}</div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ account.last_visited_at | dateString }}</div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  {{ account.visits }}
                </td>
                <td class="px-6 py-2 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                  <div v-if="user.role === 'superadmin'">
                    <i @click="confirmDemotion(account)" v-if="account.role === 'admin'" style="cursor: pointer;"  class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Administratör, klicka för att ta bort rollen"></i>
                    <i v-else-if="account.role === 'superadmin'" class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Superadministratör"></i>
                    <i @click="confirmPromotion(account)" v-else class="fa fa-star-o" style="cursor: pointer;" data-toggle="tooltip" data-placement="top" title="Gör till administratör"></i>
                  </div>
                  <div v-else>
                    <i v-if="account.role === 'admin'" class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Administratör"></i>
                    <i v-if="account.role === 'superadmin'" class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Superadministratör"></i>
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
      <div style="padding: 30px; margin-top: 20px;">
        <h3 style="text-align: center;">Är du säker på att du vill göra {{ selectedAccount && selectedAccount.email }} till administratör?</h3>
      </div>

      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px;">
        <el-button secondary @click="$modal.hide('promote')">Nej</el-button>
        <el-button @click="promotion" danger primary style="margin-left: 20px;">Ja, gör till administratör</el-button>
      </div>
    </modal>

    <modal name="demote" :adaptive="true" height="auto">
      <div style="padding: 30px; margin-top: 20px;">
        <h3 style="text-align: center;">Är du säker på att du vill ta bort administratörsrollen för {{ selectedAccount && selectedAccount.email }}?</h3>
      </div>

      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px;">
        <el-button secondary @click="$modal.hide('demote')">Nej</el-button>
        <el-button @click="demotion" danger primary style="margin-left: 20px;">Ja, ta bort</el-button>
      </div>
    </modal>
  </div>
</template>

<script>
export default {
  props: ['accounts', 'user'],
  data () {
    return {
      selectedAccount: null,
    }
  },
  mounted () {
    $(() => { $('[data-toggle="tooltip"]').tooltip() })
  },
  filters: {
    dateString (date) {
      if (!date) {
        return ''
      }

      return date.substr(0, 10)
    }
  },
  methods: {
    confirmDemotion (account) {
      this.selectedAccount = account
      this.$modal.show('demote')
    },
    demotion () {
      axios.post(`/admin/accounts/demote/${this.selectedAccount.id}`).then(() => this.reload())
    },
    confirmPromotion (account) {
      this.selectedAccount = account
      this.$modal.show('promote')
    },
    promotion () {
      axios.post(`/admin/accounts/promote/${this.selectedAccount.id}`).then(() => this.reload())
    },
  },
}
</script>

<style>

</style>