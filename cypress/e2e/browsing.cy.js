describe('Can browse the website', () => {  
  it('Can render the public pages', () => {
    cy.visit('/')
    cy.contains('Göteborg Kraftsportklubb')

    cy.get('[data-cy="navbar"] > a[href="/styrkelyft"]').click()
    cy.contains('I styrkelyft tävlar man i grenarna knäböj, bänkpress och marklyft.')

    cy.get('[data-cy="navbar"] > a[href="/gkk"]').click()
    cy.contains('Göteborg Kraftsportklubb bildades 1933 och har idag omkring 100 medlemmar.')
    
    cy.get('[data-cy="navbar"] > a[href="/medlem"]').click()
    cy.contains('Vill du bli medlem?')

    cy.get('[data-cy="navbar"] > a[href="/dokument"]').click()
    cy.contains('Samarbete med SportRehab')

    cy.get('[data-cy="navbar"] > a[href="/klubbrekord"]').click()
    cy.contains('Kvinnor')

    cy.get('[data-cy="inside"] > a[href="/insidan"]').click()
    cy.contains('Skapa konto som medlem')
  })
})
