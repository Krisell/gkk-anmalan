<template>
  <div style="text-align: center">
    <div v-if="hasPendingPayments">
      <h3 class="text-center mt-6 text-md text-red-600 max-w-[90%] mx-auto">
        Du har obetalda avgifter. Klicka på "Profil" för mer information.<br>Efter betalning kan det ta några dagar innan denna notisering försvinner.
      </h3>
    </div>
    <div v-if="user && helperCount === 0 && !user.explicit_registration_approval && isUserOlderThanOneMonth" class="max-w-[90%] mx-auto mt-6">
      <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded max-w-[800px] border-l-4 border-l-yellow-500 m-auto text-left">
        Systemet kan inte se att du har hjälpt till som funktionär under det senaste året. Detta kan påverka din möjlighet att anmäla dig till tävlingar. Kontakta styrelsen om du har frågor.
      </div>
    </div>
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
import Date from '../modules/Date.js'

export default {
  components: { LinkCard },
  props: ['user', 'unanswered', 'hasPendingPayments'],
  computed: {
    isAdmin() {
      return this.user && ['admin', 'superadmin'].includes(this.user.role)
    },
    helperCount() {
      if (!this.user || !this.user.event_registrations) {
        return 0
      }

      return this.presentLastYear(this.user.event_registrations)
    },
    isUserOlderThanOneMonth() {
      if (!this.user || !this.user.created_at) {
        return true
      }

      const createdAt = new window.Date(this.user.created_at)
      const oneMonthAgo = new window.Date()
      oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1)

      return createdAt < oneMonthAgo
    },
  },
  methods: {
    presentLastYear(registrations) {
      return registrations.filter(
        (registration) => registration.presence_confirmed && Date.withinAYear(registration.event.date),
      ).length
    },
    logout() {
      axios.post('/logout').then(() => {
        window.location.reload()
      })
    },
  },
}
</script>
