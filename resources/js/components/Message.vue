<template>
  <div
    class="p-2 border-l-8 border-l-[#439cd6] rounded bg-[#eef3fa]"
    :class="{
      'bg-[#dbf1d6] border-l-[#00bba9]': success,
      'bg-[#ffecca] border-l-[#ffc561]': warning,
      'bg-[#ffbbbd] border-l-[#f8868a]': danger,
    }"
  >
    <slot></slot>
  </div>
</template>

<script>
export default {
  props: {
    success: {
      type: Boolean,
      default: false,
    },
    warning: {
      type: Boolean,
      default: false,
    },
    danger: {
      type: Boolean,
      default: false,
    },
  },
}
</script>

<style></style>
