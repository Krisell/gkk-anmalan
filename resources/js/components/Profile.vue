<template>
  <div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-thin mb-2">Profil</h1>
    <div>Inloggad som {{ user.email }}</div>
    <div style="margin-top: 30px"></div>

    <div class="mt-8 flex items-center">
      <Button type="secondary" @click="showMemberShipAgreement">Visa medlemsavtal</Button>
      <div class="ml-6 text-sm italic">Signerat {{ renderDate(user.membership_agreement_signed_at) }}</div>
    </div>
    <div class="mt-2 flex items-center">
      <Button type="secondary" @click="showAntiDopingAgreement">Visa antidopingavtal</Button>
      <div class="ml-6 text-sm italic">Signerat {{ renderDate(user.anti_doping_agreement_signed_at) }}</div>
    </div>

    <h2 class="font-thin mt-6">Namn</h2>
    <input
      type="text"
      v-model="name.first"
      class="form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md"
    />
    <input
      type="text"
      v-model="name.last"
      class="mt-1 form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md"
    />
    <button
      @click="updateName"
      class="mt-2 inline-flex items-center px-4 py-2 border text-sm leading-5 font-medium rounded-md text-gkk bg-white border-gkk focus:outline-none focus:shadow-outline-indigo active:bg-gkk transition duration-150 ease-in-out"
    >
      <span>Uppdatera namn</span>
    </button>

    <div class="my-8 border-b border-gray-200 border-1"></div>

    <h2 class="font-thin">Epost</h2>
    <input
      v-model="email"
      type="email"
      class="form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md"
    />
    <button
      @click="updateEmail"
      class="mt-2 inline-flex items-center px-4 py-2 border text-sm leading-5 font-medium rounded-md text-gkk bg-white border-gkk focus:outline-none focus:shadow-outline-indigo active:bg-gkk transition duration-150 ease-in-out"
    >
      <span>Uppdatera epost</span>
    </button>

    <div class="my-8 border-b border-gray-200 border-1"></div>

    <div v-if="!isAdjusted.password">
      <button
        @click="startEdit('password')"
        class="mt-2 inline-flex items-center px-4 py-2 border text-sm leading-5 font-medium rounded-md text-gkk bg-white border-gkk focus:outline-none focus:shadow-outline-indigo active:bg-gkk transition duration-150 ease-in-out"
      >
        <span>Redigera lösenord</span>
      </button>
    </div>

    <div v-else>
      <input
        v-model="password.new"
        type="password"
        placeholder="Nytt lösenord"
        class="form-input block w-full sm:text-sm sm:leading-5"
      />
      <input
        v-model="password.new_confirmation"
        type="password"
        placeholder="Bekräfta lösenord"
        class="mt-1 form-input block w-full sm:text-sm sm:leading-5"
      />
      <div style="display: flex">
        <button
          @click="reset"
          class="mt-2 inline-flex items-center px-4 py-2 border text-sm leading-5 font-medium rounded-md text-gkk bg-white border-gkk focus:outline-none focus:shadow-outline-indigo active:bg-gkk transition duration-150 ease-in-out"
        >
          <span class="text-red-400">Ångra</span>
        </button>

        <button
          @click="updatePassword"
          class="ml-2 mt-2 inline-flex items-center px-4 py-2 border text-sm leading-5 font-medium rounded-md text-gkk bg-white border-gkk focus:outline-none focus:shadow-outline-indigo active:bg-gkk transition duration-150 ease-in-out"
        >
          <span>Byt lösenord</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import Documents from '../modules/Documents.js'
import Button from './ui/Button.vue'

export default {
  components: { Button },
  props: ['user'],
  data() {
    return {
      isAdjusted: {
        name: false,
        email: false,
        password: false,
      },
      error: {
        password: false,
        data: false,
      },
      edit: '',
      name: {
        first: this.user.first_name,
        last: this.user.last_name,
      },
      email: this.user.email,
      password: {
        current: '',
        new: '',
        new_confirmation: '',
      },
      errors: {
        password: false,
      },
    }
  },
  methods: {
    showMemberShipAgreement() {
      window.open(Documents.MEMBERSHIP_AGREEMENT)
    },
    showAntiDopingAgreement() {
      window.open(Documents.ANTI_DOPING_AGREEMENT)
    },
    renderDate(date) {
      return new Date(date).toLocaleDateString('sv-SE')
    },
    startEdit(type) {
      this.reset()
      this.isAdjusted[type] = true
    },
    reset(type = '') {
      this.password.current = ''
      this.edit = ''

      for (var attr in this.isAdjusted) {
        this.isAdjusted[attr] = false
      }

      if (type === 'email') {
        this.email = this.user.email
      }

      if (type === 'name') {
        this.name = {
          firstName: this.user.first_name,
          lastName: this.user.last_name,
        }
      }
    },
    updateName() {
      window.axios
        .post('/profile/name', {
          first_name: this.name.first,
          last_name: this.name.last,
        })
        .then(this.reload)
    },
    updateEmail() {
      window.axios.post('/profile/email', { email: this.email }).then(this.reload)
    },
    updatePassword() {
      window.axios
        .post('/profile/password', {
          password: this.password.new,
          password_confirmation: this.password.new_confirmation,
        })
        .then(this.reload)
        .catch(() => (this.errors.password = true))
    },
  },
}
</script>
