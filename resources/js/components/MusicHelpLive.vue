<template>
  <div class="flex flex-col sm:flex-row items-center">
      <div
        :class="{ updatedLeft: updatedDonatedAmount }"
        target="_blank"
        class="animated rounded mr-2 inline-flex items-center px-4 py-2 border text-white leading-5 font-medium  focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
        href="https://bossan.musikhjalpen.se/goeteborg-kraftsportklubb-lyfter-1-kg-per-donerad-krona">
        <img width="60px" src="https://goteborg-kraftsportklubb.web.app/mh-logo.png?v=2">
        <h3 class="ml-4 text-black text-xl font-bold">Donerat: {{ donatedAmount }} kr</h3>
      </div>
      <div class="mr-2 relative">
        <img src="https://goteborg-kraftsportklubb.web.app/img/logo-min.png" style="width: 96px;position: absolute;top: 12px;left: 12px;"/>
        <svg
          :class="{ updated: updatedDonatedAmount || updatedLiftedWeight }"
          class="animated"
          height="120"
          width="120"
          style="position: absolute; z-index: 9"
        >
          <circle
            :stroke="strokeColor"
            stroke-dasharray="360"
            :style="`stroke-dashoffset: ${remainingOfCircle}`"
            stroke-width="7"
            fill="transparent"
            r="52"
            cx="60"
            cy="60"
          />
        </svg>
        <svg
          :class="{ updated: updatedDonatedAmount || updatedLiftedWeight }"
          class="animated"
          height="120"
          width="120"
        >
          <circle
            stroke="#ddd"
            stroke-dasharray="360"
            :style="`stroke-dashoffset: 0`"
            stroke-width="3"
            fill="transparent"
            r="52"
            cx="60"
            cy="60"
          />
        </svg>
      </div>
      <div
      :class="{ updatedRight: updatedLiftedWeight }"
      class="animated rounded inline-flex items-center px-4 py-2 border text-white leading-5 font-medium  focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
    >
      <img width="60px" src="https://goteborg-kraftsportklubb.web.app/mh-logo.png?v=2">
      <h3 class="ml-4 text-black text-xl font-bold">Antal lyfta kilon: {{ liftedWeight }} kg</h3>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      liftedWeight: this.liftedWeightStart,
      donatedAmount: this.donatedStart.replace('kr', ''),
      updatedLiftedWeight: false,
      updatedDonatedAmount: false,
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
    },
    remainingOfCircle() {
      return (1 - (Number(this.liftedWeight) / Number((this.donatedAmount || '0').replace(/\s*/g, '')))) * 327 + 33
    },
    strokeColor() {
      if (this.remainingOfCircle > 33) {
        return '#253868'
      }

      return 'green'
    }
  },
  methods: {
    async update() {
      try {
        const response = await axios.get('/api/music', { timeout: 5000 })

        const newLiftedWeight = response.data.liftedWeight
        const newDonatedAmount = response.data.donatedAmount.replace('kr', '')

        if (newLiftedWeight !== this.liftedWeight) {
          if (newLiftedWeight > this.liftedWeight) {
            const audio = new Audio('https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fapplause-2.mp3?alt=media&token=02b36d78-78a8-4615-bb9b-d83e567fa6e8')
            audio.play()
          }

          this.liftedWeight = newLiftedWeight
          this.updatedLiftedWeight = true
          setTimeout(() => this.updatedLiftedWeight = false, 800)
        }

        if (newDonatedAmount !== this.donatedAmount) {
          this.donatedAmount = newDonatedAmount
          this.updatedDonatedAmount = true
          setTimeout(() => this.updatedDonatedAmount = false, 800)

          const audio = new Audio('https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fcha-ching-7053.mp3?alt=media&token=e899e985-7282-45bd-add4-97b9e7294563')
          audio.play()
        }
      } catch (error) {
        console.log(error)
      }

      window.setTimeout(() => this.update(), 2000)
    },  
  },
  mounted() {
    window.setTimeout(() => this.update(), 2000)
  },
}
</script>

<style scoped>
.animated {
  transition: transform 0.8s ease-out;
}
.updated {
  transform: scale(1.3);
}

.updatedLeft {
  transform: scale(1.2) translateX(-50px);
}

.updatedRight {
  transform: scale(1.2) translateX(50px);
  
}

circle {
    transition: stroke-dashoffset 1s ease-in-out;
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
  }
</style>

