<?php
/**
 * All those warships that we need to do spacetime travel are located here
 * @since 1.0.0
 */

/**
 * For Debugging like a PRO!
 */
if ( ! function_exists( 'print_pre' ) ) {
	function print_pre( $var ) {
		echo '<pre>';
		var_dump( $var );
		echo '</pre>';

	}
}

function xloc_get_template( $template_name, $template_path = '', $default_path = '' ) {
	$located = rel_locate_template( $template_name, $template_path, $default_path );
	include( $located );
}

function rel_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		//need to define a constant for this ? maybe
		$template_path = 'xlocate/';
	}

	if ( ! $default_path ) {
		$default_path = XLOC_DIR_PATH . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);
	// Get default template/
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return $template;
}

// add_action('init', function(){
// 	print_pre( rel_locate_template('map-listing.php') ); die;
// });

/**
 * Return the HTML structure of tool tip text.
 * @param  $tool_tip_text The help text to be displayed.
 * @return String         The HTML structure with help text to be displayed.
 */
function xlocate_the_tool_tip( $tool_tip_text ) {
	if ( ! empty( $tool_tip_text ) ) {
		printf( '<div class="tooltip">
					<span class="tooltiptext">%s</span>
				</div>', $tool_tip_text );
	}
}