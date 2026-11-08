<template>
  <div class="container mx-auto max-w-4xl px-4 pb-12">
    <Breadcrumbs class="mb-6" title="Styrelsen">
      <Button v-if="!form" @click="startCreate"><i class="fa fa-plus mr-2"></i>Lägg till</Button>
      <a href="/gkk" target="_blank" class="text-sm text-gkk hover:underline">
        <i class="fa fa-external-link mr-1"></i>Visa Om GKK
      </a>
    </Breadcrumbs>

    <p class="mb-6 text-sm text-gray-600">
      Det här visas under "Förtroendevalda" på Om GKK-sidan. Ansvarsområdena och e-postadressen visas för alla besökare,
      och ansvarsområdena samlas även i rutan "Vem kontaktar jag om …?".
    </p>

    <form
      v-if="form"
      class="mb-8 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xs"
      @submit.prevent="save"
    >
      <div class="bg-gray-800 px-5 py-3">
        <h2 class="font-medium text-white">{{ form.id ? `Redigera ${form.name}` : 'Ny styrelsemedlem' }}</h2>
      </div>
      <div class="grid gap-5 p-5 sm:grid-cols-[auto_1fr]">
        <div class="flex flex-col items-center gap-2">
          <div class="h-24 w-24 overflow-hidden rounded-full ring-4 ring-gkk/10">
            <img v-if="form.photo_url" :src="form.photo_url" :alt="form.name" class="h-full w-full object-cover" />
            <div v-else class="flex h-full w-full items-center justify-center bg-gkk text-2xl font-bold text-white">
              {{ initials(form.name) }}
            </div>
          </div>
          <input ref="photoInput" type="file" accept="image/*" class="hidden" @change="uploadPhoto" />
          <button
            type="button"
            class="text-sm text-gkk hover:underline disabled:opacity-50"
            :disabled="uploading"
            @click="$refs.photoInput.click()"
          >
            {{ uploading ? 'Laddar upp …' : form.photo_url ? 'Byt bild' : 'Ladda upp bild' }}
          </button>
          <button
            v-if="form.photo_url && !uploading"
            type="button"
            class="text-xs text-gray-500 hover:underline"
            @click="form.photo_url = null"
          >
            Ta bort bild
          </button>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
          <label class="block">
            <span class="mb-1.5 block text-sm font-medium text-gray-700">Namn</span>
            <input v-model="form.name" required :class="inputClass" />
          </label>
          <label class="block">
            <span class="mb-1.5 block text-sm font-medium text-gray-700">Roll</span>
            <input v-model="form.role" required list="board-roles" :class="inputClass" />
            <datalist id="board-roles">
              <option v-for="role in roles" :key="role" :value="role" />
            </datalist>
          </label>
          <label class="block sm:col-span-2">
            <span class="mb-1.5 block text-sm font-medium text-gray-700">Ansvarsområden</span>
            <input v-model="form.areasText" placeholder="T.ex. Ungdomsträning, Klubbkläder" :class="inputClass" />
            <span class="mt-1 block text-xs text-gray-500">Separera med kommatecken.</span>
          </label>
          <label class="block sm:col-span-2">
            <span class="mb-1.5 block text-sm font-medium text-gray-700">E-post (visas publikt)</span>
            <input v-model="form.email" type="email" placeholder="T.ex. klader@gkk-styrkelyft.se" :class="inputClass" />
          </label>
        </div>
      </div>
      <div class="flex justify-end gap-2 border-t border-gray-100 px-5 py-3">
        <Button type="secondary" @click.prevent="form = null">Avbryt</Button>
        <Button :disabled="saving || uploading">{{ form.id ? 'Spara' : 'Lägg till' }}</Button>
      </div>
    </form>

    <div class="overflow-hidden border-b border-gray-200 bg-white shadow-sm sm:rounded-lg">
      <div v-if="items.length === 0" class="px-6 py-6 text-center text-sm text-gray-500">
        Inga styrelsemedlemmar ännu.
      </div>
      <div
        v-for="(member, index) in items"
        :key="member.id"
        class="flex items-center gap-4 border-b border-gray-200 px-4 py-3 last:border-b-0 sm:px-6"
      >
        <div class="flex flex-col">
          <button
            class="text-gray-400 hover:text-gkk disabled:invisible"
            :disabled="index === 0"
            aria-label="Flytta upp"
            @click="move(index, -1)"
          >
            <i class="fa fa-chevron-up"></i>
          </button>
          <button
            class="text-gray-400 hover:text-gkk disabled:invisible"
            :disabled="index === items.length - 1"
            aria-label="Flytta ned"
            @click="move(index, 1)"
          >
            <i class="fa fa-chevron-down"></i>
          </button>
        </div>
        <div class="h-12 w-12 shrink-0 overflow-hidden rounded-full ring-2 ring-gkk/10">
          <img v-if="member.photo_url" :src="member.photo_url" :alt="member.name" class="h-full w-full object-cover" />
          <div v-else class="flex h-full w-full items-center justify-center bg-gkk text-sm font-bold text-white">
            {{ initials(member.name) }}
          </div>
        </div>
        <div class="min-w-0 flex-1">
          <div class="text-sm font-medium text-gray-900">
            {{ member.name }} <span class="ml-1 text-xs font-semibold text-gkk-light">{{ member.role }}</span>
          </div>
          <div v-if="member.areas.length" class="text-xs text-gray-600">{{ member.areas.join(' · ') }}</div>
          <div v-if="member.email" class="text-xs text-gray-500">{{ member.email }}</div>
        </div>
        <Button type="secondary" @click="startEdit(member)">Redigera</Button>
        <button
          v-tooltip="'Ta bort'"
          class="cursor-pointer text-gkk-light hover:text-gkk"
          aria-label="Ta bort"
          @click="confirmDelete(member)"
        >
          <i class="fa fa-trash-o text-lg"></i>
        </button>
      </div>
    </div>

    <Modal ref="deleteModal" :title="`Ta bort ${selected && selected.name}?`">
      <template #footer="{ close }">
        <div class="mt-4 flex items-center justify-center gap-2">
          <Button type="secondary" @click="close">Nej</Button>
          <Button type="danger" @click="destroy(close)">Ja, ta bort</Button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script>
import axios from 'axios'
import Breadcrumbs from '../ui/Breadcrumbs.vue'
import Button from '../ui/Button.vue'
import Modal from '../ui/Modal.vue'
import FirebaseFileUpload from '../../modules/FirebaseFileUpload.js'

export default {
  components: { Breadcrumbs, Button, Modal },
  props: ['members', 'jwt'],
  data() {
    return {
      items: this.members.map((member) => ({ ...member, areas: member.areas || [] })),
      form: null,
      selected: null,
      saving: false,
      uploading: false,
      roles: ['Ordförande', 'Vice ordförande', 'Kassör', 'Sekreterare', 'Ledamot', 'Suppleant'],
      inputClass:
        'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gkk focus:outline-hidden focus:ring-1 focus:ring-gkk',
    }
  },
  methods: {
    initials(name) {
      const parts = (name || '').trim().split(/\s+/).filter(Boolean)
      if (parts.length === 0) return ''
      return (parts[0][0] + (parts.length > 1 ? parts[parts.length - 1][0] : '')).toUpperCase()
    },
    startCreate() {
      this.form = { id: null, name: '', role: 'Ledamot', photo_url: null, areasText: '', email: '' }
    },
    startEdit(member) {
      this.form = { ...member, areasText: member.areas.join(', '), email: member.email || '' }
      window.scrollTo({ top: 0, behavior: 'smooth' })
    },
    async uploadPhoto(event) {
      const file = event.target.files[0]
      event.target.value = ''
      if (!file) return

      this.uploading = true
      try {
        const extension = file.name.split('.').pop().toLowerCase()
        this.form.photo_url = await FirebaseFileUpload.upload(this.jwt, file, extension)
      } catch (error) {
        this.$toast.error('Bilden kunde inte laddas upp.')
      } finally {
        this.uploading = false
      }
    },
    async save() {
      const payload = {
        name: this.form.name,
        role: this.form.role,
        photo_url: this.form.photo_url || null,
        areas: this.form.areasText
          .split(',')
          .map((area) => area.trim())
          .filter(Boolean),
        email: this.form.email || null,
      }

      this.saving = true
      try {
        if (this.form.id) {
          const { data } = await axios.put(`/admin/board/${this.form.id}`, payload)
          this.items = this.items.map((member) => (member.id === data.id ? data : member))
        } else {
          const { data } = await axios.post('/admin/board', payload)
          this.items.push(data)
        }
        this.form = null
        this.$toast.success('Sparat')
      } catch (error) {
        const errors = error.response?.data?.errors
        this.$toast.error(errors ? Object.values(errors).flat().join(' ') : 'Kunde inte spara.')
      } finally {
        this.saving = false
      }
    },
    async move(index, direction) {
      const items = [...this.items]
      const [member] = items.splice(index, 1)
      items.splice(index + direction, 0, member)
      this.items = items

      try {
        await axios.post('/admin/board/order', { ids: items.map((item) => item.id) })
      } catch (error) {
        this.$toast.error('Ordningen kunde inte sparas.')
      }
    },
    confirmDelete(member) {
      this.selected = member
      this.$refs.deleteModal.show()
    },
    async destroy(close) {
      await axios.delete(`/admin/board/${this.selected.id}`)
      this.items = this.items.filter((member) => member.id !== this.selected.id)
      if (this.form?.id === this.selected.id) this.form = null
      close()
    },
  },
}
</script>
