<template>
    <div>
        <div class="mx-auto py-6 px-4 max-w-7xl sm:px-6 lg:px-8">
          <div class="space-y-12">
            <div class="space-y-5 sm:space-y-4 md:max-w-xl lg:max-w-3xl xl:max-w-none">
              <div class="flex items-center">
                <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Lag-SM 2023</h2>
              </div>


              
              <p class="text-xl leading-normal text-gray-500">
              Den 16-17 december 2023 avgjordes Lag-SM i Styrkelyft hos Göteborg Kraftsportklubb.
              </p>

              <div class="flex items-center mt-6">
            <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl">Bilder</h2>
            </div>
            <a href="https://photos.app.goo.gl/K7ay7FxayxB9RzEr8" target="_blank">
            <img width="500" class="my-4 rounded-2xl shadow-lg hover:scale-105 transition-all" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fframelagsm.png?alt=media&token=2d9d6ad8-70da-4ca5-9f36-c8d7f8045df7">
                  <div class="mt-3 text-xl text-blue-400 underline">Bilder från tävlingen hittar ni här</div>
        </a>
            </div>
        </div>

        <div class="flex items-center mt-6">
            <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl"></h2>
        </div>
              
        <a href="https://www.instagram.com/p/C1EmdfLofzH/" target="_blank">
            <img width="500" class="my-4 rounded-2xl shadow hover:scale-105 transition-all" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Flagsminsta.png?alt=media&token=14a4548c-25f9-400f-aca6-20e184c34c2a">
        </a>

        <div class="flex items-center mt-6">
            <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl">Resultat</h2>
        </div>
        <p class="text-xl leading-normal text-gray-500 mb-32">För resultat, se <a target="_blank" 
            class="text-blue-400 underline"
            href="https://www.styrkelyft.se/verksamhet/tavling/aktuella-sm">förbundets SM-sida</a></p>
        
      </div>
    </div>
</template>

<script>
import ToggleButton from './ui/ToggleButton.vue'

export default {
    components: {
        ToggleButton
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
        }
    }
}
</script>

<style scoped>
</style>