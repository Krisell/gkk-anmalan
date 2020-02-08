<template>
  <div>
    <h2 style="margin: 20px;">Totalt har {{ accounts.length }} medlemmar skapat konto</h2>
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
            <td>
              <i v-if="account.role === 'admin'" class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Administratör"></i>
              <i @click="confirmPromotion(account)" v-if="account.role !== 'admin' && user.role === 'admin'" class="fa fa-star-o" style="cursor: pointer;" data-toggle="tooltip" data-placement="top" title="Gör till administratör"></i>
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
      return date.substr(0, 10)
    }
  },
  methods: {
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