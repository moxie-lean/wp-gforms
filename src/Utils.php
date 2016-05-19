<?php namespace Lean\GformsLogin;

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
		return class_exists( '\GFFormsModel' );
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

			// @codingStandardsIgnoreStart - $field-adminLabel is defined in gforms, we can't use snake_case.
			if ( $admin_label === $field->adminLabel ) {
				// @codingStandardsIgnoreEnd.

				$key = 'input_' . $form['fields'][ $index ]['id'];

				if ( isset( $_POST[ $key ] ) ) {
					return sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
				}
			}
		}

		return false;
	}
}
