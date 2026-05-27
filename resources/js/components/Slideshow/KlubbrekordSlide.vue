<template>
    <div class="flex flex-col items-center justify-center h-screen gap-8 max-h-screen px-8">
        <div class="text-6xl text-center text-gkk font-thin">
            {{ title }}
        </div>

        <div v-if="records.length" class="flex flex-col gap-4 w-full max-w-5xl">
            <div
                v-for="record in records"
                :key="record.id"
                class="bg-white rounded-2xl shadow-md overflow-hidden flex items-stretch"
                :class="{ 'ring-4 ring-green-400': withinAYear(record.competition_date) }"
            >
                <div class="bg-gkk text-white flex flex-col items-center justify-center px-8 py-5 min-w-[14rem]">
                    <div class="text-3xl font-semibold">{{ record.event }}</div>
                    <div class="text-xl font-thin">{{ genderLabel(record.gender) }} {{ record.weight_class }} kg</div>
                </div>
                <div class="flex-1 flex items-center justify-between px-8 py-5">
                    <div class="text-4xl text-gkk font-thin truncate">
                        {{ athleteName(record) }}
                    </div>
                    <div class="flex flex-col items-end ml-6">
                        <div class="text-5xl font-bold text-gkk whitespace-nowrap">
                            {{ record.result }} <span class="text-2xl font-thin">kg</span>
                        </div>
                        <div
                            class="text-lg"
                            :class="withinAYear(record.competition_date) ? 'text-green-600 font-medium' : 'text-gray-400'"
                        >
                            {{ formatDate(record.competition_date) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-3xl text-gray-400 font-thin">
            Inga klubbrekord registrerade ännu.
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const { title } = defineProps({
    title: {
        type: String,
        required: false,
        default: 'Senaste klubbrekorden',
    },
})

const records = ref([])

onMounted(async () => {
    const response = await axios.get('/slideshow/records')
    records.value = response.data
})

function genderLabel(gender) {
    return gender === 'F' ? 'Kvinnor' : 'Män'
}

function athleteName(record) {
    if (record.name) {
        return record.name
    }
    return record.user ? `${record.user.first_name} ${record.user.last_name}` : ''
}

function withinAYear(date) {
    if (!date) return false
    const oneYearMs = 365 * 24 * 60 * 60 * 1000
    return new Date() - new Date(date) < oneYearMs
}

function formatDate(date) {
    if (!date) return ''
    return new Date(date).toLocaleDateString('sv-SE', { year: 'numeric', month: 'short', day: 'numeric' })
}
</script>
