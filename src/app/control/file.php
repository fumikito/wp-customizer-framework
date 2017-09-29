<?php
/**
 * @package inc2734/wp-customizer-framework
 * @author inc2734
 * @license GPL-2.0+
 */

/**
 * Wrapper class for WP_Customizer_Framework_Control_File
 */
class Inc2734_WP_Customizer_Framework_Control_File extends Inc2734_WP_Customizer_Framework_Abstract_Control {

	/**
	 * Add control
	 *
	 * @param WP_Customize_Manager $wp_customize
	 * @see https://developer.wordpress.org/reference/classes/wp_customize_manager/add_control/
	 * @see https://developer.wordpress.org/reference/classes/wp_customize_manager/add_setting/
	 */
	public function register_control( WP_Customize_Manager $wp_customize ) {
		$this->args['type'] = 'upload';

		$wp_customize->add_control(
			new WP_Customize_Upload_Control(
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
		return 'esc_url_raw';
	}
}
