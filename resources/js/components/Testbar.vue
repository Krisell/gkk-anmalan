<template>
  <div v-show="show">
    Testläge aktiverat. Testdata (inkl. konton) kan raderas när som helst.
    <br>
    Alla användare har admin-access. Välkommen att testa fritt.
    <i class="fa fa-close close-button" @click="show = false"></i>
  </div>
</template>

<script>
export default {
  data () {
    return {
      show: true,
    }
  },
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
    right: 10px;
    top: 10px;
    font-size: 30px;
    cursor: pointer;
  }
</style>