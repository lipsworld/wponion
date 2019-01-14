<?php
/**
 *
 * Initial version created 28-05-2018 / 10:57 AM
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @version 1.0
 * @since 1.0
 * @package
 * @link
 * @copyright 2018 Varun Sridharan
 * @license GPLV3 Or Greater (https://www.gnu.org/licenses/gpl-3.0.txt)
 */

namespace WPOnion\Field;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\WPOnion\Field\Color_Picker' ) ) {
	/**
	 * Class Color_Picker
	 *
	 * @package WPOnion\Field
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Color_Picker extends Checkbox_Radio {

		/**
		 * Final HTML Output
		 */
		protected function output() {
			if ( empty( $this->data( 'options' ) ) ) {
				echo $this->before();

				$attributes = array(
					'type'       => 'text',
					'name'       => $this->name(),
					'data-alpha' => $this->has( 'rgba' ),
					'value'      => $this->value(),
					'class'      => 'wponion-color-picker-element',
				);

				echo '<input ' . $this->attributes( $attributes ) . '/>';
				echo $this->after();
			} else {
				echo '<div class=" colors-wrapper ' . $this->data( 'style' ) . ' ">';
				$this->field['type'] = $this->field['palette_type'];
				parent::output();
				echo '</div>';
			}
		}

		/**
		 * Loads the required plugins assets.
		 *
		 * @return mixed|void
		 */
		public function field_assets() {
			wponion_load_asset( 'wponion-colorpicker' );
		}

		/**
		 * Returns all fields default.
		 *
		 * @return array|mixed
		 */
		protected function field_default() {
			return $this->parse_args( array(
				'rgba'         => true,
				'style'        => 'round with-margin',
				'palette_type' => 'radio',
				'size'         => 25,
			), parent::field_default() );
		}

		/**
		 * @param $options
		 *
		 * @return mixed
		 */
		protected function element_value( $options ) {
			return is_numeric( $options['key'] ) ? $options['label'] : $options['key'];
		}

		/**
		 * Renders Single Option as html.
		 *
		 * @param $label_attr
		 * @param $field_attr
		 * @param $value
		 * @param $attr
		 * @param $options
		 *
		 * @return string
		 */
		protected function _element_html( $label_attr, $field_attr, $value, $attr, $options ) {
			$attr['value'] = ( is_numeric( $options['key'] ) ) ? $options['label'] : $options['key'];
			return '
			<label ' . wponion_array_to_html_attributes( $label_attr ) . ' style="width:' . absint( $this->data( 'size' ) ) . 'px; height:' . absint( $this->data( 'size' ) ) . 'px;"> 
				<input ' . $field_attr . ' ' . $this->checked( $value, $attr['value'], 'checked' ) . '   /><span class="color-palette-color" style="background:' . $attr['value'] . '">' . $attr['value'] . '</span>
			</label>';
		}
	}
}
