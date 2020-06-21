jQuery(document).ready(function ($) {
	const successMessage = jQuery('.et-customer-collector-frontend-form .notice-success');
	const infoMessage = jQuery('.et-customer-collector-frontend-form .notice-info');
	const errorMessage = jQuery('.et-customer-collector-frontend-form .notice-error');

	jQuery('.et-customer-frontend-form-submit').click(function (e) {
		let invalid = false;

		const fields = {
			'et-customer-name':    jQuery('[name="et-customer-name"]'),
			'et-customer-email':   jQuery('[name="et-customer-email"]'),
			'et-customer-budget':  jQuery('[name="et-customer-budget"]'),
			'et-customer-message': jQuery('[name="et-customer-message"]'),
			'et-customer-phone':   jQuery('[name="et-customer-phone"]'),
		};

		for (let field in fields) {
			if ((fields[field].is(':required') && fields[field].val() === '') || fields[field].is(':invalid')) {
				invalid = true;
			}
		}

		if (invalid) {
			return;
		}

		e.preventDefault();

		successMessage.hide();
		infoMessage.hide();
		errorMessage.hide();

		const data = {
			'action':              'et_save_customer',
			'et-customer-name':    fields['et-customer-name'].val(),
			'et-customer-email':   fields['et-customer-email'].val(),
			'et-customer-budget':  fields['et-customer-budget'].val(),
			'et-customer-message': fields['et-customer-message'].val(),
			'et-customer-phone':   fields['et-customer-phone'].val(),
			'nonce':               etCustomerFrontEndForm.nonce,
			'et-customer-date':    jQuery('.et-customer-collector-frontend-form .current-date').val(),
		};

		infoMessage.show();

		jQuery.post(etCustomerFrontEndForm.ajaxUrl, data, function (response) {
			infoMessage.hide();
			if (response.success) {
				successMessage.show();
				setTimeout(function () {
					successMessage.fadeOut('slow');
				}, 3000);
			} else {
				errorMessage.show();
			}
		});
	});
});