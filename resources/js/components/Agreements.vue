<template>
  <div style="text-align: center">
    <Modal ref="agreementConfirmationModal" title="Är du säker på att du godkänner avtalet? Det är din skyldighet som medlem att känna till avtalets innehåll.">
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
        <Button @click="close" type="secondary">Nej</Button>
        <Button @click="confirmSignAgreement" type="success">Ja, godkänn avtalet</Button>
      </div>
      </template>
    </Modal>

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
        <div class="text-md text-red-500" v-if="membershipAgreementStatus === 'expired'">Avtalet har uppdaterats sedan du godkänt. Läs och godkänn igen.</div>
        <div class="text-md text-red-500" v-if="membershipAgreementStatus === 'old'">Du godkände avtalet för över ett år sedan. Läs och godkänn igen.</div>
      </div>
      <div class="mt-8 flex items-center justify-center">
        <Button @click="showAntiDopingAgreement">Visa antidopingavtal</Button>
        <Button v-if="antiDopingAgreementStatus !== 'signed'" class="ml-2" type="secondary" @click="signAgreement('anti-doping')">Godkänn antidopingavtalet</Button>
      </div>
      <div class="mt-2">
        <div class="text-sm italic" v-if="antiDopingAgreementStatus === 'signed'">Signerat {{ renderDate(user.anti_doping_agreement_signed_at) }}</div>
        <div class="text-md text-red-500" v-if="antiDopingAgreementStatus === 'expired'">Avtalet har uppdaterats sedan du godkänt. Läs och godkänn igen.</div>
        <div class="text-md text-red-500" v-if="antiDopingAgreementStatus === 'old'">Du godkände avtalet för över ett år sedan. Läs och godkänn igen.</div>
      </div>
    </div>
    <h3 class="text-center mt-8 text-md">
        Du hittar sedan avtalen på Profilsidan.
    </h3>
  </div>
</template>

<script>
import axios from 'axios'
import Modal from './ui/Modal.vue'
import Button from './ui/Button.vue'
import Documents from '../modules/Documents.js'

export default {
  components: { Button, Modal },
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
      this.signingAgreementKind = kind

      this.$refs.agreementConfirmationModal.show()
    },
    async confirmSignAgreement() {
      if (this.signingAgreementKind === '') {
        return
      }

      await axios.post(`/sign-agreements/${this.signingAgreementKind}`)
      this.signingAgreementKind = ''
      
      window.location = '/insidan'
    }
  },
}
</script>
