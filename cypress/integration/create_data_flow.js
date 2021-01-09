describe('Create data flow', () => {
  beforeEach(() => {
    // reset and seed the database prior to every test
    // @todo How do we do this? DO we do this?
  })

  it('creates data flow', () => {
    cy.visit('/')

    cy.visit('/login')

    cy.get('[name=_username]').type('test@example.com')
    cy.get('[name=_password]').type('test-password')
    cy.get('[name=_submit]').click()

    cy.url().should('include', '/terms/show')

    cy.get('[name="form[accept]"]').check()
    cy.get('[name="form[submit]"]').click()

    cy.contains('Add data flow').click()

    cy.contains('Name').type('A test flow')
    cy.get('[name="data_flow_create[dataSource]"]').select('csv')

    cy.contains('Create data flow').click()
  })
})
