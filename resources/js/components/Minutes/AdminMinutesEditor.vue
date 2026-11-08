<template>
  <div class="container mx-auto max-w-3xl px-4 sm:px-0">
    <Breadcrumbs class="mb-6" :title="id ? `${form.meeting_type} ${form.number}` : 'Nytt protokoll'">
      <div class="flex gap-2 ml-auto">
        <Button v-if="id" type="secondary" @click="location(`/admin/minutes/new?copy=${id}`)">
          <i class="fa fa-files-o mr-2"></i>Kopiera
        </Button>
        <Button v-if="id" type="secondary" @click="saveAndPrint"><i class="fa fa-print mr-2"></i>Skriv ut / PDF</Button>
        <Button v-if="!locked" :disabled="saving" @click="save"
          ><i class="fa fa-floppy-o mr-2"></i>{{ saving ? 'Sparar …' : 'Spara' }}</Button
        >
      </div>
    </Breadcrumbs>

    <MinutesWorkflow
      v-if="id"
      :minutes-id="id"
      :initial-workflow="workflowState"
      :folders="folders"
      :adjuster="form.adjuster"
      :default-document-name="`${form.meeting_type} ${form.number}, ${form.year} (${form.date})`"
      :save="save"
      @change="workflowState = $event"
    />

    <p v-if="copiedFrom && !id" class="mb-4 rounded-md bg-blue-50 px-4 py-2 text-sm text-blue-800 ring-1 ring-blue-200">
      <i class="fa fa-files-o mr-2"></i>Kopia av {{ copiedFrom }}. Datum, nummer och plats följer på senaste mötet.
    </p>

    <!-- Approved minutes are locked, so what the adjuster approved is what gets published. -->
    <fieldset :disabled="locked" class="min-w-0">
      <section class="card">
        <h2 class="card-title">Mötet</h2>
        <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
          <label class="col-span-2 sm:col-span-4">
            <span class="label">Typ av möte</span>
            <input v-model="form.meeting_type" list="meeting-types" class="field" />
            <datalist id="meeting-types">
              <option value="Styrelsemöte" />
              <option value="Extra styrelsemöte" />
              <option value="Konstituerande styrelsemöte" />
            </datalist>
          </label>
          <label>
            <span class="label">Nummer</span>
            <input v-model.number="form.number" type="number" min="1" class="field" />
          </label>
          <label>
            <span class="label">Verksamhetsår</span>
            <input v-model.number="form.year" type="number" class="field" />
          </label>
          <label class="col-span-2">
            <span class="label">Datum</span>
            <input v-model="form.date" type="date" class="field" @change="dateChanged" />
          </label>
          <label>
            <span class="label">Start</span>
            <input v-model="form.start_time" type="time" class="field" />
          </label>
          <label>
            <span class="label">Slut</span>
            <input v-model="form.end_time" type="time" class="field" />
          </label>
          <label class="col-span-2">
            <span class="label">Plats</span>
            <input v-model="form.location" class="field" placeholder="t.ex. Discord (distans)" />
          </label>
        </div>
        <p class="text-xs text-gray-500 mt-2">
          {{ form.meeting_type }} {{ form.number }}, verksamhetsåret {{ form.year }}
        </p>
      </section>

      <section class="card">
        <h2 class="card-title">Deltagare</h2>
        <p class="text-xs text-gray-500 mb-3">Klicka på ett namn för att växla mellan närvarande och frånvarande.</p>
        <div class="flex flex-wrap gap-2 mb-3">
          <span
            v-for="(attendee, index) in form.attendees"
            :key="attendee.name + index"
            class="inline-flex items-center rounded-full text-sm ring-1"
            :class="
              attendee.present
                ? 'bg-green-50 text-green-800 ring-green-300'
                : 'bg-gray-100 text-gray-500 ring-gray-300 line-through'
            "
          >
            <button class="pl-3 pr-1 py-1 cursor-pointer" @click="attendee.present = !attendee.present">
              <i class="fa mr-1" :class="attendee.present ? 'fa-check' : 'fa-times'"></i>{{ attendee.name }}
            </button>
            <button
              class="pr-3 pl-1 py-1 text-gray-400 hover:text-gray-700 cursor-pointer"
              :aria-label="`Ta bort ${attendee.name}`"
              @click="form.attendees.splice(index, 1)"
            >
              ×
            </button>
          </span>
        </div>
        <form class="flex gap-2" @submit.prevent="addAttendee">
          <input
            v-model="newAttendee"
            list="member-names"
            class="field flex-1"
            placeholder="Välj medlem eller skriv ett namn"
            @input="addIfMember"
          />
          <datalist id="member-names">
            <option v-for="name in selectableMembers" :key="name" :value="name" />
          </datalist>
          <Button type="secondary">Lägg till</Button>
        </form>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4">
          <label v-for="role in roles" :key="role.key">
            <span class="label">{{ role.label }}</span>
            <input v-model="form[role.key]" list="attendee-names" class="field" />
          </label>
          <datalist id="attendee-names">
            <option v-for="name in presentNames" :key="name" :value="name" />
          </datalist>
        </div>
      </section>

      <section class="card">
        <h2 class="card-title">Dagordning</h2>

        <ol class="space-y-1 mb-4 text-sm text-gray-500">
          <li v-for="(item, index) in standardItems" :key="item.title" class="flex gap-2">
            <span class="w-6 shrink-0 font-semibold">{{ index + 1 }}.</span>
            <span
              ><span class="font-semibold">{{ item.title }}</span> — {{ item.body }}</span
            >
          </li>
        </ol>

        <div v-for="(item, index) in form.items" :key="item.uid" class="border-t border-gray-200 py-4">
          <div class="flex items-center gap-2 mb-2">
            <span class="w-6 shrink-0 font-semibold text-gkk">{{ standardItems.length + index + 1 }}.</span>
            <input v-model="item.title" class="field flex-1 font-semibold" placeholder="Rubrik" />
            <button class="icon-button" :disabled="index === 0" aria-label="Flytta upp" @click="move(index, -1)">
              <i class="fa fa-arrow-up"></i>
            </button>
            <button
              class="icon-button"
              :disabled="index === form.items.length - 1"
              aria-label="Flytta ned"
              @click="move(index, 1)"
            >
              <i class="fa fa-arrow-down"></i>
            </button>
            <button class="icon-button" aria-label="Ta bort punkt" @click="removeItem(index)">
              <i class="fa fa-trash-o"></i>
            </button>
          </div>
          <RichTextEditor
            :editable="!locked"
            v-model="item.body"
            :jwt="jwt"
            placeholder="Vad som sades och beslutades …"
          />
        </div>

        <div class="flex justify-end border-t border-gray-200 py-3">
          <Button type="secondary" @click="addItem"><i class="fa fa-plus mr-2"></i>Lägg till punkt</Button>
        </div>

        <div class="border-t border-gray-200 py-4">
          <div class="flex items-center gap-2 mb-2">
            <span class="w-6 shrink-0 font-semibold text-gkk">{{ standardItems.length + form.items.length + 1 }}.</span>
            <span class="font-semibold">Övrigt</span>
          </div>
          <RichTextEditor
            :editable="!locked"
            v-model="form.other_business"
            :jwt="jwt"
            placeholder="Lämna tomt för ”Inga övriga frågor.”"
          />
        </div>

        <div class="border-t border-gray-200 pt-4">
          <div class="flex items-center gap-2 mb-2">
            <span class="w-6 shrink-0 font-semibold text-gkk">{{ standardItems.length + form.items.length + 2 }}.</span>
            <span class="font-semibold">Nästa möte</span>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <label>
              <span class="label">Datum och tid</span>
              <input v-model="form.next_meeting_at" type="datetime-local" step="900" class="field" />
            </label>
            <label>
              <span class="label">Plats</span>
              <input v-model="form.next_meeting_location" class="field" />
            </label>
            <label>
              <span class="label">Notering (valfri)</span>
              <input v-model="form.next_meeting_note" class="field" placeholder="t.ex. Styrelsedag" />
            </label>
          </div>
        </div>
      </section>
    </fieldset>

    <div class="flex justify-end gap-2 mb-12">
      <Button v-if="id" type="secondary" @click="saveAndPrint"><i class="fa fa-print mr-2"></i>Skriv ut / PDF</Button>
      <Button v-if="!locked" :disabled="saving" @click="save"
        ><i class="fa fa-floppy-o mr-2"></i>{{ saving ? 'Sparar …' : 'Spara' }}</Button
      >
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import Breadcrumbs from '../ui/Breadcrumbs.vue'
import Button from '../ui/Button.vue'
import RichTextEditor from '../ui/RichTextEditor.vue'
import MinutesWorkflow from './MinutesWorkflow.vue'

let uid = 0
const withUid = (item) => ({ uid: ++uid, title: item.title || '', body: item.body || '' })

export default {
  components: { Breadcrumbs, Button, RichTextEditor, MinutesWorkflow },
  props: ['minutes', 'workflow', 'folders', 'nextNumbers', 'members', 'copiedFrom', 'jwt'],
  data() {
    const minutes = this.minutes
    return {
      id: minutes.id || null,
      form: {
        meeting_type: minutes.meeting_type || 'Styrelsemöte',
        year: minutes.year,
        number: minutes.number,
        date: minutes.date,
        start_time: minutes.start_time || '',
        end_time: minutes.end_time || '',
        location: minutes.location || '',
        chair: minutes.chair || '',
        secretary: minutes.secretary || '',
        adjuster: minutes.adjuster || '',
        attendees: (minutes.attendees || []).map((attendee) => ({ ...attendee })),
        items: (minutes.items || []).map(withUid),
        other_business: minutes.other_business || '',
        next_meeting_at: minutes.next_meeting_at || '',
        next_meeting_location: minutes.next_meeting_location || '',
        next_meeting_note: minutes.next_meeting_note || '',
      },
      workflowState: this.workflow || { status: 'draft' },
      newAttendee: '',
      saving: false,
      savedSnapshot: null,
      roles: [
        { key: 'chair', label: 'Mötesordförande' },
        { key: 'secretary', label: 'Sekreterare' },
        { key: 'adjuster', label: 'Protokolljusterare' },
      ],
    }
  },
  computed: {
    locked() {
      return ['approved', 'published'].includes(this.workflowState.status)
    },
    selectableMembers() {
      const added = new Set(this.form.attendees.map((attendee) => attendee.name))
      return (this.members || []).filter((name) => !added.has(name))
    },
    presentNames() {
      return this.form.attendees.filter((attendee) => attendee.present).map((attendee) => attendee.name)
    },
    standardItems() {
      const name = (value) => value || '…'
      return [
        { title: 'Mötets öppnande', body: `Mötesordförande ${name(this.form.chair)} öppnar mötet.` },
        {
          title: 'Sekreterare och protokolljusterare',
          body: `${name(this.form.secretary)} väljs till sekreterare och ${name(this.form.adjuster)} till protokolljusterare.`,
        },
        { title: 'Godkännande av föregående mötesprotokoll', body: 'Föregående mötesprotokoll godkändes.' },
        { title: 'Fastställande av dagordning', body: 'Dagordningen fastställdes.' },
      ]
    },
    payload() {
      return {
        ...this.form,
        start_time: this.form.start_time || null,
        end_time: this.form.end_time || null,
        next_meeting_at: this.form.next_meeting_at || null,
        items: this.form.items.map(({ title, body }) => ({ title, body })),
      }
    },
    dirty() {
      return JSON.stringify(this.payload) !== this.savedSnapshot
    },
  },
  mounted() {
    this.savedSnapshot = this.id ? JSON.stringify(this.payload) : null
    window.addEventListener('beforeunload', this.warnIfDirty)
    window.addEventListener('keydown', this.shortcut)
  },
  unmounted() {
    window.removeEventListener('beforeunload', this.warnIfDirty)
    window.removeEventListener('keydown', this.shortcut)
  },
  methods: {
    dateChanged() {
      const year = Number((this.form.date || '').substring(0, 4))
      if (year && year !== this.form.year) {
        this.form.year = year
        this.form.number = this.nextNumbers[year] || 1
      }
    },
    addAttendee() {
      const name = this.newAttendee.trim()
      if (name && !this.form.attendees.some((attendee) => attendee.name === name)) {
        this.form.attendees.push({ name, present: true })
      }
      this.newAttendee = ''
    },
    addIfMember(event) {
      // Picking a member from the list adds them straight away; anything else is added with Enter or the button.
      if (event.inputType === 'insertReplacementText' || !event.inputType) {
        if (this.members?.includes(this.newAttendee)) {
          this.addAttendee()
        }
      }
    },
    addItem() {
      this.form.items.push(withUid({}))
    },
    removeItem(index) {
      const item = this.form.items[index]
      if (item.title || item.body) {
        if (!window.confirm(`Ta bort punkten ”${item.title || 'utan rubrik'}”?`)) {
          return
        }
      }
      this.form.items.splice(index, 1)
    },
    move(index, direction) {
      const items = this.form.items
      const [item] = items.splice(index, 1)
      items.splice(index + direction, 0, item)
    },
    async save() {
      if (this.locked) {
        return true
      }
      if (this.form.items.some((item) => !item.title.trim())) {
        this.$toast.error('Alla punkter behöver en rubrik.')
        return false
      }

      this.saving = true
      try {
        const payload = this.payload
        if (this.id) {
          await axios.put(`/admin/minutes/${this.id}`, payload)
        } else {
          const { data } = await axios.post('/admin/minutes', payload)
          this.id = data.id
          window.history.replaceState(null, '', `/admin/minutes/${data.id}`)
        }
        this.savedSnapshot = JSON.stringify(payload)
        this.$toast.success('Protokollet är sparat.')
        return true
      } catch (error) {
        const errors = error.response?.data?.errors
        this.$toast.error(errors ? Object.values(errors)[0][0] : 'Det gick inte att spara. Försök igen.')
        return false
      } finally {
        this.saving = false
      }
    },
    async saveAndPrint() {
      // Open the tab before awaiting, so popup blockers see it as a direct result of the click.
      const tab = window.open('', '_blank')
      if (this.locked || (await this.save())) {
        tab.location = `/admin/minutes/${this.id}/print`
      } else {
        tab.close()
      }
    },
    warnIfDirty(event) {
      if (this.dirty) {
        event.preventDefault()
        event.returnValue = ''
      }
    },
    shortcut(event) {
      if ((event.metaKey || event.ctrlKey) && event.key === 's') {
        event.preventDefault()
        this.save()
      }
    },
  },
}
</script>

<style scoped>
@reference '../../../css/main.css';

.card {
  @apply bg-white rounded-lg shadow-sm ring-1 ring-gray-900/5 p-4 sm:p-6 mb-6;
}
.card-title {
  @apply text-lg font-semibold text-gkk mb-3;
}
.label {
  @apply block text-xs font-medium text-gray-600 mb-1;
}
.field {
  @apply block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-xs hover:border-gray-400 focus:border-gkk focus:ring-1 focus:ring-gkk focus:outline-hidden;
}
.icon-button {
  @apply w-8 h-8 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gkk cursor-pointer disabled:opacity-30 disabled:cursor-default;
}
</style>
