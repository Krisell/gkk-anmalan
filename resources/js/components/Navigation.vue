<template>
  <div style="text-align: center;">
    <div>
      <img style="height: 150px;" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%c3%a5f%c3%a4rg-p%c3%a5-m%c3%b6rk-bakgrund-transparent.png">
    </div>
    <div class="actions" v-if="user">
      <gkk-action-card :admin="isAdmin" @admin="location('/admin/competitions')" @click="location('/competitions')" description="Tävlingsanmälan" icon="trophy" :unanswered="unanswered.competitions"></gkk-action-card>
      <gkk-action-card :admin="isAdmin" @admin="location('/admin/events')" @click="location('/events')" description="Funktionärsanmälan" icon="users" :unanswered="unanswered.events"></gkk-action-card>
      <gkk-action-card :admin="isAdmin" @admin="$modal.show('not-implemented')" @click="cooperation" description="Intresseanmälan<br>(Under utveckling)" icon="lightbulb-o"></gkk-action-card>
      <gkk-action-card v-if="isAdmin" @click="location('/admin/accounts')" description="Administrera konton" icon="user"></gkk-action-card>
    </div>
    <div class="actions" v-if="!user">
      <gkk-action-card :admin="isAdmin" @click="location('/login')" description="Logga in" icon="sign-in"></gkk-action-card>
      <gkk-action-card :admin="isAdmin" @click="location('/register')" description="Skapa konto" icon="user"></gkk-action-card>
    </div>

    <modal name="not-implemented" :adaptive="true" height="auto">
      <div style="padding: 30px; margin-top: 20px;">
        <h3 style="text-align: center;">Denna funktion är under utveckling</h3>
      </div>

      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px;">
        <el-button @click="$modal.hide('not-implemented')">Stäng</el-button>
      </div>
    </modal>
  </div>
</template>

<script>
export default {
  props: ['user', 'unanswered'],
  computed: {
    isAdmin () {
      return this.user && this.user.role === 'admin'
    },
  },
  methods: {
    cooperation () {
      this.$modal.show('not-implemented')
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

.actions div.action-button-card {
    margin: 10px;
}

@media (max-width: 1100px) {
  .actions {
    flex-direction: column;
  }
}

</style>