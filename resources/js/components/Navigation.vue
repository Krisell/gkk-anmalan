<template>
  <div style="text-align: center">
    <div class="flex flex-col m-6 sm:flex-row items-center justify-center" v-if="user">
      <gkk-action-card
        class="m-4 max-w-xs w-64"
        :admin="isAdmin"
        @admin="location('/admin/competitions')"
        @click="location('/competitions')"
        description="Tävlingsanmälan"
        icon="trophy"
        :unanswered="unanswered.competitions"
      ></gkk-action-card>
      <gkk-action-card
        class="m-4 max-w-xs w-64"
        :admin="isAdmin"
        @admin="location('/admin/events')"
        @click="location('/events')"
        description="Funktionärsanmälan"
        icon="users"
        :unanswered="unanswered.events"
      ></gkk-action-card>
      <gkk-action-card
        class="m-4 max-w-xs w-64"
        :admin="isAdmin"
        @admin="location('/admin/documents')"
        @click="location('/documents')"
        description="Dokument"
        icon="file-o"
      ></gkk-action-card>
      <!-- <gkk-action-card :admin="isAdmin" @admin="$modal.show('not-implemented')" @click="cooperation" description="Intresseanmälan<br>(Under utveckling)" icon="lightbulb-o"></gkk-action-card> -->
      <gkk-action-card
        class="m-4 max-w-xs w-64"
        v-if="isAdmin"
        @click="location('/admin/accounts')"
        description="Administrera konton"
        icon="user"
      ></gkk-action-card>
    </div>
    <div class="flex flex-col m-6 sm:flex-row items-center justify-center" v-if="!user">
      <gkk-action-card
        class="m-4 max-w-xs w-64"
        :admin="isAdmin"
        @click="location('/login')"
        description="Logga in"
        icon="sign-in"
      ></gkk-action-card>
      <gkk-action-card
        class="m-4 max-w-xs w-64"
        :admin="isAdmin"
        @click="location('/register')"
        description="Skapa konto"
        icon="user"
      ></gkk-action-card>
    </div>

    <modal name="not-implemented" :adaptive="true" height="auto">
      <div style="padding: 30px; margin-top: 20px">
        <h3 style="text-align: center">Denna funktion är under utveckling</h3>
      </div>

      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px">
        <el-button @click="$modal.hide('not-implemented')">Stäng</el-button>
      </div>
    </modal>
  </div>
</template>

<script>
export default {
  props: ['user', 'unanswered'],
  computed: {
    isAdmin() {
      return this.user && ['admin', 'superadmin'].includes(this.user.role)
    },
  },
  methods: {
    cooperation() {
      this.$modal.show('not-implemented')
    },
  },
}
</script>
