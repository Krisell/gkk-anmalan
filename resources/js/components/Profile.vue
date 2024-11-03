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

      <h2 class="mt-8 mb-1 text-2xl">Avgifter</h2>
      <ul>
        <li v-for="payment in payments" :key="payment.id" class="mt-2">
          <div class="border-4 rounded p-4">
            <div v-if="!payment.state" class="bg-red-400 border-red-400 border rounded p-2 text-white rounded-r-none text-sm inline-block text-center px-6">
              OBETALD<br>{{ payment.sek_amount }} SEK
            </div>
            <div v-else-if="payment.state === 'PENDING'" class="bg-orange-400 border-orange-400 p-2 text-white border rounded rounded-r-none text-sm inline-block text-center px-6">VERIFIERAS<br>{{ payment.sek_amount }} SEK</div>
            <div v-else-if="payment.state === 'PAID'" class="bg-green-400 border-green-400 p-2 text-white border rounded rounded-r-none text-sm inline-block text-center px-6">BETALD<br>{{ payment.sek_amount }} SEK</div>
            <div class="p-2 border rounded border-gkk border-l-0 rounded-l-none text-sm inline-block text-center px-6">
              <div>{{ paymentTypeText(payment.type) }}</div>
              <div>{{payment.year }}</div>
            </div>
            <div v-if="payment.state === 'PENDING'" class="text-sm">
              Betalningen är giltig omedelbart och kommer verifieras i systemet av föreningens kassör senast inom några veckor.
            </div>
            <div v-if="payment.state !== 'PAID'">
                <img class="cursor-pointer w-48 mt-3 p-1 border-4 border-black rounded-lg" :src="`${qrCodes[payment.id]}`" />
                <p class="text-sm italic">Skanna för att betala med Swish</p>
                <div class="md:hidden">
                  <Button type="secondary" class="my-2" @click="loadURL(swishUrl(payment))">Klicka här för att öppna Swish på denna enhet</Button>
                </div>
                <div class="mt-2">
                  <Button 
                    v-show="!payment.state"
                    @click="showPaymentModal(payment)"
                    type="primary" 
                    class="my-2"
                    >
                      Klicka här när betalningen är genomförd
                    </Button>
                </div>
                <p class="text-sm mt-3">Vill du betala på annat sätt? Se sidan "Om GKK" för betalningsinstruktioner via Bankgiro. Se till att märka betalningen tydligt med namn och vilken avgift det gäller.</p>
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
        class="form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md"
      />
      <input
        type="text"
        v-model="name.last"
        class="mt-1 form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md"
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
        class="form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md"
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
          class="mt-1 form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md"
        />
        <input
          v-model="password.new_confirmation"
          type="password"
          placeholder="Bekräfta lösenord"
          class="mt-1 form-input block w-full sm:text-sm sm:leading-5 border-gray-300 rounded-md"
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

    <modal name="paymentPendingModal" :adaptive="true" height="auto">
      <div style="padding: 30px; margin-top: 20px">
        <h3 class="text-center text-xl mb-4">
          Har du genomfört betalningen?
        </h3>
        <p class="text-center">Detta verifieras av vår kassör inom några veckor. Tills dess kommer det stå <i>"Verifieras"</i> här. När betalningen är verifierad kommer detta ändras till <i>"Betald"</i>.</p>
      </div>

      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px">
        <Button @click="$modal.hide('paymentPendingModal')" type="secondary" class="mx-4">Tillbaka</Button>
        <Button @click="markPaymentAsPending" type="success">Ja, betalningen är genomförd</Button>
      </div>
    </modal>
  </div>
</template>

<script>
import Documents from '../modules/Documents.js'
import Button from './ui/Button.vue'
import QRCode from 'qrcode'

export default {
  components: { Button },
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
    async markPaymentAsPending() {
      await axios.patch(`/payments/${this.paymentInProcess.id}`, {
        state: 'PENDING',
      })

      window.location.reload()
    },
    showPaymentModal(payment) {
      this.paymentInProcess = payment
      this.$modal.show('paymentPendingModal')
    },
    loadURL(url) {
      window.location = url
    },
    swishUrl(payment) {
      const msg = encodeURIComponent(`${payment.type} ${payment.year}, ${this.user.first_name} ${this.user.last_name}`)
      
      return `https://app.swish.nu/1/p/sw/?sw=1235813456&amt=${payment.sek_amount}&msg=${msg}`
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
      }[paymentType] || 'Okänd avgift'
    },
    showMemberShipAgreement() {
      window.open(Documents.MEMBERSHIP_AGREEMENT)
    },
    showAntiDopingAgreement() {
      window.open(Documents.ANTI_DOPING_AGREEMENT)
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
