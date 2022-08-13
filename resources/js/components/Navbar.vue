<template>
  <nav class="bg-white shadow-md">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <div class="flex-shrink-0 flex items-center">
            <a href="/" class="flex items-center">
              <img
                class="h-12 w-auto"
                src="https://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%c3%a5f%c3%a4rg-p%c3%a5-m%c3%b6rk-bakgrund-transparent.png"
                alt="GKK logo"
              />
            </a>
          </div>
        </div>
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <span class="rounded-md shadow-sm">
              <div v-if="!user">
                <Button class="mr-1" @click="location('/login')">Logga in</Button>
                <Button @click="location('/register')">Skapa konto</Button>
              </div>

              <div v-if="user">
                <a @click="logout">
                  <Button class="mr-1" type="secondary">Logga ut</Button>
                </a>

                <Button @click="location('/profile')">Profil ({{ user.first_name }} {{ user.last_name }})</Button>
              </div>
            </span>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import Button from './ui/Button.vue'
import axios from 'axios'

export default {
  components: { Button },
  props: ['user'],
  methods: {
    logout() {
      axios.post('/logout').then((_) => (window.location = '/'))
    },
  },
}
</script>
