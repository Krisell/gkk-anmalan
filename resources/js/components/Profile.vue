<template>
  <div>
    <h1>Profil</h1>
    <div style="margin-top: 30px;"></div>
    <el-header>Namn</el-header>
    <div v-if="!isAdjusted.name">
        <el-info class="mb-2">{{ user.first_name }} {{ user.last_name }}</el-info>
        <el-button @click="startEdit('name')" secondary>Redigera</el-button>
    </div>
    <div v-else>
        <el-input class="mb-2" placeholder="Förnamn" v-model="name.first"></el-input>
        <el-input class="mb-2" placeholder="Efternamn" v-model="name.last"></el-input>

        <div style="display: flex">
          <el-button class="mr-2" @click="reset()" secondary>Ångra</el-button>
          <el-button @click="updateName">Ändra namn</el-button>
        </div>
    </div>

    <el-delimiter></el-delimiter>

    <div v-if="!isAdjusted.email">
      <el-info class="mb-2">{{ user.email }}</el-info>
      <el-button @click="startEdit('email')" secondary>Redigera</el-button>
    </div>
    <div v-else>
      <el-input class="mb-2" placeholder="E-post" v-model="email"></el-input>

      <div style="display: flex">
        <el-button class="mr-2" @click="reset()" secondary>Ångra</el-button>
        <el-button @click="updateEmail">Ändra e-post</el-button>
      </div>
    </div>

    <el-delimiter></el-delimiter>

    <div v-if="!isAdjusted.password">
      <el-button @click="startEdit('password')" secondary>Redigera lösenord</el-button>
    </div>
    <div v-else>
      <el-input class="mb-2" placeholder="Nytt lösenord" type="password" v-model="password.new"></el-input>
      <el-input class="mb-2" placeholder="Bekräfta lösenord" type="password" v-model="password.new_confirmation"></el-input>

      <div style="display: flex">
        <el-button class="mr-2" @click="reset()" secondary>Ångra</el-button>
        <el-button @click="updatePassword">Byt lösenord</el-button>
      </div>
      <p v-if="errors.password" style="color: red;">Kunde inte uppdatera lösenordet. Kontrollera inmatning.</p>
    </div>
  </div>
</template>

<script>
export default {
  props: ['user'],
  data () {
    return {
      isAdjusted: {
          name: false,
          email: false,
          password: false
      },
      error: {
        password: false,
        data: false
      },
      edit: '',
      name: {
          first: this.user.first_name,
          last: this.user.last_name
      },
      email: this.user.email,
      password: {
          current: '',
          new: '',
          new_confirmation: ''
      },
      errors: {
        password: false,
      },
    }
  },
  methods: {
    startEdit (type) {
      this.reset()
      this.isAdjusted[type] = true
    },
    reset (type = '') {
      this.password.current = ''
      this.edit = ''

      for (var attr in this.isAdjusted){
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
    updateName () {
      window.axios.post('/profile/name', {
        first_name: this.name.first,
        last_name: this.name.last,
      }).then(this.reload)
    },
    updateEmail () {
      window.axios.post('/profile/email', { email: this.email }).then(this.reload)
    },
    updatePassword () {
      window.axios.post('/profile/password', {
        password: this.password.new,
        password_confirmation: this.password.new_confirmation,
      }).then(this.reload).catch(() => this.errors.password = true)
    },
  }
}
</script>

<style scoped>
  .mb-2 {
    margin-bottom: 10px;
  }

  .mr-2 {
    margin-right: 10px;
  }

</style>