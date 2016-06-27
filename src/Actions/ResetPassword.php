<?php namespace Lean\Gforms\Actions;

use Lean\Gforms\Utils;

/**
 * Class ResetPassword.
 */
class ResetPassword
{
	/**
	 * Init.
	 *
	 * @param int $form_id The form id.
	 */
	public static function init( $form_id ) {
		if ( ! empty( $form_id ) ) {
			add_action( 'gform_validation_' . $form_id, [ __CLASS__, 'validation' ] );
			add_filter( 'gform_confirmation_' . $form_id, [ 'Lean\Gforms\Utils', 'confirmation' ] );
		}
	}

	/**
	 * Send reset password email.
	 *
	 * @param array $validation_result The validation result.
	 * @return mixed
	 */
	public static function validation( $validation_result ) {
		$form = $validation_result['form'];

		$username = Utils::get_field_value( $form, 'user_login' );

		if ( ! empty( $username ) ) {
			if ( ! function_exists( 'retrieve_password' ) ) {
				ob_start();
				require_once( ABSPATH . '/wp-login.php' );
				ob_end_clean();
			}

			$_POST['user_login'] = $username;

			retrieve_password();
		}

		return $validation_result;
	}
}
