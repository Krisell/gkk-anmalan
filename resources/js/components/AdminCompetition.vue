<template>
  <div class="container mx-auto">
    <h2 class="text-center text-xl font-thin mb-6">Anmälningar till {{competition.name }}</h2>
    <div class="flex flex-col">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Anmäld
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Vill tävla
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Kön
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Gren
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Viktklass
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Kommentar
                </th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="registration in competition.registrations" :key="registration.id">
                <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm leading-5 font-medium text-gray-900">{{ registration.user.first_name }} {{ registration.user.last_name }}</div>
                      <div class="text-sm leading-5 text-gray-500">{{ registration.created_at | dateString}}</div>
                    </div>
                  </div>
                </td>

                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500">{{ registration.status == 1 ? 'Ja' : 'Nej' }}</div>
                </td>

                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500">{{ registration.gender }}</div>
                </td>

                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500">{{ registration.events | eventsString }}</div>
                </td>

                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500">{{ registration.weight_class }}</div>
                </td>

                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 max-w-16">{{ registration.comment }}</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="text-center cursor-pointer" @click="excel" v-tooltip="'Hämta Excel-fil'">
      <svg class="mx-auto" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
width="60" height="60"
viewBox="0 0 100 100"
style=" fill:#000000;"><path fill="#a0d2a1" d="M69.307,81.575h-39c-6.6,0-12-5.4-12-12v-39c0-6.6,5.4-12,12-12h39c6.6,0,12,5.4,12,12v39C81.307,76.175,75.907,81.575,69.307,81.575z"></path><path fill="#1f212b" d="M69.307,82.575h-39c-7.18,0-13-5.82-13-13v-39c0-7.18,5.82-13,13-13h39c7.18,0,13,5.82,13,13v39C82.307,76.755,76.487,82.575,69.307,82.575z M19.307,30.575v39c0,6.075,4.925,11,11,11h39c6.075,0,11-4.925,11-11v-39c0-6.075-4.925-11-11-11h-39C24.232,19.575,19.307,24.5,19.307,30.575z"></path><path fill="#fdfcee" d="M67.186,76.575H33.428c-5.566,0-10.121-4.554-10.121-10.121V32.696c0-5.566,4.554-10.121,10.121-10.121h33.759c5.566,0,10.121,4.554,10.121,10.121v33.759C77.307,72.02,72.753,76.575,67.186,76.575z"></path><path fill="#1f212b" d="M66.47 77.575H33.144c-5.985 0-10.837-4.852-10.837-10.837V33.413c0-5.986 4.852-10.838 10.838-10.838h33.662c.276 0 .5.224.5.5s-.224.5-.5.5H33.144c-5.433 0-9.837 4.404-9.837 9.837v33.326c0 5.433 4.404 9.837 9.837 9.837H66.47c5.433 0 9.837-4.404 9.837-9.837V48.075c0-.276.224-.5.5-.5s.5.224.5.5v18.663C77.307 72.723 72.455 77.575 66.47 77.575zM76.807 45.5c-.276 0-.5-.224-.5-.5v-4c0-.276.224-.5.5-.5s.5.224.5.5v4C77.307 45.276 77.083 45.5 76.807 45.5zM76.807 39.5c-.276 0-.5-.224-.5-.5v-2c0-.276.224-.5.5-.5s.5.224.5.5v2C77.307 39.276 77.083 39.5 76.807 39.5z"></path><path fill="#a0d2a1" d="M62.929 61.878L55.331 61.878 50.148 54.548 44.702 61.878 37.071 61.878 46.539 49.971 38.894 39.558 46.602 39.558 50.183 45.22 53.998 39.558 61.842 39.558 53.827 49.971 62.929 61.878"></path><path fill="#1f212b" d="M62.929,62.379h-7.598c-0.162,0-0.314-0.079-0.408-0.211l-4.785-6.769l-5.035,6.777c-0.094,0.127-0.243,0.202-0.401,0.202h-7.631c-0.192,0-0.367-0.11-0.45-0.283s-0.061-0.378,0.059-0.528l9.23-11.607l-7.418-10.106c-0.112-0.152-0.128-0.354-0.043-0.521c0.085-0.168,0.258-0.274,0.446-0.274h7.708c0.171,0,0.331,0.088,0.422,0.232l3.172,5.017l3.388-5.028c0.093-0.138,0.248-0.221,0.415-0.221h7.843c0.19,0,0.364,0.108,0.449,0.279c0.084,0.171,0.063,0.375-0.052,0.525l-7.781,10.11l8.869,11.603c0.115,0.151,0.135,0.354,0.051,0.525C63.293,62.27,63.12,62.379,62.929,62.379z M55.59,61.379h6.328L53.43,50.275c-0.137-0.18-0.137-0.43,0.001-0.608l7.395-9.609h-6.562L50.597,45.5c-0.094,0.141-0.252,0.235-0.422,0.221c-0.169-0.003-0.325-0.09-0.415-0.232l-3.434-5.431h-6.445l7.06,9.618c0.134,0.182,0.129,0.431-0.011,0.607l-8.823,11.096h6.344l5.296-7.129c0.095-0.129,0.246-0.204,0.407-0.202c0.16,0.002,0.31,0.08,0.402,0.211L55.59,61.379z"></path></svg>
    </div>
    <GkkLink to="/admin/competitions" text="Tillbaka till alla tävlingar" />
  </div>
</template>

<script>
import zipcelx from 'zipcelx'

export default {
  props: ['competition'],
  filters: {
    dateString (date) {
      return date.substr(0, 10)
    },
    eventsString (events) {
      return Object.entries(JSON.parse(events)).filter(([event, status]) => status).map(([event]) => event.toUpperCase()).join(', ')
    },
  },
  methods: {
    excel () {
      zipcelx({
        filename: 'gkk',
        sheet: {
          data: [
            [
              { value: this.competition.name, type: 'string' },
            ],
            [], // Empty row
            [
              { value: 'Namn', type: 'string' },
              { value: 'Datum', type: 'string' },
              { value: 'Vill tävla', type: 'string' },
              { value: 'Kön', type: 'string' },
              { value: 'Gren', type: 'string' },
              { value: 'Viktklass', type: 'string' },
              { value: 'Kommentar', type: 'string' },
            ],
            ...this.competition.registrations.map(registration => {
              return [
                { value: `${registration.user.first_name} ${registration.user.last_name}`, type: 'string' },
                { value: this.$options.filters.dateString(registration.created_at), type: 'string' },
                { value: registration.status == 1 ? 'Ja' : 'Nej', type: 'string' },
                { value: registration.gender, type: 'string' },
                { value: this.$options.filters.eventsString(registration.events), type: 'string' },
                { value: registration.weight_class, type: 'string' },
                { value: registration.comment, type: 'string' },
              ]
            })
          ]
        }
      })
    }
  }
}
</script>
