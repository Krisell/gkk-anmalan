<template>
    <div>
        <div class="mx-auto py-6 px-4 max-w-7xl sm:px-6 lg:px-8">
          <div class="space-y-12">
            <div class="space-y-5 sm:space-y-4 md:max-w-xl lg:max-w-3xl xl:max-w-none">
              <div class="flex items-center">
                <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Lag-SM 2023</h2>
              </div>
              
              <p class="text-xl leading-normal text-gray-500">
              Den 16-17 december 2023 kommer Lag-SM i Styrkelyft avgöras hos Göteborg Kraftsportklubb i Göteborg.
              </p>
              <p class="text-xl leading-normal text-gray-500">
                Mer information som tider, tävlande föreningar mm kommer läggas upp längre fram.
              </p>
              <p class="text-xl leading-normal text-gray-500">
                Varmt välkoma att komma och titta!
              </p>
            </div>
        </div>

        <div class="flex items-center mt-6">
            <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl">Lag i final just nu</h2>
        </div>
        <p class="text-xl leading-normal text-gray-500 mt-2">
            Vilka som får göra upp avgörs efter Serie 4 där sista inrapporteringsdag är 3 december.
        </p>
        <div class="mt-4 mb-32 flex items-between lg:items-center justify-start flex-col lg:flex-row">
            <div class="flex flex-1">
                <div class="flex-1">
                    <h3 class="mt-4 font-bold">KSL Herrar</h3>
                    <ul role="list" class="divide-y divide-gray-100">
                        <li v-for="(team, index) in KSLHerr.slice(0, 5)" :key="team.team" class="flex gap-x-4 py-3">
                        <img class="h-12 w-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                        <div class="min-w-0">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ index + 1 }}. {{ team.team.replace(/För./, '') }}</p>
                            <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ team.points }} poäng</p>
                        </div>
                        </li>
                    </ul>
                </div>

                <div class="flex-1">
                    <h3 class="mt-4 font-bold">KSL Damer</h3>
                    <ul role="list" class="divide-y divide-gray-100">
                        <li v-for="(team, index) in KSLDam.slice(0, 5)" :key="team.team" class="flex gap-x-4 py-3">
                        <img class="w-12 h-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                        <div class="min-w-0">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ index + 1 }}. {{ team.team }}</p>
                            <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ team.points }} poäng</p>
                        </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex flex-1">
                <div class="flex-1">
                    <h3 class="mt-4 font-bold">KBP Herrar</h3>
                    <ul role="list" class="divide-y divide-gray-100">
                        <li v-for="(team, index) in KBPHerr.slice(0, 5)" :key="team.team" class="flex gap-x-4 py-3">
                        <img class="h-12 w-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                        <div class="min-w-0">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ index + 1 }}. {{ team.team }}</p>
                            <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ team.points }} poäng</p>
                        </div>
                        </li>
                    </ul>
                </div>

                <div class="flex-1">
                    <h3 class="mt-4 font-bold">KBP Damer</h3>
                    <ul role="list" class="divide-y divide-gray-100">
                        <li v-for="(team, index) in KBPDam.slice(0, 5)" :key="team.team" class="flex gap-x-4 py-3">
                        <img class="h-12 w-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                        <div class="min-w-0">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ index + 1 }}. {{ team.team }}</p>
                            <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ team.points }} poäng</p>
                        </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
      </div>
        
    </div>
</template>

<script>
export default {
    data() {
        return {
            series: [],
            KSLHerr: [],
            KSLDam: [],
            KBPHerr: [],
            KBPDam: [],
        }
    },
    async mounted() {
        // const response = await fetch('http://127.0.0.1:5001/goteborg-kraftsportklubb/europe-north1/readEliteSeries')
        const response = await fetch('https://readeliteseries-td5gx4uymq-lz.a.run.app')
        this.series = await response.json()

        this.KSLHerr = [...this.series.find(serie => serie.name === 'KSL Herr').top10]
        this.KSLDam = [...this.series.find(serie => serie.name === 'KSL Dam').top10]
        this.KBPHerr = [...this.series.find(serie => serie.name === 'KBP Herr').top10]
        this.KBPDam = [...this.series.find(serie => serie.name === 'KBP Dam').top10]
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

            return teamImages[team] || 'https://www.styrkelyft.se/pictures/nav_thumbs/ssf-logga_web_transparent_200px.png?v20210217122912'
        }
    }
}
</script>

<style scoped>
</style>