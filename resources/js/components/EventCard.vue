<template>
  <div class="action-button-card max-w-xl mx-auto" @click="$emit('click')">
    <div class="description">{{ event.name }} ({{ dateString }})</div>
    <div v-if="event.last_registration_at" style="text-align: center">
      Sista anmälningsdag: {{ event.last_registration_at }}
    </div>
    <div class="description" style="font-size: 12px; margin-top: 0; white-space: pre-wrap">
      {{ event.description }}
    </div>
    <div style="margin-top: 30px">
      <Message warning v-if="registration && registration.status == 0">
        <div>Du har anmält att du inte kan komma.</div>
        <div v-if="afterLastRegistration(event)" class="lastDatePassed">Sista anmälningsdatum har passerat.</div>
      </Message>
      <Message success v-else-if="registration && registration.status == 1">
        <div>Du är anmäld som funktionär, tack!</div>
        <div v-if="afterLastRegistration(event)" class="lastDatePassed">Sista anmälningsdatum har passerat.</div>
      </Message>
      <Message info v-else>
        <div>Du har inte meddelat om du kan delta ännu.</div>
        <div v-if="afterLastRegistration(event)" class="lastDatePassed">Sista anmälningsdatum har passerat.</div>
      </Message>

      <Message style="margin-top: 10px" v-if="event.publish_count_value > 0">
        Hittills har {{ event.publish_count_value }} GKK-medlemmar tackat ja till detta event.
      </Message>
    </div>
  </div>
</template>

<script>
import Date from '../modules/Date.js'
import Message from './Message.vue'

export default {
  components: { Message },
  props: ['event', 'registration'],
  computed: {
    dateString() {
      if (this.event.end_date) {
        return `${Date.string(this.event.date)} – ${Date.string(this.event.end_date)}`
      }

      return Date.string(this.event.date)
    },
  },
  methods: {
    afterLastRegistration(event) {
      if (!event.last_registration_at) {
        return false
      }

      return new window.Date().setHours(0, 0, 0, 0) > new window.Date(event.last_registration_at)
    },
  },
}
</script>

<style scoped lang="less">
.icon {
  min-height: 50px;
  color: #19274a;
}

i.icon {
  align-items: center;
  justify-content: center;
  display: flex;
}

.action-button-card {
  background: #ffffff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  border-radius: 2px;
  font-size: 12px;
  cursor: pointer;
  flex: 1;
  // margin-right: 10px;
  padding: 30px;
  color: #ff9e02;
  color: black;
  border: none;

  -webkit-transition: all 0.1s ease-in-out;
  transition: all 0.1s ease-in-out;
  -webkit-animation-duration: 0.1s;
  animation-duration: 0.1s;

  &:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
  }

  &:active {
    transform: translateY(1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  }

  &.danger {
    border: 1px solid #d84a38;
    color: #d84a38;
  }

  border-top: 3px solid #243868;
}

.action-button-card .description {
  //  margin-top: 20px;
  font-size: 16px;
  color: black;
  text-align: center;
}

.action-button-card i {
  font-size: 40px;
}

.action-button-card-activated {
  background: #ffffff;
  border: 1px solid #45c458;
  color: #45c458;
  box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2);
  border-radius: 2px;
}

.action-button-card-danger {
  background: #ffffff;
  border: 1px solid #d84a38;
  color: #d84a38;
  box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2);
  border-radius: 2px;
}

.lastDatePassed {
  font-size: 12px;
}
</style>
