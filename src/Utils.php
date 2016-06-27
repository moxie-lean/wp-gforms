<?php namespace Lean\Gforms;
use GFAPI;

/**
 * Utils.
 */
class Utils {
	/**
	 * Is the Gravity Forms plugin active?
	 *
	 * @return bool
	 */
	public static function is_active() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 

		return is_plugin_active( 'gravityforms/gravityforms.php' );
	}

	/**
	 * Get the value from a form field given its label name.
	 *
	 * @param object $form 		  The gravityforms form object.
	 * @param string $admin_label The admin label of the input element.
	 * @return string The sanitized value.
	 */
	public static function get_field_value( $form, $admin_label ) {
		foreach ( $form['fields'] as $index => $field ) {

			if ( $admin_label === $field->adminLabel ) {

				$key = 'input_' . $form['fields'][ $index ]['id'];

				// @codingStandardsIgnoreStart - doesn't make sense to use a nonce here, as we might be working with a detached frontend.
				if ( isset( $_POST[ $key ] ) ) {
					return sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
				}
				// @codingStandardsIgnoreEnd.
			}
		}

		return false;
	}

	/**
	 * Get cookies from current request
	 *
	 * @return array
	 */
	public static function get_cookies() {
		$headers = headers_list();
		$cookies = [];

		foreach ( $headers as $header ) {
			if ( stripos( $header, 'Set-Cookie' ) === 0 ) {
				$cookies[] = $header;
			}
		}

		return $cookies;
	}

	/**
	 * Add cookies to the confirmation message as a commented html.
	 *
	 * @param string $confirmation The current confirmation message
	 * @return string $confirmation The new confirmation message
	 */
	public static function confirmation( $confirmation ) {
		$confirmation .= '<!--cookies:' . wp_json_encode( self::get_cookies() ) . '-->';

		return $confirmation;
	}

	/**
	 * Delete the form entry after the form is submitted.
	 * Usefull for Login/Signup forms, because we don't want
	 * to store plain text passwords in the database as an entry.
	 *
	 * @param object $entry Gravity Forms entry.
	 */
	public static function delete_entries( $entry ) {
		GFAPI::delete_entry( $entry['id'] );
	}
}
