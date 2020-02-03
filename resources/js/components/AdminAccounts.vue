<template>
  <div>
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
            <td><i v-if="account.role === 'admin'" class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Administratör"></i></td>
          </tr>
        </tbody>
      </table>
  </div>
</template>

<script>
export default {
  props: ['accounts'],
  mounted () {
    $(() => { $('[data-toggle="tooltip"]').tooltip() })
  },
  filters: {
    dateString (date) {
      return date.substr(0, 10)
    }
  },
}
</script>

<style>

</style>