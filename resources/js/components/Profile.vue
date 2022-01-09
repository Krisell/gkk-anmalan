<template>
  <div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-thin mb-2">Profil</h1>
    <div style="margin-top: 30px"></div>
    <h2 class="font-thin">Namn</h2>
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
      class="mt-2 relative inline-flex items-center px-4 py-2 border text-sm leading-5 font-medium rounded-md text-gkk bg-white border-gkk focus:outline-none focus:shadow-outline-indigo active:bg-gkk transition duration-150 ease-in-out"
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
      class="mt-2 relative inline-flex items-center px-4 py-2 border text-sm leading-5 font-medium rounded-md text-gkk bg-white border-gkk focus:outline-none focus:shadow-outline-indigo active:bg-gkk transition duration-150 ease-in-out"
    >
      <span>Uppdatera epost</span>
    </button>

    <div class="my-8 border-b border-gray-200 border-1"></div>

    <div v-if="!isAdjusted.password">
      <button
        @click="startEdit('password')"
        class="mt-2 relative inline-flex items-center px-4 py-2 border text-sm leading-5 font-medium rounded-md text-gkk bg-white border-gkk focus:outline-none focus:shadow-outline-indigo active:bg-gkk transition duration-150 ease-in-out"
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
          class="mt-2 relative inline-flex items-center px-4 py-2 border text-sm leading-5 font-medium rounded-md text-gkk bg-white border-gkk focus:outline-none focus:shadow-outline-indigo active:bg-gkk transition duration-150 ease-in-out"
        >
          <span class="text-red-400">Ångra</span>
        </button>

        <button
          @click="updatePassword"
          class="ml-2 mt-2 relative inline-flex items-center px-4 py-2 border text-sm leading-5 font-medium rounded-md text-gkk bg-white border-gkk focus:outline-none focus:shadow-outline-indigo active:bg-gkk transition duration-150 ease-in-out"
        >
          <span>Byt lösenord</span>
        </button>
      </div>
    </div>

    <GkkLink to="/insidan" text="Tillbaka till insidans navigering" />
  </div>
</template>

<script>
export default {
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
