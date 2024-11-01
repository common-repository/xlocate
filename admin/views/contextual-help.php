<?php
/**
 * The template for displaying contextual help tabs in admin screen.
 *
 * @see     #
 * @author  TheFifth
 * @package xLocate
 * @version 1.0.0
 */

$screen = get_current_screen();

if ( 'toplevel_page_page-xlocate' != $screen->id ) {
	return;
}

$screen->add_help_tab( array(
	'id'      => 'xlocate_help_tab',
	'title'   => __( 'Help &amp; Support', 'xlocate' ),
	'content' =>
		'<h2>' . __( 'Help &amp; Support', 'xlocate' ) . '</h2>' .
		'<p>' . sprintf(
			__( 'Should you need help understanding, using, or extending xLocate, <a href="%s">please read our documentation</a>. You will find all kinds of resources including snippets, tutorials and much more.', 'xlocate' ),
			'#'
		) . '</p>',
) );

$screen->set_help_sidebar(
	'<p><strong>' . __( 'For more information:', 'xlocate' ) . '</strong></p>' .
	'<p><a href="#" target="_blank">' . __( 'About xLocate', 'xlocate' ) . '</a></p>'
);
