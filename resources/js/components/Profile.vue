<template>
  <div class="max-w-[95%] mx-auto pb-12">
    <div class="max-w-7xl mx-auto">
      <Breadcrumbs class="mb-6" title="Profil" />

      <div class="grid gap-6 lg:grid-cols-[minmax(0,26rem)_minmax(0,1fr)] lg:items-start">
        <div class="space-y-6">
          <!-- Medlem -->
          <section class="overflow-hidden rounded-2xl bg-white shadow-xs ring-1 ring-gray-900/5">
            <div class="h-20 bg-linear-to-r from-gkk to-gkk-light"></div>
            <div class="px-5 pb-5 sm:px-6">
              <div
                class="-mt-9 flex h-18 w-18 items-center justify-center rounded-full bg-gkk text-2xl font-semibold text-white ring-4 ring-white shadow-sm"
              >
                {{ user.first_name?.charAt(0) }}{{ user.last_name?.charAt(0) }}
              </div>
              <div class="mt-3">
                <h2 class="text-xl font-bold tracking-tight text-gray-900">
                  {{ user.first_name }} {{ user.last_name }}
                </h2>
                <div class="mt-0.5 text-sm text-gray-500">{{ user.email }}</div>
              </div>
              <div class="mt-4 flex flex-wrap gap-2">
                <span v-if="user.birth_year" class="chip">
                  <i class="fa fa-birthday-cake text-gkk/60"></i>
                  Född {{ user.birth_year }}
                </span>
                <span v-if="user.created_at" class="chip">
                  <i class="fa fa-calendar text-gkk/60"></i>
                  Medlem sedan {{ new Date(user.created_at).getFullYear() }}
                </span>
              </div>
            </div>
          </section>

          <!-- Funktionärsaktivitet -->
          <section class="flex gap-4 rounded-2xl p-5 ring-1" :class="helperAlertClasses">
            <div
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-lg"
              :class="helperAlertIconClass"
            >
              <i class="fa" :class="helperAlertIcon"></i>
            </div>
            <div class="min-w-0">
              <h3 class="font-semibold" :class="helperAlertTitleClass">{{ helperAlertTitle }}</h3>
              <div class="mt-1 space-y-1 text-sm leading-relaxed" :class="helperAlertTextClass">
                <p v-if="lastHelperDate">
                  Du hjälpte senast till som funktionär <strong>{{ renderDate(lastHelperDate) }}</strong
                  >.
                </p>
                <p v-else>Vi har ingen registrerad funktionärsaktivitet för dig.</p>
                <p>Alla tävlingsaktiva medlemmar behöver ställa upp som funktionär minst en gång per år.</p>
                <p v-if="helperStatus === 'error'">
                  Du kan för närvarande inte anmäla dig till tävlingar. Kontakta styrelsen om du har frågor.
                </p>
                <p v-else-if="helperStatus === 'warning'">
                  Det börjar bli dags att hjälpa till igen för att behålla din tävlingsrätt.
                </p>
              </div>
              <a
                v-if="helperStatus === 'error' || helperStatus === 'warning'"
                href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2F9TitYSrq6xtm6KLWDoCU7Azz8nIXep.pdf?alt=media&token=5cb38219-19c2-469b-9301-785f9eea3c28"
                target="_blank"
                class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold hover:underline"
                :class="helperAlertTitleClass"
              >
                Läs mer om funktionärskraven
                <i class="fa fa-arrow-right text-xs"></i>
              </a>
            </div>
          </section>

          <!-- Avtal -->
          <section class="overflow-hidden rounded-2xl bg-white shadow-xs ring-1 ring-gray-900/5">
            <header class="section-header">
              <span class="section-icon"><i class="fa fa-file-text-o"></i></span>
              <div>
                <h2 class="text-base font-semibold text-gray-900">Avtal</h2>
                <p class="text-sm text-gray-500">Dokument du har godkänt som medlem</p>
              </div>
            </header>
            <div
              v-if="earliestAgreementExpiry"
              class="flex gap-3 border-b border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800 sm:px-6"
            >
              <i class="fa fa-exclamation-triangle mt-0.5 text-amber-600"></i>
              <p>
                Avtalen behöver godkännas varje år. Läs igenom och godkänn dem igen senast
                <strong>{{ renderDate(earliestAgreementExpiry) }}</strong
                >.
              </p>
            </div>
            <ul class="divide-y divide-gray-100">
              <li v-for="agreement in agreements" :key="agreement.name">
                <button
                  type="button"
                  class="group flex w-full items-center gap-4 px-5 py-4 text-left transition-colors hover:bg-gray-50 sm:px-6"
                  @click="agreement.open"
                >
                  <span
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500 transition-colors group-hover:bg-gkk/10 group-hover:text-gkk"
                  >
                    <i class="fa fa-file-pdf-o"></i>
                  </span>
                  <div class="min-w-0 flex-1">
                    <div class="text-sm font-medium text-gray-900">{{ agreement.name }}</div>
                    <div
                      v-if="agreement.status === 'expiring'"
                      class="mt-0.5 flex items-center gap-1 text-xs text-amber-700"
                    >
                      <i class="fa fa-clock-o"></i>
                      Löper ut {{ renderDate(agreement.expiresAt) }}
                    </div>
                    <div v-else-if="agreement.signedAt" class="mt-0.5 flex items-center gap-1 text-xs text-green-700">
                      <i class="fa fa-check-circle"></i>
                      Signerat {{ renderDate(agreement.signedAt) }}
                      <span v-if="agreement.expiresAt" class="text-gray-500"
                        >· giltigt till {{ renderDate(agreement.expiresAt) }}</span
                      >
                    </div>
                  </div>
                  <span class="view-btn shrink-0">
                    <i class="fa fa-eye"></i>
                    Visa
                  </span>
                </button>
                <div v-if="agreement.status === 'expiring'" class="px-5 pb-4 sm:px-6 sm:pl-[4.75rem]">
                  <Button class="w-full justify-center sm:w-auto" @click="signAgreement(agreement.key)"
                    >Godkänn {{ agreement.name.toLowerCase() }}et</Button
                  >
                </div>
              </li>
            </ul>
          </section>
        </div>

        <div class="space-y-6">
          <!-- Avgifter -->
          <section class="overflow-hidden rounded-2xl bg-white shadow-xs ring-1 ring-gray-900/5">
            <header class="section-header">
              <span class="section-icon"><i class="fa fa-credit-card"></i></span>
              <div>
                <h2 class="text-base font-semibold text-gray-900">Avgifter</h2>
                <p class="text-sm text-gray-500">Medlemsavgifter, licenser och tävlingsavgifter</p>
              </div>
            </header>
            <div v-if="sortedPayments.length" class="divide-y divide-gray-100">
              <div v-for="payment in sortedPayments" :key="payment.id">
                <div
                  class="group flex items-center gap-4 px-5 py-4 transition-colors sm:px-6"
                  :class="payment.fortnox_invoice_document_number ? 'cursor-pointer hover:bg-gray-50' : ''"
                  @click="
                    payment.state === 'PAID'
                      ? downloadReceipt(payment)
                      : payment.fortnox_invoice_document_number
                        ? loadURL(`/invoices/${payment.id}`, true)
                        : null
                  "
                >
                  <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                    :class="paymentStatus(payment).iconClass"
                  >
                    <i class="fa" :class="paymentStatus(payment).icon"></i>
                  </span>

                  <div class="min-w-0 flex-1">
                    <div class="font-medium text-gray-900">{{ paymentTypeText(payment.type) }} {{ payment.year }}</div>
                    <div
                      v-if="payment.type === 'COMPETITION' && payment.competition"
                      class="truncate text-sm text-gray-500"
                    >
                      {{ payment.competition.name }}
                      <span v-if="payment.competition.date">· {{ renderDate(payment.competition.date) }}</span>
                    </div>
                    <span
                      class="mt-1.5 inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold tracking-wide uppercase"
                      :class="paymentStatus(payment).badgeClass"
                      >{{ paymentStatus(payment).label }}</span
                    >
                  </div>

                  <div class="shrink-0 text-right">
                    <div class="font-semibold tabular-nums text-gray-900">
                      {{ formatAmount(payment.sek_amount - payment.sek_discount) }}
                    </div>
                    <div v-if="payment.fortnox_invoice_document_number" class="view-btn mt-2">
                      <i class="fa" :class="payment.state === 'PAID' ? 'fa-check-square-o' : 'fa-file-text-o'"></i>
                      {{ payment.state === 'PAID' ? 'Visa kvitto' : 'Visa faktura' }}
                    </div>
                  </div>
                </div>

                <!-- Info för obetalda -->
                <div v-if="payment.state !== 'PAID'" class="px-5 pb-4 sm:px-6" @click.stop>
                  <ul
                    class="space-y-1.5 rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-600 ring-1 ring-gray-900/5 sm:ml-14"
                  >
                    <template v-if="!payment.fortnox_invoice_document_number">
                      <li class="info-row">
                        <i class="fa fa-clock-o"></i>Faktura kommer skapas och skickas ut inom kort.
                      </li>
                      <li class="info-row">
                        <i class="fa fa-envelope-o"></i>Faktura skickas till din epost och du kommer även kunna se den
                        här.
                      </li>
                      <li class="info-row"><i class="fa fa-mobile"></i>Betalning sker via Swish eller Bankgiro.</li>
                    </template>
                    <template v-else>
                      <li class="info-row">
                        <i class="fa fa-envelope-o"></i>Faktura har även skickats till din epost.
                      </li>
                      <li class="info-row">
                        <i class="fa fa-graduation-cap"></i>
                        <span>
                          Om fakturan är felaktig, exempelvis om du är student, skicka in studentbevis till
                          <a class="font-medium text-gkk underline text-nowrap" href="mailto:info@gkk-styrkelyft.se"
                            >info@gkk-styrkelyft.se</a
                          >
                          så korrigerar vi fakturan inom kort.
                        </span>
                      </li>
                      <li class="info-row"><i class="fa fa-mobile"></i>Betalning sker via Swish eller Bankgiro.</li>
                      <li class="info-row">
                        <i class="fa fa-hourglass-half"></i>Efter betalning kan det ta några bankdagar innan status
                        uppdateras här.
                      </li>
                      <li class="md:hidden">
                        <Button class="mt-1 w-full justify-center" @click.stop="loadURL(swishUrl(payment))"
                          >Betala med Swish på denna enhet</Button
                        >
                      </li>
                    </template>
                  </ul>
                </div>
              </div>
            </div>
            <p v-else class="px-5 py-8 text-center text-sm text-gray-500 sm:px-6">Du har inga avgifter registrerade.</p>
          </section>

          <!-- Kontouppgifter -->
          <section class="overflow-hidden rounded-2xl bg-white shadow-xs ring-1 ring-gray-900/5">
            <header class="section-header">
              <span class="section-icon"><i class="fa fa-user"></i></span>
              <div>
                <h2 class="text-base font-semibold text-gray-900">Kontouppgifter</h2>
                <p class="text-sm text-gray-500">Namn, epost och lösenord</p>
              </div>
            </header>
            <div class="divide-y divide-gray-100">
              <!-- Namn -->
              <div class="form-row">
                <label class="form-label">Namn</label>
                <div>
                  <div class="grid gap-3 sm:grid-cols-2">
                    <input v-model="name.first" type="text" placeholder="Förnamn" class="text-input" />
                    <input v-model="name.last" type="text" placeholder="Efternamn" class="text-input" />
                  </div>
                  <Button type="secondary" class="mt-3" @click="updateName">Uppdatera namn</Button>
                </div>
              </div>

              <!-- Epost -->
              <div class="form-row">
                <label class="form-label">Epost</label>
                <div>
                  <input v-model="email" type="email" class="text-input" />
                  <Button type="secondary" class="mt-3" @click="updateEmail">Uppdatera epost</Button>
                </div>
              </div>

              <!-- Lösenord -->
              <div class="form-row">
                <label class="form-label">Lösenord</label>
                <div>
                  <Button v-if="!isAdjusted.password" type="secondary" @click="startEdit('password')"
                    >Byt lösenord</Button
                  >
                  <div v-else>
                    <div class="grid gap-3 sm:grid-cols-2">
                      <input v-model="password.new" type="password" placeholder="Nytt lösenord" class="text-input" />
                      <input
                        v-model="password.new_confirmation"
                        type="password"
                        placeholder="Bekräfta lösenord"
                        class="text-input"
                      />
                    </div>
                    <div class="mt-3 flex gap-2">
                      <Button @click="updatePassword">Spara lösenord</Button>
                      <button
                        type="button"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 transition-colors hover:text-gray-900"
                        @click="reset"
                      >
                        Avbryt
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>

  <Modal
    ref="agreementConfirmationModal"
    title="Är du säker på att du godkänner avtalet? Det är din skyldighet som medlem att känna till avtalets innehåll."
  >
    <template #footer="{ close }">
      <div class="mt-4 flex items-center justify-center gap-2">
        <Button type="secondary" @click="close">Nej</Button>
        <Button @click="confirmSignAgreement">Ja, godkänn avtalet</Button>
      </div>
    </template>
  </Modal>

  <!-- Full-screen loading overlay -->
  <div v-if="loadingReceipt" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="flex flex-col items-center rounded-2xl bg-white p-8 shadow-xl">
      <i class="fa fa-spinner fa-spin mb-3 text-3xl text-gkk"></i>
      <span class="text-gray-700">Laddar kvitto...</span>
    </div>
  </div>
</template>

<style scoped>
@reference '../../css/main.css';

.chip {
  @apply inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700;
}
.section-header {
  @apply flex items-center gap-3 border-b border-gray-100 px-5 py-4 sm:px-6;
}
.section-icon {
  @apply flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gkk/5 text-gkk;
}
.view-btn {
  @apply inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-gkk shadow-xs ring-1 ring-gray-200 transition-colors group-hover:bg-gkk group-hover:text-white group-hover:ring-gkk;
}
.info-row {
  @apply flex gap-2.5;
}
.info-row > i {
  @apply mt-0.5 w-4 shrink-0 text-center text-gray-400;
}
.form-row {
  @apply grid gap-2 px-5 py-5 sm:grid-cols-[10rem_1fr] sm:gap-6 sm:px-6;
}
.form-label {
  @apply text-sm font-medium text-gray-700 sm:pt-2;
}
.text-input {
  @apply block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-xs transition focus:border-gkk focus:ring-2 focus:ring-gkk/20 focus:outline-none;
}
</style>

<script>
import Breadcrumbs from './ui/Breadcrumbs.vue'
import Documents from '../modules/Documents.js'
import Button from './ui/Button.vue'
import Modal from './ui/Modal.vue'
import QRCode from 'qrcode'
import { PDFDocument, rgb, degrees } from 'pdf-lib'

export default {
  components: { Breadcrumbs, Button, Modal },
  props: ['user', 'payments', 'lastHelperDate', 'agreementStatuses'],
  data() {
    return {
      isAdjusted: {
        name: false,
        email: false,
        password: false,
      },
      error: {
        password: false,
        data: false,
      },
      edit: '',
      name: {
        first: this.user.first_name,
        last: this.user.last_name,
      },
      email: this.user.email,
      password: {
        current: '',
        new: '',
        new_confirmation: '',
      },
      errors: {
        password: false,
      },
      qrCodes: {},
      paymentInProcess: null,
      loadingReceipt: false,
      signingAgreementKind: '',
    }
  },
  computed: {
    helperDaysSinceLast() {
      if (!this.lastHelperDate) return Infinity
      const last = new window.Date(this.lastHelperDate)
      const now = new window.Date()
      return Math.floor((now - last) / (1000 * 60 * 60 * 24))
    },
    isNewMember() {
      if (!this.user?.created_at) return false
      const createdAt = new window.Date(this.user.created_at)
      const oneYearAgo = new window.Date()
      oneYearAgo.setFullYear(oneYearAgo.getFullYear() - 1)
      return createdAt > oneYearAgo
    },
    helperStatus() {
      // 12 months + 3 weeks ≈ 386 days
      if (this.helperDaysSinceLast > 386) {
        return this.isNewMember ? 'warning' : 'error'
      }
      // 10 months ≈ 304 days
      if (this.helperDaysSinceLast > 304) return 'warning'
      return 'success'
    },
    helperAlertClasses() {
      return {
        'bg-red-50 ring-red-200': this.helperStatus === 'error',
        'bg-amber-50 ring-amber-200': this.helperStatus === 'warning',
        'bg-green-50 ring-green-200': this.helperStatus === 'success',
      }
    },
    helperAlertIconClass() {
      return {
        'bg-red-100 text-red-600': this.helperStatus === 'error',
        'bg-amber-100 text-amber-600': this.helperStatus === 'warning',
        'bg-green-100 text-green-600': this.helperStatus === 'success',
      }
    },
    helperAlertTitleClass() {
      return {
        'text-red-800': this.helperStatus === 'error',
        'text-amber-800': this.helperStatus === 'warning',
        'text-green-800': this.helperStatus === 'success',
      }
    },
    helperAlertTextClass() {
      return {
        'text-red-700/90': this.helperStatus === 'error',
        'text-amber-800/90': this.helperStatus === 'warning',
        'text-green-700/90': this.helperStatus === 'success',
      }
    },
    helperAlertIcon() {
      return {
        'fa-times-circle': this.helperStatus === 'error',
        'fa-exclamation-triangle': this.helperStatus === 'warning',
        'fa-check-circle': this.helperStatus === 'success',
      }
    },
    helperAlertTitle() {
      return {
        error: 'Funktionärskrav ej uppfyllt',
        warning: 'Funktionärskrav löper snart ut',
        success: 'Funktionärskrav uppfyllt',
      }[this.helperStatus]
    },
    agreements() {
      return [
        {
          key: 'membership',
          name: 'Medlemsavtal',
          signedAt: this.user.membership_agreement_signed_at,
          status: this.agreementStatuses.membership.status,
          expiresAt: this.agreementStatuses.membership.expires_at,
          open: this.showMemberShipAgreement,
        },
        {
          key: 'anti-doping',
          name: 'Antidopingavtal',
          signedAt: this.user.anti_doping_agreement_signed_at,
          status: this.agreementStatuses.anti_doping.status,
          expiresAt: this.agreementStatuses.anti_doping.expires_at,
          open: this.showAntiDopingAgreement,
        },
        { name: 'Antidopingplan', open: this.showAntiDopingPlan },
      ]
    },
    earliestAgreementExpiry() {
      return this.agreements
        .filter((agreement) => agreement.status === 'expiring')
        .map((agreement) => agreement.expiresAt)
        .sort()[0]
    },
    sortedPayments() {
      return [...this.payments].sort((a, b) => b.id - a.id)
    },
  },
  async created() {
    for (const payment of this.payments) {
      this.qrCodes = {
        ...this.qrCodes,
        [payment.id]: await this.paymentQRCode(payment),
      }
    }
  },
  methods: {
    async downloadReceipt(payment) {
      if (payment.state !== 'PAID') return

      this.loadingReceipt = true
      try {
        const response = await window.axios.get(`/invoices/${payment.id}`, { responseType: 'arraybuffer' })
        const pdfDoc = await PDFDocument.load(response.data)

        const pages = pdfDoc.getPages()
        const firstPage = pages[0]
        const { width, height } = firstPage.getSize()

        const stampText = 'BETALD'
        const fontSize = 60

        firstPage.drawText(stampText, {
          x: width / 2 - 100,
          y: height / 2,
          size: fontSize,
          color: rgb(0, 0.5, 0),
          rotate: degrees(30),
          opacity: 0.4,
        })

        const pdfBytes = await pdfDoc.save()
        const blob = new Blob([pdfBytes], { type: 'application/pdf' })
        window.open(URL.createObjectURL(blob))
      } finally {
        this.loadingReceipt = false
      }
    },
    loadURL(url, newTab = false) {
      if (newTab) {
        return window.open(url)
      }

      window.location = url
    },
    swishUrl(payment) {
      const swishNumber = 1235813456
      const msg = encodeURIComponent(
        `Fakturanummer ${payment.fortnox_invoice_document_number}, ${this.user.first_name} ${this.user.last_name}`,
      )
      const amount = payment.sek_amount - payment.sek_discount

      return `https://app.swish.nu/1/p/sw/?sw=${swishNumber}&amt=${amount}&msg=${msg}`
    },
    paymentQRCode(payment) {
      return QRCode.toDataURL(this.swishUrl(payment), {
        margin: 0,
        color: { light: '#0000' },
      })
    },
    paymentStatus(payment) {
      if (payment.state === 'PAID') {
        return {
          label: 'Betald',
          icon: 'fa-check',
          iconClass: 'bg-green-100 text-green-600',
          badgeClass: 'bg-green-50 text-green-700 ring-1 ring-green-600/20',
        }
      }
      if (!payment.fortnox_invoice_document_number) {
        return {
          label: 'Inväntar fakturering',
          icon: 'fa-clock-o',
          iconClass: 'bg-amber-100 text-amber-600',
          badgeClass: 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
        }
      }
      return {
        label: 'Inväntar betalning',
        icon: 'fa-exclamation',
        iconClass: 'bg-red-100 text-red-600',
        badgeClass: 'bg-red-50 text-red-700 ring-1 ring-red-600/20',
      }
    },
    formatAmount(amount) {
      return `${Math.max(amount, 0).toLocaleString('sv-SE')} kr`
    },
    paymentTypeText(paymentType) {
      return (
        {
          MEMBERSHIP: 'Medlemsavgift',
          SSFLICENSE: 'Tävlingslicens',
          COMPETITION: 'Tävlingsavgift',
        }[paymentType] || 'Okänd avgift'
      )
    },
    showMemberShipAgreement() {
      window.open(Documents.MEMBERSHIP_AGREEMENT)
    },
    showAntiDopingAgreement() {
      window.open(Documents.ANTI_DOPING_AGREEMENT)
    },
    showAntiDopingPlan() {
      window.open(Documents.ANTI_DOPING_PLAN)
    },
    signAgreement(kind) {
      this.signingAgreementKind = kind
      this.$refs.agreementConfirmationModal.show()
    },
    async confirmSignAgreement() {
      await window.axios.post(`/sign-agreements/${this.signingAgreementKind}`)
      window.location.reload()
    },
    renderDate(date) {
      return new Date(date).toLocaleDateString('sv-SE')
    },
    startEdit(type) {
      this.reset()
      this.isAdjusted[type] = true
    },
    reset(type = '') {
      this.password.current = ''
      this.edit = ''

      for (var attr in this.isAdjusted) {
        this.isAdjusted[attr] = false
      }

      if (type === 'email') {
        this.email = this.user.email
      }

      if (type === 'name') {
        this.name = {
          firstName: this.user.first_name,
          lastName: this.user.last_name,
        }
      }
    },
    updateName() {
      window.axios
        .post('/profile/name', {
          first_name: this.name.first,
          last_name: this.name.last,
        })
        .then(this.reload)
    },
    updateEmail() {
      window.axios.post('/profile/email', { email: this.email }).then(this.reload)
    },
    updatePassword() {
      window.axios
        .post('/profile/password', {
          password: this.password.new,
          password_confirmation: this.password.new_confirmation,
        })
        .then(this.reload)
        .catch(() => (this.errors.password = true))
    },
  },
}
</script>
