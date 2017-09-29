<?php
/**
 * @package inc2734/wp-customizer-framework
 * @author inc2734
 * @license GPL-2.0+
 */

/**
 * Wrapper class for Inc2734_WP_Customizer_Framework_WP_Customize_Multiple_Checkbox_Control
 */
class Inc2734_WP_Customizer_Framework_Control_Multiple_Checkbox extends Inc2734_WP_Customizer_Framework_Abstract_Control {

	/**
	 * Add control
	 *
	 * @param WP_Customize_Manager $wp_customize
	 * @see https://developer.wordpress.org/reference/classes/wp_customize_manager/add_control/
	 * @see https://developer.wordpress.org/reference/classes/wp_customize_manager/add_setting/
	 */
	public function register_control( WP_Customize_Manager $wp_customize ) {
		$this->args['type'] = 'multiple-checkbox';

		$wp_customize->add_control(
			new Inc2734_WP_Customizer_Framework_WP_Customize_Multiple_Checkbox_Control(
				$wp_customize,
				$this->get_id(),
				$this->_generate_register_control_args()
			)
		);
	}

	/**
	 * Sanitize callback function
	 *
	 * @return string|function Function name or function for sanitize
	 */
	public function sanitize_callback() {
		return function( $value ) {
			if ( ! is_array( $value ) ) {
				$value = explode( ',', $value );
			}

			$sanitized_values = [];

			foreach ( $value as $v ) {
				if ( ! array_key_exists( $v, $this->args['choices'] ) ) {
					continue;
				}
				$sanitized_values[] = $v;
			}

			$sanitized_values = array_map( 'sanitize_text_field', $sanitized_values );
			return implode( ',', $sanitized_values );
		};
	}
}
