<?php
/**
 *
 * Initial version created 18-05-2018 / 06:26 AM
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @version 1.0
 * @since 1.0
 * @package
 * @link
 * @copyright 2018 Varun Sridharan
 * @license GPLV3 Or Greater (https://www.gnu.org/licenses/gpl-3.0.txt)
 */

namespace WPOnion;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\WPOnion\Core_Ajax' ) ) {
	/**
	 * Class Core_Ajax
	 *
	 * @package WPOnion
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Core_Ajax extends Bridge {
		/**
		 * Core_Ajax constructor.
		 */
		public function __construct() {
			add_action( 'wp_ajax_wponion-ajax', array( &$this, 'handle_ajax_request' ) );
		}

		/**
		 * Handles Ajax Request.
		 */
		public function handle_ajax_request() {
			if ( isset( $_REQUEST['wponion-ajax'] ) && ! empty( $_REQUEST['wponion-ajax'] ) ) {
				if ( ! defined( 'WPONION_DOING_AJAX' ) ) {
					define( 'WPONION_DOING_AJAX', true );
				}
				$function = sanitize_text_field( $_REQUEST['wponion-ajax'] );
				$function = str_replace( '-', '_', sanitize_title( $function ) );
				if ( method_exists( $this, $function ) ) {
					$this->$function();
				}
			}
			wp_die();
		}

		/**
		 * Handles Icon Picker Ajax Request.
		 */
		public function icon_picker() {
			$libs     = Icons::icon_list();
			$enabled  = ( isset( $_REQUEST['enabled'] ) ) ? $_REQUEST['enabled'] : true;
			$disabled = ( isset( $_REQUEST['disabled'] ) ) ? $_REQUEST['disabled'] : false;

			if ( is_array( $enabled ) ) {
				foreach ( $libs as $name => $_n ) {
					if ( ! in_array( $name, $enabled ) ) {
						unset( $libs[ $name ] );
					}
				}
			} elseif ( is_string( $enabled ) && ( true !== $enabled || false !== $enabled ) ) {
				if ( isset( $libs[ $enabled ] ) ) {
					$libs = $libs[ $enabled ];
				}
			}

			if ( is_array( $disabled ) && is_array( $libs ) ) {
				foreach ( $libs as $name => $_n ) {
					if ( in_array( $name, $disabled ) ) {
						unset( $libs[ $name ] );
					}
				}
			}

			$default_lib  = is_array( $libs ) ? current( array_keys( $libs ) ) : $libs;
			$selected_lib = ( isset( $_REQUEST['wponion-icon-lib'] ) ) ? $_REQUEST['wponion-icon-lib'] : $default_lib;
			$selected_lib = ( ! isset( $libs[ $selected_lib ] ) ) ? $default_lib : $selected_lib;
			$json         = \WPOnion\Icons::get( $selected_lib );
			$html         = '<div class="wponion-icon-picker-model-header">';
			$html         = $html . '<input type="text" placeholder="' . __( 'Search Icon' ) . '"/>';

			if ( is_array( $libs ) && count( $libs ) > 1 ) {
				$html = $html . '<select>';
				foreach ( $libs as $lib => $ejson ) {
					$is_selected = ( $lib === $selected_lib ) ? ' selected="selected" ' : '';
					$html        = $html . '<option value="' . $lib . '" ' . $is_selected . '>' . $ejson . '</option>';
				}

				$html .= '</select>';
			}
			$html .= '</div>';
			if ( is_array( $json ) && ! empty( $json ) ) {
				$html .= '<div class="wponion-icon-picker-container-scroll"><div class="wponion-icon-picker-container">';
				foreach ( $json as $json_title => $icons ) {
					if ( is_array( $icons ) ) {
						foreach ( $icons as $key => $icon ) {
							$_icon = ( is_numeric( $key ) ) ? $icon : $key;
							$title = ( is_numeric( $key ) ) ? $icon : $icon;

							$html .= '<div class="wponion-icon-preview-wrap">';
							$html .= '<span data-icon="' . $_icon . '" title="' . $title . '" class="wponion-icon-preview">' . wponion_icon( $_icon ) . '</span>';
							$html .= '</div>';
						}
					} else {
						$_icon = ( is_numeric( $json_title ) ) ? $icons : $json_title;
						$title = ( is_numeric( $json_title ) ) ? $icons : $icons;
						$html  = $html . '<div class="wponion-icon-preview-wrap">';
						$html  = $html . '<span data-icon="' . $_icon . '" title="' . $title . '" class="wponion-icon-preview">' . wponion_icon( $_icon ) . '</span>';
						$html  = $html . '</div>';
					}
				}
				$html .= '</div>';
			} else {
				wp_send_json_error( __( 'Icon Library Not found' ) );
			}
			$html .= '</div>';
			wp_send_json_success( $html );
			wp_die();
		}

		/**
		 * Handles Ajax Metabox Save.
		 */
		public function save_metabox() {
			if ( isset( $_REQUEST['metabox_id'] ) && isset( $_REQUEST['plugin_id'] ) ) {
				wponion_localize();
				$plugin_id       = sanitize_text_field( $_REQUEST['plugin_id'] );
				$unique          = sanitize_text_field( $_REQUEST['unique'] );
				$this->plugin_id = $plugin_id;
				$this->module    = 'metabox';
				$instance        = wponion_metabox_registry( $unique );
				$post_id         = sanitize_text_field( $_REQUEST['wponion_postid'] );
				if ( $instance ) {
					$instance->set_post_id( $post_id );
					$instance->save_metabox( $post_id );
					$this->_action( 'ajax_before_render' );
					$instance->on_page_load();
					$instance->render( $post_id );
					$this->_action( 'ajax_render' );
				}
			}
		}

		/**
		 * Generates Oembed Preview.
		 */
		public function oembed_preview() {
			$args  = array( 'width' => 360 );
			$embed = wp_oembed_get( $_REQUEST['value'], $args );

			if ( ! $embed ) {
				global $wp_embed;
				$temp                           = $wp_embed->return_false_on_fail;
				$wp_embed->return_false_on_fail = true; // Do not fallback to make a link.
				$embed                          = $wp_embed->shortcode( $args, $_REQUEST['value'] );
				$wp_embed->return_false_on_fail = $temp;
			}

			if ( $embed ) {
				wp_send_json_success( $embed );
			}
			wp_send_json_error();
		}

		/**
		 * Handles Saving Bulk Edit Data.
		 */
		public function save_bulk_edit() {
			if ( isset( $_POST['post_ids'] ) ) {
				if ( is_array( $_POST['post_ids'] ) ) {
					foreach ( $_POST['post_ids'] as $id ) {
						do_action( 'wponion_save_bulk_edit', $id );
					}
				}
			}
		}

		/**
		 * Creates A New backup of a module.
		 */
		public function new_module_data_backup() {
			$module = ( isset( $_POST['module'] ) ) ? $_POST['module'] : false;
			$unique = ( isset( $_POST['unique'] ) ) ? $_POST['unique'] : false;
			$extra  = ( isset( $_POST['extra'] ) ) ? $_POST['extra'] : false;
			if ( false === $module ) {
				wp_send_json_error( __( 'Error Code : #BKP189' ) );
			}

			$status = Backup_Handler::new_backup( $unique, $module, $extra );
			if ( $status ) {
				wp_send_json_success( Backup_Handler::get_backup_lists( $unique, $module, $extra ) );
			}
			wp_send_json_error( __( 'Error Code : #BKP201' ) );
		}

		/**
		 * Deletes A Module Backup.
		 */
		public function delete_module_data_backup() {
			$module    = ( isset( $_POST['module'] ) ) ? $_POST['module'] : false;
			$unique    = ( isset( $_POST['unique'] ) ) ? $_POST['unique'] : false;
			$extra     = ( isset( $_POST['extra'] ) ) ? $_POST['extra'] : false;
			$backup_id = ( isset( $_POST['backup_id'] ) ) ? $_POST['backup_id'] : false;

			if ( false === $module ) {
				wp_send_json_error( __( 'Error Code : #BKP210' ) );
			}

			$status = Backup_Handler::delete_backup( $backup_id, $unique, $module, $extra );
			if ( $status ) {
				wp_send_json_success( Backup_Handler::get_backup_lists( $unique, $module, $extra ) );
			}
			wp_send_json_error( __( 'Error Code : #BKP217' ) );
		}

		/**
		 * Force Downloads A Backup.
		 */
		public function download_backup() {
			$module    = ( isset( $_REQUEST['module'] ) ) ? $_REQUEST['module'] : false;
			$unique    = ( isset( $_REQUEST['unique'] ) ) ? $_REQUEST['unique'] : false;
			$extra     = ( isset( $_REQUEST['extra'] ) ) ? $_REQUEST['extra'] : false;
			$backup_id = ( isset( $_REQUEST['backupid'] ) ) ? $_REQUEST['backupid'] : false;
			$file_name = $unique . '_' . $module . '_' . date( 'Y-m-d-hi-s', $backup_id );
			$backup    = Backup_Handler::get_backup( $backup_id, $unique, $module, $extra );

			$now = gmdate( 'D, d M Y H:i:s' );
			header( 'Expires: Tue, 03 Jul 2001 06:00:00 GMT' );
			header( 'Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate' );
			header( 'Last-Modified: ' . $now . ' GMT' );
			header( 'Content-Type: application/force-download' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Type: application/download' );
			header( 'Content-Disposition: attachment;filename=' . $file_name . '.json' );
			header( 'Content-Transfer-Encoding: binary' );
			echo json_encode( $backup );

		}

		/**
		 * Restores Database in model.
		 */
		public function restore_module_data_backup() {
			$module    = ( isset( $_POST['module'] ) ) ? $_POST['module'] : false;
			$unique    = ( isset( $_POST['unique'] ) ) ? $_POST['unique'] : false;
			$extra     = ( isset( $_POST['extra'] ) ) ? $_POST['extra'] : false;
			$backup_id = ( isset( $_POST['backup_id'] ) ) ? $_POST['backup_id'] : false;

			if ( $backup_id && $unique && $module ) {
				Backup_Handler::restore_backup( $backup_id, $unique, $module, $extra );
				wp_send_json_success( __( 'Backup Successfully Restored' ) );
			}

			wp_send_json_error( __( 'Error Code: #BKP259' ) );

		}

		/**
		 * Removes Stick notice if user click remove notice button.
		 */
		public function remove_admin_notice() {
			if ( isset( $_REQUEST['notice_hander'] ) && isset( $_REQUEST['notice_id'] ) && isset( $_REQUEST['wp_nounce'] ) ) {
				$wp_nounce = $_REQUEST['wp_nounce'];
				if ( wp_verify_nonce( $wp_nounce, 'wpo-admin-notice-sticky-remove' ) ) {
					$notice    = $_REQUEST['notice_hander'];
					$notice_id = $_REQUEST['notice_id'];
					$_ins      = wponion_admin_notices( $notice );
					if ( false !== $_ins ) {
						$_ins->remove( $notice_id );
						wp_send_json_success();
					}
				}
			}
			wp_send_json_error();

		}

	}
}
return new Core_Ajax;
