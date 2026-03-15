<template>
  <div>
    <nav
      class="bg-white fixed flex h-screen p-5 shadow-xl top-16 transition-all duration-300 w-52 z-10"
      :class="[navIsOpen ? 'left-0' : '-left-52']"
    >
      <ul class="flex flex-col uppercase text-sm tracking-wide w-full">
        <li class="mb-6" @click="navIsOpen = false">
          <div class="cursor-pointer text-gray-400 hover:text-gkk transition-colors text-xs">Stäng</div>
        </li>
        <li class="py-2 hover:text-gkk transition-colors">
          <a :class="site === 'landing' ? 'text-gkk font-medium border-l-2 border-gkk pl-2 -ml-2' : 'text-gray-700'" href="/">Start</a>
        </li>
        <li class="py-2 hover:text-gkk transition-colors">
          <a :class="site === 'powerlifting' ? 'text-gkk font-medium border-l-2 border-gkk pl-2 -ml-2' : 'text-gray-700'" href="/styrkelyft">Styrkelyft</a>
        </li>
        <li class="py-2 hover:text-gkk transition-colors">
          <a :class="site === 'about' ? 'text-gkk font-medium border-l-2 border-gkk pl-2 -ml-2' : 'text-gray-700'" href="/gkk">Om GKK</a>
        </li>
        <li class="py-2 hover:text-gkk transition-colors">
          <a :class="site === 'member' ? 'text-gkk font-medium border-l-2 border-gkk pl-2 -ml-2' : 'text-gray-700'" href="/medlem">Medlemskap</a>
        </li>
        <li class="py-2 hover:text-gkk transition-colors">
          <a :class="site === 'documents' ? 'text-gkk font-medium border-l-2 border-gkk pl-2 -ml-2' : 'text-gray-700'" href="/dokument">Länkar</a>
        </li>
        <li class="py-2 hover:text-gkk transition-colors">
          <a :class="site === 'records' ? 'text-gkk font-medium border-l-2 border-gkk pl-2 -ml-2' : 'text-gray-700'" href="/klubbrekord">Klubbrekord</a>
        </li>
        <li class="py-2 mt-4 border-t pt-4">
          <a
            class="px-3 py-1.5 rounded-md transition-all"
            :class="site === '' ? 'bg-gkk text-white' : 'text-gkk hover:bg-gkk/10'"
            href="/insidan">Insidan</a>
        </li>
      </ul>
    </nav>
    <nav class="bg-white shadow-md fixed top-0 w-full z-50">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <a href="/" class="flex items-center">
                <img
                  class="hidden lg:flex absolute left-3 top-3 w-24 h-24"
                  src="https://goteborg-kraftsportklubb.web.app/img/logo-min.png"
                  alt="GKK logo"
                />
                <img
                  class="flex lg:hidden h-8 w-auto left-4 absolute"
                  src="https://goteborg-kraftsportklubb.web.app/img/logo-min.png"
                  alt="GKK logo"
                />
              </a>

              <div class="flex lg:hidden absolute left-16">
                <i @click="navIsOpen = !navIsOpen" class="ml-2 fa fa-bars text-2xl cursor-pointer text-gray-600 hover:text-gkk transition-colors"></i>
              </div>
              <div data-cy="navbar" class="hidden lg:flex absolute left-0 lg:left-24 items-center">
                <a
                  class="ml-16 lg:ml-8 uppercase text-sm tracking-wide transition-colors duration-200 hover:text-gkk pb-1"
                  :class="site === 'landing' ? 'border-b-2 border-gkk text-gkk font-medium' : 'text-gray-700'"
                  href="/"
                  >Start</a
                >
                <a
                  class="ml-5 lg:ml-6 uppercase text-sm tracking-wide transition-colors duration-200 hover:text-gkk pb-1"
                  :class="site === 'powerlifting' ? 'border-b-2 border-gkk text-gkk font-medium' : 'text-gray-700'"
                  href="/styrkelyft"
                  >Styrkelyft</a
                >
                <a
                  class="ml-5 lg:ml-6 uppercase text-sm tracking-wide transition-colors duration-200 hover:text-gkk pb-1"
                  :class="site === 'about' ? 'border-b-2 border-gkk text-gkk font-medium' : 'text-gray-700'"
                  href="/gkk"
                  >Om GKK</a
                >
                <a
                  class="ml-5 lg:ml-6 uppercase text-sm tracking-wide transition-colors duration-200 hover:text-gkk pb-1"
                  :class="site === 'member' ? 'border-b-2 border-gkk text-gkk font-medium' : 'text-gray-700'"
                  href="/medlem"
                  >Medlemskap</a
                >
                <a
                  class="ml-5 lg:ml-6 uppercase text-sm tracking-wide transition-colors duration-200 hover:text-gkk pb-1"
                  :class="site === 'documents' ? 'border-b-2 border-gkk text-gkk font-medium' : 'text-gray-700'"
                  href="/dokument"
                  >Länkar</a
                >
                <a
                  class="ml-5 lg:ml-6 uppercase text-sm tracking-wide transition-colors duration-200 hover:text-gkk pb-1"
                  :class="site === 'records' ? 'border-b-2 border-gkk text-gkk font-medium' : 'text-gray-700'"
                  href="/klubbrekord"
                  >Klubbrekord</a
                >
              </div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
              <div data-cy="inside">
                <a
                  class="uppercase text-sm tracking-wide px-3 py-1.5 rounded-md transition-all duration-200"
                  :class="site === '' ? 'bg-gkk text-white' : 'text-gkk hover:bg-gkk/10'"
                  href="/insidan">
                  Insidan
                </a>
              </div>
            </div>

            <div v-if="user" class="flex-shrink-0">
              <div @click="logout">
                <a class="uppercase text-xs text-gray-500 hover:text-gkk transition-colors duration-200 cursor-pointer"> Logga ut </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <div v-if="user && site === ''" class="fixed top-[64px] z-10">
      <!-- Mobile menu button -->
      <div
        @click="showSubMenu = true"
        class="md:hidden bg-white cursor-pointer text-gkk inline-flex items-center gap-2 px-4 py-2.5 shadow-md rounded-br-lg border-b border-r border-gray-200 text-sm font-medium"
      >
        <i class="fa fa-bars"></i>
        <span>Meny</span>
      </div>

      <!-- Backdrop -->
      <transition name="fade">
        <div
          v-if="showSubMenu"
          class="md:hidden fixed inset-0 bg-black/30 -top-[64px]"
          @click="showSubMenu = false"
        ></div>
      </transition>

      <!-- Sidebar -->
      <nav
        class="fixed top-[64px] h-[calc(100vh-64px)] w-[240px] bg-white border-r border-gray-200 flex flex-col transition-transform duration-300 md:translate-x-0 overflow-y-auto"
        :class="showSubMenu ? 'translate-x-0' : '-translate-x-full'"
        aria-label="Tabs"
      >
        <div class="flex-1 pt-16 pb-4">
          <!-- Regular links -->
          <div class="px-3 mb-2">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 px-3 mb-2">Medlem</div>
            <a
              v-for="tab in memberTabs"
              :key="tab.name"
              :href="tab.href"
              @click="showSubMenu = false"
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors mb-0.5"
              :class="tab.current ? 'bg-gkk/10 text-gkk' : 'text-gray-700 hover:bg-gray-100'"
            >
              <i class="fa w-5 text-center" :class="[`fa-${tab.icon}`, tab.current ? 'text-gkk' : 'text-gray-400']"></i>
              <span>{{ tab.name }}</span>
            </a>
          </div>

          <!-- Admin links -->
          <div v-if="isAdmin" class="px-3 mt-4">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 px-3 mb-2">Admin</div>
            <a
              v-for="tab in adminTabs"
              :key="tab.name"
              :href="tab.href"
              @click="showSubMenu = false"
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors mb-0.5"
              :class="tab.current ? 'bg-gkk/10 text-gkk' : 'text-gray-700 hover:bg-gray-100'"
            >
              <i class="fa w-5 text-center" :class="[`fa-${tab.icon}`, tab.current ? 'text-gkk' : 'text-gray-400']"></i>
              <span>{{ tab.name }}</span>
            </a>
          </div>
        </div>

        <!-- User info at bottom -->
        <div v-if="user" class="border-t border-gray-200 px-4 py-3">
          <div class="text-xs text-gray-500 truncate">{{ user.email }}</div>
        </div>
      </nav>
    </div>

    <Modal ref="impersonationModal" title="Ange epost eller användarid">
      <div class="flex flex-col gap-2 items-center">
        <input 
          @keypress.enter="impersonate"
          class="mx-auto border p-2 rounded" 
          id="impersonatedUser" 
          v-model="impersonatedUser">
      </div>
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button @click="close" type="secondary">Tillbaka</Button>
          <Button @click="impersonate" type="danger">Aktivera</Button>
        </div>
      </template>
    </modal>
  </div>
</template>

<script>
import TypeTrigger from '@krisell/type-trigger'
import Button from './ui/Button.vue'
import Modal from './ui/Modal.vue'
import axios from 'axios'

export default {
  components: { Button, Modal },
  props: ['user', 'site', 'view'],
  data() {
    return {
      memberTabs: [
        { name: 'Start', href: '/insidan', icon: 'newspaper-o', current: this.view === 'inside' },
        { name: 'Tävlingsanmälan', href: '/competitions', icon: 'th-list', current: this.view === 'competition' },
        { name: 'Funktionärsanmälan', href: '/events', icon: 'users', current: this.view === 'event' },
        { name: 'Poängtoppen', href: '/points', icon: 'trophy', current: this.view === 'points' },
        { name: 'Dokument', href: '/member-documents', icon: 'file-o', current: this.view === 'member-documents' },
        { name: 'Profil', href: '/profile', icon: 'user-circle', current: this.view === 'profile' },
      ],
      adminTabs: [
        { name: 'Rekord', href: '/admin/results', icon: 'trophy', current: this.view === 'records' },
        { name: 'Konton', href: '/admin/accounts', icon: 'list-alt', current: this.view === 'accounts' },
        { name: 'Betalningar', href: '/admin/payments', icon: 'credit-card', current: this.view === 'payments' },
        { name: 'Aktivitetslogg', href: '/admin/activity-logs', icon: 'history', current: this.view === 'activity-logs' },
        { name: 'Slideshow', href: '/admin/slideshow', icon: 'television', current: this.view === 'slideshow' },
        { name: 'Betalningsverktyg', href: '/admin/payment-tools', icon: 'wrench', current: this.view === 'payment-tools' },
      ],
      navIsOpen: false,
      showSubMenu: false,
      impersonatedUser: '',
    }
  },
  computed: {
    isAdmin() {
      return this.user && ['admin', 'superadmin'].includes(this.user.role)
    },
  },
  methods: {
    logout() {
      axios.post('/logout').then((_) => (window.location = '/insidan'))
    },
    async impersonate() {
      await axios.post(`/admin/impersonate/${this.impersonatedUser}`)
      
      window.location = '/profile'
    },
  },
  mounted() {
    TypeTrigger.register('imp', () => {
      if (this.site !== '') {
        return
      }

      if (! this.user || this.user.role !== 'superadmin') {
        return
      }

      setTimeout(() => {
        this.$refs.impersonationModal.show() 
        document.querySelector('#impersonatedUser').focus()
      }, 100)
    })

    TypeTrigger.register('unimpme', async () => {
      if (this.site !== '') {
        return
      }

      await axios.delete('/impersonate')
      window.location = '/profile'
    })
  },
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
