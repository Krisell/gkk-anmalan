<template>
  <a
    :href="path"
    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gkk/10 text-gkk text-[10px] font-semibold uppercase tracking-wider hover:bg-gkk/20"
  >
    <i class="fa fa-lock"></i>
    Admin
  </a>
</template>

<script>
export default {
  props: {
    path: String,
  },
}
</script>
