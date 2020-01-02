<template>
  <div class="actions">
    <action-card description="Tävlingsanmälan för GKK-aktiv" icon="trophy"></action-card>
    <action-card description="Funktionärsanmälan för GKK-aktiva" icon="users"></action-card>
    <action-card description="Intresseanmälan från andra föreningar att tävla med GKK" icon="lightbulb-o"></action-card>
    <action-card @click="admin" description="Admin (endast administratörer)" icon="lock"></action-card>
  </div>
</template>

<script>
export default {
  methods: {
    admin () {
      console.log("ADMIN")
      window.location = '/admin'
    }
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