<?php
/**
 *
 * Initial version created 20-05-2018 / 09:24 AM
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @version 1.0
 * @since 1.0
 * @package
 * @link
 * @copyright 2018 Varun Sridharan
 * @license GPLV3 Or Greater (https://www.gnu.org/licenses/gpl-3.0.txt)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'WPOnion_Field_fieldset' ) ) {
	class WPOnion_Field_fieldset extends WPOnion_Field {

		protected function init_subfields() {
			if ( $this->has( 'fields' ) ) {
				foreach ( $this->data( 'fields' ) as $field_id => $field ) {
					$this->field[ $field_id ] = $this->sub_field( $field, _wponion_get_field_value( $field, $this->get_value() ), $this->name(), true );
				}
			}
		}

		protected function output() {
			echo '<div class="wponion-fieldset-wrap">';

			if ( $this->has( 'fields' ) ) {
				if ( $this->has( 'heading' ) ) {
					echo wponion_add_element( array(
						'type'    => 'subheading',
						'content' => $this->data( 'heading' ),
					) );
				}

				foreach ( $this->data( 'fields' ) as $field ) {
					$this->sub_field( $field, _wponion_get_field_value( $field, $this->get_value() ), $this->name(), false );
				}
			}

			echo '</div>';
		}

		protected function field_default() {
			return array(
				'fields'   => array(),
				'heading'  => null,
				'un_array' => false,
			);
		}

		public function field_assets() {
			// TODO: Implement field_assets() method.
		}
	}
}
