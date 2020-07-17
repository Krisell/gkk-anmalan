<template>
  <div>
    <h1 style="text-align: center;">Admin</h1>

    <div class="type">
      <h3>Tävlingsanmälan</h3>
      <table class="table">
        <thead>
          <tr>
            <th class="gkk" scope="col">Tävlingsnamn</th>
            <th class="gkk" scope="col">Sista anmälningsdag</th>
            <th class="gkk" scope="col">Datum</th>
            <th class="gkk" scope="col">Tid</th>
            <th class="gkk" scope="col">Plats</th>
            <th class="gkk" scope="col">Antal anmälda</th>
            <th class="gkk" scope="col">Åtgärder</th>
          </tr>
        </thead>
        <tbody>
          <tr class="competition-row" v-for="competition in competitions" :key="competition.id" @click="location(`/admin/competitions/${competition.id}`)" style="cursor: pointer;">
            <td>{{ competition.name }}</td>
            <td>{{ (competition.last_registration_at || '').slice(0, 10) }}</td>
            <td>{{ competition | dateString }}</td>
            <td>{{ competition.time }}</td>
            <td>{{ competition.location }}</td>
            <td>{{ countYes(competition) }} (av {{ competition.registrations.length }})</td>
            <td @click="e => e.stopPropagation()" scope="row">
              <i data-toggle="tooltip" data-placement="top" title="Redigera tävling" @click="edit(competition)" class="fa fa-cogs"></i>
              <i style="margin-left: 5px;" data-toggle="tooltip" data-placement="top" title="Radera denna tävling" @click="confirmDelete(competition)" class="fa fa-trash"></i>
            </td>
          </tr>
        </tbody>
      </table>

      <el-button v-if="!editing" @click="showNewCompetition = !showNewCompetition">
        <i class="fa fa-plus"></i>&nbsp;Ny tävling
      </el-button>

      <form style="margin-top: 20px;" v-show="showNewCompetition">
        <div class="form-group">
          <input v-model="competition.name" class="form-control" name="name" placeholder="Namn på tävling">
        </div>
        <div class="form-group">
          <div>Datum</div>
          <input v-model="competition.date" class="form-control" type="date" name="date">

          <div>till (lämna tom för endagstävling)</div>
          <input v-model="competition.end_date" class="form-control" type="date" name="date">
        </div>

        <div class="form-group">
          <div>Ev. sista anmälningsdag</div>
          <input v-model="competition.last_registration_at" class="form-control" type="date" name="last_registration_at">
        </div>

        <div style="display: flex; margin-bottom: 5px; align-items: center;">
          <el-toggle-button v-model="events.ksl" />
          <div style="margin-left: 10px;">KSL</div>
        </div>

        <div style="display: flex; margin-bottom: 5px; align-items: center;">
          <el-toggle-button v-model="events.kbp" />
          <div style="margin-left: 10px;">KBP</div>
        </div>

        <div style="display: flex; margin-bottom: 5px; align-items: center;">
          <el-toggle-button v-model="events.sl" />
          <div style="margin-left: 10px;">SL</div>
        </div>

        <div style="display: flex; margin-bottom: 15px; align-items: center;">
          <el-toggle-button v-model="events.bp" />
          <div style="margin-left: 10px;">BP</div>
        </div>

        <div class="form-group">
          <input v-model="competition.time" class="form-control" name="time" placeholder="Ungefärlig tid, ex. 8 – 15">
        </div>
        <div class="form-group">
          <input v-model="competition.location" class="form-control" name="location" placeholder="Plats, ex Friskis Majorna">
        </div>
        <div class="form-group">
          <textarea rows="6" v-model="competition.description" class="form-control" name="description" placeholder="Ev. ytterligare info"></textarea>
        </div>

        <div style="display: flex; margin-bottom: 5px; align-items: center;">
          <el-toggle-button v-model="competition.publish_count" />
          <div style="margin-left: 10px;">Visa antal anmälda för medlemmar</div>
        </div>

        <div style="display: flex; margin-bottom: 20px; align-items: center;">
          <el-toggle-button v-model="competition.publish_list" />
          <div style="margin-left: 10px;">Visa anmälningslista för medlemmar (namn, vikt, gren)</div>
        </div>

        <div style="display: flex; margin-bottom: 20px; align-items: center;">
          <div style="margin-right: 10px;">Visningsalternativ</div>
          <el-dropdown wide v-model="competition.show_status" :options="showStatusOptions" />
        </div>

        <div style="display: flex">
          <el-button v-if="!editing" @click="createCompetition" primary>Skapa tävling</el-button>
          <el-button style="margin-right: 10px;" secondary v-if="editing" @click="cancelUpdate">Ångra</el-button>
          <el-button primary v-if="editing" @click="updateCompetition">Uppdatera tävling</el-button>
        </div>

        <div v-if="newCompetitionError" style="color: red;">
          Kunde inte skapa tävling, kontrollera inmatning och anlutning.
        </div>
      </form>
    </div>

    <GkkLink to="/" text="Tillbaka till startsidan" />

    <modal name="delete-competition" :adaptive="true" height="auto">
      <div style="padding: 30px; margin-top: 20px;">
        <h3 style="text-align: center;">Är du säker på att du vill radera {{ selectedCompetition && selectedCompetition.name }}?</h3>
      </div>

      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px;">
        <el-button secondary @click="$modal.hide('delete-competition')">Nej</el-button>
        <el-button style="margin-left: 10px;" danger primary @click="deleteCompetition">Radera</el-button>
      </div>
    </modal>
  </div>
</template>

<script>
export default {
  props: ['competitions'],
  data () {
    return {
      editing: false,
      showNewCompetition: false,
      newCompetitionError: false,
      selectedCompetition: null,
      events: {
        ksl: true,
        kbp: true,
        sl: false,
        bp: false,
      },
      competition: {
        name: '',
        date: '',
        time: '',
        location: '',
        description: '',
        publish_count: false,
        publish_list: false,
        last_registration_at: null,
        show_status: 'default',
      },
      showStatusOptions: [
        { value: 'default', label: 'Default (visas tills datum passerat)' },
        { value: 'show', label: 'Visa' },
        { value: 'hide', label: 'Dölj' }
      ],
    }
  },
  filters: {
    dateString (competition) {
      if (!competition.end_date) {
        return competition.date
      }

      return `${competition.date} – ${competition.end_date}`
    }
  },
  mounted () {
    console.log(this.competitions)
    $(() => { $('[data-toggle="tooltip"]').tooltip() })
  },
  methods: {
    confirmDelete (competition) {
      this.selectedCompetition = competition
      this.$modal.show('delete-competition')
    },
    deleteCompetition () {
      window.axios.delete(`/admin/competitions/${this.selectedCompetition.id}`).then(this.reload)
    },
    countYes (competition) {
      return competition.registrations.filter(registration => registration.status == 1).length
    },
    edit (competition) {
      Object.assign(this.competition, competition)

      if (this.competition.events) {
        this.events = JSON.parse(this.competition.events)
      }

      this.showNewCompetition = true
      this.editing = true
    },
    createCompetition () {
      this.newCompetitionError = false

      window.axios({
        method: 'post',
        url: '/admin/competitions',
        data: {
          ...this.competition,
          events: JSON.stringify(this.events),
        }
      }).then(this.reload).catch(err => {
        this.newCompetitionError = true
      })
    },
    cancelUpdate () {
      this.reload()
    },
    updateCompetition () {
      this.newCompetitionError = false

      window.axios({
        method: 'patch',
        url: `/admin/competitions/${this.competition.id}`,
        data: {
          ...this.competition,
          events: JSON.stringify(this.events),
        }
      }).then(this.reload).catch(err => {
        this.newCompetitionError = true
      })
    }
  }
}
</script>

<style scoped lang="less">
  .type {
    margin-bottom: 40px;
  }

  .competition-row:hover {
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  }

  th, td, thead, table {
    border: none;
  }

  th.gkk {
    border-bottom: 1px solid #253969;
  }
</style>
