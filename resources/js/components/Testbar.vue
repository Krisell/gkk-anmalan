<template>
  <div v-show="show">
    Testläge aktiverat.
    <br>
    All data kan raderas när som helst.
    <br>
    Alla användare har admin-access.
    <br>
    Välkommen att testa fritt.
    <i class="fa fa-close close-button" @click="hide"></i>
  </div>
</template>

<script>
export default {
  data () {
    return {
      show: true,
    }
  },
  mounted () {
    if (window.sessionStorage.getItem('hide-test-bar') === 'true') {
      this.show = false
    }
  },
  methods: {
    hide () {
      window.sessionStorage.setItem('hide-test-bar', 'true')
      this.show = false
    }
  }
}
</script>

<style scoped>
  div {
    position: fixed;
    bottom: 0;
    width: 100%;
    text-align: center;
    background: orange;
    color: white;
    padding: 5px;
  }

  .close-button {
    position: absolute;
    cursor: pointer;
    right: 3px;
    top: 0px;
    font-size: 40px;
  }
</style>