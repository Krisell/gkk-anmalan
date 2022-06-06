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
          <a href="/">Start</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a href="/powerlifting">Styrkelyft</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a href="/about">Om GKK</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a href="/member">Medlemskap</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a href="/documents">Dokument &amp; program</a>
        </li>
        <li class="mt-4 hover:text-gkk">
          <a href="/records">Klubbrekord</a>
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
  </div>
</template>

<script>
import axios from 'axios'

export default {
  props: ['user', 'site'],
  data() {
    return {
      navIsOpen: false,
    }
  },
  methods: {
    logout() {
      axios.post('/logout').then((_) => (window.location = '/'))
    },
  },
}
</script>
