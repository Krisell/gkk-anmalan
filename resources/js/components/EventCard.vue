<template>
  <div class="action-button-card" @click="$emit('click')">
    <div style="display: flex; flex-direction: row; justify-content: space-around; align-items: center;">
      <div class="icon" style="margin-right: 20px;"><i class="icon fa fa-users"></i></div>
      <el-message danger v-if="registration && registration.status == 0">Du har anmält att du inte kan komma.</el-message>
      <el-message success v-else-if="registration && registration.status == 1">Du är anmäld som funktionär, tack!</el-message>
      <el-message info v-else>Du har inte meddelat om du kan delta ännu.</el-message>
    </div>
    <div>
      <div class="description">{{ event.name }} ({{ dateString }})</div>
      <div class="description" style="font-size: 12px; margin-top: 0;">{{ event.description }}</div>
    </div>
  </div>
</template>

<script>
import moment from 'moment'

export default {
  props: ['event', 'registration'],
  computed: {
    dateString () {
      let day = {
        1: 'Måndag',
        2: 'Tisdag',
        3: 'Onsdag',
        4: 'Torsdag',
        5: 'Fredag',
        6: 'Lördag',
        7: 'Söndag'
      }[moment(this.event.date).isoWeekday(1).weekday()]

      return `${day}, ${moment(this.event.date).date()} ${moment(this.event.date).locale('sv').format('MMMM')}`
    }
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
  height: 170px;
  background: #FFFFFF;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  border-radius: 2px;
  font-size: 12px;
  cursor: pointer;
  flex: 1;
  margin-right: 10px;
  padding: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #ff9e02;
  color: black;
  border: none;

  -webkit-transition: all .1s ease-in-out;
  transition: all .1s ease-in-out;
  -webkit-animation-duration: .1s;
  animation-duration: .1s;

  &:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 10px rgba(0,0,0,0.20);
  }

  &:active {
    transform: translateY(1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  }

  &.danger {
    border: 1px solid #d84a38;
    color: #d84a38;
  }

  border-top: 3px solid #243868;
}

 .action-button-card .description {
   margin-top: 20px;
   font-size: 16px;
   color: black;
   text-align: center;
 }

 .action-button-card i {
   font-size: 40px;
 }

 .action-button-card-activated {
   background: #FFFFFF;
   border: 1px solid #45C458;
   color: #45C458;
   box-shadow: 0 2px 4px 0 rgba(0,0,0,0.20);
   border-radius: 2px;
 }

 .action-button-card-danger {
   background: #FFFFFF;
   border: 1px solid #D84A38;
   color: #D84A38;
   box-shadow: 0 2px 4px 0 rgba(0,0,0,0.20);
   border-radius: 2px;
 }
</style>

