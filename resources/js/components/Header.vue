<template>
  <div>
    <nav class="fixed inset-x-0 top-0 z-50 border-b border-gray-900/5 bg-white/90 shadow-sm backdrop-blur-md">
      <div class="relative mx-auto flex h-16 max-w-7xl items-center gap-4 px-4 sm:px-6 lg:px-8">
        <a
          href="/"
          class="relative flex shrink-0 items-center gap-3 lg:w-[88px] 2xl:absolute 2xl:right-full 2xl:top-1/2"
          aria-label="Göteborg Kraftsportklubb"
        >
          <img
            class="h-10 w-10 rounded-full lg:absolute lg:left-0 lg:-top-7 lg:h-[88px] lg:w-[88px] lg:max-w-none 2xl:left-auto 2xl:right-0 lg:ring-4 lg:ring-white transition-transform duration-300 lg:hover:scale-105"
            src="https://goteborg-kraftsportklubb.web.app/img/logo-min.png"
            alt="GKK logo"
          />
          <span class="text-sm font-bold leading-tight text-gkk lg:hidden">Göteborg<br />Kraftsportklubb</span>
        </a>

        <div data-cy="navbar" class="hidden items-center gap-1 lg:flex 2xl:-ml-3.5">
          <a
            v-for="link in publicLinks"
            :key="link.href"
            :href="link.href"
            class="rounded-full px-3.5 py-2 text-sm font-medium transition-colors duration-200"
            :class="site === link.site ? 'bg-gkk/10 text-gkk' : 'text-gray-600 hover:bg-gray-100 hover:text-gkk'"
            >{{ link.name }}</a
          >
        </div>

        <div class="ml-auto flex items-center gap-2 sm:gap-3">
          <button
            v-if="user"
            @click="logout"
            class="hidden text-sm font-medium text-gray-500 transition-colors hover:text-gkk sm:block"
          >
            Logga ut
          </button>
          <div data-cy="inside" class="shrink-0">
            <a
              class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold shadow-sm transition duration-200"
              :class="
                site === ''
                  ? 'bg-gkk text-white ring-2 ring-gkk/20 ring-offset-2'
                  : 'bg-gkk text-white hover:-translate-y-0.5 hover:bg-gkk-light hover:shadow-md'
              "
              href="/insidan"
            >
              <i class="fa" :class="user ? 'fa-user-circle' : 'fa-lock'"></i>
              Insidan
            </a>
          </div>
          <button
            @click="navIsOpen = !navIsOpen"
            class="flex h-10 w-10 items-center justify-center rounded-full text-gray-600 transition-colors hover:bg-gray-100 hover:text-gkk lg:hidden"
            :aria-expanded="navIsOpen"
            aria-label="Meny"
          >
            <i class="fa text-xl" :class="navIsOpen ? 'fa-times' : 'fa-bars'"></i>
          </button>
        </div>
      </div>

      <transition name="dropdown">
        <div v-if="navIsOpen" class="border-t border-gray-900/5 bg-white px-4 pb-6 pt-3 shadow-xl lg:hidden">
          <a
            v-for="link in publicLinks"
            :key="link.href"
            :href="link.href"
            class="flex items-center justify-between rounded-xl px-4 py-3 text-base font-medium transition-colors"
            :class="site === link.site ? 'bg-gkk/10 text-gkk' : 'text-gray-700 hover:bg-gray-50'"
          >
            {{ link.name }}
            <i class="fa fa-angle-right text-lg" :class="site === link.site ? 'text-gkk' : 'text-gray-300'"></i>
          </a>
          <button
            v-if="user"
            @click="logout"
            class="mt-2 w-full rounded-xl px-4 py-3 text-left text-base font-medium text-gray-500 hover:bg-gray-50"
          >
            Logga ut
          </button>
        </div>
      </transition>
    </nav>

    <transition name="fade">
      <div v-if="navIsOpen" class="fixed inset-0 z-40 bg-gray-900/20 lg:hidden" @click="navIsOpen = false"></div>
    </transition>

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
          class="mx-auto border p-2 rounded-sm"
          id="impersonatedUser"
          v-model="impersonatedUser"
        />
      </div>
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button @click="close" type="secondary">Tillbaka</Button>
          <Button @click="impersonate" type="danger">Aktivera</Button>
        </div>
      </template>
    </Modal>
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
        {
          name: 'Start',
          href: '/insidan',
          icon: 'newspaper-o',
          current: this.view === 'inside',
        },
        {
          name: 'Tävlingsanmälan',
          href: '/competitions',
          icon: 'th-list',
          current: this.view === 'competition',
        },
        {
          name: 'Funktionärsanmälan',
          href: '/events',
          icon: 'users',
          current: this.view === 'event',
        },
        {
          name: 'Poängtoppen',
          href: '/points',
          icon: 'trophy',
          current: this.view === 'points',
        },
        {
          name: 'Dokument',
          href: '/member-documents',
          icon: 'file-o',
          current: this.view === 'member-documents',
        },
        {
          name: 'Profil',
          href: '/profile',
          icon: 'user-circle',
          current: this.view === 'profile',
        },
      ],
      adminTabs: [
        {
          name: 'Rekord',
          href: '/admin/results',
          icon: 'trophy',
          current: this.view === 'records',
        },
        {
          name: 'Konton',
          href: '/admin/accounts',
          icon: 'list-alt',
          current: this.view === 'accounts',
        },
        {
          name: 'Slideshow',
          href: '/admin/slideshow',
          icon: 'television',
          current: this.view === 'slideshow',
        },
        {
          name: 'Betalningar',
          href: '/admin/payments',
          icon: 'credit-card',
          current: this.view === 'payments',
        },
        {
          name: 'Fortnox',
          href: '/admin/payment-tools',
          icon: 'wrench',
          current: this.view === 'payment-tools',
        },
        {
          name: 'Aktivitetslogg',
          href: '/admin/activity-logs',
          icon: 'history',
          current: this.view === 'activity-logs',
        },
      ],
      navIsOpen: false,
      showSubMenu: false,
      impersonatedUser: '',
    }
  },
  computed: {
    publicLinks() {
      return [
        { name: 'Start', href: '/', site: 'landing' },
        { name: 'Styrkelyft', href: '/styrkelyft', site: 'powerlifting' },
        { name: 'Om GKK', href: '/gkk', site: 'about' },
        { name: 'Medlemskap', href: '/medlem', site: 'member' },
        { name: 'Länkar', href: '/dokument', site: 'documents' },
        { name: 'Klubbrekord', href: '/klubbrekord', site: 'records' },
      ]
    },
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

      if (!this.user || this.user.role !== 'superadmin') {
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
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.dropdown-enter-active,
.dropdown-leave-active {
  transition:
    opacity 0.2s ease,
    transform 0.2s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
