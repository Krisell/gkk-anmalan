<template>
  <div>
    <h1 style="text-align: center;">Admin</h1>

    <div style="margin: 10px;">
      <h3>Funktionärsanmälningar</h3>
      <div v-for="event in organizerEvents" :key="event.id">
        {{ event.name }}
      </div>

      <button @click="showNewOrganizationEvent = !showNewOrganizationEvent" type="button" class="btn btn-primary">
        <i class="fa fa-plus"></i>&nbsp;Nytt event för funktionärsanmälningar
      </button>

      <form style="margin-top: 20px;" v-show="showNewOrganizationEvent">
        <div class="form-group">
          <input v-model="event.name" class="form-control" name="name" placeholder="Namn på event">
        </div>
        <div class="form-group">
          <input v-model="event.date" class="form-control" type="date" name="date">
        </div>
        <div class="form-group">
          <input v-model="event.time" class="form-control" name="time" placeholder="Ungefärlig tid, ex. 8 – 15">
        </div>
        <div class="form-group">
          <input v-model="event.location" class="form-control" name="location" placeholder="Plats, ex Friskis Majorna">
        </div>
        <div class="form-group">
          <textarea v-model="event.description" class="form-control" name="description" placeholder="Ev. ytterligare info"></textarea>
        </div>
        <button @click="createEvent" type="button" class="btn btn-primary">Skapa event</button>
        <div v-if="newOrganizationEventError" style="color: red;">
          Kunde inte skapa event, kontrollera inmatning och anlutning.
        </div>
      </form>
    </div>

    <div style="margin: 10px;">
      <h3>Tävlingsanmälningar</h3>

      <!-- <div v-for="event in organizerEvents" :key="event.id">
        {{ event }}
      </div> -->

      <button @click="showNewOrganizationEvent = !showNewOrganizationEvent" type="button" class="btn btn-primary">
        <i class="fa fa-plus"></i>&nbsp;Nytt event för tävlingsanmälan
      </button>
    </div>

    <div style="margin: 10px;">
      <h3>Intresseanmälningar</h3>
<!--
      <div v-for="event in organizerEvents" :key="event.id">
        {{ event }}
      </div> -->

      <button @click="showNewOrganizationEvent = !showNewOrganizationEvent" type="button" class="btn btn-primary">
        <i class="fa fa-plus"></i>&nbsp;Nytt event för intresseanmälningar (??)
      </button>
    </div>
  </div>
</template>

<script>
export default {
  props: ['organizer-events'],
  data () {
    return {
      showNewOrganizationEvent: false,
      newOrganizationEventError: false,
      event: {
        name: '',
        date: '',
        time: '',
        location: '',
        description: '',
      },
    }
},
methods: {
  createEvent () {
      this.newOrganizationEventError = false

      window.axios({
        method: 'post',
        url: '/organizer-events',
        data: this.event
      }).then(response => {
        window.location.reload()
      }).catch(err => {
        this.newOrganizationEventError = true
      })
    }
  }
}
</script>

<style scoped lang="less">

</style>
