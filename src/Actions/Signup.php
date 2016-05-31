<?php namespace Lean\Gforms\Actions;

use Lean\Gforms\Utils;

/**
 * Class Signup.
 */
class Signup
{
	/**
	 * Init.
	 *
	 * @param int $form_id The form id.
	 */
	public static function init( $form_id ) {
		if ( Utils::is_active() && ! empty( $form_id ) ) {
			add_action( 'gform_validation_' . $form_id, [ __CLASS__, 'validation' ] );
			add_filter( 'gform_confirmation_' . $form_id, [ 'Lean\Gforms\Utils', 'confirmation' ] );
		}
	}

	/**
	 * Validate the data and insert the user.
	 *
	 * @param array $validation_result The results.
	 * @return mixed
	 * @throws \Exception
	 */
	public static function validation( $validation_result ) {
		$valid_args = [
			'user_login',
			'user_pass',
			'user_email',
			'display_name',
			'first_name',
			'last_name',
		];

		$form = $validation_result['form'];

		$args = [];

		$errors = [];

		foreach ( $valid_args as $arg ) {
			$value = Utils::get_field_value( $form, $arg );

			if ( false !== $value ) {
				$args[ $arg ] = $value;
			}
		}

		if ( ! ( isset( $args['user_email'] ) && isset( $args['user_pass'] ) ) ) {
			throw new \Exception( 'The signup form must have user_email and user_pass fields.' );
		}

		$errors['user_email'] = self::email_validation( $args['user_email'] );

		$errors['user_pass'] = self::password_validation( $args['user_pass'] );

		if ( array_filter( $errors ) ) {
			// There's an error in a specific field if we get here.
			foreach ( $form['fields'] as &$field ) {
				if ( isset( $errors[ $field->adminLabel ] ) && $errors[ $field->adminLabel ] ) {
					$field->validation_message = $errors[ $field->adminLabel ];
					$field->failed_validation = true;
				}
			}

			$validation_result['is_valid'] = false;

			return $validation_result;
		}

		$args['user_login'] = isset( $args['user_login'] ) && $args['user_login'] ?
			$args['user_login'] :
			self::generate_username( $args['user_email'] );

		$user_id = wp_insert_user( $args );

		if ( is_wp_error( $user_id ) ) {
			// There was an error when inserting the user if we get here.
			foreach ( $user_id->errors as $error ) {
				$validation_result['form']['fields'][0]->validation_message = '<p>' . $error[0] . '</p>';
			}

			$validation_result['form']['fields'][0]->failed_validation = true;

			$validation_result['is_valid'] = false;

			return $validation_result;
		}

		wp_new_user_notification( $user_id, null, 'both' );

		return $validation_result;
	}

	/**
	 * Validate the email address.
	 *
	 * @param string $email The email.
	 * @return string
	 */
	private static function email_validation( $email ) {
		if ( ! is_email( $email ) ) {
			return 'Invalid email address.';
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
	private static function password_validation( $password ) {
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

	/**
	 * Work out a username based on a string.
	 *
	 * @param string $string The string to base the username on.
	 * @return string The unique username.
	 */
	private static function generate_username( $string ) {
		$username = sanitize_title( $string );

		$index = 1;
		while ( username_exists( $username ) ) {
			if ( $index > 1 ) {
				$username = substr( $username, 0, -( strlen( $index ) + 1 ) );
			}
			$index ++;
			$username .= '-' . $index;
		}

		return $username;
	}
}
