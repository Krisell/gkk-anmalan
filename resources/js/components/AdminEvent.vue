<template>
  <div>
    <h1>Administrera funktionärsanmälan</h1>

    <div class="type">
      <h3>Anmälningar</h3>
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Namn</th>
            <th scope="col">Datum</th>
            <th scope="col">Kan hjälpa till</th>
            <th scope="col">Kommentar</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="registration in event.registrations" :key="registration.id" style="cursor: pointer;">
            <td>{{ registration.user.first_name }} {{ registration.user.last_name }}</td>
            <td>{{ registration.created_at | dateString}}</td>
            <td>{{ registration.status == 1 ? 'Ja' : 'Nej' }}</td>
            <td style="max-width: 200px;">{{ registration.comment }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <GkkLink to="/admin" text="Tillbaka till adminsidan" />
  </div>
</template>

<script>
export default {
  props: ['event'],
  filters: {
    dateString (date) {
      return date.substr(0, 10)
    }
  },
}
</script>

<style scoped lang="less">
  .type {
    margin-bottom: 40px;
  }
</style>
