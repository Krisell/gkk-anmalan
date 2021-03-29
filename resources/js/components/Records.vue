<template>
  <div class="container mx-auto max-w-3xl">
    <h1 class="text-center text-3xl font-hairline m2-6">Klubbrekord</h1>

    <div class="text-center">
      <div class="flex flex-col" v-for="gender in ['Kvinnor', 'Män']" :key="gender">
        <h2 class="font-hairline text-2xl mb-4 mt-6">{{ gender }}</h2>
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
          <div
            class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200"
          >
            <table class="min-w-full">
              <thead>
                <tr>
                  <th
                    class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Klass
                  </th>
                  <th
                    class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Gren (KSL)
                  </th>
                  <th
                    class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Namn
                  </th>
                  <th
                    class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Vikt (kg)
                  </th>
                  <th
                    class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Datum
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white">
                <tr v-for="result in recordsFor(gender)" :key="result.id">
                  <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                    <div class="flex items-center">
                      <div class="mx-auto">
                        <div class="text-sm leading-5 text-gray-900">
                          {{ result.weight_class }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                    <div class="flex items-center">
                      <div class="mx-auto">
                        <div class="text-sm leading-5 text-gray-900">
                          {{ result.event }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                    <div class="flex items-center">
                      <div class="mx-auto">
                        <div class="text-sm leading-5 text-gray-900">
                          {{ result.name || name(result.user) }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                    <div class="flex items-center">
                      <div class="mx-auto">
                        <div class="text-sm leading-5 text-gray-900">
                          {{ result.result }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                    <div class="flex items-center">
                      <div class="mx-auto">
                        <div
                          v-tooltip.bottom="'Nytt senaste året'"
                          :class="{ 'text-green-500': withinAYear(result.competition_date) }"
                          class="text-sm leading-5 text-gray-900"
                        >
                          {{ result.competition_date }}
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <GkkLink to="/" text="Tillbaka till startsidan" />
  </div>
</template>

<script>
import Date from '../modules/Date.js'

export default {
  props: ['results'],
  methods: {
    withinAYear(date) {
      return Date.withinAYear(date)
    },
    name(user) {
      return user ? `${user.first_name} ${user.last_name}` : ''
    },
    recordsFor(gender) {
      let genderResults = this.results.filter((result) =>
        gender === 'Kvinnor' ? result.gender === 'F' : result.gender === 'M',
      )

      return genderResults.sort((a, b) => {
        if (a.weight_class !== b.weight_class) {
          return a.weight_class.localeCompare(b.weight_class, undefined, { numeric: true, sensitivity: 'base' })
        }

        const eventOrder = ['Knäböj', 'Bänkpress', 'Marklyft', 'Totalt']
        return eventOrder.indexOf(a.event) - eventOrder.indexOf(b.event)
      })
    },
  },
}
</script>
