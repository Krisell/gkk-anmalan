<template>
  <div class="flex mt-4 w-full">
    <ui-button @click="location(to)" class="mx-auto">
      <div class="w-full text-center">
        {{ text }}
      </div>
    </ui-button>
  </div>
</template>

<script>
export default {
  props: ['to', 'text'],
}
</script>
