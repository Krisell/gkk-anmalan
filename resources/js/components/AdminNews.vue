<template>
  <div class="container mx-auto max-w-3xl">
    <h1 class="text-center text-3xl font-thin mb-6">Ny nyhet</h1>
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

    <input v-if="news" id="news-content" :value="news.body" type="hidden" name="content" />
    <input v-if="!news" id="news-content" type="hidden" name="content" />
    <trix-editor
      ref="editor"
      @trix-change="change"
      class="bg-white trix-content"
      style="min-height: 300px"
      placeholder="Nyhetens text ..."
      input="news-content"
    ></trix-editor>

    <Button v-if="!news" class="mt-2" @click="create"> Skapa nyhet </Button>
    <Button v-if="news" class="mt-2" @click="update"> Uppdatera nyhet </Button>
    <Button v-if="news" type="secondary" class="mt-2" @click="deleteNews"> Radera nyhet </Button>
  </div>
</template>

<script>
import FirebaseFileUpload from '../modules/FirebaseFileUpload.js'
import Button from './ui/Button.vue'
import axios from 'axios'

export default {
  components: { Button },
  props: ['news', 'jwt'],
  data() {
    return {
      title: '',
      body: '',
      published_at_date: '',
    }
  },
  mounted() {
    if (this.news) {
      this.title = this.news.title
      this.body = this.news.body
      this.published_at_date = this.news.published_at_date
    }

    this.$refs.editor.addEventListener('trix-attachment-add', async (event) => {
      const file = event.attachment.file

      if (!file) {
        return
      }

      const extension = file.name.split('.')[file.name.split('.').length - 1]

      ;[10, 20, 30, 40, 50, 60, 70, 80, 90].forEach((progress) => {
        window.setTimeout(() => {
          event.attachment.setUploadProgress(progress)
        }, progress * 10)
      })

      const url = await FirebaseFileUpload.upload(this.jwt, file, extension)

      event.attachment.setAttributes({ url: url, href: url })
    })
  },
  methods: {
    change(event) {
      const html = event.target.value
      this.body = html
    },
    create() {
      axios({
        method: 'post',
        url: '/admin/news',
        data: {
          title: this.title,
          body: this.body,
          published_at_date: this.published_at_date,
        },
      }).then((response) => {
        window.location = '/'
      })
    },
    update() {
      axios({
        method: 'post',
        url: `/admin/news/${this.news.id}`,
        data: {
          title: this.title,
          body: this.body,
          published_at_date: this.published_at_date,
        },
      }).then((response) => {
        window.location = '/'
      })
    },
    deleteNews() {
      axios.delete(`/admin/news/${this.news.id}`).then(() => {
        window.location = '/'
      })
    },
  },
}
</script>
