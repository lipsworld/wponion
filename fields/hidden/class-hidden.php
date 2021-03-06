<?php
/**
 *
 * Project : wponion
 * Date : 01-11-2018
 * Time : 11:17 AM
 * File : google_maps.php
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @version 1.0
 * @package wponion
 * @copyright 2018 Varun Sridharan
 * @license GPLV3 Or Greater (https://www.gnu.org/licenses/gpl-3.0.txt)
 */

namespace WPOnion\Field;
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\WPOnion\Field\Hidden' ) ) {
	/**
	 * Class Hidden
	 *
	 * @package WPOnion\Field
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Hidden extends \WPOnion\Field {

		public function handle_field_args( $data = array() ) {
			$data['only_field'] = true;
			return $data;
		}

		public function output() {
			$attr = $this->attributes( array(
				'type'              => 'hidden',
				'class'             => $this->element_class(),
				'value'             => $this->value(),
				'name'              => $this->name(),
				'data-wponion-jsid' => $this->js_field_id(),
			) );
			echo '<input type="hidden" name="' . $this->name() . '"  ' . $attr . '/>';
		}

		public function field_assets() {
		}

		protected function field_default() {
			return array();
		}
	}
}
