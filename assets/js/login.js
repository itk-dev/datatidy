require('../scss/login.scss')

// Set class depending on field content.
$('.login-form .form-control').on('blur input', function () {
  var $field = $(this).closest('.form-group')
  if (this.value) {
    $field.addClass('filled')
  } else {
    $field.removeClass('filled')
  }
})

// Apply the 'filled' class when input has focus.
$('.login-form .form-control').on('focus', function () {
  var $field = $(this).closest('.form-group')
  $field.addClass('filled')
})

$('.login-form .form-control').trigger('blur')
