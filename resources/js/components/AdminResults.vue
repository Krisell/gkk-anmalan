<template>
  <div class="container mx-auto max-w-3xl">
    <h1 class="text-center text-3xl font-thin m2-6">Administration av klubbrekord</h1>

    <h3 class="text-center text-xl font-thin m2-6">Nytt resultat</h3>
    <form class="mt-4 mb-6 max-w-xl mx-auto">
      <div class="mt-4">
        <div class="flex items-center">
          <input
            value="F"
            v-model="result.gender"
            type="radio"
            id="gender_F"
            class="form-radio h-6 w-6 text-gkk transition duration-150 ease-in-out"
          />
          <label for="gender_F" class="ml-3">
            <span class="block text-sm leading-5 font-medium text-gray-700">Kvinnor</span>
          </label>
        </div>
        <div class="mt-4 flex items-center">
          <input
            value="M"
            v-model="result.gender"
            type="radio"
            id="gender_M"
            class="form-radio h-6 w-6 text-gkk transition duration-150 ease-in-out"
          />
          <label for="gender_M" class="ml-3">
            <span class="block text-sm leading-5 font-medium text-gray-700">Män</span>
          </label>
        </div>
      </div>

      <div class="mb-2 mt-6">
        <h4 class="ml-2 text-md font-thin">Lyftare</h4>
        <select
          v-model="result.user_id"
          class="
            mt-1
            block
            form-select
            w-64
            py-2
            px-3
            border border-gray-300
            bg-white
            rounded-md
            shadow-sm
            focus:outline-none focus:shadow-outline-blue focus:border-blue-300
            transition
            duration-150
            ease-in-out
            sm:text-sm sm:leading-5
          "
        >
          <option v-for="lifter in users" :key="lifter.key" :value="lifter.id">
            {{ lifter.first_name }} {{ lifter.last_name }}
          </option>
        </select>
        <h5 class="text-xs font-thin">Om personen saknas här behöver han/hon först skapa ett konto.</h5>
      </div>

      <div class="mb-2 mt-6">
        <h4 class="ml-2 text-md font-thin">Viktklass (kg)</h4>
        <select
          v-model="result.weight_class"
          v-if="result.gender === 'M'"
          class="
            mt-1
            block
            form-select
            w-64
            py-2
            px-3
            border border-gray-300
            bg-white
            rounded-md
            shadow-sm
            focus:outline-none focus:shadow-outline-blue focus:border-blue-300
            transition
            duration-150
            ease-in-out
            sm:text-sm sm:leading-5
          "
        >
          <option>52</option>
          <option>59</option>
          <option>66</option>
          <option>74</option>
          <option>83</option>
          <option>93</option>
          <option>105</option>
          <option>120</option>
          <option>120+</option>
        </select>

        <select
          v-model="result.weight_class"
          v-if="result.gender === 'F'"
          class="
            mt-1
            block
            form-select
            w-64
            py-2
            px-3
            border border-gray-300
            bg-white
            rounded-md
            shadow-sm
            focus:outline-none focus:shadow-outline-blue focus:border-blue-300
            transition
            duration-150
            ease-in-out
            sm:text-sm sm:leading-5
          "
        >
          <option>43</option>
          <option>47</option>
          <option>52</option>
          <option>57</option>
          <option>63</option>
          <option>72</option>
          <option>84</option>
          <option>84+</option>
        </select>
      </div>

      <div class="mb-2 mt-2">
        <h4 class="ml-2 text-md font-thin">Gren</h4>
        <select
          v-model="result.event"
          class="
            mt-1
            block
            form-select
            w-64
            py-2
            px-3
            border border-gray-300
            bg-white
            rounded-md
            shadow-sm
            focus:outline-none focus:shadow-outline-blue focus:border-blue-300
            transition
            duration-150
            ease-in-out
            sm:text-sm sm:leading-5
          "
        >
          <option>Knäböj</option>
          <option>Bänkpress</option>
          <option>Marklyft</option>
          <option>Total</option>
        </select>
      </div>

      <div class="mb-2 mt-2">
        <h4 class="ml-2 text-md font-thin">Datum</h4>
        <input
          v-model="result.competition_date"
          class="
            appearance-none
            rounded-none
            relative
            block
            w-64
            px-3
            py-2
            border border-gray-300
            placeholder-gray-500
            text-gray-900
            focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10
            sm:text-sm sm:leading-5
          "
          type="date"
          name="date"
        />
      </div>

      <div class="mb-2 mt-2">
        <h4 class="ml-2 text-md font-thin">Resultat (kg)</h4>
        <input
          v-model="result.result"
          class="
            appearance-none
            rounded-none
            relative
            block
            w-64
            px-3
            py-2
            border border-gray-300
            placeholder-gray-500
            text-gray-900
            focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10
            sm:text-sm sm:leading-5
          "
          type="number"
          name="date"
        />
      </div>
      <div class="flex mt-4">
        <ui-button prevent @click="createResult">Skapa resultat</ui-button>
      </div>

      <div v-if="createResultError" class="mt-2">
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path
                  fill-rule="evenodd"
                  d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm leading-5 text-yellow-700">
                Kunde inte skapa tävling, kontrollera inmatning och anlutning.
              </p>
            </div>
          </div>
        </div>
      </div>
    </form>

    <div class="text-center">
      <div class="flex flex-col" v-for="gender in ['Kvinnor', 'Män']" :key="gender">
        <h2 class="font-thin text-2xl mb-4 mt-6">{{ gender }}</h2>
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
          <div
            class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200"
          >
            <table class="min-w-full">
              <thead>
                <tr>
                  <th
                    class="
                      px-6
                      py-3
                      border-b border-gray-200
                      bg-gray-50
                      text-center text-xs
                      leading-4
                      font-medium
                      text-gray-500
                      uppercase
                      tracking-wider
                    "
                  >
                    Klass
                  </th>
                  <th
                    class="
                      px-6
                      py-3
                      border-b border-gray-200
                      bg-gray-50
                      text-center text-xs
                      leading-4
                      font-medium
                      text-gray-500
                      uppercase
                      tracking-wider
                    "
                  >
                    Gren (KSL)
                  </th>
                  <th
                    class="
                      px-6
                      py-3
                      border-b border-gray-200
                      bg-gray-50
                      text-center text-xs
                      leading-4
                      font-medium
                      text-gray-500
                      uppercase
                      tracking-wider
                    "
                  >
                    Namn
                  </th>
                  <th
                    class="
                      px-6
                      py-3
                      border-b border-gray-200
                      bg-gray-50
                      text-center text-xs
                      leading-4
                      font-medium
                      text-gray-500
                      uppercase
                      tracking-wider
                    "
                  >
                    Vikt (kg)
                  </th>
                  <th
                    class="
                      px-6
                      py-3
                      border-b border-gray-200
                      bg-gray-50
                      text-center text-xs
                      leading-4
                      font-medium
                      text-gray-500
                      uppercase
                      tracking-wider
                    "
                  >
                    Datum
                  </th>
                  <th
                    class="
                      px-6
                      py-3
                      border-b border-gray-200
                      bg-gray-50
                      text-center text-xs
                      leading-4
                      font-medium
                      text-gray-500
                      uppercase
                      tracking-wider
                    "
                  ></th>
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
                          v-if="withinAYear(result.competition_date)"
                          v-tooltip.bottom="'Nytt senaste året'"
                          class="text-sm leading-5 text-green-500"
                        >
                          {{ result.competition_date }}
                        </div>
                        <div v-else class="text-sm leading-5 text-gray-900">
                          {{ result.competition_date }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                    <i @click="remove(result)" class="fa fa-trash cursor-pointer"></i>
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
import axios from 'axios'

export default {
  props: ['results', 'users'],
  data() {
    return {
      result: {
        gender: 'F',
        competition_date: '',
        weight_class: '',
        event: 'Knäböj',
        user_id: 0,
        result: '',
      },
      createResultError: false,
    }
  },
  methods: {
    remove(result) {
      axios.delete(`/admin/results/${result.id}`).then(() => window.location.reload())
    },
    createResult() {
      axios.post('/admin/results', this.result).then(() => window.location.reload())
    },
    withinAYear(date) {
      return Date.withinAYear(date)
    },
    name(user) {
      return user ? `${user.first_name} ${user.last_name}` : ''
    },
    recordsFor(gender) {
      const genderResults = this.results.filter((result) =>
        gender === 'Kvinnor' ? result.gender === 'F' : result.gender === 'M',
      )

      // Filter out the best result in each class, and then sort based on class and event
      return genderResults
        .filter(
          (res) =>
            genderResults.filter(
              (r) =>
                r.weight_class === res.weight_class && r.event === res.event && Number(r.result) > Number(res.result),
            ).length === 0,
        )
        .sort((a, b) => {
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
