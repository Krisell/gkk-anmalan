<template>
  <div>
    <table class="table">
        <thead>
          <tr>
            <th class="gkk" scope="col">Namn</th>
            <th class="gkk" scope="col">Epost</th>
            <th class="gkk" scope="col">Registreringsdatum</th>
          </tr>
        </thead>
        <tbody>
          <tr class="competition-row" v-for="account in accounts" :key="account.id">
            <td>{{ account.first_name }} {{ account.last_name }}</td>
            <td>{{ account.email }}</td>
            <td>{{ account.created_at | dateString }}</td>
          </tr>
        </tbody>
      </table>
  </div>
</template>

<script>
export default {
  props: ['accounts'],
  filters: {
    dateString (date) {
      return date.substr(0, 10)
    }
  },
}
</script>

<style>

</style>