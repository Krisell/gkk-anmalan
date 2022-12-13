<template>
  <div class="container mx-auto max-w-3xl">
    <h1 class="text-center text-3xl font-thin mb-6">Musikhjälpen admin</h1>
    <div class="flex justify-center items-center gap-2 mb-6">
      <input
        class="rounded relative block w-32 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
        placeholder="Vikt"
        type="number"
        v-model="weight_lifted"
      /> kg x
      <input
        class="rounded relative block w-32 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
        placeholder="Repetitioner"
        type="number"
        v-model="repetitions"
      /> = {{ weight_lifted * repetitions }} kg
      <Button @click="send">
        <i class="fa fa-check-circle-o" style="margin-right: 10px"></i>Bokför
      </Button>
    </div>
    
    <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">

    <table class="min-w-full text-center">
      <thead>
        <tr>
          <th
            class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
          >
            Tidpunkt
          </th>
          <th
            class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
          >
            Vikt
          </th>
          <th
            class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
          >
            Reps
          </th>
          <th
            class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
          >
            Totalt lyft
          </th>
          <th
            class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
          >
          </th>
        </tr>
      </thead>
      <tbody class="bg-white">
        <tr v-for="set in musicHelpSets" :key="set.id">
          <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
            <div class="text-sm leading-5 font-medium text-gray-900">
              {{ dateString(set.created_at) }}
            </div>
          </td>
          <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
            <div class="text-sm leading-5 font-medium text-gray-900">
              {{ set.weight_lifted }} kg
            </div>
          </td>
          <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
            <div class="text-sm leading-5 font-medium text-gray-900">
              {{ set.repetitions }}
            </div>
          </td>
          <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
            <div class="text-sm leading-5 font-medium text-gray-900">
              {{ set.repetitions * set.weight_lifted }} kg
            </div>
          </td>
            <td class="px-6 py-2 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
              <Button class="ml-2" type="secondary" @click="deleteSet(set)">Radera set</Button>
            </td>
        </tr>
      </tbody>
    </table>
  </div>
  </div>
</template>

<script>
import Button from './ui/Button.vue'
import moment from 'moment'
import axios from 'axios'

export default {
  components: { Button },
  data() {
    return {
      weight_lifted: '',
      repetitions: '',
    }
  },
  props: {
    musicHelpSets: {
      type: Array,
      required: true,
    },
  },
  methods: {
    dateString(date) {
      console.log(date)
      return moment.utc(date).local().format('YYYY-MM-DD HH:mm:ss')
    },
    async send() {
      if (!this.weight_lifted || !this.repetitions) {
        return
      }

      await axios.post('/admin/music', {
        weight_lifted: this.weight_lifted,
        repetitions: this.repetitions,
      })

      window.location.reload()
    },
    async deleteSet(set) {
      await axios.delete('/admin/music/' + set.id)

      window.location.reload()
    }
  }
}
</script>
