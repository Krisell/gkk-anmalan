<template>
  <div>
    <div class="mx-auto py-6 px-4 max-w-7xl sm:px-6 lg:px-8">
      <div class="space-y-12">
        <div class="space-y-5 sm:space-y-4 md:max-w-xl lg:max-w-3xl xl:max-w-none">
          <div class="flex items-center">
            <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Lag-SM 2024</h2>
          </div>
          <h3 class="text-xl font-extrabold tracking-tight sm:text-2xl">
            GKK Tog medalj i alla 4 tävlingsklasser (Herrar och Damer i Klassisk Styrkelyft och Klassisk Bänkpress). Årets bedrift!
          </h3>
          <!-- <p>
            Kompletta serier hittar ni här:
            <a class="underline" target="_blank" href="https://online.styrkelyft.se/web/series.aspx"
              >https://online.styrkelyft.se/web/series.aspx</a
            >
          </p> -->

          <!-- <p class="text-xl leading-normal text-gray-500">
              Den 16-17 december 2023 avögrs Lag-SM i Styrkelyft hos Göteborg Kraftsportklubb.
              </p>
              <p class="text-xl leading-normal text-gray-500">
                <b>Klassisk styrkelyft:</b> Lördag 16 december (herrar först, därefter damer)
                <br>
                <b>Klassisk bänkpress:</b> Söndag 17 december (herrar först, därefter damer)
              </p>
              <p class="text-xl leading-normal text-gray-500">
                Varmt välkoma att komma och titta!
              </p> -->
        </div>
      </div>

      <!-- <div class="flex items-center mt-6">
            <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl">Tävlingsinformation</h2>
        </div>
        <p class="text-xl leading-normal text-gray-500 mt-2">
            <div class="mt-4 ml-2 mb-2"><b>Plats:</b> Göteborg Kraftsportklubb, Karl Johansgatan 152, 414 51 Göteborg</div>
            <img 
                class="rounded-2xl shadow-xl cursor-pointer inline-block ml-2 w-[500px]"
                @click="showImage('https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fkarta%20(1).png?alt=media&token=5668792a-f351-4175-b1ab-87e0b59215f0')" 
                src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fkarta%20(1).png?alt=media&token=5668792a-f351-4175-b1ab-87e0b59215f0"
            >
        </p>
        <p class="text-xl leading-normal text-gray-500 mt-2">
            <div class="mt-6 ml-2 mb-2"><b>Ställning:</b> Eleiko med hård dyna</div>
            <img 
                class="rounded-2xl shadow-xl cursor-pointer inline-block ml-2 w-[500px]"
                @click="showImage('https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fstallning.png?alt=media&token=bed8ed7c-e478-426f-80b8-e3e9caa44b9d')" 
                src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fstallning.png?alt=media&token=bed8ed7c-e478-426f-80b8-e3e9caa44b9d"
            >
        </p>

        <div class="flex items-center mt-6">
            <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl">Lag i final just nu (top 5)</h2>
        </div>
        <p class="text-xl leading-normal text-gray-500 mt-2">
            Vilka som får göra upp avgörs efter Serie 4 där sista inrapporteringsdag är 28 november.
        </p>
        <p>
            <select
                v-model="showRound"
                class="mt-4 form-select block pl-3 pr-10 py-2 text-base leading-6 border-gray-300 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 sm:text-sm sm:leading-5"
            >
                <option value="latest">Visa aktuell ställning under pågående omgång</option>
                <option value="three">Visa ställningen efter 3 avslutade omgångar</option>
            </select>
        </p> -->
      <!-- <div class="mt-4 mb-32 flex items-between lg:items-center justify-start flex-col lg:flex-row">
        <div class="flex flex-1">
          <div class="flex-1">
            <h3 class="mt-4 font-bold">KSL Herrar</h3>
            <ul role="list" class="divide-y divide-gray-100">
              <li
                v-for="(team, index) in sortedKSLHerr().slice(0, TEAMS_TO_KEEP)"
                :key="team.team"
                class="flex gap-x-4 py-3"
              >
                <img class="h-12 w-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                <div class="min-w-0">
                  <p class="text-sm font-semibold leading-6 text-gray-900">
                    {{ index + 1 }}. {{ team.team.replace(/För./, '') }}
                  </p>
                  <p class="mt-1 truncate text-xs leading-5 text-gray-500">
                    {{ showRound === 'latest' ? team.points : team.afterThreeRounds }} poäng
                  </p>
                </div>
              </li>
              <li>
                <div class="border border-t-0 border-l-0 border-r-0 border-b-1 border-gkk opacity-50 h-4"></div>
              </li>
              <li
                v-for="(team, index) in sortedKSLHerr().slice(TEAMS_TO_KEEP, TEAMS_TO_KEEP + 3)"
                :key="team.team"
                class="flex gap-x-4 py-3 opacity-20"
              >
                <img class="h-12 w-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                <div class="min-w-0">
                  <p class="text-sm font-semibold leading-6 text-gray-900">
                    {{ index + 1 + TEAMS_TO_KEEP }}. {{ team.team.replace(/För./, '') }}
                  </p>
                  <p class="mt-1 truncate text-xs leading-5 text-gray-500">
                    {{ showRound === 'latest' ? team.points : team.afterThreeRounds }} poäng
                  </p>
                </div>
              </li>
            </ul>
          </div>

          <div class="flex-1">
            <h3 class="mt-4 font-bold">KSL Damer</h3>
            <ul role="list" class="divide-y divide-gray-100">
              <li
                v-for="(team, index) in sortedKSLDam().slice(0, TEAMS_TO_KEEP)"
                :key="team.team"
                class="flex gap-x-4 py-3"
              >
                <img class="w-12 h-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                <div class="min-w-0">
                  <p class="text-sm font-semibold leading-6 text-gray-900">{{ index + 1 }}. {{ team.team }}</p>
                  <p class="mt-1 truncate text-xs leading-5 text-gray-500">
                    {{ showRound === 'latest' ? team.points : team.afterThreeRounds }} poäng
                  </p>
                </div>
              </li>
              <li>
                <div class="border border-t-0 border-l-0 border-r-0 border-b-1 border-gkk opacity-50 h-4"></div>
              </li>
              <li
                v-for="(team, index) in sortedKSLDam().slice(TEAMS_TO_KEEP, TEAMS_TO_KEEP + 3)"
                :key="team.team"
                class="flex gap-x-4 py-3 opacity-20"
              >
                <img class="h-12 w-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                <div class="min-w-0">
                  <p class="text-sm font-semibold leading-6 text-gray-900">
                    {{ index + 1 + TEAMS_TO_KEEP }}. {{ team.team.replace(/För./, '') }}
                  </p>
                  <p class="mt-1 truncate text-xs leading-5 text-gray-500">
                    {{ showRound === 'latest' ? team.points : team.afterThreeRounds }} poäng
                  </p>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <div class="flex flex-1">
          <div class="flex-1">
            <h3 class="mt-4 font-bold">KBP Herrar</h3>
            <ul role="list" class="divide-y divide-gray-100">
              <li
                v-for="(team, index) in sortedKBPHerr().slice(0, TEAMS_TO_KEEP)"
                :key="team.team"
                class="flex gap-x-4 py-3"
              >
                <img class="h-12 w-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                <div class="min-w-0">
                  <p class="text-sm font-semibold leading-6 text-gray-900">{{ index + 1 }}. {{ team.team }}</p>
                  <p class="mt-1 truncate text-xs leading-5 text-gray-500">
                    {{ showRound === 'latest' ? team.points : team.afterThreeRounds }} poäng
                  </p>
                </div>
              </li>
              <li>
                <div class="border border-t-0 border-l-0 border-r-0 border-b-1 border-gkk opacity-50 h-4"></div>
              </li>
              <li
                v-for="(team, index) in sortedKBPHerr().slice(TEAMS_TO_KEEP, TEAMS_TO_KEEP + 3)"
                :key="team.team"
                class="flex gap-x-4 py-3 opacity-20"
              >
                <img class="h-12 w-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                <div class="min-w-0">
                  <p class="text-sm font-semibold leading-6 text-gray-900">
                    {{ index + 1 + TEAMS_TO_KEEP }}. {{ team.team.replace(/För./, '') }}
                  </p>
                  <p class="mt-1 truncate text-xs leading-5 text-gray-500">
                    {{ showRound === 'latest' ? team.points : team.afterThreeRounds }} poäng
                  </p>
                </div>
              </li>
            </ul>
          </div>

          <div class="flex-1">
            <h3 class="mt-4 font-bold">KBP Damer</h3>
            <ul role="list" class="divide-y divide-gray-100">
              <li
                v-for="(team, index) in sortedKBPDam().slice(0, TEAMS_TO_KEEP)"
                :key="team.team"
                class="flex gap-x-4 py-3"
              >
                <img class="h-12 w-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                <div class="min-w-0">
                  <p class="text-sm font-semibold leading-6 text-gray-900">{{ index + 1 }}. {{ team.team }}</p>
                  <p class="mt-1 truncate text-xs leading-5 text-gray-500">
                    {{ showRound === 'latest' ? team.points : team.afterThreeRounds }} poäng
                  </p>
                </div>
              </li>
              <li>
                <div class="border border-t-0 border-l-0 border-r-0 border-b-1 border-gkk opacity-50 h-4"></div>
              </li>
              <li
                v-for="(team, index) in sortedKBPDam().slice(TEAMS_TO_KEEP, TEAMS_TO_KEEP + 3)"
                :key="team.team"
                class="flex gap-x-4 py-3 opacity-20"
              >
                <img class="h-12 w-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                <div class="min-w-0">
                  <p class="text-sm font-semibold leading-6 text-gray-900">
                    {{ index + 1 + TEAMS_TO_KEEP }}. {{ team.team.replace(/För./, '') }}
                  </p>
                  <p class="mt-1 truncate text-xs leading-5 text-gray-500">
                    {{ showRound === 'latest' ? team.points : team.afterThreeRounds }} poäng
                  </p>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div> -->
    </div>
  </div>
</template>

<script>
import ToggleButton from './ui/ToggleButton.vue'

export default {
  components: {
    ToggleButton,
  },
  data() {
    return {
      TEAMS_TO_KEEP: 5,
      series: [],
      KSLHerr: [],
      KSLDam: [],
      KBPHerr: [],
      KBPDam: [],
      showRound: 'latest',
    }
  },
  methods: {
    teamImage(team) {
      const teamImages = {
        'Göteborg KK': 'https://goteborg-kraftsportklubb.web.app/img/logo-min.png',
        'För. Pure Power': 'https://goteborg-kraftsportklubb.web.app/foreningsloggor/pp.png',
        'Täby AK': 'https://goteborg-kraftsportklubb.web.app/foreningsloggor/taby.png',
        'Sundbybergs TK': 'https://goteborg-kraftsportklubb.web.app/foreningsloggor/sundbyberg.png',
        'Falkenbergs KSK ': 'https://goteborg-kraftsportklubb.web.app/foreningsloggor/falkenberg.png',
        'Södertälje AK': 'https://goteborg-kraftsportklubb.web.app/foreningsloggor/sodertalje.png',
        'Vedums AIS': 'https://goteborg-kraftsportklubb.web.app/foreningsloggor/vedum.png',
        'Linköpings AK': 'https://goteborg-kraftsportklubb.web.app/foreningsloggor/linkoping.png',
        'Lunds TK': 'https://goteborg-kraftsportklubb.web.app/foreningsloggor/lund.png',
        'Örebro KK': 'https://goteborg-kraftsportklubb.web.app/foreningsloggor/orebro.png',
        'Upsala TK': 'https://goteborg-kraftsportklubb.web.app/foreningsloggor/upsala.png',
      }

      return (
        teamImages[team] ||
        'https://www.styrkelyft.se/pictures/nav_thumbs/ssf-logga_web_transparent_200px.png?v20210217122912'
      )
    },
    sortedKSLHerr() {
      const data = [...this.KSLHerr]

      if (this.showRound === 'latest') {
        return data.sort((a, b) => b.points - a.points)
      }

      return data.sort((a, b) => b.afterThreeRounds - a.afterThreeRounds)
    },
    sortedKSLDam() {
      const data = [...this.KSLDam]

      if (this.showRound === 'latest') {
        return data.sort((a, b) => b.points - a.points)
      }

      return data.sort((a, b) => b.afterThreeRounds - a.afterThreeRounds)
    },
    sortedKBPHerr() {
      const data = [...this.KBPHerr]

      if (this.showRound === 'latest') {
        return data.sort((a, b) => b.points - a.points)
      }

      return data.sort((a, b) => b.afterThreeRounds - a.afterThreeRounds)
    },
    sortedKBPDam() {
      const data = [...this.KBPDam]

      if (this.showRound === 'latest') {
        return data.sort((a, b) => b.points - a.points)
      }

      return data.sort((a, b) => b.afterThreeRounds - a.afterThreeRounds)
    },
    showImage(url) {
      window.open(url, '_blank')
    },
  },
}
</script>

<style scoped></style>
