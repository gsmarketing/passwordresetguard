/**
 * Password Reset Guard - CAPTCHA Validation
 */

(function( document ) {
	'use strict';

	/**
	 * Validate captcha on form submission
	 */
	function validateCaptcha( e ) {
		const answerField = document.getElementById( 'prg_captcha' );
		const num1Field = document.querySelector( 'input[name="prg_captcha_num1"]' );
		const num2Field = document.querySelector( 'input[name="prg_captcha_num2"]' );
		const operationField = document.querySelector( 'input[name="prg_captcha_operation"]' );

		if ( ! answerField || ! num1Field || ! num2Field || ! operationField ) {
			return true;
		}

		const answer = parseInt( answerField.value, 10 );
		const num1 = parseInt( num1Field.value, 10 );
		const num2 = parseInt( num2Field.value, 10 );
		const operation = operationField.value;

		if ( isNaN( answer ) ) {
			e.preventDefault();
			answerField.focus();
			answerField.classList.add( 'prg-error' );
			return false;
		}

		answerField.classList.remove( 'prg-error' );
		return true;
	}

	/**
	 * Initialize captcha validation on document ready
	 */
	document.addEventListener( 'DOMContentLoaded', function() {
		const loginForm = document.querySelector( 'form[name="lostpasswordform"]' ) ||
		                  document.querySelector( '.login form' );

		if ( loginForm ) {
			loginForm.addEventListener( 'submit', validateCaptcha );
		}

		// Add visual feedback
		const answerField = document.getElementById( 'prg_captcha' );
		if ( answerField ) {
			answerField.addEventListener( 'input', function() {
				this.classList.remove( 'prg-error' );
			} );
		}
	} );

})( document );
