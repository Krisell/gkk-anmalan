<template>
  <div style="text-align: center">
    <div v-if="user && user.granted_by == 0">
      <h3 class="text-center mt-6 font-thin text-xl">
        Välkommen till GKK!<br />
        Innan du kan börja använda systemet behöver ditt konto godkännas av administratören.
      </h3>
    </div>
    <div v-else class="flex flex-col m-6 sm:flex-row items-center justify-center">
      <gkk-action-card
        v-if="user"
        class="m-4 max-w-xs w-64"
        @click="location('/competitions')"
        description="Tävlingsanmälan"
        icon="th-list"
      ></gkk-action-card>
      <gkk-action-card
        v-if="user"
        class="m-4 max-w-xs w-64"
        @click="location('/events')"
        description="Funktionärsanmälan"
        icon="users"
        :unanswered="unanswered.events"
      ></gkk-action-card>
      <gkk-action-card
        v-if="user"
        class="m-4 max-w-xs w-64"
        @click="location('/member-documents')"
        description="Dokument"
        icon="file-o"
      ></gkk-action-card>
      <gkk-action-card
        v-if="user"
        class="m-4 max-w-xs w-64"
        @click="location('/profile')"
        description="Profil"
        icon="user-circle"
      ></gkk-action-card>
      <gkk-action-card
        v-if="isAdmin"
        class="m-4 max-w-xs w-64"
        @click="location('/admin/accounts')"
        description="Administrera konton"
        icon="list-alt"
        :unanswered="unanswered.ungranted"
      ></gkk-action-card>
      <gkk-action-card
        v-if="!user"
        class="m-4 max-w-xs w-64"
        @click="location('/register')"
        description="Skapa konto som medlem"
        icon="user-circle"
      ></gkk-action-card>
      <gkk-action-card
        v-if="!user"
        class="m-4 max-w-xs w-64"
        @click="location('/login')"
        description="Logga in som medlem"
        icon="sign-in"
      ></gkk-action-card>
    </div>

    <a class="text-xl underline" v-if="isAdmin" href="/admin/music">Musikhjälpen admin</a>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  props: ['user', 'unanswered'],
  computed: {
    isAdmin() {
      return this.user && ['admin', 'superadmin'].includes(this.user.role)
    },
  },
  methods: {
    logout() {
      axios.post('/logout').then(() => {
        window.location.reload()
      })
    },
  },
}
</script>
