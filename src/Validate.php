<?php namespace Lean\Gforms;

/**
 * Validation functions.
 */
class Validate {
	/**
	 * Validate the email address.
	 *
	 * @param string $email The email.
	 * @return string
	 */
	public static function email( $email ) {
		if ( ! is_email( $email ) ) {
			return 'Invalid email address.';
		}

		if ( is_user_logged_in() ) {
			return '';
		}

		if ( email_exists( $email ) ) {
			return 'This email address already exists.';
		}

		return '';
	}

	/**
	 * Validate the password.
	 *
	 * @param string $password The password.
	 * @return string
	 */
	public static function password( $password ) {
		if ( strlen( $password ) < 8 ) {
			return 'Your password too short. It must be at least 8 characters.';
		}

		if ( ! preg_match( '#[0-9]+#', $password ) ) {
			return 'Your password must include at least one number.';
		}

		if ( ! preg_match( '#[a-zA-Z]+#', $password ) ) {
			return 'Your password must include at least one letter.';
		}

		return '';
	}
}
