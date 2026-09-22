const CompetitionAdminTasks = {
  typeLabel(type) {
    return (
      {
        submit_registration: 'Skicka in anmälan',
        pay_registration_fee: 'Inbetalning av anmälningsavgift',
      }[type] || type
    )
  },

  statusLabel(status) {
    return (
      {
        pending: 'Väntande',
        done: 'Klar',
        not_applicable: 'Ej aktuellt',
      }[status] || status
    )
  },
}

export default CompetitionAdminTasks
