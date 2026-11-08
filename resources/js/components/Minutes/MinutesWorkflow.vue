<template>
  <section class="mb-6 rounded-lg p-4 sm:p-5 ring-1" :class="tone">
    <div class="flex flex-wrap items-start gap-3">
      <div class="flex-1 min-w-[16rem]">
        <template v-if="workflow.status === 'draft'">
          <h2 class="font-semibold"><i class="fa fa-pencil mr-2"></i>Utkast</h2>
          <p class="text-sm mt-1">
            När protokollet är klart skickar du det till protokolljusteraren
            <strong v-if="adjuster">{{ adjuster }}</strong
            ><span v-else>(välj en under Deltagare)</span>.
          </p>
        </template>

        <template v-else-if="workflow.status === 'review'">
          <h2 class="font-semibold"><i class="fa fa-clock-o mr-2"></i>Väntar på justering av {{ adjuster }}</h2>
          <p class="text-sm mt-1">
            Skicka länken till {{ adjuster }}, t.ex. på Discord. Du kan fortsätta ändra – justeraren ser alltid den
            senast sparade versionen.
          </p>
        </template>

        <template v-else-if="workflow.status === 'changes_requested'">
          <h2 class="font-semibold"><i class="fa fa-exclamation-circle mr-2"></i>{{ adjuster }} vill ha ändringar</h2>
          <blockquote class="text-sm mt-2 border-l-4 border-amber-400 pl-3 whitespace-pre-line">
            {{ workflow.review_comment }}
          </blockquote>
          <p class="text-sm mt-2">Gör ändringarna, spara och skicka igen. Samma länk fungerar.</p>
        </template>

        <template v-else-if="workflow.status === 'approved'">
          <h2 class="font-semibold">
            <i class="fa fa-check-circle mr-2"></i>Justerat {{ workflow.approved_at }} av
            {{ workflow.approved_by_name }}
          </h2>
          <p class="text-sm mt-1">Protokollet är låst och kan publiceras för medlemmarna under Dokument.</p>
        </template>

        <template v-else-if="workflow.status === 'published'">
          <h2 class="font-semibold"><i class="fa fa-globe mr-2"></i>Publicerat {{ workflow.published_at }}</h2>
          <p class="text-sm mt-1">
            Finns under Dokument<span v-if="publishedFolder"> i mappen ”{{ publishedFolder.name }}”</span>.
            <a :href="workflow.published_url" target="_blank" class="underline">Visa som medlem</a>
          </p>
        </template>
      </div>

      <div class="flex flex-wrap gap-2">
        <Button v-if="workflow.status === 'draft'" :disabled="!adjuster || busy" @click="sendForReview">
          <i class="fa fa-paper-plane mr-2"></i>Skicka för justering
        </Button>
        <Button v-if="workflow.status === 'changes_requested'" :disabled="busy" @click="sendForReview">
          <i class="fa fa-paper-plane mr-2"></i>Skicka igen
        </Button>
        <Button v-if="workflow.status === 'approved'" type="secondary" :disabled="busy" @click="reopen">
          <i class="fa fa-unlock mr-2"></i>Lås upp för ändringar
        </Button>
        <Button v-if="workflow.status === 'published'" type="secondary" :disabled="busy" @click="unpublish">
          Avpublicera
        </Button>
      </div>
    </div>

    <div v-if="workflow.review_url" class="flex gap-2 mt-3">
      <input
        :value="workflow.review_url"
        readonly
        class="field flex-1 font-mono text-xs"
        @focus="$event.target.select()"
      />
      <Button type="secondary" @click="copy"><i class="fa fa-clipboard mr-2"></i>Kopiera länk</Button>
    </div>

    <form
      v-if="workflow.status === 'approved'"
      class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] gap-2 mt-3 items-end"
      @submit.prevent="publish"
    >
      <label>
        <span class="block text-xs font-medium mb-1">Mapp i Dokument</span>
        <select v-model="folderId" class="field" required>
          <option v-for="folder in folders" :key="folder.id" :value="folder.id">
            {{ folder.name }}{{ folder.only_administrators ? ' (endast admin)' : '' }}
          </option>
        </select>
      </label>
      <label>
        <span class="block text-xs font-medium mb-1">Namn på dokumentet</span>
        <input v-model="documentName" class="field" required />
      </label>
      <Button :disabled="busy || !folderId"><i class="fa fa-globe mr-2"></i>Publicera</Button>
    </form>
  </section>
</template>

<script>
import axios from 'axios'
import Button from '../ui/Button.vue'

export default {
  components: { Button },
  // `save` saves the editor first, so what is sent for review is what is on screen. It resolves to false on failure.
  props: ['minutesId', 'initialWorkflow', 'folders', 'adjuster', 'defaultDocumentName', 'save'],
  emits: ['change'],
  data() {
    const folders = this.folders || []
    const minutesFolder =
      folders.find((folder) => /protokoll/i.test(folder.name) && !folder.only_administrators) ||
      folders.find((folder) => !folder.only_administrators) ||
      folders[0]
    return {
      workflow: this.initialWorkflow,
      busy: false,
      folderId: minutesFolder?.id || null,
      documentName: this.defaultDocumentName,
    }
  },
  computed: {
    tone() {
      return {
        draft: 'bg-white ring-gray-900/5 text-gray-700',
        review: 'bg-blue-50 ring-blue-200 text-blue-900',
        changes_requested: 'bg-amber-50 ring-amber-200 text-amber-900',
        approved: 'bg-green-50 ring-green-200 text-green-900',
        published: 'bg-green-50 ring-green-200 text-green-900',
      }[this.workflow.status]
    },
    publishedFolder() {
      return (this.folders || []).find((folder) => folder.id === this.workflow.document_folder_id)
    },
  },
  watch: {
    defaultDocumentName(name) {
      this.documentName = name
    },
  },
  methods: {
    async run(request, message) {
      this.busy = true
      try {
        const { data } = await request()
        this.workflow = data
        this.$emit('change', data)
        if (message) {
          this.$toast.success(message)
        }
      } catch (error) {
        this.$toast.error(error.response?.data?.message || 'Något gick fel. Försök igen.')
      } finally {
        this.busy = false
      }
    },
    async sendForReview() {
      if (!(await this.save())) {
        return
      }
      await this.run(() => axios.post(`/admin/minutes/${this.minutesId}/review`), 'Kopiera länken och skicka den.')
    },
    reopen() {
      if (window.confirm('Låsa upp protokollet? Det behöver justeras igen innan det kan publiceras.')) {
        this.run(() => axios.post(`/admin/minutes/${this.minutesId}/reopen`), 'Protokollet är upplåst.')
      }
    },
    publish() {
      this.run(
        () =>
          axios.post(`/admin/minutes/${this.minutesId}/publish`, {
            document_folder_id: this.folderId,
            name: this.documentName,
          }),
        'Protokollet är publicerat under Dokument.',
      )
    },
    unpublish() {
      if (window.confirm('Ta bort protokollet från Dokument?')) {
        this.run(() => axios.delete(`/admin/minutes/${this.minutesId}/publish`), 'Protokollet är avpublicerat.')
      }
    },
    async copy() {
      await navigator.clipboard.writeText(this.workflow.review_url)
      this.$toast.success('Länken är kopierad.')
    },
  },
}
</script>

<style scoped>
@reference '../../../css/main.css';

.field {
  @apply block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-xs focus:border-gkk focus:ring-1 focus:ring-gkk focus:outline-hidden;
}
</style>
