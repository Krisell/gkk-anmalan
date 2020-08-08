<template>
  <div class="container mx-auto">
    <h1 class="text-center text-3xl font-hairline mb-6">Admin</h1>
    <h2 class="text-center text-xl font-thin mb-6">Tävlingsanmälan</h2>
    <div class="flex flex-col">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Tävling
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Anmälan senast
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Tid
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Plats
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Antal anmälda
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Åtgärder
                </th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="competition in competitions" :key="competition.id" @click="location(`/admin/competitions/${competition.id}`)" style="cursor: pointer;">
                <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm leading-5 font-medium text-gray-900">{{ competition.name }}</div>
                      <div class="text-sm leading-5 text-gray-500">{{ competition | dateString }}</div>
                    </div>
                  </div>
                </td>
                <!-- <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ competition.name }}</div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ (competition.last_registration_at || '').slice(0, 10) }}</div>
                </td> -->
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ (competition.last_registration_at || '').slice(0, 10) }}</div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ competition.time }}</div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ competition.location }}</div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ countYes(competition) }} (av {{ competition.registrations.length }})</div>
                </td>
                <td @click="e => e.stopPropagation()" class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center justify-center">
                    <svg v-tooltip="'Redigera tävling'" class="w-6 text-gkk-light hover:text-gkk" @click="edit(competition)" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <svg v-tooltip="'Radera tävling'" class="w-6 ml-2 text-gkk-light hover:text-gkk" @click="confirmDelete(competition)" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>


      <!-- <table class="table">
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
          <tr class="competition-row" v-for="competition in competitions" :key="competition.id" @click="location(`/admin/competitions/${competition.id}`)">
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
      </table> -->

      <ui-button class="mt-2" v-if="!editing" @click="showNewCompetition = !showNewCompetition">
        <div class="flex items-center justify-center">
          <svg class="w-6 -ml-2 mr-2" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          <div>Ny tävling</div>
        </div>
      </ui-button>


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
        return competition.date || '&nbsp;'
      }

      return `${competition.date} – ${competition.end_date}`
    }
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

  // .competition-row:hover {
  //   box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  // }

  // th, td, thead, table {
  //   border: none;
  // }

  // th.gkk {
  //   border-bottom: 1px solid #253969;
  // }
</style>
