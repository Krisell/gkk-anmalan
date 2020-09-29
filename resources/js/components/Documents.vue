<template>
  <div class="container mx-auto">
    <h1 class="text-center text-3xl font-hairline mb-6">Dokument</h1>

    <div v-for="document in documents" :key="document.url" class="flex justify-center align-center mb-6">
      <button
        @click="open(document.url)"
        class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
      >
        <i class="fa fa-file-o mr-2"></i>
        {{ document.name }}
      </button>
    </div>

    <GkkLink to="/" text="Tillbaka till startsidan" />
  </div>
</template>

<script>
export default {
  props: ['documents'],
  methods: {
    open(url) {
      window.open(url)
    },
  },
}
</script>
