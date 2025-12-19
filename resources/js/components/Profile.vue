<template>
  <div class="max-w-[95%] mx-auto">
    <div class="max-w-3xl mx-auto">
      <h1 class="text-3xl font-thin mb-2">Profil</h1>
      <div>Inloggad som {{ user.email }}</div>
      <div style="margin-top: 30px"></div>

      <h2 class="mt-8 mb-1 text-2xl">Avtal</h2>
      <div class="mt-2 flex items-center">
        <Button type="secondary" @click="showMemberShipAgreement">Visa medlemsavtal</Button>
        <div class="ml-6 text-sm italic">Signerat {{ renderDate(user.membership_agreement_signed_at) }}</div>
      </div>
      <div class="mt-2 flex items-center">
        <Button type="secondary" @click="showAntiDopingAgreement">Visa antidopingavtal</Button>
        <div class="ml-6 text-sm italic">Signerat {{ renderDate(user.anti_doping_agreement_signed_at) }}</div>
      </div>
      <div class="mt-2 flex items-center">
        <Button type="secondary" @click="showAntiDopingPlan">Visa antidopingplan</Button>
      </div>

      <h2 class="mt-8 mb-1 text-2xl">Medlemsavgift och SSF-licens</h2>
      <ul>
        <li v-for="payment in payments" :key="payment.id" class="mt-2">
          <div class="border-4 rounded p-4">
            <div class="flex">
              <div class="p-2 border rounded border-gkk border-r-0 rounded-r-none text-sm inline-block text-center px-6">
                <div>{{ paymentTypeText(payment.type) }} {{payment.year }}</div>
                <div>{{ Math.max(payment.sek_amount - payment.sek_discount, 0) }} SEK</div>
              </div>
              <div v-if="payment.state === 'PAID'" class="bg-gkk border-gkk p-2 text-white border rounded rounded-l-none text-sm text-center px-6 flex items-center flex-col cursor-pointer">
                BETALD
                <div @click="loadURL(`/invoices/${payment.id}`, true)" v-if="payment.fortnox_invoice_document_number" class="text-xs block underline">Klicka för att öppna faktura</div>
              </div>
              <div v-else-if="!payment.fortnox_invoice_document_number" class="bg-blue-400 border-blue-400 border rounded p-2 text-white rounded-l-none text-sm text-center px-6 flex items-center">
                INVÄNTAR FAKTURERING
              </div>
              <div v-else-if="payment.fortnox_invoice_document_number && payment.state !== 'PAID'" class="bg-red-400 border-red-400 p-2 text-white border rounded rounded-l-none text-sm text-center px-6 flex items-center flex-col cursor-pointer" @click="loadURL(`/invoices/${payment.id}`, true)">
                <div>INVÄNTAR BETALNING</div>
                <div class="text-xs block underline">Klicka för att öppna faktura</div>
              </div>
            </div>
            <div v-if="!payment.fortnox_invoice_document_number">
              <div class="mt-4">
                Faktura kommer skapas och skickas ut inom kort.
              </div>
              <div class="mt-2">
                Faktura skickas till din epost och du kommer även kunna se den här.
              </div>
              <div class="mt-2">
                Betalning sker via Swish eller Bankgiro.
              </div>
            </div>
            <div v-else-if="payment.state !== 'PAID'">
              <div class="mt-4">
                Faktura har även skickats till din epost.
              </div>
              <div class="mt-2">
                Om fakturan är felaktig, exempelvis om du är student, skicka in studentbevis till <a class="underline text-nowrap" href="mailto:info@gkk-styrkelyft.se">info@gkk-styrkelyft.se</a> så korrigerar vi fakturan inom kort.
              </div>
              <div class="mt-2">
                Betalning sker via Swish eller Bankgiro.
              </div>
              <div>
                <Button type="secondary" class="my-2 md:hidden" @click="loadURL(swishUrl(payment))">Klicka här för att betala med Swish på denna enhet</Button>
              </div>
              <div class="mt-2">
                Efter betalning kan det ta några bankdagar innan status uppdateras här.
              </div>
            </div>
          </div>
        </li>
      </ul> 

      <h2 class="mt-8 mb-1 text-2xl">Kontouppgifter</h2>
      <h3 class="font-light mt-2 mb-2">Födelseår</h3>
      {{ user.birth_year }}
      <h3 class="font-light mt-6 mb-2">Namn</h3>
      <input
        type="text"
        v-model="name.first"
        class="form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md p-2 border"
      />
      <input
        type="text"
        v-model="name.last"
        class="mt-1 form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md p-2 border"
      />
      <button
        @click="updateName"
        class="mt-2 inline-flex items-center px-4 py-2 border text-sm leading-5 font-medium rounded-md text-gkk bg-white border-gkk focus:outline-none focus:shadow-outline-indigo active:bg-gkk transition duration-150 ease-in-out"
      >
        <span>Uppdatera namn</span>
      </button>

      <h3 class="font-light mt-6 mb-2">Epost</h3>
      <input
        v-model="email"
        type="email"
        class="form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md p-2 border"
      />
      <button
        @click="updateEmail"
        class="mt-2 inline-flex items-center px-4 py-2 border text-sm leading-5 font-medium rounded-md text-gkk bg-white border-gkk focus:outline-none focus:shadow-outline-indigo active:bg-gkk transition duration-150 ease-in-out"
      >
        <span>Uppdatera epost</span>
      </button>

      <h3 class="font-light mt-6 mb-2">Lösenord</h3>
      <div v-if="!isAdjusted.password">
        <button
          @click="startEdit('password')"
          class="mt-2 inline-flex items-center px-4 py-2 border text-sm leading-5 font-medium rounded-md text-gkk bg-white border-gkk focus:outline-none focus:shadow-outline-indigo active:bg-gkk transition duration-150 ease-in-out"
        >
          <span>Redigera lösenord</span>
        </button>
      </div>

      <div v-else>
        <input
          v-model="password.new"
          type="password"
          placeholder="Nytt lösenord"
          class="mt-1 form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md p-2 border"
        />
        <input
          v-model="password.new_confirmation"
          type="password"
          placeholder="Bekräfta lösenord"
          class="mt-1 form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md p-2 border"
        />
        <div style="display: flex">
          <button
            @click="reset"
            class="mt-2 inline-flex items-center px-4 py-2 border text-sm leading-5 font-medium rounded-md text-gkk bg-white border-gkk focus:outline-none focus:shadow-outline-indigo active:bg-gkk transition duration-150 ease-in-out"
          >
            <span class="text-red-400">Ångra</span>
          </button>

          <button
            @click="updatePassword"
            class="ml-2 mt-2 inline-flex items-center px-4 py-2 border text-sm leading-5 font-medium rounded-md text-gkk bg-white border-gkk focus:outline-none focus:shadow-outline-indigo active:bg-gkk transition duration-150 ease-in-out"
          >
            <span>Byt lösenord</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Documents from '../modules/Documents.js'
import Button from './ui/Button.vue'
import Modal from './ui/Modal.vue'
import QRCode from 'qrcode'

export default {
  components: { Button, Modal },
  props: ['user', 'payments'],
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
    }
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
