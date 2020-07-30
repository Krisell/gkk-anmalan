<template>
<div class="flex mt-4">
  <button @click="location(to)" class="mx-auto mt-2 relative inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-gkk hover:bg-indigo-500 focus:outline-none focus:shadow-outline-indigo focus:border-indigo-600 active:bg-gkk transition duration-150 ease-in-out">
    <span>{{ text }}</span>
  </button>
</div>
</template>

<script>
export default {
  props: ['to', 'text'],
}
</script>
