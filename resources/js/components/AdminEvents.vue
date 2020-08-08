<template>
  <div>
    <h1 style="text-align: center;">Admin</h1>

    <div class="type">
      <h3>Funktionärsanmälningar</h3>
      <table class="table">
        <thead>
          <tr>
            <th class="gkk" scope="col">Event</th>
            <th class="gkk" scope="col">Sista anmälningsdag</th>
            <th class="gkk" scope="col">Datum</th>
            <th class="gkk" scope="col">Tid</th>
            <th class="gkk" scope="col">Plats</th>
            <th class="gkk" scope="col">Antal tackat ja</th>
            <th class="gkk" scope="col">Åtgärder</th>
          </tr>
        </thead>
        <tbody>
          <tr class="event-row" v-for="event in events" :key="event.id" @click="location(`/admin/events/${event.id}`)" style="cursor: pointer;">
            <td>{{ event.name }}</td>
            <td>{{ (event.last_registration_at || '').slice(0, 10) }}</td>
            <td>{{ event.date }}</td>
            <td>{{ event.time }}</td>
            <td>{{ event.location }}</td>
            <td>{{ countYes(event) }} (av {{ event.registrations.length }})</td>
            <td @click="e => e.stopPropagation()" scope="row">
              <i v-tooltip="'Redigera event'" @click="edit(event)" class="fa fa-cogs"></i>
              <i v-tooltip="'Radera event'" style="margin-left: 5px;" @click="confirmDelete(event)" class="fa fa-trash"></i>
            </td>
          </tr>
        </tbody>
      </table>

      <el-button v-if="!editing" @click="showNewEvent = !showNewEvent">
        <i class="fa fa-plus"></i>&nbsp;Nytt event för funktionärsanmälningar
      </el-button>

      <form style="margin-top: 20px;" v-show="showNewEvent">
        <div class="form-group">
          <input v-model="event.name" class="form-control" name="name" placeholder="Namn på event">
        </div>
        <div class="form-group">
          <div>Datum</div>
          <input v-model="event.date" class="form-control" type="date" name="date">
        </div>

        <div class="form-group">
          <div>Ev. sista anmälningsdag</div>
          <input v-model="event.last_registration_at" class="form-control" type="date" name="date">
        </div>
        <div class="form-group">
          <input v-model="event.time" class="form-control" name="time" placeholder="Ungefärlig tid, ex. 8 – 15">
        </div>
        <div class="form-group">
          <input v-model="event.location" class="form-control" name="location" placeholder="Plats, ex Friskis Majorna">
        </div>
        <div class="form-group">
          <textarea rows="6" v-model="event.description" class="form-control" name="description" placeholder="Ev. ytterligare info"></textarea>
        </div>

        <div style="display: flex; margin-bottom: 5px; align-items: center;">
          <el-toggle-button v-model="event.publish_count" />
          <div style="margin-left: 10px;">Visa antal anmälda för medlemmar</div>
        </div>

        <div style="display: flex; margin-bottom: 20px; align-items: center;">
          <el-toggle-button v-model="event.publish_list" />
          <div style="margin-left: 10px;">Visa anmälningslista för medlemmar (endast namn)</div>
        </div>

        <div style="display: flex; margin-bottom: 20px; align-items: center;">
          <div style="margin-right: 10px;">Visningsalternativ</div>
          <el-dropdown wide v-model="event.show_status" :options="showStatusOptions" />
        </div>

        <div style="display: flex">
          <el-button v-if="!editing" @click="createEvent" primary>Skapa event</el-button>
          <el-button style="margin-right: 10px;" secondary v-if="editing" @click="cancelUpdate">Ångra</el-button>
          <el-button primary v-if="editing" @click="updateEvent">Uppdatera event</el-button>
        </div>

        <div v-if="newEventError" style="color: red;">
          Kunde inte skapa event, kontrollera inmatning och anlutning.
        </div>
      </form>
    </div>

    <GkkLink to="/" text="Tillbaka till startsidan" />

    <modal name="delete-event" :adaptive="true" height="auto">
      <div style="padding: 30px; margin-top: 20px;">
        <h3 style="text-align: center;">Är du säker på att du vill radera {{ selectedEvent && selectedEvent.name }}?</h3>
      </div>

      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px;">
        <el-button secondary @click="$modal.hide('delete-event')">Nej</el-button>
        <el-button style="margin-left: 10px;" danger primary @click="deleteEvent">Radera</el-button>
      </div>
    </modal>
  </div>
</template>

<script>
export default {
  props: ['events'],
  data () {
    return {
      editing: false,
      showNewEvent: false,
      newEventError: false,
      selectedEvent: null,
      event: {
        name: '',
        date: '',
        time: '',
        location: '',
        description: '',
        publish_count: false,
        publish_list: false,
        show_status: 'default',
      },
      showStatusOptions: [
        { value: 'default', label: 'Default (visas tills datum passerat)' },
        { value: 'show', label: 'Visa' },
        { value: 'hide', label: 'Dölj' }
      ],
    }
  },
  methods: {
    confirmDelete (event) {
      this.selectedEvent = event
      console.log(event)
      this.$modal.show('delete-event')
    },
    deleteEvent () {
      window.axios.delete(`/admin/events/${this.selectedEvent.id}`).then(() => window.location.reload())
    },
    countYes (event) {
      return event.registrations.filter(registration => registration.status == 1).length
    },
    edit (event) {
      Object.assign(this.event, event)
      this.showNewEvent = true
      this.editing = true
    },
    createEvent () {
      this.newEventError = false

      window.axios({
        method: 'post',
        url: '/admin/events',
        data: this.event
      }).then(this.reload).catch(err => {
        this.newEventError = true
      })
    },
    cancelUpdate () {
      this.reload()
    },
    updateEvent () {
      this.newEventError = false

      window.axios({
        method: 'patch',
        url: `/admin/events/${this.event.id}`,
        data: this.event
      }).then(this.reload).catch(err => {
        this.newEventError = true
      })
    }
  }
}
</script>

<style scoped lang="less">
  .type {
    margin-bottom: 40px;
  }

  .event-row:hover {
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  }

  th, td, thead, table {
    border: none;
  }

  th.gkk {
    border-bottom: 1px solid #253969;
  }
</style>
