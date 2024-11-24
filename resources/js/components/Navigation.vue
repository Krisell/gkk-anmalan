<template>
  <div style="text-align: center">
    <div v-if="user && user.granted_by == 0">
      <h3 class="text-center mt-6 font-thin text-xl">
        Välkommen till GKK!<br />
        Innan du kan börja använda systemet behöver ditt konto godkännas av administratören.
      </h3>
    </div>
    <div v-else class="flex flex-col m-6 sm:flex-row items-center justify-center">
      <LinkCard
        v-if="user"
        class="m-4 max-w-xs w-64"
        href="/competitions"
        description="Tävlingsanmälan"
        icon="th-list"
      ></LinkCard>
      <LinkCard
        v-if="user"
        class="m-4 max-w-xs w-64"
        href="/events"
        description="Funktionärsanmälan"
        icon="users"
        :unanswered="unanswered.events"
      ></LinkCard>
      <LinkCard
        v-if="user"
        class="m-4 max-w-xs w-64"
        href="/member-documents"
        description="Dokument"
        icon="file-o"
      ></LinkCard>
      <LinkCard
        v-if="user"
        class="m-4 max-w-xs w-64"
        href="/profile"
        description="Profil"
        icon="user-circle"
      ></LinkCard>
      <LinkCard
        v-if="isAdmin"
        class="m-4 max-w-xs w-64"
        href="/admin/accounts"
        description="Administrera konton"
        icon="list-alt"
        :unanswered="unanswered.ungranted"
      ></LinkCard>
      <LinkCard
        v-if="!user"
        class="m-4 max-w-xs w-64"
        href="/register"
        description="Skapa konto som medlem"
        icon="user-circle"
      ></LinkCard>
      <LinkCard
        v-if="!user"
        class="m-4 max-w-xs w-64"
        href="/login"
        description="Logga in som medlem"
        icon="sign-in"
      ></LinkCard>
    </div>
  </div>
</template>

<script>
import LinkCard from './LinkCard.vue'
import axios from 'axios'

export default {
  components: { LinkCard },
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
