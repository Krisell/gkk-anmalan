<template>
  <div style="text-align: center">
    <div>
      <h3 class="text-center mt-6 font-thin text-xl">
        Välkommen till GKK!<br />
        För att komma vidare behöver du först godkänna vårt <b>Medlemsavtal</b> och <b>Antidopingavtal</b>.
      </h3>
    </div>
    <div>
      <div class="mt-8 flex items-center justify-center">
        <Button @click="showMemberShipAgreement">Visa medlemsavtal</Button>
        <Button v-if="membershipAgreementStatus !== 'signed'" class="ml-2" type="secondary" @click="signAgreement('membership')">Godkänn medlemsavtalet</Button>
      </div>
      <div class="mt-2">
        <div class="text-sm italic" v-if="membershipAgreementStatus === 'signed'">Signerat {{ renderDate(user.membership_agreement_signed_at) }}</div>
        <div class="text-md" v-if="membershipAgreementStatus === 'expired'">Avtalet har uppdaterats sedan du godkänt. Läs och godkänn igen.</div>
        <div class="text-md" v-if="membershipAgreementStatus === 'old'">Du godkände avtalet för över ett år sedan. Läs och godkänn igen.</div>
      </div>
      <div class="mt-8 flex items-center justify-center">
        <Button @click="showAntiDopingAgreement">Visa antidopingavtal</Button>
        <Button v-if="antiDopingAgreementStatus !== 'signed'" class="ml-2" type="secondary" @click="signAgreement('anti-doping')">Godkänn antidopingavtalet</Button>
      </div>
      <div class="mt-2">
        <div class="text-sm italic" v-if="antiDopingAgreementStatus === 'signed'">Signerat {{ renderDate(user.anti_doping_agreement_signed_at) }}</div>
        <div class="text-md" v-if="antiDopingAgreementStatus === 'expired'">Avtalet har uppdaterats sedan du godkänt. Läs och godkänn igen.</div>
        <div class="text-md" v-if="antiDopingAgreementStatus === 'old'">Du godkände avtalet för över ett år sedan. Läs och godkänn igen.</div>
      </div>
    </div>
    <h3 class="text-center mt-8 text-md">
        Du hittar sedan avtalen på Profilsidan.
    </h3>
  </div>
</template>

<script>
import axios from 'axios'
import Button from './ui/Button.vue'
import Documents from '../modules/Documents.js'

export default {
  components: { Button },
  props: ['user', 'membershipAgreementStatus', 'antiDopingAgreementStatus'],
  methods: {
    renderDate(date) {
      return new Date(date).toLocaleDateString('sv-SE')
    },
    showMemberShipAgreement() {
      window.open(Documents.MEMBERSHIP_AGREEMENT)
    },
    showAntiDopingAgreement() {
      window.open(Documents.ANTI_DOPING_AGREEMENT)
    },
    signAgreement(kind) {
      axios.post(`/sign-agreements/${kind}`).then(() => {
        window.location = '/insidan'
      })
    },
  },
}
</script>
