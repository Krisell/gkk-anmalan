<template>
  <div
    class="overflow-hidden rounded-3xl bg-white shadow-xl shadow-gkk/10 ring-1 ring-gray-900/5 lg:flex lg:min-h-[721px] lg:flex-col"
    @click="onClick"
  >
    <nav
      class="grid grid-cols-2 gap-1 border-b border-gray-100 bg-gray-50 p-1.5"
      aria-label="Logga in eller skapa konto"
    >
      <a
        v-for="tab in tabs"
        :key="tab.key"
        :href="tab.href"
        :data-auth-tab="tab.key"
        :aria-current="mode === tab.key ? 'page' : null"
        class="flex items-center justify-center gap-2 rounded-2xl px-4 py-2.5 text-sm font-semibold transition"
        :class="
          mode === tab.key
            ? 'bg-white text-gkk shadow-sm ring-1 ring-gray-900/5'
            : 'text-gray-500 hover:bg-white/60 hover:text-gkk'
        "
      >
        <i class="fa" :class="tab.icon"></i> {{ tab.label }}
      </a>
    </nav>

    <slot v-if="mode === 'login'" name="login" />
    <slot v-else name="register" />
  </div>

  <div v-if="mode === 'register' && $slots['register-after']" class="text-center text-sm text-gray-500 lg:col-start-2">
    <slot name="register-after" />
  </div>
</template>

<script>
const tabs = [
  { key: 'login', href: '/login', label: 'Logga in', icon: 'fa-sign-in' },
  { key: 'register', href: '/register', label: 'Skapa konto', icon: 'fa-user-plus' },
]

export default {
  props: {
    initial: { type: String, default: 'login' },
  },
  data() {
    return {
      mode: this.initial,
      tabs,
    }
  },
  mounted() {
    window.addEventListener('popstate', this.onPopState)
  },
  beforeUnmount() {
    window.removeEventListener('popstate', this.onPopState)
  },
  methods: {
    // Any link marked with data-auth-tab (the tabs, or links inside the forms) switches without a reload
    onClick(event) {
      const link = event.target.closest('[data-auth-tab]')

      if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return
      }

      event.preventDefault()
      this.select(link.dataset.authTab)
    },
    select(mode) {
      if (mode === this.mode) {
        return
      }

      this.mode = mode
      window.history.pushState({}, '', tabs.find((tab) => tab.key === mode).href)
    },
    onPopState() {
      const tab = tabs.find((tab) => tab.href === window.location.pathname)

      if (tab) {
        this.mode = tab.key
      }
    },
  },
}
</script>
