<template>
  <div>
    <h2 style="margin: 20px;">{{ accounts.length }} medlemmar har registrerat ett konto</h2>
    <table class="table">
        <thead>
          <tr>
            <th class="gkk" scope="col">Namn</th>
            <th class="gkk" scope="col">Epost</th>
            <th class="gkk" scope="col">Registreringsdatum</th>
            <th class="gkk" scope="col">Antal besök</th>
            <th class="gkk" scope="col">Senaste besök</th>
            <th class="gkk" scope="col"></th>
          </tr>
        </thead>
        <tbody>
          <tr class="competition-row" v-for="account in accounts" :key="account.id">
            <td>{{ account.first_name }} {{ account.last_name }}</td>
            <td>{{ account.email }}</td>
            <td>{{ account.created_at | dateString }}</td>
            <td>{{ account.visits }}</td>
            <td>{{ account.last_visited_at | dateString }}</td>
            <td v-if="user.role === 'superadmin'">
              <i @click="confirmDemotion(account)" v-if="account.role === 'admin'" style="cursor: pointer;"  class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Administratör, klicka för att ta bort rollen"></i>
              <i v-else-if="account.role === 'superadmin'" class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Superadministratör"></i>
              <i @click="confirmPromotion(account)" v-else class="fa fa-star-o" style="cursor: pointer;" data-toggle="tooltip" data-placement="top" title="Gör till administratör"></i>
            </td>
            <td v-else>
              <i v-if="account.role === 'admin'" class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Administratör"></i>
              <i v-if="account.role === 'superadmin'" class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Superadministratör"></i>
            </td>
          </tr>
        </tbody>
      </table>

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