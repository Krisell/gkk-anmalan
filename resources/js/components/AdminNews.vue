<template>
  <div>
    <h1>Ny nyhet</h1>
    <el-input style="font-size: 20px; padding: 20px;" v-model="title" placeholder="Nyhetens titel"></el-input>

    <h3 style="margin-top: 30px;">Nyhetens text</h3>
    <trix-editor @trix-change="change" class="trix-content"></trix-editor>

    <el-button style="margin-top: 10px;" @click="create">Skapa nyhet</el-button>
  </div>
</template>

<script>
export default {
  data () {
    return {
      title: '',
      body: '',
    }
  },
  methods: {
    change (event) {
      const html = event.target.value
      this.body = html
    },
    create () {
      console.log(this.title, this.body)
      window.axios({
        method: 'post',
        url: '/admin/news',
        data: {
          title: this.title,
          body: this.body,
        },
      }).then(response => {
        window.location = '/'
      })
    }
  }
}
</script>

<style>

</style>