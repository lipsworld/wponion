<?php
/**
 *
 * Initial version created 02-07-2018 / 12:37 PM
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @version 1.0
 * @since 1.0
 * @package
 * @link
 * @copyright 2018 Varun Sridharan
 * @license GPLV3 Or Greater (https://www.gnu.org/licenses/gpl-3.0.txt)
 */

namespace WPOnion\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\WPOnion\Modules\woocommerce' ) ) {
	/**
	 * Class woocommerce
	 *
	 * @package WPOnion\Modules
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class woocommerce extends \WPOnion\Bridge\Module {
		/**
		 * module
		 *
		 * @var string
		 */
		protected $module = 'woocommerce';

		/**
		 * Stores Current POST ID.
		 *
		 * @var null
		 */
		protected $post_id = null;

		/**
		 * default_wc_tabs
		 *
		 * @var mixed|null|void
		 */
		public $default_wc_tabs = null;

		/**
		 * group_fields
		 *
		 * @var array
		 */
		public $group_fields = array();

		/**
		 * groups_to_add
		 *
		 * @var array
		 */
		public $groups_to_add = array();

		/**
		 * variation_errors
		 *
		 * @var array
		 */
		public $variation_errors = array();

		/**
		 * variation_fields
		 *
		 * @var array
		 */
		public $variation_fields = array(
			'pricing'    => array(),
			'options'    => array(),
			'inventory'  => array(),
			'dimensions' => array(),
			'tax'        => array(),
			'download'   => array(),
			'default'    => array(),
		);

		/**
		 * Taxonomy constructor.
		 *
		 * @param array $settings
		 * @param array $fields
		 */
		public function __construct( array $settings = array(), $fields = array() ) {
			parent::__construct( $fields, $settings );
		}

		/**
		 * @param string $extra_class
		 * @param bool   $bootstrap
		 *
		 * @return array|string
		 */
		public function wrap_class( $extra_class = '', $bootstrap = false ) {
			return wponion_html_class( $extra_class, $this->default_wrap_class( $bootstrap ) );
		}

		/**
		 * Inits. Current instance.
		 *
		 * @return mixed|void
		 */
		public function on_init() {
			$this->add_action( 'load-post.php', 'on_page_load' );
			$this->add_action( 'load-post-new.php', 'on_page_load' );
			$this->add_action( 'woocommerce_admin_process_product_object', 'save_product_data' );

			/*$this->add_action( 'woocommerce_save_product_variation', 'save_variation_fields', 10, 2 );
			$this->add_action( 'woocommerce_variation_options', 'wc_variation_options', 10, 3 );
			$this->add_action( 'woocommerce_variation_options_pricing', 'wc_variation_pricing', 10, 3 );
			$this->add_action( 'woocommerce_variation_options_inventory', 'wc_variation_inventory', 10, 3 );
			$this->add_action( 'woocommerce_variation_options_dimensions', 'wc_variation_dimensions', 10, 3 );
			$this->add_action( 'woocommerce_variation_options_tax', 'wc_variation_tax', 10, 3 );
			$this->add_action( 'woocommerce_variation_options_download', 'wc_variation_download', 10, 3 );
			$this->add_action( 'woocommerce_product_after_variable_attributes', 'wc_variation_variable_attributes', 10, 3 );*/
		}

		/**
		 * Runs on Page Load.
		 */
		public function on_page_load() {
			global $typenow, $post, $product;
			if ( 'product' === $typenow ) {
				$this->handle_fields_data();
				$this->add_action( 'wp_ajax_woocommerce_load_variations', 'handle_options', 1 );
				$this->add_action( 'woocommerce_product_data_tabs', 'add_wc_tabs' );
				$this->add_action( 'woocommerce_product_data_panels', 'add_wc_fields', 99 );
				$this->add_action( 'admin_enqueue_scripts', 'load_style_script' );

				$this->add_action( 'woocommerce_product_options_advanced', 'advanced_page' );
				$this->add_action( 'woocommerce_product_options_general_product_data', 'general_page' );
				$this->add_action( 'woocommerce_product_options_inventory_product_data', 'stock_page' );
				$this->add_action( 'woocommerce_product_options_related', 'linked_page' );
				$this->add_action( 'woocommerce_product_options_shipping', 'shipping_page' );
			}
		}

		/**
		 * Loads Required Assets.
		 */
		public function load_style_script() {
			wponion_load_core_assets();
		}

		/**
		 * Checks if given data is also for variation or only for variation.
		 *
		 * @param      $data
		 *
		 * @return bool|mixed
		 */
		public function is_variation( $data ) {
			if ( isset( $data['is_variation'] ) && isset( $data['only_variation'] ) ) {
				return 'only';
			}
			return ( isset( $data['is_variation'] ) && ! isset( $data['only_variation'] ) ) ? true : false;
		}

		/**
		 * @param $data
		 *
		 * @return bool|string
		 */
		protected function get_group_data( $data ) {
			if ( isset( $data['group'] ) ) {
				return $data['group'];
			} elseif ( isset( $data['title'] ) && isset( $data['name'] ) && false === $data['title'] ) {
				return $data['name'];
			} elseif ( isset( $data['title'] ) && isset( $data['name'] ) && false === $data['name'] ) {
				return strtolower( sanitize_title( $data['title'] ) );
			}
			return false;
		}

		/**
		 * Handles Fields Array and convert them. based on the needs.
		 */
		public function handle_fields_data() {
			foreach ( $this->fields as $page_id => $page ) {
				$is_variation = $this->is_variation( $page );

				if ( 'only' === $is_variation ) {

				} else {
					$group_slug   = $this->get_group_data( $page );
					$is_new_group = ( false === $group_slug ) ? true : false;
					$group_slug   = ( false === $group_slug ) ? $page['name'] : $group_slug;

					if ( false !== $group_slug ) {
						if ( ! isset( $this->group_fields[ $group_slug ] ) ) {
							$this->group_fields[ $group_slug ] = array();
						}
						$this->group_fields[ $group_slug ][] = $page;
					}

					if ( true === $is_new_group ) {
						$this->groups_to_add[ $page['name'] ] = $page;
					}
				}
			}
		}

		/**
		 * Adds Custom New Tabs To WC.
		 *
		 * @param $tabs
		 *
		 * @return mixed
		 */
		public function add_wc_tabs( $tabs ) {
			if ( is_array( $this->groups_to_add ) ) {
				foreach ( $this->groups_to_add as $key => $data ) {
					$tabs[ $key ]          = array(
						'label'    => isset( $data['title'] ) ? $data['title'] : false,
						'target'   => isset( $data['name'] ) ? sanitize_title( 'wponion_' . $this->plugin_id() . '_' . $data['name'] ) : false,
						'class'    => isset( $data['class'] ) ? $data['class'] : false,
						'priority' => isset( $data['priority'] ) ? $data['priority'] : null,
					);
					$tabs[ $key ]['class'] = wponion_html_class( $tabs[ $key ]['class'], $this->show_hide_class( $data ), false );
				}
			}
			return $tabs;
		}

		/**
		 * Converts Into Usable HTML Class.
		 *
		 * @param $classes
		 * @param $prefix
		 *
		 * @return array|mixed
		 */
		protected function _show_hide_html_class( $classes, $prefix ) {
			if ( is_array( $classes ) ) {
				foreach ( $classes as $k => $v ) {
					$classes[ $k ] = $prefix . $v;
				}
				return $classes;
			} elseif ( is_string( $classes ) ) {
				return $prefix . $classes;
			}
			return array();
		}

		/**
		 * Handles Show / Hide WC HTML Class.
		 *
		 * @param $data
		 *
		 * @return array|string
		 */
		protected function show_hide_class( $data ) {
			$return = array();

			if ( isset( $data['show'] ) ) {
				$return = wponion_html_class( $this->_show_hide_html_class( $data['show'], 'show_if_' ) );
			}

			if ( isset( $data['hide'] ) ) {
				$return = wponion_html_class( $return, $this->_show_hide_html_class( $data['hide'], 'hide_if_' ) );
			}

			return wponion_html_class( $return, array(), false );
		}

		/**
		 * @param       $group
		 * @param array $extra_wrap_class
		 */
		protected function render_tab_fields( $group, $extra_wrap_class = array() ) {
			$wrap_class = $this->wrap_class( $this->parse_args( $extra_wrap_class, array( 'wponion-wc-metabox-fields' ) ) );
			echo '<div class="' . $wrap_class . '">';
			foreach ( $group['fields'] as $field ) {
				$field = $this->parse_args( $field, array( 'wrap_class' => array() ) );

				if ( ! is_array( $field['wrap_class'] ) ) {
					$field['wrap_class'] = array( $field['wrap_class'] );
				}

				$field['wrap_class'] = wponion_html_class( $field['wrap_class'], $this->show_hide_class( $field ) );

				echo $this->render_field( $field, false, false );
			}
			echo '</div>';
		}

		/**
		 * Renders Fields HTML.
		 */
		public function add_wc_fields() {
			wp_nonce_field( 'wpsf-framework-wc-metabox', 'wpsf-framework-wc-metabox-nonce' );
			foreach ( $this->groups_to_add as $group ) {
				$default = array(
					'fields' => '',
					'name'   => '',
					'title'  => '',
				);
				$group   = wp_parse_args( $group, $default );
				$id      = sanitize_title( 'wponion_' . $this->plugin_id() . '_' . $group['name'] );
				echo '<div id="' . $id . '" class="panel woocommerce_options_panel hidden">';
				$this->render_tab_fields( $group );
				echo '</div>';
			}
		}

		/**
		 * Renders Page HTML.
		 *
		 * @param $page
		 */
		public function render_page_fields( $page ) {
			global $post, $thepostid;
			$ID = is_object( $post ) ? $post->ID : $thepostid;
			$this->set_post_id( $ID );

			if ( isset( $this->group_fields[ $page ] ) ) {
				$_fields = $this->group_fields[ $page ];
				foreach ( $_fields as $data ) {
					$this->render_tab_fields( $data );
				}
			}
		}

		/**
		 * Renders HTML Output for WC Tab : advanced
		 */
		public function advanced_page() {
			$this->render_page_fields( 'advanced' );
		}

		/**
		 * Renders HTML Output for WC Tab : general
		 */
		public function general_page() {
			$this->render_page_fields( 'general' );
		}

		/**
		 * Renders HTML Output for WC Tab : stock
		 */
		public function stock_page() {
			$this->render_page_fields( 'stock' );
		}

		/**
		 * Renders HTML Output for WC Tab : linked
		 */
		public function linked_page() {
			$this->render_page_fields( 'linked' );
		}

		/**
		 * Renders HTML Output for WC Tab : shipping
		 */
		public function shipping_page() {
			$this->render_page_fields( 'shipping' );
		}


		/**
		 * Resets Post ID.
		 *
		 * @param $post_id
		 */
		protected function set_post_id( $post_id ) {
			$this->post_id = $post_id;
			$this->get_db_values();
			$this->options_cache = false;
			$this->get_cache();
		}

		/**
		 * Retrives Stored DB Values.
		 *
		 * @return array|mixed
		 */
		public function get_db_values() {
			if ( ! isset( $this->db_values[ $this->post_id ] ) ) {
				$this->db_values[ $this->post_id ] = get_post_meta( $this->post_id, $this->unique, true );
				if ( ! is_array( $this->db_values ) ) {
					$this->db_values = array();
				}
			}
			return $this->db_values[ $this->post_id ];
		}

		/**
		 * UPDates DB Values.
		 *
		 * @param $value
		 */
		protected function save_db_values( $value ) {
			$this->db_values[ $this->post_id ] = $value;
			update_post_meta( $this->post_id, $this->unique, $value );
		}

		/**
		 * Returns Unique Cache ID For each instance but only once.
		 *
		 * @return string
		 */
		protected function get_cache_id() {
			return 'wponion_' . wponion_hash_string( $this->post_id . '_' . $this->unique() . '_' . $this->module() ) . '_cache';
		}

		/**
		 * Stores Cache Data.
		 *
		 * @param array $data
		 */
		public function set_cache( $data = array() ) {
			$data['wponion_version'] = WPONION_DB_VERSION;
			update_post_meta( $this->post_id, $this->get_cache_id(), $data );
			$this->options_cache = $data;
		}

		/**
		 * Retrives Stored DB Cache.
		 *
		 * @return mixed
		 */
		protected function get_db_cache() {
			return get_post_meta( $this->post_id, $this->get_cache_id(), true );
		}

		/**
		 * @param $product \WC_Product
		 */
		public function save_product_data( $product ) {
			$product_id = $product->get_id();
			$this->set_post_id( $product_id );
			#var_dump( $_POST[ $this->unique ] );
			$instance = new \WPOnion\DB\WooCommerce_Save_Handler();
			$instance->init_class( array(
				'module'      => 'woocommerce',
				'plugin_id'   => $this->plugin_id(),
				'unique'      => $this->unique,
				'fields'      => $this->fields,
				'user_values' => $_POST[ $this->unique ],
				'db_values'   => $this->get_db_values(),
				'args'        => array( 'settings' => &$this ),
			) )
				->run( false );
			#var_dump( $this->get_db_values(), $instance->get_values() );
			$this->save_db_values( $instance->get_values() );
			$this->options_cache['field_errors'] = $instance->get_errors();
			$this->set_cache( $this->options_cache );
			#exit;
		}
	}
}
