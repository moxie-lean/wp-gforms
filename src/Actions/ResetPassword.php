<?php namespace Lean\GformsLogin\Actions;

use Lean\GformsLogin\Utils;

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
		if ( Utils::is_active() && ! empty( $form_id ) ) {
			add_action( 'gform_validation_' . $form_id, [ __CLASS__, 'validation' ] );
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

		$username = Utils::get_field_value( $form, 'username' );

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
