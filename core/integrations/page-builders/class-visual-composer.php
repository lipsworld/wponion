<?php
/**
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @version 1.0
 * @since 1.0
 * @link
 * @copyright 2018 Varun Sridharan
 * @license GPLV3 Or Greater (https://www.gnu.org/licenses/gpl-3.0.txt)
 */

namespace WPOnion\Integrations\Page_Builders;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\WPOnion\Integrations\Page_Builders\Visual_Composer' ) ) {
	/**
	 * Class Visual_Composer
	 *
	 * @package WPOnion\Integrations\Page_Builders
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	final class Visual_Composer {
		/**
		 * Stores Field Types.
		 *
		 * @var array
		 * @access
		 * @static
		 */
		protected static $integrated_fields = array();

		/**
		 * Field Prefix.
		 *
		 * @var string
		 * @access
		 * @static
		 */
		private static $param_prefix = 'wponion_';

		/**
		 * @var bool
		 * @access
		 * @static
		 */
		private static $is_wponion_used = false;

		/**
		 * Inits Base Class.
		 *
		 * @static
		 */
		public static function init() {
			self::$integrated_fields = wponion_field_types()::get( 'vc' );
			self::register_fields();
			add_action( 'vc_edit_form_fields_after_render', array( __CLASS__, 'is_wponion_used' ) );
			add_action( 'vc_backend_editor_enqueue_js_css', array( __CLASS__, 'load_assets' ), 1 );
			if ( defined( 'WPONION_FRONTEND' ) && true === WPONION_FRONTEND ) {
				add_action( 'vc_frontend_editor_enqueue_js_css', array( __CLASS__, 'load_assets' ), 1 );
			}
		}

		/**
		 * Loads All Required Assets.
		 *
		 * @static
		 */
		public static function load_assets() {
			$scripts = \WPOnion\Assets::get( 'script' );
			$styles  = \WPOnion\Assets::get( 'styles' );

			foreach ( $scripts as $script ) {
				wp_enqueue_script( $script );
			}
			foreach ( $styles as $style ) {
				wp_enqueue_style( $style );
			}
			wp_enqueue_media();
			add_thickbox();
			do_action( 'wponion_visual_composer_load_assets' );
		}

		public static function register_fields() {
			foreach ( self::$integrated_fields as $field ) {
				vc_add_shortcode_param( self::$param_prefix . $field, array( __CLASS__, 'render_field' ), false );
			}
		}

		/**
		 * @param $field_args
		 * @param $value
		 * @param $type
		 *
		 * @static
		 * @return string
		 */
		public static function render_field( $field_args, $value, $type ) {
			self::$is_wponion_used = true;
			$class                 = wponion_module_html_class( 'vc' );
			$field_type            = self::field_type( $field_args['type'] );
			$output                = '<div class="' . $class . ' wponion-vc-field-' . $field_type . '">';
			$output                = $output . self::render( $field_args, $value, $field_args['type'] );
			$output                = $output . '</div>';
			return $output;
		}

		/**
		 * Renders HTML output for a field.
		 *
		 * @param $field_args
		 * @param $value
		 * @param $type
		 *
		 * @static
		 * @return string
		 */
		public static function render( $field_args, $value, $type ) {
			$class = self::get_class( self::field_type( $type ) );
			if ( false === $class ) {
				/* translators: Added Field Type. */
				return '<p>' . sprintf( __( 'Visual Composer Integration For WPOnion Field (%s) Not Found' ), $type ) . '</p>';
			}

			$field_args['type'] = self::field_type( $type );
			$instance           = new $class( $field_args, $value );
			ob_start();
			echo $instance->output();
			return ob_get_clean();
		}

		/**
		 * @param $type
		 *
		 * @static
		 * @return \WPOnion\Field|\WPOnion\Field\Visual_Composer\Base
		 */
		public static function get_class( $type ) {
			$class = wponion_get_field_class( $type, 'Visual_Composer' );
			if ( false === $class ) {
				$class = wponion_get_field_class( 'Base', 'Visual_Composer' );
			}
			return $class;
		}

		/**
		 * Returns A Proper Field Type.
		 *
		 * @param $type
		 *
		 * @static
		 * @return mixed
		 */
		public static function field_type( $type ) {
			return str_replace( self::$param_prefix, '', $type );
		}

		/**
		 * Validates if Field Type is WPOnion's Core Field.
		 *
		 * @param $type
		 *
		 * @static
		 * @return bool
		 */
		public static function is_wponion( $type ) {
			return ( false !== strpos( $type, self::$param_prefix ) );
		}

		/**
		 * Check if WPOnion Is Used and if so then renders few small JS to trigger init function.
		 *
		 * @static
		 */
		public static function is_wponion_used() {
			if ( true === self::$is_wponion_used ) {
				wponion_localize()->render_js_args();
				echo '<script type="text/javascript">wponion_vc_init()</script>';
			}
		}
	}
}
