<template>
  <div>
    <h1 style="text-align: center;">Admin</h1>

    <div class="type">
      <h3>Funktionärsanmälningar</h3>
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Event</th>
            <th scope="col">Datum</th>
            <th scope="col">Tid</th>
            <th scope="col">Plats</th>
            <th scope="col">Antal anmälda</th>
            <th scope="col"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="event in events" :key="event.id" @click="administrateEvent(event)" style="cursor: pointer;">
            <td>{{ event.name }}</td>
            <td>{{ event.date }}</td>
            <td>{{ event.time }}</td>
            <td>{{ event.location }}</td>
            <td>{{ event.registrations.length }}</td>
            <th scope="row"><i class="fa fa-th-list"></i></th>
          </tr>
        </tbody>
      </table>

      <button @click="showNewEvent = !showNewEvent" type="button" class="btn btn-primary">
        <i class="fa fa-plus"></i>&nbsp;Nytt event för funktionärsanmälningar
      </button>

      <form style="margin-top: 20px;" v-show="showNewEvent">
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
        <div v-if="newEventError" style="color: red;">
          Kunde inte skapa event, kontrollera inmatning och anlutning.
        </div>
      </form>
    </div>

    <!-- <div class="type">
      <h3>Tävlingsanmälningar</h3>

      <button @click="showNewEvent = !showNewEvent" type="button" class="btn btn-primary">
        <i class="fa fa-plus"></i>&nbsp;Nytt event för tävlingsanmälan
      </button>
    </div>

    <div class="type">
      <h3>Intresseanmälningar</h3>

      <button @click="showNewEvent = !showNewEvent" type="button" class="btn btn-primary">
        <i class="fa fa-plus"></i>&nbsp;Nytt event för intresseanmälningar (??)
      </button>
    </div> -->
  </div>
</template>

<script>
export default {
  props: ['events'],
  data () {
    return {
      showNewEvent: false,
      newEventError: false,
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
      this.newEventError = false

      window.axios({
        method: 'post',
        url: '/admin/events',
        data: this.event
      }).then(response => {
        window.location.reload()
      }).catch(err => {
        this.newEventError = true
      })
    },
    administrateEvent (event) {
      window.location = `/admin/events/${event.id}`
    }
  }
}
</script>

<style scoped lang="less">
  .type {
    margin-bottom: 40px;
  }
</style>
