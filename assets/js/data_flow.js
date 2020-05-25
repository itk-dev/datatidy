import 'select2'
import 'jquery.json-viewer/json-viewer/jquery.json-viewer'

require('../scss/data-flow.scss')

const $ = require('jquery')

// @see https://symfony.com/doc/current/form/dynamic_form_modification.html#dynamic-generation-for-submitted-forms
$(() => {
  const $preview = $('#data-flow-preview')
  $('[data-run-flow-transform-id]').on('click', function () {
    const id = $(this).data('run-flow-transform-id')
    $preview.attr('src', $preview.data('src').replace('__id__', id))
  })

  $('#new-tranform-transformer').select2({
    placeholder: 'Select transform',
    allowClear: true
  })

  const buildCollectionTypes = (context) => {
    // Collection types
    // @see https://symfony.com/doc/current/reference/forms/types/collection.html#adding-and-removing-items
    $('[data-collection-add-new-widget-selector]', context).on('click', function () {
      const $container = $($(this).data('collection-add-new-widget-selector'))
      let counter = $container.data('widget-counter') || $container.children().length
      const template = $container.attr('data-prototype').replace(/__name__/g, counter)
      counter++
      $container.data('widget-counter', counter)
      const item = $(template)
      $container.append(item)
      buildCollectionTypes(item)
      buildOptionsForms(item)
    })

    $('[data-collection-remove-widget-selector]', context).on('click', function () {
      const $container = $($(this).data('collection-remove-widget-selector'))
      $container.remove()
    })
  }

  const buildOptionsForms = (context) => {
    $('select[data-options-form]', context).on('change', function () {
      const $form = $(this).closest('form')
      const dataTargetNamePrefix = $(this).attr('name').replace(/\[[^\]]+\]$/, '')
      const data = {
        'data_flow[settings][name]': 'ajax',
        'data_flow[data_source][ttl]': 'ajax',
        'data_flow[data_source][dataSource]': 'ajax',
        [dataTargetNamePrefix + '[description]']: 'ajax',
        [$(this).attr('name')]: $(this).val()
      }
      const $target = $('#' + $(this).attr('id').replace(/_[^_]+$/, '_') + $(this).data('options-form'))
      $target.html('<div class="text-center"><i class="fas fa-spinner fa-pulse fa-3x mr-3"></i><span class="sr-only">loading ...</span></div>')
      $.ajax({
        url: $form.attr('action'),
        type: $form.attr('method'),
        data: data,
        success: (html) => {
          $target.replaceWith(
            $(html).find('#' + $target.attr('id'))
          )
        },
        error: (error) => {
          $target.replaceWith($('<div class="alert alert-danger"/>').html(error))
        }
      })
    })
  }

  const $transformer = $('#data_transform_transformer')
  $transformer.on('change', function () {
    const $form = $(this).closest('form')
    // Simulate form data, but only include the selected value.
    const data = {
      [$transformer.attr('name')]: $transformer.val(),
      'data_transform[name]': 'ajax',
      ajax: true
    }

    const $target = $('#data_transform_transformerOptions')
    $target.html('<div class="text-center"><i class="fas fa-spinner fa-pulse fa-3x mr-3"></i><span class="sr-only">loading ...</span></div>')
    // Submit data via AJAX to the form's action path.
    $.ajax({
      url: $form.attr('action'),
      type: $form.attr('method'),
      data: data,
      success: (html) => {
        $target.replaceWith(
          $(html).find('#data_transform_transformerOptions')
        )
        buildCollectionTypes()
      },
      error: (error) => {
        $target.replaceWith($('<div class="alert alert-danger"/>').html(error))
      }
    })
  })

  buildCollectionTypes()
  buildOptionsForms()

  // Show alert when user is leaving a dirty form unsubmitted
  let isSubmitting = false
  const form = $('form[name="data_flow"]')

  form.submit(function () {
    isSubmitting = true
  })

  form.data('initial-state', form.serialize())

  $(window).on('beforeunload', function () {
    if (!isSubmitting && form.serialize() !== form.data('initial-state')) {
      return 'You have unsaved changes which will not be saved.' // This will not be shown, but Chrome requires a return value.
    }
  })
})

$(function () {
  $('[data-toggle="popover"]').popover()
  $('.json-data-view').each((index, el) => $(el).jsonViewer(JSON.parse($(el).html()), {
    collapsed: true
  }))
})
