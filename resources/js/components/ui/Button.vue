<template>
  <button
    @click="click"
    class="
      relative
      inline-flex
      items-center
      px-4
      py-2
      border
      text-sm
      leading-5
      font-medium
      rounded-md
      focus:outline-none focus:shadow-outline-indigo focus:border-indigo-600
      active:bg-gkk
      transition
      duration-150
      ease-in-out
    "
    :class="
      type === 'secondary'
        ? 'bg-white text-gkk border-gkk hover:bg-gray-200'
        : 'bg-gkk hover:bg-gkk-light text-white border-transparent'
    "
  >
    <slot></slot>
  </button>
</template>

<script>
export default {
  props: {
    type: {
      type: String,
      default: 'primary',
    },
    prevent: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    click(e) {
      if (this.prevent) {
        e.preventDefault()
      }

      this.$emit('click')
    },
  },
}
</script>
