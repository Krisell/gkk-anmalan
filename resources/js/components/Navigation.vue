<template>
  <div>
    <div v-if="hasPendingPayments" class="max-w-6xl mx-auto px-4 mt-4">
      <div class="bg-red-50 border border-red-200 border-l-4 border-l-red-500 text-red-700 px-4 py-3 rounded-sm">
        Du har obetalda avgifter. Klicka på "Profil" för mer information.<br />Efter betalning kan det ta några dagar
        innan denna notisering försvinner.
      </div>
    </div>

    <div
      v-if="user && helperCount === 0 && !user.explicit_registration_approval && isUserOlderThanOneMonth"
      class="max-w-6xl mx-auto px-4 mt-4"
    >
      <div
        class="bg-yellow-50 border border-yellow-200 border-l-4 border-l-yellow-500 text-yellow-700 px-4 py-3 rounded-sm"
      >
        Systemet kan inte se att du har hjälpt till som funktionär under det senaste året. Detta kan påverka din
        möjlighet att anmäla dig till tävlingar. Kontakta styrelsen om du har frågor.
      </div>
    </div>

    <div v-if="user && user.granted_by == 0" class="text-center">
      <h3 class="mt-6 font-thin text-xl">
        Välkommen till GKK!<br />
        Innan du kan börja använda systemet behöver ditt konto godkännas av administratören.
      </h3>
    </div>

    <template v-else-if="user">
      <div class="max-w-6xl mx-auto px-4 pt-4">
        <div class="flex items-start justify-between gap-4 flex-wrap">
          <div>
            <h1 class="text-2xl font-semibold text-gkk">
              Välkommen tillbaka<template v-if="user.first_name">, {{ user.first_name }}</template
              >!
            </h1>
            <p class="text-gray-500 mt-1 text-sm">Här är vad som händer i klubben just nu.</p>
          </div>
          <div class="flex items-center gap-2 text-sm text-gray-500">
            <i class="fa fa-calendar-o"></i>
            <span>{{ today }}</span>
          </div>
        </div>

        <div class="mt-6 grid gap-3 grid-cols-2 md:grid-cols-3 lg:grid-cols-6">
          <a
            v-for="card in cards"
            :key="card.href"
            :href="card.href"
            class="group relative bg-white rounded-xl border border-gray-100 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all p-4 flex flex-col gap-6"
            :class="{ 'ring-1 ring-gkk/20 bg-gkk/5': card.admin }"
          >
            <div
              v-if="card.admin"
              class="absolute top-3 left-3 text-[10px] font-semibold uppercase tracking-wider text-gkk/70"
            >
              Admin
            </div>
            <div
              v-if="card.unanswered > 0"
              class="absolute top-3 right-3 bg-gkk text-white text-xs font-medium rounded-full min-w-[22px] h-[22px] px-1.5 flex items-center justify-center"
            >
              {{ card.unanswered }}
            </div>
            <div class="text-gkk" :class="{ 'mt-4': card.admin }">
              <i class="fa text-2xl" :class="`fa-${card.icon}`"></i>
            </div>
            <div class="mt-auto min-w-0">
              <div class="font-semibold text-gray-900 text-sm leading-tight">{{ card.title }}</div>
              <div class="flex items-center justify-between gap-2 mt-1">
                <div class="text-xs text-gray-500 leading-snug min-w-0">{{ card.description }}</div>
                <i class="fa fa-angle-right text-gray-400 group-hover:text-gkk transition-colors text-lg shrink-0"></i>
              </div>
              <span
                v-if="card.adminHref && isAdmin"
                role="link"
                tabindex="0"
                @click.stop.prevent="location(card.adminHref)"
                class="inline-flex items-center gap-1 mt-2 px-2 py-0.5 rounded-full bg-gkk/10 text-gkk text-[10px] font-semibold uppercase tracking-wider hover:bg-gkk/20 cursor-pointer"
              >
                <i class="fa fa-lock"></i>
                Admin
              </span>
            </div>
          </a>
        </div>

        <section
          v-if="isAdmin && pendingTasks.length"
          class="mt-6 bg-white rounded-xl border border-gray-100 shadow-xs"
        >
          <div class="px-4 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
            <div>
              <h2 class="font-semibold text-gray-900">Att göra</h2>
              <p class="text-sm text-gray-500">Tävlingsanmälningar som behöver följas upp</p>
            </div>
            <span
              class="bg-gkk text-white text-xs font-medium rounded-full min-w-[22px] h-[22px] px-1.5 flex items-center justify-center"
            >
              {{ pendingTasks.length }}
            </span>
          </div>
          <ul class="divide-y divide-gray-100">
            <li
              v-for="task in pendingTasks"
              :key="task.id"
              class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-3"
            >
              <div class="flex-1 min-w-0">
                <div class="font-medium text-gray-900">{{ taskLabel(task.type) }}</div>
                <a :href="`/admin/competitions/${task.competition_id}`" class="text-sm text-gkk hover:underline">
                  {{ task.competition.name }}
                </a>
              </div>
              <div class="flex gap-2 shrink-0">
                <button
                  @click="completeTask(task, 'done')"
                  class="px-3 py-1.5 rounded-sm bg-gkk text-white text-sm hover:bg-gkk/90"
                >
                  Klar
                </button>
                <button
                  @click="completeTask(task, 'not_applicable')"
                  class="px-3 py-1.5 rounded-sm border border-gray-300 text-gray-700 text-sm hover:bg-gray-50"
                >
                  Ej aktuellt
                </button>
              </div>
            </li>
          </ul>
        </section>
      </div>
    </template>

    <div v-if="!user" class="flex flex-col sm:flex-row items-center justify-center gap-4 m-6">
      <a
        href="/register"
        class="bg-white rounded-xl border border-gray-100 shadow-xs hover:shadow-md transition-all p-6 flex flex-col items-center justify-center w-64 text-center"
      >
        <i class="fa fa-user-circle text-3xl text-gkk mb-3"></i>
        <div class="font-semibold text-gray-900">Skapa konto som medlem</div>
      </a>
      <a
        href="/login"
        class="bg-white rounded-xl border border-gray-100 shadow-xs hover:shadow-md transition-all p-6 flex flex-col items-center justify-center w-64 text-center"
      >
        <i class="fa fa-sign-in text-3xl text-gkk mb-3"></i>
        <div class="font-semibold text-gray-900">Logga in som medlem</div>
      </a>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import moment from 'moment'
import CompetitionAdminTasks from '../modules/CompetitionAdminTasks.js'
import Date from '../modules/Date.js'

export default {
  props: ['user', 'unanswered', 'hasPendingPayments', 'adminTasks'],
  data() {
    return {
      pendingTasks: [...(this.adminTasks || [])],
    }
  },
  computed: {
    isAdmin() {
      return this.user && ['admin', 'superadmin'].includes(this.user.role)
    },
    today() {
      return moment().locale('sv').format('D MMMM YYYY')
    },
    cards() {
      const member = [
        {
          href: '/profile',
          title: 'Profil',
          description: 'Information om dig',
          icon: 'user-circle-o',
        },
        {
          href: '/competitions',
          title: 'Tävling',
          description: 'Tävlingsanmälan',
          icon: 'trophy',
          adminHref: '/admin/competitions',
        },
        {
          href: '/events',
          title: 'Funktionär',
          description: 'Funktionärsanmälan',
          icon: 'users',
          unanswered: this.unanswered?.events,
          adminHref: '/admin/events',
        },
        {
          href: '/member-documents',
          title: 'Dokument',
          description: 'Protokoll mm',
          icon: 'file-text-o',
          adminHref: '/admin/documents',
        },
      ]

      if (!this.isAdmin) {
        return member
      }

      return [
        ...member,
        {
          href: '/admin/accounts',
          title: 'Konton',
          description: 'Hantera medlemmar',
          icon: 'address-book-o',
          admin: true,
        },
        {
          href: '/admin/payments',
          title: 'Betalningsadmin',
          description: 'Hantera betalningar',
          icon: 'credit-card',
          admin: true,
        },
        {
          href: '/admin/signature-requests',
          title: 'Signering',
          description: 'Skicka ut dokument för signering',
          icon: 'pencil-square-o',
          admin: true,
        },
        {
          href: '/admin/activity-logs',
          title: 'Aktivitetslogg',
          description: 'Se vad som hänt',
          icon: 'history',
          admin: true,
        },
      ]
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
    taskLabel(type) {
      return CompetitionAdminTasks.typeLabel(type)
    },
    completeTask(task, status) {
      axios
        .patch(`/admin/competition-tasks/${task.id}`, { status })
        .then(() => {
          this.pendingTasks = this.pendingTasks.filter((item) => item.id !== task.id)
          this.$toast.success(status === 'done' ? 'Uppgiften är klar.' : 'Uppgiften är markerad som ej aktuell.')
        })
        .catch(() => {
          this.$toast.error('Det gick inte att uppdatera uppgiften. Försök igen.')
        })
    },
  },
}
</script>
