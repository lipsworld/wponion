<?php
if ( ! is_array( $settings->fields() ) ) {
	return;
}
foreach ( $settings->fields() as $option ) {
	if ( false === $settings->valid_option( $option ) ) {
		continue;
	}
	$slug             = $option['name'];
	$is_parent_active = $settings->is_tab_active( $option['name'], false );
	$parent_active    = ( true === $is_parent_active ) ? ' wponion-parent-wraps ' : 'wponion-parent-wraps hidden';
	?>
	<div id="wponion-tab-<?php echo $slug; ?>" class="<?php echo $parent_active; ?>">
		<div class="postbox">
			<?php
			echo $wponion_wp_theme->submenu_html( $slug );

			if ( isset( $option['sections'] ) ) {
				$first_section = $settings->get_first_section( $option );

				?>
				<div class="inside">
					<?php
					foreach ( $option['sections'] as $section ) {
						if ( false === $settings->valid_option( $section, true ) ) {
							continue;
						}
						$child_slug        = $section['name'];
						$is_section_active = $settings->is_tab_active( $option['name'], $section['name'] );
						$section_active    = ( true === $is_section_active ) ? ' wponion-section-wraps ' : 'wponion-section-wraps hidden';
						echo '<div id="wponion-tab-' . $slug . '-' . $child_slug . '" class="' . $section_active . '" data-section-id="' . $child_slug . '">';
						foreach ( $section['fields'] as $field ) {
							echo $settings->render_field( $field );
						}
						echo '</div>';
					}
					?>
				</div>
				<?php

			} elseif ( isset( $option['fields'] ) ) {
				echo '<div class="inside">';
				foreach ( $option['fields'] as $field ) {
					echo $settings->render_field( $field );
				}
				echo '</div>';

			} elseif ( isset( $option['callback'] ) && false !== $option['callback'] ) {
				$with_wrap = ( isset( $option['with_wrap'] ) && true === $option['with_wrap'] || ! isset( $option['with_wrap'] ) ) ? true : false;
				if ( $with_wrap ) {
					echo '<div class="inside">';
				}

				if ( $with_wrap ) {
					echo '</div>';
				}
			}

			?>
		</div>
	</div>
	<?php
}
?>

