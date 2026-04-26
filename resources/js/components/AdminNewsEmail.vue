<template>
  <div class="container mx-auto max-w-3xl">
    <h1 class="text-2xl font-semibold mb-6">
      <a href="/insidan" class="inline-flex items-center gap-2 text-gray-400 hover:text-gkk transition-colors group">
        <i class="fa fa-angle-left"></i>
        <span class="underline underline-offset-4 decoration-gray-300 group-hover:decoration-gkk">Start</span>
      </a>
      <span class="text-gray-300 mx-2">/</span>
      <span class="text-gkk">Gör epostutskick</span>
    </h1>

    <div class="bg-blue-50 border border-blue-100 text-blue-900 text-sm rounded-md px-4 py-3 mb-6">
      <p class="font-medium mb-1">Skicka denna nyhet som epost</p>
      <p class="text-blue-800">
        Justera vid behov titel och innehåll nedan – ändringarna gäller endast detta utskick och påverkar inte nyheten
        på webbsidan. Vill du även uppdatera nyheten gör du det
        <a v-if="item && item.id" :href="`/admin/news/${item.id}`" class="underline">här</a>
        <span v-else>på sidan för redigering av nyheten</span> först.
      </p>
    </div>

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

    <div v-if="item && item.id" class="mt-6 border border-gray-200 rounded-md bg-white overflow-hidden">
      <button
        type="button"
        class="w-full flex items-center justify-between text-left px-4 py-3 hover:bg-gray-50 transition-colors"
        :class="{ 'border-b border-gray-200': sendoutOpen }"
        @click="toggleSendoutPanel"
      >
        <span class="flex items-center gap-3">
          <i class="fa fa-paper-plane text-gkk"></i>
          <span class="font-medium">Skicka som mail</span>
          <span class="text-xs text-gray-500">Förhandsgranska, gör provutskick eller skicka till medlemmar</span>
        </span>
        <i class="fa text-gray-400" :class="sendoutOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
      </button>

      <div v-if="sendoutOpen" class="px-4 py-5">
        <div class="flex flex-wrap gap-2 mb-2">
          <Button type="secondary" @click="showPreview">Se färdigt mail i webbläsaren</Button>
          <Button type="secondary" @click="testSend">Gör provutskick till endast dig själv</Button>
        </div>

        <p v-if="status === 'test-successful'" class="text-sm text-green-700 mb-2">Testmail skickat!</p>
        <p v-if="status === 'send-successful'" class="text-sm text-green-700 mb-2">{{ sendMessage }}</p>

        <div class="border-t border-gray-200 my-5"></div>

        <div class="flex items-center justify-between mb-3">
          <h3 class="font-medium">Mottagare</h3>
          <button
            class="text-xs text-gray-500 hover:text-gkk inline-flex items-center gap-1"
            :disabled="loadingRecipients"
            @click="loadRecipients"
          >
            <i class="fa fa-refresh" :class="{ 'fa-spin': loadingRecipients }"></i>
            Uppdatera lista
          </button>
        </div>

        <div v-if="!recipientsLoaded" class="text-sm text-gray-500">Hämtar mottagare ...</div>

        <div v-if="recipientsLoaded">
          <p class="text-xs text-gray-600 mb-3">
            {{ selectedCount }} av {{ recipients.length }} aktiva medlemmar valda.
            {{ alreadySentCount }} har redan fått detta mail.
          </p>

          <div class="flex flex-wrap gap-3 mb-3 text-xs">
            <button class="text-gkk underline" @click="selectAll">Markera alla</button>
            <button class="text-gkk underline" @click="selectNone">Avmarkera alla</button>
            <button class="text-gkk underline" @click="selectUnsent">Markera endast de som inte fått det</button>
          </div>

          <div class="border rounded max-h-80 overflow-y-auto bg-white">
            <label
              v-for="recipient in recipients"
              :key="recipient.id"
              class="flex items-center justify-between px-3 py-2 border-b last:border-b-0 hover:bg-gray-50 cursor-pointer text-sm"
            >
              <span class="flex items-center gap-2">
                <input type="checkbox" :value="recipient.id" v-model="selectedIds" />
                <span>{{ recipient.name }}</span>
                <span class="text-xs text-gray-500">{{ recipient.email }}</span>
              </span>
              <span v-if="recipient.sent_at" class="text-xs text-gray-500">
                Skickat {{ formatSentAt(recipient.sent_at) }}
              </span>
            </label>
          </div>

          <Button class="mt-4" :disabled="selectedCount === 0 || sending" @click="confirmAndSend">
            {{ sending ? 'Skickar ...' : `Skicka till ${selectedCount} mottagare` }}
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
import PostRedirect from '../modules/PostRedirect'
import Button from './ui/Button.vue'

export default {
  components: { Button },
  props: ['item'],
  data() {
    return {
      status: '',
      sendMessage: '',
      recipients: [],
      selectedIds: [],
      recipientsLoaded: false,
      loadingRecipients: false,
      sendoutOpen: false,
      sending: false,
    }
  },
  computed: {
    selectedCount() {
      return this.selectedIds.length
    },
    alreadySentCount() {
      return this.recipients.filter((r) => r.sent_at).length
    },
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
    toggleSendoutPanel() {
      this.sendoutOpen = !this.sendoutOpen
      if (this.sendoutOpen && !this.recipientsLoaded) {
        this.loadRecipients()
      }
    },
    loadRecipients() {
      const isFirstLoad = !this.recipientsLoaded
      this.loadingRecipients = true
      axios
        .get(`/admin/news/email/${this.item.id}/recipients`)
        .then((response) => {
          this.recipients = response.data.recipients
          this.recipientsLoaded = true
          if (isFirstLoad && this.selectedIds.length === 0) {
            this.selectUnsent()
          }
        })
        .finally(() => {
          this.loadingRecipients = false
        })
    },
    selectAll() {
      this.selectedIds = this.recipients.map((r) => r.id)
    },
    selectNone() {
      this.selectedIds = []
    },
    selectUnsent() {
      this.selectedIds = this.recipients.filter((r) => !r.sent_at).map((r) => r.id)
    },
    formatSentAt(date) {
      return moment(date).locale('sv').format('YYYY-MM-DD HH:mm')
    },
    confirmAndSend() {
      const sentAlready = this.recipients.filter(
        (r) => r.sent_at && this.selectedIds.includes(r.id),
      ).length
      let message = `Vill du skicka mailet till ${this.selectedCount} mottagare?`
      if (sentAlready > 0) {
        message += ` ${sentAlready} av dem har redan fått detta mail tidigare.`
      }
      if (!window.confirm(message)) {
        return
      }
      this.send()
    },
    send() {
      this.sending = true
      this.status = ''
      axios
        .post(`/admin/news/email/${this.item.id}/send`, {
          title: this.item.title,
          body: this.item.body,
          user_ids: this.selectedIds,
        })
        .then((response) => {
          this.sendMessage = response.data.message
          this.status = 'send-successful'
          this.selectedIds = []
          this.loadRecipients()
        })
        .finally(() => {
          this.sending = false
        })
    },
  },
}
</script>

<style></style>
