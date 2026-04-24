<template>
  <div :id="`news-${item.id}`" class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden" :class="{ 'bg-gkk/5': expanded }">
    <div class="flex items-start gap-3 p-4 cursor-pointer" @click="expanded = !expanded">
      <div class="w-10 h-10 rounded-full bg-gkk/10 text-gkk flex items-center justify-center shrink-0">
        <i class="fa fa-bullhorn"></i>
      </div>
      <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
          <h3 class="text-base font-semibold text-gray-900 min-w-0">{{ item.title }}</h3>
          <span class="text-xs text-gray-400">{{ publishDate }}</span>
        </div>
        <div v-if="!expanded" class="text-xs text-gray-400 mt-1">Klicka för att visa</div>
      </div>
      <div v-if="isAdmin" class="relative shrink-0" @click.stop>
        <button
          class="w-8 h-8 rounded-full text-gray-400 hover:text-gkk hover:bg-gkk/10 flex items-center justify-center transition-colors"
          @click="menuOpen = !menuOpen"
          aria-label="Mer"
        >
          <i class="fa fa-ellipsis-h"></i>
        </button>
        <div
          v-if="menuOpen"
          v-click-outside="() => (menuOpen = false)"
          class="absolute right-0 top-10 bg-white rounded-md shadow-lg ring-1 ring-black/5 py-1 z-10 min-w-[180px]"
        >
          <a
            :href="`/admin/news/${item.id}`"
            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          >
            <i class="fa fa-pencil text-gray-400 w-4"></i>
            <span>Redigera nyhet</span>
          </a>
          <a
            :href="`/admin/news/email/${item.id}`"
            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          >
            <i class="fa fa-envelope text-gray-400 w-4"></i>
            <span>Gör epostutskick</span>
          </a>
        </div>
      </div>
    </div>
    <div v-if="expanded" class="px-4 pb-4 pl-[4.25rem]">
      <div class="prose prose-sm max-w-none text-gray-700" v-html="item.body"></div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['item', 'user', 'default-expanded'],
  data() {
    return {
      expanded: this.defaultExpanded || false,
      menuOpen: false,
    }
  },
  computed: {
    isAdmin() {
      return this.user && ['admin', 'superadmin'].includes(this.user.role)
    },
    publishDate() {
      if (this.item.published_at_date) {
        return this.item.published_at_date
      }

      return this.item.created_at.slice(0, 10)
    },
  },
  directives: {
    clickOutside: {
      beforeMount(el, binding) {
        el._clickOutsideHandler = (event) => {
          if (!el.contains(event.target)) {
            binding.value(event)
          }
        }
        setTimeout(() => document.addEventListener('click', el._clickOutsideHandler), 0)
      },
      unmounted(el) {
        document.removeEventListener('click', el._clickOutsideHandler)
      },
    },
  },
}
</script>
