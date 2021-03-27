<template>
  <div class="container mx-auto max-w-3xl">
    <h1 class="text-center text-3xl font-hairline mb-6">Ny nyhet</h1>
    <input
      placeholder="Nyhetens titel"
      class="appearance-none rounded-none relative block w-full px-3 py-2 mb-4 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
      v-model="title"
    />
    <input
      placeholder="Datum som visas (lämna tomt om du vill använda dagens datum)"
      class="appearance-none rounded-none relative block w-full px-3 py-2 mb-4 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
      v-model="published_at_date"
    />

    <trix-editor @trix-change="change" class="bg-white trix-content" placeholder="Nyhetens text ..."></trix-editor>

    <ui-button class="mt-2" @click="create"> Skapa nyhet </ui-button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      title: '',
      body: '',
      published_at_date: '',
    }
  },
  methods: {
    change(event) {
      const html = event.target.value
      this.body = html
    },
    create() {
      window
        .axios({
          method: 'post',
          url: '/admin/news',
          data: {
            title: this.title,
            body: this.body,
            published_at_date: this.published_at_date,
          },
        })
        .then((response) => {
          window.location = '/'
        })
    },
  },
}
</script>
