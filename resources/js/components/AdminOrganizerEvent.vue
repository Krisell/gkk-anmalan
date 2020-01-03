<template>
  <div>
    <h1 style="text-align: center;">Administrera funktionärsanmälan</h1>

    <div class="type">
      <h3>Anmälningar</h3>
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Namn</th>
            <th scope="col">Datum</th>
            <th scope="col">Kommentar</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="registration in event.registrations" :key="registration.id" style="cursor: pointer;">
            <td>{{ registration.name }}</td>
            <td>{{ registration.create_at }}</td>
            <td>{{ event.comment }}</td>
            <th scope="row"><i class="fa fa-th-list"></i></th>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  props: ['event'],
}
</script>

<style scoped lang="less">
  .type {
    margin-bottom: 40px;
  }
</style>
