<?php
/**
 *
 * Initial version created 18-06-2018 / 10:54 AM
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @version 1.0
 * @since 1.0
 * @package
 * @link
 * @copyright 2018 Varun Sridharan
 * @license GPLV3 Or Greater (https://www.gnu.org/licenses/gpl-3.0.txt)
 */

$ins = new \WPOnion\Modules\Metabox( array(
	'option_name'   => '_wcrbp_metabox',
	'metabox_title' => 'Sample Metabox',
	'post_type'     => 'page',
	'metabox_id'    => false,
	'context'       => 'normal',
	'priority'      => null,
) );


$ins->page( 'Core Fields', 'core_fields' )
	->section( 'Text Field', 'text' )
	->merge_fields( $wponion_text_options );

$ins->section( 'Textarea', 'textarea' )
	->merge_fields( $wponion_textarea );

$ins->section( 'Checkbox', 'checkbox' )
	->merge_fields( $wponion_checkbox );

$ins->section( 'Radio', 'radio' )
	->merge_fields( $wponion_radio );

$ins->section( 'Color Palette', 'color-palette' )
	->merge_fields( $wponion_color_palette );

$ins->section( 'Color Picker', 'color_picker' )
	->merge_fields( $wponion_color_picker );

$ins->section( 'Font Picker', 'font_picker' )
	->merge_fields( $wponion_font_picker );


$ins->page( 'Core Fields 2', 'core_fields_2' )
	->section( 'Text Field', 'text' )
	->merge_fields( $wponion_text_options )
	->section( 'Textarea', 'textarea' )
	->merge_fields( $wponion_textarea )
	->section( 'Checkbox', 'checkbox' )
	->merge_fields( $wponion_checkbox )
	->section( 'Radio', 'radio' )
	->merge_fields( $wponion_radio )
	->section( 'Color Palette', 'color-palette' )
	->merge_fields( $wponion_color_palette )
	->section( 'Color Picker', 'color_picker' )
	->merge_fields( $wponion_color_picker )
	->section( 'Font Picker', 'font_picker' )
	->merge_fields( $wponion_font_picker );


$ins->page( 'Core Fields 3', 'core_fields_3' )
	->section( 'Text Field', 'text' )
	->merge_fields( $wponion_text_options )
	->section( 'Textarea', 'textarea' )
	->merge_fields( $wponion_textarea )
	->section( 'Checkbox', 'checkbox' )
	->merge_fields( $wponion_checkbox )
	->section( 'Radio', 'radio' )
	->merge_fields( $wponion_radio )
	->section( 'Color Palette', 'color-palette' )
	->merge_fields( $wponion_color_palette )
	->section( 'Color Picker', 'color_picker' )
	->merge_fields( $wponion_color_picker )
	->section( 'Font Picker', 'font_picker' )
	->merge_fields( $wponion_font_picker );
$ins->init();