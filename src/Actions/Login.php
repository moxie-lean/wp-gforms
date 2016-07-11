<?php namespace Lean\Gforms\Actions;

use Lean\Gforms\Utils;

/**
 * Class Login.
 */
class Login
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
			add_filter( 'gform_after_submission_' . $form_id, [ 'Lean\Gforms\Utils', 'delete_entries' ] );
		}
	}

	/**
	 * Log them in.
	 *
	 * @param array $validation_result The validation result.
	 * @return mixed
	 */
	public static function validation( $validation_result ) {
		$form = $validation_result['form'];

		$username = Utils::get_field_value( $form, 'user_login' );

		if ( strpos( $username, '@' ) ) {
			$user_info = get_user_by( 'email', $username );
			$username = $user_info ? $user_info->user_login : false;
		}

		$password = Utils::get_field_value( $form, 'user_pass' );

		$user = wp_signon( [
			'user_login' => $username,
			'user_password' => $password,
			'remember' => true,
		] );

		if ( is_wp_error( $user ) ) {
			$validation_result['is_valid'] = false;
			$validation_result['form']['fields'][0]->validation_message = 'Invalid username or password.';
			$validation_result['form']['fields'][0]->failed_validation = true;
		}

		wp_set_current_user( $user->ID );

		return $validation_result;
	}
}
