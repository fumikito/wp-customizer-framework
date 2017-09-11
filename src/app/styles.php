<?php
/**
 * @package inc2734/wp-customizer-framework
 * @author inc2734
 * @license GPL-2.0+
 */

class Inc2734_WP_Customizer_Framework_Styles {

	/**
	 * Style settings
	 * @var array
	 *      @var array selectors
	 *      @var array properties
	 *      @var string media_query
	 */
	protected $styles = [];

	public function __construct() {
		add_filter( 'tiny_mce_before_init', function( $mceInit ) {
			if ( ! isset( $mceInit['content_style'] ) ) {
				$mceInit['content_style'] = '';
			}
			return $mceInit;
		}, 9 );

		add_action( 'wp_print_styles', [ $this, '_wp_print_styles' ] );
		add_filter( 'tiny_mce_before_init', [ $this, '_tiny_mce_before_init' ] );
	}

	/**
	 * Styles for front-end
	 *
	 * @return void
	 */
	public function _wp_print_styles() {
		echo '<style>';
		foreach ( $this->styles as $style ) {
			foreach ( $style['selectors'] as $i => $selector ) {
				$style['selectors'][ $i ] = 'body ' . $selector;
			}

			$selectors  = implode( ',', $style['selectors'] );
			$properties = implode( ';', $style['properties'] );

			if ( ! $style['media_query'] ) {
				printf(
					'%1$s { %2$s }',
					$selectors,
					$properties
				);
			} else {
				printf(
					'%1$s { %2$s { %3$s } }',
					$style['media_query'],
					$selectors,
					$properties
				);
			}
		}
		echo '</style>';
	}

	/**
	 * Styles for TinyMCE
	 *
	 * @param array $mceInit
	 * @return array
	 */
	public function _tiny_mce_before_init( $mceInit ) {
		foreach ( $this->styles as $style ) {
			foreach ( $style['selectors'] as $i => $selector ) {
				$style['selectors'][ $i ] = '.mce-content-body.wp-editor ' . $selector;
			}

			$selectors  = addslashes( implode( ',', $style['selectors'] ) );
			$properties = addslashes( implode( ';', $style['properties'] ) );

			if ( ! $style['media_query'] ) {
				$mceInit['content_style'] .= "{$selectors} { {$properties} }";
			} else {
				$mceInit['content_style'] .= "{$style['media_query']} { {$selectors} { {$properties} } }";
			}
		}
		return $mceInit;
	}

	/**
	 * Registers style setting
	 *
	 * @param string|array $selectors
	 * @param string|array $properties
	 * @param string $media_query
	 * @return void
	 */
	public function register( $selectors, $properties, $media_query = null ) {
		if ( ! is_array( $selectors ) ) {
			$selectors = explode( ',', $selectors );
		}

		if ( ! is_array( $properties ) ) {
			$properties = explode( ';', $properties );
		}

		$this->styles[] = [
			'selectors'   => $selectors,
			'properties'  => $properties,
			'media_query' => $media_query,
		];
	}

	/**
	 * A little bit brighter
	 *
	 * @param hex $hex
	 * @return hex
	 */
	public function light( $hex ) {
		return $this->_color_luminance( $hex, 0.2 );
	}

	/**
	 * A little brighter
	 *
	 * @param hex $hex
	 * @return hex
	 */
	public function lighter( $hex ) {
		return $this->_color_luminance( $hex, 0.335 );
	}

	/**
	 * A brighter
	 *
	 * @param hex $hex
	 * @return hex
	 */
	public function lightest( $hex ) {
		return $this->_color_luminance( $hex, 0.37 );
	}

	/**
	 * A little bit dark
	 *
	 * @param hex $hex
	 * @return hex
	 */
	public function dark( $hex ) {
		return $this->_color_luminance( $hex, -0.2 );
	}

	/**
	 * A little dark
	 *
	 * @param hex $hex
	 * @return hex
	 */
	public function darker( $hex ) {
		return $this->_color_luminance( $hex, -0.335 );
	}

	/**
	 * A dark
	 *
	 * @param hex $hex
	 * @return hex
	 */
	public function darkest( $hex ) {
		return $this->_color_luminance( $hex, -0.37 );
	}

	/**
	 * To brighten up
	 *
	 * @param hex $hex
	 * @param int $percent
	 * @return hex
	 */
	public function lighten( $hex, $percent ) {
		return $this->_color_luminance( $hex, $percent );
	}

	/**
	 * To make it dark
	 *
	 * @param hex $hex
	 * @param int $percent
	 * @return hex
	 */
	public function darken( $hex, $percent ) {
		return $this->_color_luminance( $hex, $percent * -1 );
	}

	/**
	 * Change brightness
	 *
	 * @param hex $hex
	 * @param int $percent
	 * @return hex
	 */
	protected function _color_luminance( $hex, $percent ) {
		$hex = $this->_hex_normalization( $hex );
		$new_hex = '#';

		for ($i = 0; $i < 3; $i++) {
			$dec = hexdec( substr( $hex, $i * 2, 2 ) );
			$dec = min( max( 0, $dec + $dec * $percent ), 255 );
			$new_hex .= str_pad( dechex( $dec ) , 2, 0, STR_PAD_LEFT );
		}

		return $new_hex;
	}

	/**
	 * hex to rgba
	 *
	 * @param hex $hex
	 * @param int $percent
	 * @return rgba
	 */
	public function rgba( $hex, $percent ) {
		$hex = $this->_hex_normalization( $hex );
		$rgba = [];

		for ($i = 0; $i < 3; $i++) {
			$dec = hexdec( substr( $hex, $i * 2, 2 ) );
			$rgba[] = $dec;
		}

		$rgba = implode( ',', $rgba );
		$rgba = "rgba({$rgba}, $percent)";

		return $rgba;
	}

	/**
	 * Normalize hex
	 * .e.g  #000000 -> 000000
	 * .e.g  #000 -> 000000
	 *
	 * @param hex $hex
	 * @return hex
	 */
	protected function _hex_normalization( $hex ) {
		$hex = preg_replace( '/[^0-9a-f]/i', '', ltrim( $hex, '#' ) );

		if ( strlen( $hex ) < 6 ) {
			$hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
		}

		return $hex;
	}
}
