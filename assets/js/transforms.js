require('../scss/transforms.scss')

const $ = require('jquery')
import 'select2'

// @see https://symfony.com/doc/current/form/dynamic_form_modification.html#dynamic-generation-for-submitted-forms
$(() => {
    const $preview = $('#data-flow-preview')
    $('[data-run-flow-transform-id]').on('click', function() {
        const id = parseInt($(this).data('run-flow-transform-id'))
        $preview.attr('src', $preview.data('src').replace('__id__', id))
    })

    $('#new-tranform-transformer').select2()

    const buildCollectionTypes = () => {
        // Collection types
        // @see https://symfony.com/doc/current/reference/forms/types/collection.html#adding-and-removing-items
        $('[data-collection-add-new-widget-selector]').on('click', function() {
            const $container = $($(this).data('collection-add-new-widget-selector'))
            let counter = $container.data('widget-counter') || $container.children().length
            let template = $container.attr('data-prototype').replace(/__name__/g, counter)
            counter++
            $container.data('widget-counter', counter)
            $container.append($(template))
        })
    }

    const $transformer = $('#data_transform_transformer')
    $transformer.on('change', function() {
        const $form = $(this).closest('form')
        // Simulate form data, but only include the selected sport value.
        const data = {
            [$transformer.attr('name')]: $transformer.val(),
            ajax: true
        }

        const $target = $('#data_transform_transformerOptions')
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
                buildCollectionTypes()
            },
            error: (error) => {
                $target.replaceWith($('<div/>').html('xxx'))
                console.log(arguments)
            }
        })
    })

    buildCollectionTypes()
})
