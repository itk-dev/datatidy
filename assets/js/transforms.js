require('../scss/transforms.scss')

const $ = require('jquery')
import 'select2'

// @see https://symfony.com/doc/current/form/dynamic_form_modification.html#dynamic-generation-for-submitted-forms
$(() => {
    const $preview = $('#data-flow-preview')
    $('[data-run-flow-to-step]').on('click', function() {
        const step = parseInt($(this).data('run-flow-to-step'))
        $preview.attr('src', $preview.data('src')+'?steps='+step)
    })

    $('#new-tranform-transformer').select2()

    const $transformer = $('#data_transform_transformer')
    $transformer.on('change', function() {
        const $form = $(this).closest('form')
        // Simulate form data, but only include the selected sport value.
        const data = {
            [$transformer.attr('name')]: $transformer.val(),
            ajax: true
        }

        $target = $('#data_transform_transformerOptions')
        $target.html('<span class="loader">Loading …</loader>')
        // Submit data via AJAX to the form's action path.
        $.ajax({
            url : $form.attr('action'),
            type: $form.attr('method'),
            data : data, //$form.serializeArray(),
            success: (html) => {
                // Replace current position field ...
                $target.replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html).find('#data_transform_transformerOptions')
                )
            },
            error: (error) => {
                $target.replaceWith($('<div/>').html('xxx'))
                console.log(arguments)
            }
        })
    })
})
