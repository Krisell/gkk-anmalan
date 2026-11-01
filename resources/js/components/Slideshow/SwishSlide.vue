<template>
  <div class="relative overflow-hidden">
    <div
      class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,rgba(49,66,112,0.10),transparent_60%),radial-gradient(ellipse_at_top_right,rgba(246,201,69,0.22),transparent_55%)]"
    ></div>

    <div class="relative h-full grid grid-cols-[1.1fr_1fr] items-center gap-[5vw] px-[7vw] py-[6vh]">
      <section class="min-w-0">
        <div
          class="inline-flex items-center gap-[0.8vw] rounded-full bg-gkk text-white px-[1.4vw] py-[0.9vh] text-[min(1.3vw,2.3vh)] font-bold uppercase tracking-[0.18em]"
        >
          <i class="fa fa-heart text-amber-300"></i>
          Stöd klubben
        </div>
        <h1 class="mt-[4vh] text-[min(5.6vw,10vh)] font-extrabold leading-[0.95] tracking-tight text-gkk text-balance">
          Swisha en gåva till GKK
        </h1>
        <p class="mt-[3vh] max-w-[90%] text-[min(2vw,3.4vh)] leading-snug text-gkk-light text-balance">
          Skanna koden med mobilkameran så öppnas Swish. Tack för att du stöttar Göteborg Kraftsportklubb!
        </p>
        <div
          class="mt-[5vh] inline-flex flex-col rounded-[1.6vh] bg-white px-[2vw] py-[2vh] shadow-xl shadow-gkk/10 ring-1 ring-gkk/10"
        >
          <span class="text-[min(1.1vw,1.9vh)] font-semibold uppercase tracking-[0.18em] text-gkk-light">
            Swishnummer
          </span>
          <span class="mt-[0.5vh] text-[min(3.6vw,6.2vh)] font-extrabold leading-none tabular-nums text-gkk">
            {{ SWISH_DISPLAY }}
          </span>
        </div>
      </section>

      <section class="flex flex-col items-center">
        <div class="relative rounded-[3.5vh] bg-white p-[3.5vh] shadow-2xl shadow-gkk/20 ring-1 ring-gkk/10">
          <!-- eslint-disable-next-line vue/no-v-html -- an SVG we generate from a fixed address -->
          <div class="h-[min(52vh,30vw)] w-[min(52vh,30vw)] [&>svg]:h-full [&>svg]:w-full" v-html="qr"></div>
          <div v-if="qr" class="absolute inset-0 flex items-center justify-center">
            <div class="rounded-[1.6vh] bg-white p-[1.1vh] shadow-md">
              <img class="h-[min(8vh,4.6vw)] w-[min(8vh,4.6vw)] object-contain" :src="LOGO" alt="" />
            </div>
          </div>
        </div>
        <p class="mt-[2.5vh] text-[min(1.6vw,2.7vh)] font-semibold text-gkk">
          <i class="fa fa-camera mr-[0.5vw] text-gkk-light"></i>
          Skanna med mobilkameran
        </p>
      </section>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import QRCode from 'qrcode'

const SWISH_NUMBER = '1235813456'
const SWISH_DISPLAY = '123-581 34 56'
// Opens Swish with the club's number and a message filled in; the giver chooses the amount.
const SWISH_URL = `https://app.swish.nu/1/p/sw/?sw=${SWISH_NUMBER}&msg=${encodeURIComponent('Gåva till GKK')}`
const LOGO = 'https://goteborg-kraftsportklubb.web.app/img/logo-min.png'

const qr = ref('')

onMounted(async () => {
  // High error correction, so the logo in the middle doesn't stop the code from scanning.
  qr.value = await QRCode.toString(SWISH_URL, {
    type: 'svg',
    errorCorrectionLevel: 'H',
    margin: 0,
    color: { dark: '#314270', light: '#ffffff' },
  })
})
</script>
