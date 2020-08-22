<template>
  <div class="action-button-card max-w-xl mx-auto" @click="$emit('click')">
    <div class="description">{{ competition.name }} ({{ dateString }})</div>
    <div v-if="competition.last_registration_at" style="text-align: center;">
      Sista anmälningsdag: {{ competition.last_registration_at }}
    </div>
    <div class="description" style="font-size: 12px; margin-top: 0; white-space: pre-wrap;">
      {{ competition.description }}
    </div>
    <div style="margin-top: 30px;">
      <el-message warning v-if="registration && registration.status == 0">
        <div>Du har tackat nej till denna tävling.</div>
        <div v-if="afterLastRegistration(competition)" class="lastDatePassed">Sista anmälningsdatum har passerat.</div>
      </el-message>
      <el-message success v-else-if="registration && registration.status == 1">
        <div>Du har anmält intresse att tävla!</div>
        <div v-if="afterLastRegistration(competition)" class="lastDatePassed">Sista anmälningsdatum har passerat.</div>
      </el-message>
      <el-message info v-else>
        <div>Du har inte meddelat om du vill tävla ännu.</div>
        <div v-if="afterLastRegistration(competition)" class="lastDatePassed">Sista anmälningsdatum har passerat.</div>
      </el-message>

      <el-message style="margin-top: 10px;" v-if="competition.publish_count_value > 0">
        Hittills har {{ competition.publish_count_value }} GKK-medlemmar tackat ja till denna tävling.
      </el-message>
    </div>
  </div>
</template>

<script>
import Date from '../modules/Date.js'

export default {
  props: ['competition', 'registration'],
  computed: {
    dateString() {
      if (this.competition.end_date) {
        return `${Date.string(this.competition.date)} – ${Date.string(this.competition.end_date)}`
      }

      return Date.string(this.competition.date)
    },
  },
  methods: {
    afterLastRegistration(competition) {
      if (!competition.last_registration_at) {
        return false
      }

      return new window.Date().setHours(0, 0, 0, 0) > new window.Date(competition.last_registration_at)
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
