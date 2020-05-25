$(() => {
  const filterForm = $('form[name="data_flow_filter"]')
  filterForm.change(function () {
    filterForm.submit()
  })
})
