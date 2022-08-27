<template>
  <div style="text-align: center">
    <div>
      <h3 class="text-center mt-6 font-thin text-xl">
        Välkommen till GKK!<br />
        För att komma vidare behöver du först godkänna vårt <b>Medlemsavtal</b> och <b>Antidopingavtal</b>.
      </h3>
      <p class="mt-4">
        Denna information visas för alla medlemmar – även om du redan tidigare har godkänt dessa i pappersform.
      </p>
    </div>
    <div class="mt-8">
      <Button type="secondary" @click="showMemberShipAgreement">Visa medlemsavtal</Button>
    </div>
    <div class="mt-2">
      <Button type="secondary" @click="showAntiDopingAgreement">Visa antidopingavtal</Button>
    </div>
    <div class="mt-8">
      <Button @click="signAgreements">Godkänn avtalen</Button>
      <p class="text-xs mt-2">Läs avtalen innan du godkänner!</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import Button from './ui/Button.vue'

const MEMBERSHIP_AGREEMENT =
  'https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FMedlemsavtal%20GKK.pdf?alt=media&token=2a2ac4ed-2cec-4865-84ff-a28ac65d30fb'
const ANTI_DOPING_AGREEMENT =
  'https://www.gkk-styrkelyft.se/wp-content/uploads/2019/01/Antidopingavtal-mellan-medlemmen-och-klubben-20190110.pdf'

export default {
  components: { Button },
  props: ['user'],
  methods: {
    showMemberShipAgreement() {
      window.open(MEMBERSHIP_AGREEMENT)
    },
    showAntiDopingAgreement() {
      window.open(ANTI_DOPING_AGREEMENT)
    },
    signAgreements() {
      axios.post('/sign-agreements').then(() => {
        window.location = '/insidan'
      })
    },
  },
}
</script>
