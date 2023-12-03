<template>
    <div>
        <div class="mx-auto py-6 px-4 max-w-7xl sm:px-6 lg:px-8">
          <div class="space-y-12">
            <div class="space-y-5 sm:space-y-4 md:max-w-xl lg:max-w-3xl xl:max-w-none">
              <div class="flex items-center">
                <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Lag-SM 2023</h2>
              </div>


              
              <p class="text-xl leading-normal text-gray-500">
              Den 16-17 december 2023 avgörs Lag-SM i Styrkelyft hos Göteborg Kraftsportklubb.
              </p>

              <div class="flex items-center mt-6">
            <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl">Tävlingsinformation</h2>
            </div>

              <p class="text-xl leading-normal text-gray-500">
                <b>Lördag 16 december</b><br>
                Klassisk Styrkelyft herr: Invägning 08.00-09.30 - Tävlingsstart 10.00<br>
                Klassisk Styrkelyft dam: Invägning 12.00-13.30 - Tävlingsstart 14.00<br>
            </p>
            <p class="text-xl leading-normal text-gray-500">
                <b>Söndag 17 december</b><br>
                Klassisk Bänkpress herr: Invägning 09.00-10.30 - Tävlingsstart 11.00<br>
                Klassisk Bänkpress dam: Invägning 10.30-12.00 - Tävlingsstart 12.30<br>
            </p>
            <p class="text-xl leading-normal text-gray-500">
                Både i KSL och KBP sker uppdelning i två grupper med 10 lyftare i varje. Lagen avgör själva hur de delar upp sina lyftare.
                </p>
              <p class="text-xl leading-normal text-gray-500">
                <b>Gratis entré för publik. Varmt välkoma att komma och titta!</b>
              </p>
            </div>
        </div>

        <div class="flex items-center mt-6">
            <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl">Plats</h2>
        </div>
        <p class="text-xl leading-normal text-gray-500 mt-2 mb-2">
            Göteborg Kraftsportklubb, Karl Johansgatan 152, 414 51 Göteborg
        </p>
            <img 
                class="rounded-2xl shadow-xl cursor-pointer inline-block ml-2 w-[500px]"
                @click="showImage('https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fkarta%20(1).png?alt=media&token=5668792a-f351-4175-b1ab-87e0b59215f0')" 
                src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fkarta%20(1).png?alt=media&token=5668792a-f351-4175-b1ab-87e0b59215f0"
            >
        <div class="flex items-center mt-6">
            <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl">Tävlingsutrustning</h2>
        </div>
        <p class="text-xl leading-normal text-gray-500 mt-2 mb-2">
        Ställning tävling: Eleiko med hård dyna<br>
        Ställning uppvärmning: Eleiko med mjuk dyna samt ER<br>
            </p>
            <img 
                class="rounded-2xl shadow-xl cursor-pointer inline-block ml-2 w-[500px]"
                @click="showImage('https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fstallning.png?alt=media&token=bed8ed7c-e478-426f-80b8-e3e9caa44b9d')" 
                src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fstallning.png?alt=media&token=bed8ed7c-e478-426f-80b8-e3e9caa44b9d"
            >

        <div class="flex items-center mt-6">
            <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl">Tävlande lag</h2>
        </div>
        <p class="text-xl leading-normal text-gray-500 mt-2">
            Se <a class="underline text-blue-400" target="_blank" href="https://www.styrkelyft.se/verksamhet/tavling/aktuella-sm">Styrkelyft.se Aktuella SM</a> för aktuell lista på anmälda lag.
        </p>
        <div class="flex items-center mt-6">
            <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl">Slutställning i Elitserien</h2>
        </div>
        <p class="text-xl leading-normal text-gray-500 mt-2 mb-2">
                Top 5 i respektive Elitserie går till final. Observera att Linköpings AK avstår deltagande i Dam KBP, och Göteborg tar plats i den finalen i kraft av 6:e plats i Elitserien.
            </p>
        <div class="mt-4 mb-32 flex items-between lg:items-center justify-start flex-col lg:flex-row">
            <div class="flex flex-1">
                <div class="flex-1">
                    <h3 class="mt-4 font-bold">KSL Herrar</h3>
                    <ul role="list" class="divide-y divide-gray-100">
                        <li v-for="(team, index) in sortedKSLHerr().slice(0, TEAMS_TO_KEEP)" :key="team.team" class="flex gap-x-4 py-3">
                        <img class="h-12 w-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                        <div class="min-w-0">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ index + 1 }}. {{ team.team.replace(/För./, '') }}</p>
                            <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ showRound === 'latest' ? team.points : team.afterThreeRounds }} poäng</p>
                        </div>
                        </li>
                    </ul>
                </div>

                <div class="flex-1">
                    <h3 class="mt-4 font-bold">KSL Damer</h3>
                    <ul role="list" class="divide-y divide-gray-100">
                        <li v-for="(team, index) in sortedKSLDam().slice(0, TEAMS_TO_KEEP)" :key="team.team" class="flex gap-x-4 py-3">
                        <img class="w-12 h-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                        <div class="min-w-0">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ index + 1 }}. {{ team.team }}</p>
                            <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ showRound === 'latest' ? team.points : team.afterThreeRounds }} poäng</p>
                        </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex flex-1">
                <div class="flex-1">
                    <h3 class="mt-4 font-bold">KBP Herrar</h3>
                    <ul role="list" class="divide-y divide-gray-100">
                        <li v-for="(team, index) in sortedKBPHerr().slice(0, TEAMS_TO_KEEP)" :key="team.team" class="flex gap-x-4 py-3">
                        <img class="h-12 w-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                        <div class="min-w-0">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ index + 1 }}. {{ team.team }}</p>
                            <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ showRound === 'latest' ? team.points : team.afterThreeRounds }} poäng</p>
                        </div>
                        </li>
                    </ul>
                </div>

                <div class="flex-1">
                    <h3 class="mt-4 font-bold">KBP Damer</h3>
                    <ul role="list" class="divide-y divide-gray-100">
                        <li v-for="(team, index) in sortedKBPDam().slice(0, TEAMS_TO_KEEP)" :key="team.team" class="flex gap-x-4 py-3">
                        <img class="h-12 w-12 flex-none rounded-full bg-gray-50" :src="teamImage(team.team)" alt="" />
                        <div class="min-w-0">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ index + 1 }}. {{ team.team }}</p>
                            <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ showRound === 'latest' ? team.points : team.afterThreeRounds }} poäng</p>
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