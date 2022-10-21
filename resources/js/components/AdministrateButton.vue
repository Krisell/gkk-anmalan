<template>
    <div 
      class="flex justify-center mb-4" 
      @click="goto(path)"
    >
      <Button type="danger">Klicka för att administrera</Button>
    </div>
</template>

<script>
import Button from './ui/Button.vue'

export default {
    components: { Button },
    props: {
        path: String,
    },
    methods: {
        goto(path) {
            window.location = path
        },
    },
}
</script> 