<?php
/**
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @version 1.0
 * @since 1.0
 * @link
 * @copyright 2019 Varun Sridharan
 * @license GPLV3 Or Greater (https://www.gnu.org/licenses/gpl-3.0.txt)
 */

namespace WPO;

if ( ! class_exists( 'WPO\Spacing' ) ) {
	/**
	 * Class Spacing
	 *
	 * @package WPO
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 *
	 * @method top()
	 * @method bottom()
	 * @method left()
	 * @method right()
	 * @method all()
	 * @method unit()
	 * @method unit_options()
	 * @method icons()
	 */
	class Spacing extends Field {
		/**
		 * Spacing constructor.
		 *
		 * @param bool  $id
		 * @param bool  $title
		 * @param array $args
		 */
		public function __construct( $id = false, $title = false, $args = array() ) {
			parent::__construct( 'spacing', $id, $title, $args );
		}

		/**
		 * @param $top
		 *
		 * @return $this
		 */
		public function set_top( $top ) {
			$this['top'] = $top;
			return $this;
		}

		/**
		 * @param $bottom
		 *
		 * @return $this
		 */
		public function set_bottom( $bottom ) {
			$this['bottom'] = $bottom;
			return $this;
		}

		/**
		 * @param $left
		 *
		 * @return $this
		 */
		public function set_left( $left ) {
			$this['left'] = $left;
			return $this;
		}

		/**
		 * @param $right
		 *
		 * @return $this
		 */
		public function set_right( $right ) {
			$this['right'] = $right;
			return $this;
		}

		/**
		 * @param bool $all
		 *
		 * @return $this
		 */
		public function set_all( $all = true ) {
			$this['all'] = $all;
			return $this;
		}

		/**
		 * @param $unit
		 *
		 * @return $this
		 */
		public function set_unit( $unit ) {
			$this['unit'] = $unit;
			return $this;
		}

		/**
		 * Default Options Are :
		 * array(
		 *    'px' => 'px',
		 *    '%'  => '%',
		 *    'em' => 'em',
		 * )
		 *
		 * @param $options
		 *
		 * @return $this
		 */
		public function set_unit_options( $options ) {
			$this['unit_options'] = $options;
			return $this;
		}


		/**
		 * Default Icons Are :
		 * array(
		 *    'top'    => '<i class="dashicons dashicons-arrow-up-alt"></i>',
		 *    'bottom' => '<i class="dashicons dashicons-arrow-down-alt"></i>',
		 *    'left'   => '<i class="dashicons dashicons-arrow-left-alt"></i>',
		 *    'right'  => '<i class="dashicons dashicons-arrow-right-alt"></i>',
		 *    'all'    => '<i class="dashicons dashicons-move"></i>',
		 * )
		 *
		 * @param $icons
		 *
		 * @return $this
		 */
		public function set_icons( $icons ) {
			$this['icons'] = $icons;
			return $this;
		}

		/**
		 * @return array
		 */
		protected function defaults() {
			return array(
				'top'          => true,
				'bottom'       => true,
				'left'         => true,
				'right'        => true,
				'all'          => false,
				'unit'         => true,
				'unit_options' => array(
					'px' => 'px',
					'%'  => '%',
					'em' => 'em',
				),
				'icons'        => array(
					'top'    => '<i class="dashicons dashicons-arrow-up-alt"></i>',
					'bottom' => '<i class="dashicons dashicons-arrow-down-alt"></i>',
					'left'   => '<i class="dashicons dashicons-arrow-left-alt"></i>',
					'right'  => '<i class="dashicons dashicons-arrow-right-alt"></i>',
					'all'    => '<i class="dashicons dashicons-move"></i>',
				),
			);
		}
	}
}
