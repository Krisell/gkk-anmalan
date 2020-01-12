<template>
    <el-button style="margin: auto; margin-top: 20px;" link @click="back">
      {{ text }}
    </el-button>
</template>

<script>
export default {
  props: ['to', 'text'],
  methods: {
    back () {
      window.location = this.to
    }
  }
}
</script>

<style scoped>
  button {
    outline: none;
  }
</style>