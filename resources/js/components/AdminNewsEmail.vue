<template>
  <div class="container mx-auto max-w-3xl">
    <h1 class="text-center text-3xl font-thin mb-6">Gör epostutskick</h1>

    <h3 class="mb-4 text-xs font-thin">
      Du kan justera mailets innehåll nedan, men detta uppdaterar inte nyheten på webbsidan.<br />Vill du göra ändringar
      även i den nyheten kan du göra det först.
    </h3>
    <input
      placeholder="Nyhetens titel"
      class="appearance-none rounded-none relative block w-full px-3 py-2 mb-4 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
      v-model="item.title"
    />
    <input v-if="item" id="news-content" :value="item.body" type="hidden" name="content" />
    <trix-editor
      ref="editor"
      @trix-change="change"
      class="bg-white trix-content"
      style="min-height: 300px"
      input="news-content"
    ></trix-editor>

    <p v-if="status === 'test-successful'">Testmail skickat!</p>

    <Button v-if="item" class="mt-2 mr-2" @click="showPreview"> Se färdigt mail i webbläsaren </Button>
    <Button v-if="item" class="mt-2" @click="testSend"> Gör provutskick till endast dig själv </Button>
    <Button v-if="item" type="secondary" class="mt-2">
      Denna knapp ska göra utskick till samtliga medlemmar, men det är inte implementerat ännu
    </Button>
  </div>
</template>

<script>
import PostRedirect from '../modules/PostRedirect'
import Button from './ui/Button.vue'

export default {
  components: { Button },
  props: ['item'],
  data() {
    return {
      status: '',
    }
  },
  methods: {
    change(event) {
      const html = event.target.value
      this.item.body = html
    },
    testSend() {
      this.status = ''
      axios
        .post('/admin/news/email/test', {
          title: this.item.title,
          body: this.item.body,
        })
        .then(() => {
          this.status = 'test-successful'
        })
    },
    showPreview() {
      this.status = ''

      PostRedirect.send({
        open: true,
        url: '/admin/news/email/preview',
        data: {
          title: this.item.title,
          body: this.item.body,
        },
      })
    },
  },
}
</script>

<style></style>
