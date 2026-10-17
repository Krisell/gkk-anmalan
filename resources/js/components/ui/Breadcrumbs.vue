<template>
  <div class="min-w-0">
    <nav aria-label="Brödsmulor" class="mb-3">
      <ol
        class="inline-flex max-w-full flex-wrap items-center gap-0.5 rounded-full bg-white p-1 text-sm font-medium text-gray-500 shadow-xs ring-1 ring-gray-900/5"
      >
        <li v-for="(crumb, index) in crumbs" :key="crumb.href" class="flex items-center gap-0.5">
          <i v-if="index > 0" class="fa fa-angle-right px-0.5 text-xs text-gray-300" aria-hidden="true"></i>
          <a
            :href="crumb.href"
            class="group inline-flex items-center gap-2 rounded-full py-1 transition-colors hover:bg-gkk/5 hover:text-gkk"
            :class="index === 0 ? 'pl-1 pr-2.5' : 'px-2.5'"
          >
            <span
              v-if="crumb.icon"
              class="flex h-6 w-6 items-center justify-center rounded-full text-xs transition-colors"
              :class="index === 0 ? 'bg-gkk text-white group-hover:bg-gkk-light' : 'bg-gkk/5 text-gkk/70'"
              aria-hidden="true"
            >
              <i class="fa" :class="`fa-${crumb.icon}`"></i>
            </span>
            {{ crumb.name }}
          </a>
        </li>
      </ol>
    </nav>
    <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
      <h1 class="text-2xl font-extrabold tracking-tight text-gkk text-balance sm:text-3xl">{{ title }}</h1>
      <slot />
    </div>
    <div class="mt-3 h-1 w-14 rounded-full bg-linear-to-r from-gkk to-gkk-lightest" aria-hidden="true"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: { type: String, required: true },
  items: { type: Array, default: () => [] },
})

const icons = {
  '/competitions': 'th-list',
  '/events': 'users',
  '/admin/competitions': 'th-list',
  '/admin/events': 'users',
  '/admin/signature-requests': 'pencil',
}

const crumbs = computed(() => [
  { name: 'Start', href: '/insidan', icon: 'home' },
  ...props.items.map((item) => ({ icon: icons[item.href], ...item })),
])
</script>
