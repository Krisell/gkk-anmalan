<template>
  <div class="inline">
    <div class="inline" v-if="!remaining">{{ liftedWeight }}</div>
    <div class="inline" v-if="remaining">{{ remainingWeight }}</div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      liftedWeight: this.liftedWeightStart,
      donated: this.donatedStart.replace('kr', ''),
    }
  },
  props: {
    liftedWeightStart: {
      type: Number,
      required: true
    },
    donatedStart: {
      type: String,
      default: '',
    },
    remaining: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    remainingWeight() {
      console.log(this.donated.replace(/\s*/g, ''), this.liftedWeight)
      return Number(this.donated.replace(/\s*/g, '')) - Number(this.liftedWeight)
    }
  }
}
</script>

