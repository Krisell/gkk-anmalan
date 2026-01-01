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

    <div v-if="user && site === ''" class="fixed top-[70px] z-10">
      <div>
        <div 
          @click="showSubMenu = true" 
          class="md:hidden bg-gkk cursor-pointer text-white relative inline-flex items-center px-4 py-2 border leading-5 font-medium focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out -mt-4 -ml-2 rounded-br-lg">
          Insidan meny&nbsp;&nbsp;<i class="fa fa-arrow-right"></i>
        </div>
        <div class="-ml-64 md:ml-0 transition-all md:block" :class="{ 'ml-0': showSubMenu }">
          <div>
            <i v-if="showSubMenu" class="md:hidden fa fa-arrow-left cursor-pointer z-10 text-xl absolute left-2 top-0 text-white" @click="showSubMenu = false"></i>
            <nav class="-mb-px flex flex-col fixed h-screen top-[64px] bg-gkk pt-12 w-[210px]" aria-label="Tabs">
              <a v-for="tab in tabs" :key="tab.name" :href="tab.href" v-show="(!tab.admin) || isAdmin"
                class="ml-4 mb-3 mt-2 group inline-flex items-center py-2 px-1 font-medium text-sm text-white" 
                >
                <i class="mr-2 fa" :class="`fa-${tab.icon}`"></i>
                <span :class="[tab.current ? 'underline' : '']">{{ tab.name }}</span>
              </a>

              <div v-if="user" class="fixed bottom-2 left-2 text-white text-xs">Inloggad som<br/> {{ user.email }}</div>
            </nav>
          </div>
        </div>
      </div>
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
      tabs: [
        { name: 'Insidan start', href: '/insidan', icon: 'newspaper-o', current: this.view === 'inside' },
        { name: 'Tävlingsanmälan', href: '/competitions', icon: 'th-list', current: this.view === 'competition' },
        { name: 'Funktionärsanmälan', href: '/events', icon: 'users', current: this.view === 'event' },
        { name: 'Poängtoppen', href: '/points', icon: 'trophy', current: this.view === 'points' },
        { name: 'Dokument', href: '/member-documents', icon: 'file-o', current: this.view === 'member-documents' },
        { name: 'Profil', href: '/profile', icon: 'user-circle', current: this.view === 'profile' },
        { name: 'Admin - Rekord', href: '/admin/results', icon: 'trophy', current: this.view === 'records', admin: true },
        { name: 'Admin - Konton', href: '/admin/accounts', icon: 'list-alt', current: this.view === 'accounts', admin: true },
        { name: 'Admin - Slideshow', href: '/admin/slideshow', icon: 'television', current: this.view === 'slideshow', admin: true },
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
