<template>
  <div class="max-w-[95%] mx-auto pb-12">
    <div class="max-w-3xl mx-auto">
      <!-- Medlem -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-sm mb-6 px-5 py-4 flex items-center gap-4">
        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gkk text-white font-semibold text-lg shrink-0">
          {{ user.first_name?.charAt(0) }}{{ user.last_name?.charAt(0) }}
        </div>
        <div>
          <div class="font-medium text-gray-900">{{ user.first_name }} {{ user.last_name }}</div>
          <div class="text-sm text-gray-500">{{ user.email }}</div>
        </div>
      </div>

      <!-- Funktionärsaktivitet -->
      <div class="mb-6 p-4 rounded-lg border" :class="helperAlertClasses">
        <div class="flex items-center gap-2 font-semibold" :class="helperAlertTitleClass">
          <i class="fa" :class="helperAlertIcon"></i>
          <span>{{ helperAlertTitle }}</span>
        </div>
        <div class="mt-2 text-sm" :class="helperAlertTextClass">
          <template v-if="lastHelperDate">
            Du hjälpte senast till som funktionär <strong>{{ renderDate(lastHelperDate) }}</strong>.
          </template>
          <template v-else>
            Vi har ingen registrerad funktionärsaktivitet för dig.
          </template>
          <br>Alla tävlingsaktiva medlemmar behöver ställa upp som funktionär minst en gång per år.
          <template v-if="helperStatus === 'error'">
            <br>Du kan för närvarande inte anmäla dig till tävlingar. Kontakta styrelsen om du har frågor.
          </template>
          <template v-else-if="helperStatus === 'warning'">
            <br>Det börjar bli dags att hjälpa till igen för att behålla din tävlingsrätt.
          </template>
          <template v-if="helperStatus === 'error' || helperStatus === 'warning'">
            <br><a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2F9TitYSrq6xtm6KLWDoCU7Azz8nIXep.pdf?alt=media&token=5cb38219-19c2-469b-9301-785f9eea3c28" target="_blank" class="underline font-semibold">Läs mer om funktionärskraven</a>
          </template>
        </div>
      </div>

      <!-- Avtal -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-sm mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
          <h2 class="text-lg font-medium text-gray-900">Avtal</h2>
        </div>
        <div class="divide-y divide-gray-100">
          <div class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-gray-50 transition-colors" @click="showMemberShipAgreement">
            <div>
              <div class="text-sm font-medium text-gray-900">Medlemsavtal</div>
              <div class="text-xs text-gray-500">Signerat {{ renderDate(user.membership_agreement_signed_at) }}</div>
            </div>
            <div class="shrink-0 ml-4 flex flex-col items-center text-gray-500 hover:text-gkk transition-colors">
              <i class="fa fa-external-link text-sm"></i>
              <span class="text-xs mt-0.5">Visa avtal</span>
            </div>
          </div>
          <div class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-gray-50 transition-colors" @click="showAntiDopingAgreement">
            <div>
              <div class="text-sm font-medium text-gray-900">Antidopingavtal</div>
              <div class="text-xs text-gray-500">Signerat {{ renderDate(user.anti_doping_agreement_signed_at) }}</div>
            </div>
            <div class="shrink-0 ml-4 flex flex-col items-center text-gray-500 hover:text-gkk transition-colors">
              <i class="fa fa-external-link text-sm"></i>
              <span class="text-xs mt-0.5">Visa avtal</span>
            </div>
          </div>
          <div class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-gray-50 transition-colors" @click="showAntiDopingPlan">
            <div>
              <div class="text-sm font-medium text-gray-900">Antidopingplan</div>
            </div>
            <div class="shrink-0 ml-4 flex flex-col items-center text-gray-500 hover:text-gkk transition-colors">
              <i class="fa fa-external-link text-sm"></i>
              <span class="text-xs mt-0.5">Visa plan</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Betalningar -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-sm mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
          <h2 class="text-lg font-medium text-gray-900">Avgifter</h2>
        </div>
        <div class="divide-y divide-gray-100">
          <div v-for="payment in sortedPayments" :key="payment.id">
            <div
              class="transition-all duration-150"
              :class="payment.fortnox_invoice_document_number ? 'cursor-pointer hover:bg-gray-50 group' : ''"
              @click="payment.state === 'PAID' ? downloadReceipt(payment) : payment.fortnox_invoice_document_number ? loadURL(`/invoices/${payment.id}`, true) : null"
            >
              <div class="flex items-center px-5 py-4">
                <!-- Left color accent -->
                <div class="w-1 self-stretch rounded-full shrink-0 mr-4" :class="payment.state === 'PAID' ? 'bg-green-500' : payment.fortnox_invoice_document_number ? 'bg-red-400' : 'bg-amber-400'"></div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2">
                    <span class="font-medium text-gray-900">{{ paymentTypeText(payment.type) }} {{ payment.year }}</span>
                    <span v-if="payment.state === 'PAID'" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">BETALD</span>
                    <span v-else-if="!payment.fortnox_invoice_document_number" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">INVÄNTAR FAKTURERING</span>
                    <span v-else class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">INVÄNTAR BETALNING</span>
                  </div>
                  <div v-if="payment.type === 'COMPETITION' && payment.competition" class="text-sm text-gray-500 mt-0.5">
                    {{ payment.competition.name }}
                    <span v-if="payment.competition.date">({{ renderDate(payment.competition.date) }})</span>
                  </div>
                  <div class="text-sm text-gray-500 mt-0.5">{{ Math.max(payment.sek_amount - payment.sek_discount, 0) }} SEK</div>
                </div>

                <!-- Action hint -->
                <div v-if="payment.fortnox_invoice_document_number" class="shrink-0 ml-4 flex flex-col items-center text-gray-500 group-hover:text-gkk transition-colors">
                  <i class="fa fa-external-link text-sm"></i>
                  <span class="text-xs mt-0.5">{{ payment.state === 'PAID' ? 'Visa kvitto' : 'Visa faktura' }}</span>
                </div>
              </div>

              <!-- Info section for unpaid -->
              <div v-if="!payment.fortnox_invoice_document_number" class="border-t border-gray-100 bg-gray-50 px-5 py-3 text-sm text-gray-500 space-y-1" @click.stop>
                <p>Faktura kommer skapas och skickas ut inom kort.</p>
                <p>Faktura skickas till din epost och du kommer även kunna se den här.</p>
                <p>Betalning sker via Swish eller Bankgiro.</p>
              </div>
              <div v-else-if="payment.state !== 'PAID'" class="border-t border-gray-100 bg-gray-50 px-5 py-3 text-sm text-gray-500 space-y-1" @click.stop>
                <p>Faktura har även skickats till din epost.</p>
                <p>Om fakturan är felaktig, exempelvis om du är student, skicka in studentbevis till <a class="underline text-nowrap" href="mailto:info@gkk-styrkelyft.se">info@gkk-styrkelyft.se</a> så korrigerar vi fakturan inom kort.</p>
                <p>Betalning sker via Swish eller Bankgiro.</p>
                <Button type="secondary" class="my-1 md:hidden" @click.stop="loadURL(swishUrl(payment))">Klicka här för att betala med Swish på denna enhet</Button>
                <p>Efter betalning kan det ta några bankdagar innan status uppdateras här.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Kontouppgifter -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
          <h2 class="text-lg font-medium text-gray-900">Kontouppgifter</h2>
        </div>
        <div class="px-5 py-4 space-y-6">
          <!-- Födelseår -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Födelseår</label>
            <div class="text-sm text-gray-900">{{ user.birth_year }}</div>
          </div>

          <!-- Namn -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Namn</label>
            <div class="space-y-2">
              <input
                type="text"
                v-model="name.first"
                class="form-input block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
              />
              <input
                type="text"
                v-model="name.last"
                class="form-input block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
              />
            </div>
            <Button type="secondary" class="mt-2" @click="updateName">Uppdatera namn</Button>
          </div>

          <!-- Epost -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Epost</label>
            <input
              v-model="email"
              type="email"
              class="form-input block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
            />
            <Button type="secondary" class="mt-2" @click="updateEmail">Uppdatera epost</Button>
          </div>

          <!-- Lösenord -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Lösenord</label>
            <div v-if="!isAdjusted.password">
              <Button type="secondary" @click="startEdit('password')">Redigera lösenord</Button>
            </div>
            <div v-else class="space-y-2">
              <input
                v-model="password.new"
                type="password"
                placeholder="Nytt lösenord"
                class="form-input block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
              />
              <input
                v-model="password.new_confirmation"
                type="password"
                placeholder="Bekräfta lösenord"
                class="form-input block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
              />
              <div class="flex gap-2">
                <Button type="secondary" @click="updatePassword">Byt lösenord</Button>
                <button
                  @click="reset"
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-500 hover:text-red-700 transition-colors"
                >
                  Avbryt
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Full-screen loading overlay -->
  <div v-if="loadingReceipt" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 flex flex-col items-center shadow-xl">
      <i class="fa fa-spinner fa-spin text-3xl text-gkk mb-3"></i>
      <span class="text-gray-700">Laddar kvitto...</span>
    </div>
  </div>
</template>

<script>
import Documents from '../modules/Documents.js'
import Button from './ui/Button.vue'
import Modal from './ui/Modal.vue'
import QRCode from 'qrcode'
import { PDFDocument, rgb, degrees } from 'pdf-lib'

export default {
  components: { Button, Modal },
  props: ['user', 'payments', 'lastHelperDate'],
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
        'bg-red-50 border-red-300': this.helperStatus === 'error',
        'bg-amber-50 border-amber-300': this.helperStatus === 'warning',
        'bg-green-50 border-green-300': this.helperStatus === 'success',
      }
    },
    helperAlertTitleClass() {
      return {
        'text-red-700': this.helperStatus === 'error',
        'text-amber-700': this.helperStatus === 'warning',
        'text-green-700': this.helperStatus === 'success',
      }
    },
    helperAlertTextClass() {
      return {
        'text-red-600': this.helperStatus === 'error',
        'text-amber-600': this.helperStatus === 'warning',
        'text-green-600': this.helperStatus === 'success',
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
      const swishNumber = 1235813456;
      const msg = encodeURIComponent(`Fakturanummer ${payment.fortnox_invoice_document_number}, ${this.user.first_name} ${this.user.last_name}`)
      const amount = payment.sek_amount - payment.sek_discount
      
      return `https://app.swish.nu/1/p/sw/?sw=${swishNumber}&amt=${amount}&msg=${msg}`
    },
    paymentQRCode(payment) {
      return QRCode.toDataURL(this.swishUrl(payment), {
        margin: 0,
        color: { light: '#0000' },
      })
    },
    paymentTypeText(paymentType) {
      return {
        MEMBERSHIP: 'Medlemsavgift',
        SSFLICENSE: 'Tävlingslicens',
        COMPETITION: 'Tävlingsavgift',
      }[paymentType] || 'Okänd avgift'
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
