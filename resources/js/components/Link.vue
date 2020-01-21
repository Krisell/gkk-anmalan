<template>
    <el-button style="margin: auto; margin-top: 20px;" link @click="location(to)">
      {{ text }}
    </el-button>
</template>

<script>
export default {
  props: ['to', 'text'],
}
</script>

<style scoped>
  button {
    outline: none;
  }
</style>