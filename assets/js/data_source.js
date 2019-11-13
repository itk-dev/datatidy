const $ = require('jquery')

$(() => {
  const $dataSource = $('#data_source_dataSource')
  $dataSource.change(function () {
    // ... retrieve the corresponding form.
    const $form = $(this).closest('form')
    // Simulate form data, but only include the selected sport value.
    const data = {
      'data_source[name]': 'ajax',
      [$dataSource.attr('name')]: $dataSource.val()
    }

    const $target = $('#data_source_dataSourceOptions')
    $target.html('<span class="loader">Loading …</loader>')
    $.ajax({
      url: $form.attr('action'),
      type: $form.attr('method'),
      data: data,
      success: (html) => {
        // Replace current position field ...
        $target.replaceWith(
          // ... with the returned one from the AJAX response.
          $(html).find('#data_source_dataSourceOptions')
        )
      },
      error: (error) => {
        $target.replaceWith($('<div class="alert alert-danger"/>').html(error.responseText))
      }
    })
  })
})
