<template>
  <div class="mx-auto max-w-2xl px-4 pb-12">
    <Modal
      ref="agreementConfirmationModal"
      title="Är du säker på att du godkänner avtalet? Det är din skyldighet som medlem att känna till avtalets innehåll."
    >
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button @click="close" type="secondary">Nej</Button>
          <Button @click="confirmSignAgreement" type="success">Ja, godkänn avtalet</Button>
        </div>
      </template>
    </Modal>

    <div class="text-center">
      <img
        class="mx-auto h-24 sm:h-28"
        src="https://goteborg-kraftsportklubb.web.app/img/logo-min.png"
        alt="Göteborgs Kraftsportklubb"
      />
      <h1 class="mt-6 text-2xl font-bold tracking-tight text-gray-900">{{ heading }}</h1>
      <p class="mx-auto mt-3 max-w-lg text-gray-600 leading-relaxed">{{ intro }}</p>
    </div>

    <section class="mt-8 overflow-hidden rounded-2xl bg-white shadow-xs ring-1 ring-gray-900/5">
      <ul class="divide-y divide-gray-100">
        <li v-for="agreement in agreementList" :key="agreement.key" class="px-5 py-5 sm:px-6">
          <div class="flex items-start gap-4">
            <span
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
              :class="statusStyle(agreement.status).iconClass"
            >
              <i class="fa" :class="statusStyle(agreement.status).icon"></i>
            </span>
            <div class="min-w-0 flex-1">
              <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                <h2 class="font-semibold text-gray-900">{{ agreement.name }}</h2>
                <span
                  class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold tracking-wide uppercase"
                  :class="statusStyle(agreement.status).badgeClass"
                  >{{ statusStyle(agreement.status).label }}</span
                >
              </div>
              <p class="mt-1 text-sm text-gray-600">{{ statusText(agreement) }}</p>
              <div class="mt-4 flex flex-wrap gap-2">
                <Button type="secondary" @click="agreement.open">
                  <i class="fa fa-file-pdf-o mr-2"></i>
                  Läs avtalet
                </Button>
                <Button v-if="agreement.status !== 'signed'" @click="signAgreement(agreement.key)">
                  Godkänn {{ agreement.name.toLowerCase() }}et
                </Button>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </section>

    <div class="mt-8 flex flex-col items-center gap-3 text-center">
      <Button v-if="allSigned" @click="continueToInside">Fortsätt</Button>
      <template v-else-if="canPostpone">
        <Button
          type="secondary"
          class="w-full justify-center px-6 py-3 text-base! sm:w-auto disabled:opacity-50"
          :disabled="postponing"
          @click="postpone"
        >
          Hantera senare på profilsidan
          <i class="fa fa-arrow-right ml-2 text-sm"></i>
        </Button>
        <p class="text-xs text-gray-500">
          Du kan läsa och godkänna avtalen på profilsidan fram till {{ renderDate(earliestExpiry) }}.
        </p>
      </template>
      <p v-else class="text-sm text-gray-500">Du hittar sedan avtalen på profilsidan.</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import Modal from './ui/Modal.vue'
import Button from './ui/Button.vue'
import Documents from '../modules/Documents.js'

const BLOCKING_STATUSES = ['unsigned', 'expired', 'old']

export default {
  components: { Button, Modal },
  props: ['user', 'agreements'],
  data() {
    return {
      signingAgreementKind: '',
      postponing: false,
    }
  },
  computed: {
    agreementList() {
      return [
        {
          key: 'membership',
          name: 'Medlemsavtal',
          open: this.showMemberShipAgreement,
          ...this.agreements.membership,
        },
        {
          key: 'anti-doping',
          name: 'Antidopingavtal',
          open: this.showAntiDopingAgreement,
          ...this.agreements.anti_doping,
        },
      ]
    },
    mustSign() {
      return this.agreementList.some((agreement) => BLOCKING_STATUSES.includes(agreement.status))
    },
    allSigned() {
      return this.agreementList.every((agreement) => agreement.status === 'signed')
    },
    canPostpone() {
      return !this.mustSign && this.agreementList.some((agreement) => agreement.status === 'expiring')
    },
    isNewMember() {
      return this.agreementList.every((agreement) => agreement.status === 'unsigned')
    },
    earliestExpiry() {
      return this.agreementList
        .filter((agreement) => agreement.status === 'expiring')
        .map((agreement) => agreement.expires_at)
        .sort()[0]
    },
    heading() {
      if (this.isNewMember) return 'Välkommen till GKK!'
      if (this.allSigned) return 'Tack!'
      return 'Dags att förnya dina avtal'
    },
    intro() {
      if (this.isNewMember) {
        return 'För att komma vidare behöver du först läsa och godkänna vårt medlemsavtal och antidopingavtal.'
      }
      if (this.allSigned) {
        return 'Du har godkänt alla avtal och kan fortsätta till Insidan.'
      }
      if (this.mustSign) {
        return 'Avtalen behöver godkännas varje år, och när de har uppdaterats. Läs igenom och godkänn dem för att komma vidare.'
      }
      return 'Avtalen godkänns en gång per år och det är snart dags att godkänna dem igen. Du kan göra det nu eller senare via profilsidan.'
    },
  },
  methods: {
    statusStyle(status) {
      if (status === 'signed') {
        return {
          label: 'Godkänt',
          icon: 'fa-check',
          iconClass: 'bg-green-100 text-green-600',
          badgeClass: 'bg-green-50 text-green-700 ring-1 ring-green-600/20',
        }
      }
      if (status === 'expiring') {
        return {
          label: 'Dags att förnya',
          icon: 'fa-clock-o',
          iconClass: 'bg-amber-100 text-amber-600',
          badgeClass: 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
        }
      }
      return {
        label: status === 'unsigned' ? 'Ej godkänt' : 'Behöver godkännas',
        icon: 'fa-exclamation',
        iconClass: 'bg-red-100 text-red-600',
        badgeClass: 'bg-red-50 text-red-700 ring-1 ring-red-600/20',
      }
    },
    statusText(agreement) {
      return {
        unsigned: 'Du har inte godkänt avtalet ännu.',
        expired: 'Avtalet har uppdaterats sedan du godkände det. Läs och godkänn igen.',
        old: 'Du godkände avtalet för över ett år sedan. Läs och godkänn igen.',
        expiring: `Ditt godkännande löper ut ${this.renderDate(agreement.expires_at)}. Läs och godkänn igen.`,
        signed: `Signerat ${this.renderDate(agreement.signed_at)}, giltigt till ${this.renderDate(agreement.expires_at)}.`,
      }[agreement.status]
    },
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
    },
    continueToInside() {
      window.location = '/insidan'
    },
    async postpone() {
      this.postponing = true
      try {
        const { data } = await axios.post('/sign-agreements/postpone')
        window.location = data.redirect
      } finally {
        this.postponing = false
      }
    },
  },
}
</script>
