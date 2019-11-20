require('../scss/login.scss')


$("#login-form input").on("blur input focus", function() {
	var $field = $(this).closest(".form-group");
	if (this.value) {
		$field.addClass("filled");
	} else {
		$field.removeClass("filled");
	}
});

$("#login-form input").on("focus", function() {
	var $field = $(this).closest(".form-group");
	if (this) {
		$field.addClass("filled");
	} else {
		$field.removeClass("filled");
	}
});
