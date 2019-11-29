const $ = require('jquery')

$(() => {
  const $dataSource = $('#data_source_dataSource')
  $dataSource.change(function () {
    // ... retrieve the corresponding form.
    const $form = $(this).closest('form')
    // Simulate form data, but only include the selected data source value.
    const data = {
      'data_source[name]': 'ajax',
      'data_source[ttl]': '0',
      [$dataSource.attr('name')]: $dataSource.val()
    }

    const $target = $('#data_source_dataSourceOptions')
    $target.html('<div class="text-center"><i class="fas fa-spinner fa-pulse fa-3x mr-3"></i><span class="sr-only">loading ...</span></div>')
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
