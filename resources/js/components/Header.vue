<template>
  <div>
    <nav
      class="bg-white fixed flex h-screen p-4 shadow-xl top-16 transition-all w-48 z-10"
      :class="[navIsOpen ? 'left-0' : '-left-48']"
    >
      <ul class="flex flex-col uppercase">
        <li class="mr-8" @click="navIsOpen = false">
          <div class="cursor-pointer text-teachiq-red">Stäng</div>
        </li>
        <li class="mt-8 hover:text-gkk">
          <a :class="site === 'landing' ? 'border-b-2 border-black' : ''" href="/">Start</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a :class="site === 'powerlifting' ? 'border-b-2 border-black' : ''" href="/styrkelyft">Styrkelyft</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a :class="site === 'about' ? 'border-b-2 border-black' : ''" href="/gkk">Om GKK</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a :class="site === 'member' ? 'border-b-2 border-black' : ''" href="/medlem">Medlemskap</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a :class="site === 'documents' ? 'border-b-2 border-black' : ''" href="/dokument">Dokument &amp; program</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a :class="site === 'records' ? 'border-b-2 border-black' : ''" href="/klubbrekord">Klubbrekord</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a :class="site === 'musikhjalpen' ? 'border-b-2 border-black' : ''" href="/musikhjalpen">Musikhjälpen</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a :class="site === '' ? 'border-b-2 border-black' : ''" href="/insidan">Insidan</a>
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
                <i @click="navIsOpen = !navIsOpen" class="ml-2 fa fa-bars text-2xl cursor-pointer"></i>
                <!-- <div class="ml-8 lg:ml-8 uppercase border-b-2 border-black">Start</div> -->
              </div>
              <div data-cy="navbar" class="hidden lg:flex absolute left-0 lg:left-24">
                <a
                  class="ml-16 lg:ml-8 uppercase hover:border-b-1 hover:border-black"
                  :class="site === 'landing' ? 'border-b-2 border-black' : ''"
                  href="/"
                  >Start</a
                >
                <a
                  class="ml-2 lg:ml-6 uppercase hover:border-b-1 hover:border-black"
                  :class="site === 'powerlifting' ? 'border-b-2 border-black' : ''"
                  href="/styrkelyft"
                  >Styrkelyft</a
                >
                <a
                  class="ml-2 lg:ml-6 uppercase hover:border-b-1 hover:border-black"
                  :class="site === 'about' ? 'border-b-2 border-black' : ''"
                  href="/gkk"
                  >Om GKK</a
                >
                <a
                  class="ml-6 uppercase hover:border-b-1 hover:border-black"
                  :class="site === 'member' ? 'border-b-2 border-black' : ''"
                  href="/medlem"
                  >Medlemskap</a
                >
                <a
                  class="ml-6 uppercase hover:border-b-1 hover:border-black"
                  :class="site === 'documents' ? 'border-b-2 border-black' : ''"
                  href="/dokument"
                  >Dokument &amp; program</a
                >
                <a
                  class="ml-6 uppercase hover:border-b-1 hover:border-black"
                  :class="site === 'records' ? 'border-b-2 border-black' : ''"
                  href="/klubbrekord"
                  >Klubbrekord</a
                >
                <a
                  class="ml-6 uppercase hover:border-b-1 text-[#009a79] border border-[#009a79] p-[2px] -m-[2px] px-1"
                  :class="site === 'musikhjalpen' ? 'border-b-2 border-black' : ''"
                  href="/musikhjalpen"
                  >Musikhjälpen</a
                >
              </div>
            </div>
          </div>
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <span class="rounded-md shadow-sm">
                <div data-cy="inside">
                  <a class="uppercase" :class="site === '' ? 'border-b-2 border-black' : ''" href="/insidan">
                    Insidan
                  </a>
                </div>
              </span>
            </div>

            <div v-if="user" class="flex-shrink-0 ml-4">
              <span class="rounded-md shadow-sm">
                <div @click="logout">
                  <a class="uppercase text-xs" href="#"> Logga ut </a>
                </div>
              </span>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <div v-if="user && site === ''" class="fixed top-[70px]">
      <div>
        <div 
          @click="showSubMenu = true" 
          class="md:hidden bg-gkk cursor-pointer text-white relative inline-flex items-center px-4 py-2 border leading-5 font-medium focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out -mt-4 -ml-2 rounded-br-lg">
          Visa meny&nbsp;&nbsp;➦
        </div>
        <div class="-ml-64 md:ml-0 transition-all md:block" :class="{ 'ml-0': showSubMenu }">
          <div>
            <i v-if="showSubMenu" class="md:hidden fa fa-times-circle-o cursor-pointer z-10 text-3xl absolute left-2 top-0 text-white" @click="showSubMenu = false"></i>
            <nav class="-mb-px flex flex-col fixed h-screen top-[64px] bg-gkk pt-12 w-[210px]" aria-label="Tabs">
              <a v-for="tab in tabs" :key="tab.name" :href="tab.href" v-if="(!tab.admin) || isAdmin"
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
  </div>
</template>

<script>
import axios from 'axios'

export default {
  props: ['user', 'site', 'view'],
  data() {
    return {
      tabs: [
        { name: 'Insidan start', href: '/insidan', icon: 'newspaper-o', current: this.view === 'inside' },
        { name: 'Tävlingsanmälan', href: '/competitions', icon: 'th-list', current: this.view === 'competition' },
        { name: 'Funktionärsanmälan', href: '/events', icon: 'users', current: this.view === 'event' },
        { name: 'Dokument', href: '/member-documents', icon: 'file-o', current: this.view === 'member-documents' },
        { name: 'Profil', href: '/profile', icon: 'user-circle', current: this.view === 'profile' },
        { name: 'Administrera rekord', href: '/admin/results', icon: 'trophy', current: this.view === 'records', admin: true },
        { name: 'Administrera konton', href: '/admin/accounts', icon: 'list-alt', current: this.view === 'accounts', admin: true },
      ],
      navIsOpen: false,
      showSubMenu: false,
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
  },
}
</script>
