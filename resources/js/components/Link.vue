<template>
  <div class="flex mt-4 w-full">
    <Button @click="location(to)" class="mx-auto">
      <div class="w-full text-center">
        {{ text }}
      </div>
    </Button>
  </div>
</template>

<script>
import Button from './ui/Button.vue'

export default {
  components: { Button },
  props: ['to', 'text'],
}
</script>
