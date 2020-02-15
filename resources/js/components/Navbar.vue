<template>
  <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
      <div class="container">
          <a class="navbar-brand" href="/">
            <div style="display: flex; align-items: center;">
              <img style="height: 30px; margin-right: 5px;" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%c3%a5f%c3%a4rg-p%c3%a5-m%c3%b6rk-bakgrund-transparent.png">
              GKK
            </div>
          </a>

          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                  <a class="nav-link" href="/" style="display: flex; align-items: center;">
                    <div style="margin-right: 5px;">Start</div>
                    <!-- <img style="height: 30px; margin-right: 5px;" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%c3%a5f%c3%a4rg-p%c3%a5-m%c3%b6rk-bakgrund-transparent.png"> -->
                  </a>
                </li>
              </ul>

              <ul class="navbar-nav">
                <template v-if="!user">
                  <li class="nav-item"><a class="nav-link" href="/login">Logga in</a></li>
                  <li class="nav-item"><a class="nav-link" href="/register">Skapa konto</a></li>
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
          </div>
      </div>
  </nav>
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

<style>

</style>