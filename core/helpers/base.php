<?php
/**
 *
 * Initial version created 05-05-2018 / 04:37 PM
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @version 1.0
 * @since 1.0
 * @link
 * @copyright 2018 Varun Sridharan
 * @license GPLV3 Or Greater (https://www.gnu.org/licenses/gpl-3.0.txt)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! function_exists( 'wponion_is_ajax' ) ) {
	/**
	 * Checks if current request is ajax.
	 *
	 * @return bool
	 */
	function wponion_is_ajax() {
		return ( isset( $_POST ) && isset( $_POST['action'] ) && 'heartbeat' === $_POST['action'] ) ? true : false;
	}
}

if ( ! function_exists( 'wponion_get_template' ) ) {
	/**
	 * Get other templates (e.g. product attributes) passing attributes and including the file.
	 *
	 * @access public
	 *
	 * @param string $template_name Template name.
	 * @param array  $args Arguments. (default: array).
	 * @param string $template_path Template path. (default: '').
	 */
	function wponion_get_template( $template_name, $args = array(), $template_path = '' ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args ); // @codingStandardsIgnoreLine
		}

		$located = wponion_locate_template( $template_name, $template_path );

		if ( ! file_exists( $located ) ) {
			return;
		}

		// Allow 3rd party plugin filter template file from their plugin.
		$located = apply_filters( 'wponion_template', $located, $template_name, $args, $template_path );
		do_action( 'wponion_before_template_part', $template_name, $template_path, $located, $args );
		include $located; // @codingStandardsIgnoreLine
		do_action( 'wponion_after_template_part', $template_name, $template_path, $located, $args );
	}
}

if ( ! function_exists( 'wponion_locate_template' ) ) {
	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 * yourtheme/$template_path/$template_name
	 * yourtheme/$template_name
	 * $default_path/$template_name
	 *
	 * @access public
	 *
	 * @param string $template_name Template name.
	 * @param string $template_path Template path. (default: '').
	 *
	 * @return string
	 */
	function wponion_locate_template( $template_name, $template_path = '' ) {
		$template_path = ( ! $template_path ) ? $template_path = 'wponion/' : $template_path;
		$default_path  = WPONION_PATH . 'templates/';
		$template      = locate_template( array( trailingslashit( $template_path ) . $template_name, $template_name ) );

		if ( ! $template ) {
			if ( file_exists( trailingslashit( $template_path ) . $template_name ) ) {
				$template = trailingslashit( $template_path ) . $template_name;
			}
		}

		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		return apply_filters( 'wponion_locate_template', $template, $template_name, $template_path );
	}
}

if ( ! function_exists( 'wponion_get_template_html' ) ) {
	/**
	 * Like wc_get_template, but returns the HTML instead of outputting.
	 *
	 * @see wponion_get_template
	 * @since 2.5.0
	 *
	 * @param string $template_name Template name.
	 * @param array  $args Arguments. (default: array).
	 * @param string $template_path Template path. (default: '').
	 *
	 * @return string
	 */
	function wponion_get_template_html( $template_name, $args = array(), $template_path = '' ) {
		ob_start();
		wponion_get_template( $template_name, $args, $template_path );
		return ob_get_clean();
	}
}

if ( ! function_exists( 'wponion_get_var' ) ) {
	/**
	 * Getting POST Var
	 *
	 * @param        $var
	 * @param string $default
	 *
	 * @return string
	 */
	function wponion_get_var( $var, $default = '' ) {
		if ( isset( $_POST[ $var ] ) ) {
			if ( is_array( $_POST[ $var ] ) ) {
				return $_POST[ $var ];
			} else {
				return sanitize_text_field( $_POST[ $var ] );
			}
		}
		if ( isset( $_GET[ $var ] ) ) {
			if ( is_array( $_GET[ $var ] ) ) {
				return $_GET[ $var ];
			} else {
				return sanitize_text_field( $_GET[ $var ] );
			}
		}
		return $default;
	}
}

if ( ! function_exists( 'wponion_validate_parent_section_ids' ) ) {
	/**
	 * Checks if given section and parent id are valid and none of them has empty values.
	 *
	 * @param array $ids
	 *
	 * @return array|bool
	 */
	function wponion_validate_parent_section_ids( $ids = array() ) {
		if ( empty( array_filter( $ids ) ) ) {
			return false;
		} elseif ( empty( $ids['section_id'] ) && ! empty( $ids['parent_id'] ) ) {
			return array(
				'section_id' => false,
				'parent_id'  => $ids['parent_id'],
			);
		} elseif ( ! empty( $ids['section_id'] ) && empty( $ids['parent_id'] ) ) {
			return array(
				'section_id' => false,
				'parent_id'  => $ids['section_id'],
			);
		} else {
			return array(
				'section_id' => $ids['section_id'],
				'parent_id'  => $ids['parent_id'],
			);
		}
	}
}

if ( ! function_exists( 'wponion_debug_assets' ) ) {
	/**
	 * Checks if assets needs to be loaded a unminifed version.
	 *
	 * @param string $file_name
	 * @param string $ext
	 *
	 * @return string
	 */
	function wponion_debug_assets( $file_name = '', $ext = 'css' ) {
		return ( ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) || defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? $file_name . '.' . $ext : $file_name . '.min.' . $ext;
	}
}

if ( ! function_exists( 'wponion_plugin_localize' ) ) {
	/**
	 * wponion localize_script plugin.js
	 *
	 * @param string $object
	 * @param array  $data
	 *
	 * @return bool
	 */
	function wponion_plugin_localize( $object = '', $data = array() ) {
		$add = wp_localize_script( 'wponion-plugins', $object, $data );
		return ( false === $add ) ? wp_localize_script( 'wponion-core', $object, $data ) : wponion_js_vars( $object, $data, true );
	}
}

if ( ! function_exists( 'wponion_localize_object_name' ) ) {
	/**
	 * Returns a quniue js key.
	 *
	 * @param string $prefix
	 * @param string $surfix
	 * @param string $inner_content
	 *
	 * @return string
	 */
	function wponion_localize_object_name( $prefix = '', $surfix = '', $inner_content = '' ) {
		return $prefix . wponion_hash_string( $inner_content ) . $surfix;

	}
}

if ( ! function_exists( 'wponion_js_vars' ) ) {
	/**
	 * Converts PHP Array into JS JSON String with script tag and returns it.
	 *
	 * @param      $object_name
	 * @param      $l10n
	 * @param bool $with_script_tag
	 *
	 * @return string
	 */
	function wponion_js_vars( $object_name, $l10n, $with_script_tag = true ) {
		foreach ( (array) $l10n as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				continue;
			}
			$l10n[ $key ] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );
		}
		$script = null;
		if ( ! empty( $object_name ) ) {
			$script = "var $object_name = " . wp_json_encode( $l10n ) . ';';
		} else {
			$script = wp_json_encode( $l10n );
		}
		if ( ! empty( $after ) ) {
			$script .= "\n$after;";
		}
		if ( $with_script_tag ) {
			$h = "<script type='text/javascript'>\n /* <![CDATA[ */\n";
			$h = $h . " $script\n";
			$h = $h . " /* ]]> */\n </script>\n";
			return $h;
		}
		return $script;
	}
}

if ( ! function_exists( 'wponion_array_to_html_attributes' ) ) {
	/**
	 * Converts PHP Array To HTML Attributes.
	 *
	 * @param $attributes
	 *
	 * @return string
	 */
	function wponion_array_to_html_attributes( $attributes ) {
		$atts = '';
		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $key => $value ) {
				$value = ( is_array( $value ) ) ? wp_json_encode( $value ) : $value;
				if ( 'only-key' === $value ) {
					$atts .= ' ' . esc_attr( $key );
				} else {
					$atts .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
				}
			}
		}
		return $atts;
	}
}

if ( ! function_exists( 'wponion_html_class' ) ) {
	/**
	 * Handles HTML Class and returns only unique and usable html clss.
	 *
	 * @param array|string $user_class
	 * @param array|string $default_class
	 * @param bool         $return_string
	 *
	 * @return string|array
	 */
	function wponion_html_class( $user_class = array(), $default_class = array(), $return_string = true ) {
		if ( ! is_array( $user_class ) ) {
			$user_class = explode( ' ', $user_class );
		}

		if ( ! is_array( $default_class ) ) {
			$default_class = explode( ' ', $default_class );
		}

		$user_class = array_merge( $default_class, $user_class );
		$user_class = array_filter( array_unique( $user_class ) );
		if ( true === $return_string ) {
			return implode( ' ', $user_class );
		}
		return $user_class;

	}
}

if ( ! function_exists( 'wponion_hash_string' ) ) {
	/**
	 * Returns A MD5 Hash.
	 *
	 * @param $string
	 *
	 * @return string
	 */
	function wponion_hash_string( $string = '' ) {
		return md5( $string );
	}
}

if ( ! function_exists( 'wponion_hash_array' ) ) {
	/**
	 * Returns A MD Encoded Value of a array.
	 *
	 * @param $array
	 *
	 * @return string
	 */
	function wponion_hash_array( $array ) {
		return wponion_hash_string( wp_json_encode( $array ) );
	}
}

if ( ! function_exists( 'wponion_is_debug' ) ) {
	/**
	 * Checks if WP_Debug Is enabled.
	 * WP_DEBUG
	 * WPONION_DEV_MODE
	 *
	 * @return bool
	 */
	function wponion_is_debug() {
		if ( defined( 'WPONION_DEV_MODE' ) && false === WPONION_DEV_MODE ) {
			return false;
		}
		return ( defined( 'WPONION_DEV_MODE' ) && true === WPONION_DEV_MODE || defined( 'WP_DEBUG' ) && true === WP_DEBUG );
	}
}

if ( ! function_exists( 'wponion_field_debug' ) ) {
	/**
	 * Checks if field debug is enabled.
	 *
	 * @return bool
	 */
	function wponion_field_debug() {
		if ( defined( 'WPONION_FIELD_DEBUG' ) && false === WPONION_FIELD_DEBUG ) {
			return false;
		}
		return ( defined( 'WPONION_FIELD_DEBUG' ) && true === WPONION_FIELD_DEBUG || wponion_is_debug() );
	}
}

if ( ! function_exists( 'wponion_read_json_files' ) ) {
	/**
	 * Reads Given File Path.
	 *
	 * @param $file_path
	 *
	 * @return array|mixed|object
	 */
	function wponion_read_json_files( $file_path ) {
		return ( file_exists( $file_path ) ) ? json_decode( file_get_contents( $file_path ), true ) : array();
	}
}

if ( ! function_exists( 'wponion_get_term_meta' ) ) {
	/**
	 * Returns Terms Meta Info.
	 *
	 * @param string $term_id
	 * @param string $unique
	 *
	 * @return mixed
	 */
	function wponion_get_term_meta( $term_id = '', $unique = '' ) {
		if ( function_exists( 'get_term_meta' ) ) {
			return get_term_meta( $term_id, $unique, true );
		}
		$key = 'wponion_' . wponion_hash_string( $term_id . '_' . $unique );
		return get_option( $key, true );
	}
}

if ( ! function_exists( 'wponion_update_term_meta' ) ) {
	/**
	 * Updates Term Meta.
	 *
	 * @param string $term_id
	 * @param string $unique
	 * @param array  $values
	 *
	 * @return bool|int|\WP_Error
	 */
	function wponion_update_term_meta( $term_id = '', $unique = '', $values = array() ) {
		if ( function_exists( 'update_term_meta' ) ) {
			return update_term_meta( $term_id, $unique, $values );
		}

		$key = 'wponion_' . wponion_hash_string( $term_id . '_' . $unique );
		return update_option( $key, $values );
	}
}

if ( ! function_exists( 'wponion_delete_term_meta' ) ) {
	/**
	 * Deletes a Term Meta.
	 *
	 * @param string $term_id
	 * @param string $unique
	 *
	 * @return bool
	 */
	function wponion_delete_term_meta( $term_id = '', $unique = '' ) {
		if ( function_exists( 'delete_term_meta' ) ) {
			return delete_term_meta( $term_id, $unique );
		}
		return delete_option( 'wponion_' . wponion_hash_string( $term_id . '_' . $unique ) );
	}
}

if ( ! function_exists( 'wponion_is_callable' ) ) {
	/**
	 * Checks if given value is a callback.
	 *
	 * @param $callback
	 *
	 * @return bool
	 */
	function wponion_is_callable( $callback ) {
		if ( is_callable( $callback ) ) {
			return true;
		}

		if ( is_string( $callback ) && has_action( $callback ) ) {
			return true;
		}

		if ( is_string( $callback ) && has_filter( $callback ) ) {
			return true;
		}
		return false;
	}
}

if ( ! function_exists( 'wponion_callback' ) ) {
	/**
	 * Custom function to handle multiple callback options
	 * 1. Function
	 * 2. Inline Function
	 * 3. Class instance
	 * 4. Class Static Method
	 * 5. do_action
	 * 6. apply_filters.
	 *
	 * @param       $callback
	 * @param array $args
	 *
	 * @return bool|false|mixed|string
	 */
	function wponion_callback( $callback, $args = array() ) {
		$data = false;
		try {
			if ( is_callable( $callback ) ) {
				$args = ( ! is_array( $args ) ) ? array( $args ) : $args;
				$data = call_user_func_array( $callback, $args );
			} elseif ( is_string( $callback ) && has_filter( $callback ) ) {
				$data = call_user_func_array( 'apply_filters', array_merge( array( $callback ), $args ) );
			} elseif ( is_string( $callback ) && has_action( $callback ) ) {
				ob_start();
				$args = ( ! is_array( $args ) ) ? array( $args ) : $args;
				echo call_user_func_array( 'do_action', array_merge( array( $callback ), $args ) );
				$data = ob_get_clean();
				ob_flush();
			}
		} catch ( Exception $exception ) {
			$data = false;
		}
		return $data;
	}
}

if ( ! function_exists( 'wponion_update_option' ) ) {
	/**
	 * Custom Wrapper For update_option & update_site_option.
	 *
	 * @param string $option
	 * @param mixed  $value
	 * @param bool   $autoload
	 * @param bool   $force_local
	 */
	function wponion_update_option( $option, $value, $autoload = false, $force_local = false ) {
		if ( is_network_admin() && is_multisite() && false === $force_local ) {
			update_site_option( $option, $value );
		} else {
			update_option( $option, $value, $autoload );
		}
	}
}

if ( ! function_exists( 'wponion_get_option' ) ) {
	/**
	 * Custom Wrapper for get_option / get_site_option.
	 *
	 * @param      $option_name
	 * @param      $default
	 * @param bool $force_local
	 *
	 * @return mixed
	 */
	function wponion_get_option( $option_name, $default, $force_local = false ) {
		if ( is_multisite() && is_network_admin() && false === $force_local ) {
			return get_site_option( $option_name, $default );
		}
		return get_option( $option_name, $default );
	}
}

if ( ! function_exists( 'wponion_inline_ajax' ) ) {
	/**
	 * @param string $action
	 * @param array  $args
	 * @param string $button_html
	 *
	 * @return string
	 */
	function wponion_inline_ajax( $action = '', $args = array(), $button_html = '' ) {
		if ( is_scalar( $args ) && empty( $button_html ) ) {
			$button_html = $args;
			$args        = array();
		}
		$args      = wp_parse_args( $args, array(
			'method'   => 'post',
			'url'      => admin_url( 'admin-ajax.php' ),
			'part_url' => false,
			'data'     => array(),
			'success'  => false,
			'error'    => false,
			'always'   => false,
			'action'   => $action,
		) );
		$unique_id = wponion_hash_array( $args );
		wponion_localize()->add( $unique_id, array( 'inline_ajax' => $args ) );
		if ( ! empty( $button_html ) ) {
			$button_html = preg_replace( '/<a (.+?)>/i', "<a $1 data-wponion-inline-ajax='" . $unique_id . "'>", $button_html );
			return preg_replace( '/<button (.+?)>/i', "<button $1  data-wponion-inline-ajax='" . $unique_id . "'>", $button_html );
		}
		return $unique_id;
	}
}


// WPOnion Assets Related Functions.
require_once WPONION_PATH . 'core/helpers/assets.php';

// WPOnion Fields Related Functions.
require_once WPONION_PATH . 'core/helpers/field.php';

// WPOnion Registry Related Functions.
require_once WPONION_PATH . 'core/helpers/registry.php';

// WPOnion Field Sanitize Related Functions.
require_once WPONION_PATH . 'core/helpers/sanitize.php';

// WPOnion Module Related Functions
require_once WPONION_PATH . 'core/helpers/module.php';

// WPOnion Module Related Functions
require_once WPONION_PATH . 'core/helpers/validator.php';

// WPOnion Theme Related Functions
require_once WPONION_PATH . 'core/helpers/theme.php';

// WPOnion Theme Related Functions
require_once WPONION_PATH . 'core/helpers/admin-notice.php';
