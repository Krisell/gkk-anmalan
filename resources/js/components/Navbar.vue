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
          <a :class="site === 'powerlifting' ? 'border-b-2 border-black' : ''" href="/powerlifting">Styrkelyft</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a :class="site === 'about' ? 'border-b-2 border-black' : ''" href="/about">Om GKK</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a :class="site === 'member' ? 'border-b-2 border-black' : ''" href="/member">Medlemskap</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a :class="site === 'documents' ? 'border-b-2 border-black' : ''" href="/documents">Dokument &amp; program</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a :class="site === 'records' ? 'border-b-2 border-black' : ''" href="/records">Klubbrekord</a>
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
              <div class="hidden lg:flex absolute left-0 lg:left-24">
                <a
                  class="ml-16 lg:ml-8 uppercase hover:border-b-1 hover:border-black"
                  :class="site === 'landing' ? 'border-b-2 border-black' : ''"
                  href="/"
                  >Start</a
                >
                <a
                  class="ml-2 lg:ml-6 uppercase hover:border-b-1 hover:border-black"
                  :class="site === 'powerlifting' ? 'border-b-2 border-black' : ''"
                  href="/powerlifting"
                  >Styrkelyft</a
                >
                <a
                  class="ml-2 lg:ml-6 uppercase hover:border-b-1 hover:border-black"
                  :class="site === 'about' ? 'border-b-2 border-black' : ''"
                  href="/about"
                  >Om GKK</a
                >
                <a
                  class="ml-6 uppercase hover:border-b-1 hover:border-black"
                  :class="site === 'member' ? 'border-b-2 border-black' : ''"
                  href="/member"
                  >Medlemskap</a
                >
                <a
                  class="ml-6 uppercase hover:border-b-1 hover:border-black"
                  :class="site === 'documents' ? 'border-b-2 border-black' : ''"
                  href="/documents"
                  >Dokument &amp; program</a
                >
                <a
                  class="ml-6 uppercase hover:border-b-1 hover:border-black"
                  :class="site === 'records' ? 'border-b-2 border-black' : ''"
                  href="/records"
                  >Klubbrekord</a
                >
              </div>
            </div>
          </div>
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <span class="rounded-md shadow-sm">
                <div>
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
        <div class="md:hidden">
          <label for="tabs" class="sr-only">Select a tab</label>
          <select id="tabs" name="tabs" class="block w-full rounded-md border-gray-300 focus:border-gkk focus:ring-gkk">
            <option v-for="tab in tabs" :key="tab.name" :selected="tab.current">{{ tab.name }}</option>
          </select>
        </div>
        <div class="hidden md:block">
          <div>
            <nav class="-mb-px flex space-x-8 ml-2 lg:ml-32" aria-label="Tabs">
              <a v-for="tab in tabs" :key="tab.name" :href="tab.href" :class="[tab.current ? 'border-gkk text-gkk' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'group inline-flex items-center py-2 px-1 border-b-2 font-medium text-sm']" :aria-current="tab.current ? 'page' : undefined">
                <i class="mr-2 fa" :class="`fa-${tab.icon}`"></i>
                <span>{{ tab.name }}</span>
              </a>
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
        { name: 'Insidan start', href: '/insidan', icon: 'unlock-alt', current: this.view === 'inside' },
        { name: 'Tävlingsanmälan', href: '/competitions', icon: 'th-list', current: this.view === 'competition' },
        { name: 'Funktionärsanmälan', href: '/events', icon: 'users', current: this.view === 'event' },
        { name: 'Medlemsdokument', href: '/member-documents', icon: 'file-o', current: this.view === 'member-documents' },
        { name: `Profil (${ this.user.email })`, href: '/profile', icon: 'user-circle', current: this.view === 'profile' },
      ],
      navIsOpen: false,
    }
  },
  methods: {
    logout() {
      axios.post('/logout').then((_) => (window.location = '/insidan'))
    },
  },
}
</script>
