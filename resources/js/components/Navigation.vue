<template>
  <div style="text-align: center;">
    <div>
      <img style="height: 150px;" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%c3%a5f%c3%a4rg-p%c3%a5-m%c3%b6rk-bakgrund-transparent.png">
    </div>
    <div class="actions">
      <gkk-action-card @click="competition" description="Tävlingsanmälan" icon="trophy"></gkk-action-card>
      <gkk-action-card @click="event" description="Funktionärsanmälan" icon="users"></gkk-action-card>
      <gkk-action-card @click="cooperation" description="Intresseanmälan" icon="lightbulb-o"></gkk-action-card>
      <gkk-action-card @click="admin" description='Admin<br><span style="font-size: 10px;">(Endast administratörer)</span>' icon="lock"></gkk-action-card>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    admin () {
      window.location = '/admin'
    },
    event () {
      window.location = '/events'
    },
    competition () {

    },
    cooperation () {

    },
  }
}
</script>

<style>
.actions {
    display: flex;
    margin: 30px;
    flex-direction: row;
}

.actions div {
    margin: 10px;
}

@media (max-width: 1100px) {
  .actions {
    flex-direction: column;
  }
}

</style>