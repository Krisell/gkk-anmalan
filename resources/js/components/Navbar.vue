<template>
<nav class="bg-white shadow-md">
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
      <div class="flex">
        <div class="flex-shrink-0 flex items-center">
          <a href="/" class="flex items-center">
            <img class="h-12 w-auto" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%c3%a5f%c3%a4rg-p%c3%a5-m%c3%b6rk-bakgrund-transparent.png" alt="GKK logo">
          </a>
        </div>
      </div>
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <span class="rounded-md shadow-sm">
            <div v-if="!user">
              <a href="/login">
                <ui-button>Logga in</ui-button>
              </a>

              <a href="/register">
                <ui-button>Skapa konto</ui-button>
              </a>
            </div>

            <div v-if="user">
              <a @click="logout">
                <ui-button type="secondary">Logga ut</ui-button>
              </a>

              <a href="/profile">
                <ui-button>Profil ({{ user.first_name }} {{ user.last_name }})</ui-button>
              </a>
            </div>
          </span>
        </div>
      </div>
    </div>
  </div>
</nav>

  <!-- <nav class="flex justify-between align-center p-6">
        <a class="navbar-brand" href="/">
          <div style="display: flex; align-items: center;">
            <img style="height: 30px; margin-right: 5px;" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%c3%a5f%c3%a4rg-p%c3%a5-m%c3%b6rk-bakgrund-transparent.png">
            GKK
          </div>
        </a>

        <ul class="flex">
          <template v-if="!user">
            <li><a class="nav-link" href="/login">Logga in</a></li>
            <li><a class="nav-link" href="/register">Skapa konto</a></li>
          </template>
          <template v-else>
            <li class="nav-item dropdown">
              <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  {{ user.first_name }} {{ user.last_name }} <span class="caret"></span>
              </a>

              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                  <a @click="location('/profile')" class="dropdown-item" href="#">Profil</a>
                  <a @click="logout" class="dropdown-item" href="#">Logga ut</a>
              </div>
            </li>
          </template>
        </ul>
  </nav> -->
</template>

<script>
import axios from 'axios'

export default {
  props: ['user'],
  methods: {
    logout () {
      axios.post('/logout').then(_ => window.location = '/')
    }
  }
}
</script>
