(function( $ ) {
	'use strict';

	/**
	 * Notice
	 *
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 */

	/**
	 * AJAX
	 *
	 * Calls an AJAX callback for the site front-end.
	 * Add a button with class ".ajax" to test.
	 */
	function handleSubmit($form){
		var formData = new FormData($form[0]);
		formData.append('action','process_forms');
		formData.append('nonce',proviso.nonce);
		formData.append('callback', $form.data('form'));
		$.ajax( {
			url    : proviso.ajaxUrl,
			type   : 'POST',
			data   : formData,
			dataType: 'json',
			processData: false,
			contentType: false,
			error : function( XMLHttpRequest, textStatus, errorThrown ) {
				$form.find('[data-error]').text('There has been an error').show();
			},
			success : function( response, textStatus, XMLHttpRequest ) {
				if(response.content.state === 'error'){
					$form.find('[data-error]').text(response.content.message).show();
				} else if(response.content.state === 'account_linked') {
					$form.find('[data-feedback]').text(response.content).show();
				} else {
					$form.find('[data-feedback]').text(response.content).show();
				}
			},
			complete : function( reply, textStatus ) {
				console.log( textStatus );
			}
		});
	}
	function clearFeedback($form){
		$form.find('[data-feedback], [data-error]').hide().text('');
	}

	$(function() {
		$('[data-form="validate-to-link"], [data-form="lms-signup"]').on( 'submit', function(event) {
			event.preventDefault();
			clearFeedback($(this));
			handleSubmit($(this));
		});
	});

})( jQuery );