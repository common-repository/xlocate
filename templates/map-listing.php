<?php
$lat           = sanitize_text_field(filter_input( INPUT_GET, 'lat' ));
$lng           = sanitize_text_field(filter_input( INPUT_GET, 'lng' ));
$xlocate_opts  = get_option( 'xlocate_settings' );
$skin_selected = isset( $xlocate_opts['skin'] ) ? $xlocate_opts['skin'] : '';
	$skin = 'xlocate-skin-' . $skin_selected;
// give others to add custom classes
$xlocate_listing_wrapper_class = apply_filters( 'xlocate_listing_wrapper_class', array( $skin ) );
?>
<div id="xlocate-listing-wrapper" class="<?php echo implode( ' ', $xlocate_listing_wrapper_class ); ?>">
    <noscript><?php _e( 'Locator Plugin Requires Javascript to function, please enable Javascript', 'xlocated' ); ?></noscript>
    <div id="search-wrapper">
        <form id="search-form">
            <input type="text" id="search-address" name="search" title="Enter Address">
            <input type="hidden" id="lat" name="lat" value="<?php echo esc_attr($lat); ?>">
            <input type="hidden" id="lng" name="lng" value="<?php echo esc_attr($lng); ?>">
            <span id="find-location"></span>
            <select id="xlocate-category" name="xlocate-category" title="Select by Category">
                <option value="">Select Category</option>
				<?php
				$args  = array( 'hide_empty' => false );
				$terms = get_terms( 'xlocate-category', $args );
				foreach ( $terms as $term ) {
					echo '<option value="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</option>';
				}
				?>
            </select>
            <select name="search_radius" title="Search Radius">
				<?php
				for ( $i = 5; $i <= 100; $i = $i + 5 ) {
					$selected = ( ! empty( $xlocate_opts ) && $xlocate_opts['default_radius'] == $i ) ? 'selected' : false;
					echo '<option ' . $selected . ' value="' . $i . '">' . $i . '</option>';
				}
				?>
            </select>
            <input type="submit" name="submit" value="Filter">
        </form>

        <div id="out"></div>
        <div id="mobile-map-view-option">
            <div id="list-view" class="map-view">
                <a href="#"> List </a>
            </div>
            <div id="map-view" class="map-view">
                <a href="#"> Map </a>
            </div>
            <div style="clear:both"></div>
        </div>
    </div><!-- search wrapper -->
    <div id="xlocate-estate-listing">
        <p>Start searching to see Results.</p>
        <!-- content will be generated via javascript -->
    </div>
    <div id="map_wrapper">
        <div id="xlocate-loader">
            <div class="windows8">
                <div class="wBall" id="wBall_1">
                    <div class="wInnerBall"></div>
                </div>
                <div class="wBall" id="wBall_2">
                    <div class="wInnerBall"></div>
                </div>
                <div class="wBall" id="wBall_3">
                    <div class="wInnerBall"></div>
                </div>
                <div class="wBall" id="wBall_4">
                    <div class="wInnerBall"></div>
                </div>
                <div class="wBall" id="wBall_5">
                    <div class="wInnerBall"></div>
                </div>
            </div>
        </div>
        <div id="map_canvas" class="mapping"></div>
    </div>
    <div style="clear: both"></div>
    <div class="xlocate-pagination">
        <button id="xlocate-load-more" data-paged="1" data-maxpages="" style="display: none">Load More...</button>
    </div>
</div><!-- close the door -->