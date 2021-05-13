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
      <ui-button type="secondary" @click="showMemberShipAgreement">Visa medlemsavtal</ui-button>
    </div>
    <div class="mt-2">
      <ui-button type="secondary" @click="showAntiDopingAgreement">Visa antidopingavtal</ui-button>
    </div>
    <div class="mt-8">
      <ui-button @click="signAgreements">Godkänn avtalen</ui-button>
      <p class="text-xs mt-2">Läs avtalen innan du godkänner!</p>
    </div>
  </div>
</template>

<script>
export default {
  props: ['user'],
  methods: {
    showMemberShipAgreement() {
      window.open(
        'https://www.gkk-styrkelyft.se/wp-content/uploads/2019/12/Go%CC%88teborg-Kraftsportklubbs-medlemsavtal.pdf',
      )
    },
    showAntiDopingAgreement() {
      window.open(
        'https://www.gkk-styrkelyft.se/wp-content/uploads/2019/01/Antidopingavtal-mellan-medlemmen-och-klubben-20190110.pdf',
      )
    },
    signAgreements() {
      axios.post('/sign-agreements').then(() => {
        window.location = '/'
      })
    },
  },
}
</script>
