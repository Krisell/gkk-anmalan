<template>
  <nav class="bg-white shadow-md fixed top-0 w-full">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <div class="flex-shrink-0 flex items-center">
            <a href="/" class="flex items-center">
              <img
                class="h-12 w-auto"
                style="position: absolute; left: 40px; top: 10px; width: 100px; height: 100px"
                src="https://goteborg-kraftsportklubb.web.app/img/logo-min.png"
                alt="GKK logo"
              />
            </a>

            <div class="hidden lg:flex">
              <a
                class="ml-6 uppercase hover:border-b-1 hover:border-black"
                :class="site === 'landing' ? 'border-b-2 border-black' : ''"
                href="/"
                >Startsidan</a
              >
              <a
                class="ml-6 uppercase hover:border-b-1 hover:border-black"
                :class="site === 'powerlifting' ? 'border-b-2 border-black' : ''"
                href="/powerlifting"
                >Om styrkelyft</a
              >
              <a
                class="ml-6 uppercase hover:border-b-1 hover:border-black"
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
                <a class="uppercase" :class="site === '' ? 'border-b-2 border-black' : ''" href="/insidan"> Insidan </a>
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
</template>

<script>
import axios from 'axios'

export default {
  props: ['user', 'site'],
  methods: {
    logout() {
      axios.post('/logout').then((_) => (window.location = '/'))
    },
  },
}
</script>
