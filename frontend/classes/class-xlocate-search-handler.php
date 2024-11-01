<?php
/**
 * Class xlocateSearchHandler
 */
class xlocateSearchHandler {
	public static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		add_action( 'wp_ajax_get_xlocate_map', array( $this, 'get_xlocate_map' ) );
		add_action( 'wp_ajax_nopriv_get_xlocate_map', array( $this, 'get_xlocate_map' ) );
		add_action( 'pre_get_posts', array( $this, 'xlocate_search' ) );
	}

	public function get_xlocate_map() {
		$paged = filter_input( INPUT_GET, 'paged' );

		$response = array( 'listing' => '', 'mapData' => '' );
		//$listing             = get_xlocate_query_listing( $paged );
		$marker     = array();
		$infowindow = array();
		//$response['listing'] = $listing;
		$response['mapData'] = array();
		$args                = array(
			// may be need to be more inclusive of different post types
			'post_type'      => 'house',
			// needs to be uniformity of posts per page if results need to match throughout the code @Darth Vader
			'posts_per_page' => 10,
			'xlocate_query'  => true,
		);

		$args['paged'] = $paged;

		if ( $paged ) {
			$response['paginated'] = true;
		}

		add_filter( 'posts_clauses', array( $this, 'xlocate_query_clauses' ) );
		$xlocate_results = new WP_Query( $args );
		//This may not work need to double triple check @darth vader
		remove_filter( 'posts_clauses', array( $this, 'xlocate_query_clauses' ) );
		if ( $xlocate_results->have_posts() ) {
			while ( $xlocate_results->have_posts() ): $xlocate_results->the_post();

				$post_id = get_the_id();
				$title   = get_the_title();
				//$content = get_the_content();

				$lat     = get_post_meta( $post_id, 'xlocate_lat', true );
				$lng     = get_post_meta( $post_id, 'xlocate_lng', true );
				$address = get_post_meta( $post_id, 'xlocate_formatted_address', true );

				$marker[]           = array( $address, $lat, $lng );
				$infowindow_content = '<div class="info_content"><h3>' . $title . '</h3>' . '<p>' . $address . '</p></div>';
				$infowindow[]       = array( $infowindow_content );
				ob_start();
				?>
                <div class="xlocate-list">
                    <div class="xlocate-estate-image">
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail();
						}
						?>
                    </div>
                    <div class="xlocate-description">
                        <h3><?php echo get_the_title(); ?></h3>
                        <p><?php echo get_the_excerpt(); ?></p>
                        <a class="xlocate-detail" href="<?php the_permalink(); ?>">See Details</a>
                    </div>
                </div>
				<?php
				$response['listing'] .= ob_get_clean();
			endwhile;
			wp_reset_postdata();
			$response['mapData']  = array(
				'markers'           => $marker,
				'infowindowcontent' => $infowindow,
			);
			$response['maxpages'] = $xlocate_results->max_num_pages;
		} else {
			$response['listing'] = '<p>' .esc_html('Sorry, No results found !') . '</p>';
			$response['hide_map'] = 1;
		}

		wp_send_json( $response );
	}

	public function xlocate_search( $query ) {
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			return $query;
		} else {
			$is_xlocate_query = $query->get( 'xlocate_query' );
			if ( $is_xlocate_query ) {

				/* Filter by Category */
				$category_id = filter_input( INPUT_POST, 'xlocate-category' );
				if ( $category_id != '' ) {
					$tax_query = array(
						array(
							'taxonomy' => 'xlocate-category',
							'field'    => 'id',
							'terms'    => array( $category_id ),
						),
					);
					$query->set( 'tax_query', $tax_query );
				}
			}
		}

		return $query;
	}

	public function xlocate_query_clauses( $clauses ) {
		global $wpdb;

		//These Field will be set by $_GET requests or $_POST reuests
		//Depending upon ajax request or serach form request
		$latitude  = filter_input( INPUT_POST, 'lat' );
		$longitude = filter_input( INPUT_POST, 'lng' );
		$distance  = filter_input( INPUT_POST, 'search_radius' );

		$distance = ! empty( $distance ) ? $distance : 5;
		#die( var_dump( $latitude ) );

		$general_settings = get_option( 'xlocate_settings' );
		if( $general_settings['default_radius_type'] == "miles" ) {
			$search_unit = 3959; //Miles
        } else {
			$search_unit = 6371; //Kilometer
        }
		// To search by kilometers instead of miles, replace 3959 with 6371.

//https://developers.google.com/maps/documentation/javascript/store-locator#putting-it-all-together
// SELECT id, ( 3959 * acos( cos( radians(37) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(-122) ) + sin( radians(37) ) * sin( radians( lat ) ) ) ) AS distance FROM markers HAVING distance < 25 ORDER BY distance LIMIT 0 , 20;

		$db_fields = array(
			'',
			'latitude',
			'longitude'
		);

		$db_fields = implode( ', rstlocations.', $db_fields );

		$clauses['join']   .= " INNER JOIN {$wpdb->prefix}xloc_locations rstlocations ON $wpdb->posts.ID = rstlocations.post_id ";
		$clauses['fields'] .= $wpdb->prepare( "{$db_fields},
    ROUND( %d * acos( cos( radians( %s ) ) * cos( radians( rstlocations.latitude ) ) * cos( radians( rstlocations.longitude ) - radians( %s ) ) + sin( radians( %s ) ) * sin( radians( rstlocations.latitude) ) ),1 ) AS distance",
			array( $search_unit, $latitude, $longitude, $latitude ) );
		$clauses['where']  .= " AND ( rstlocations.latitude != 0.000000 && rstlocations.longitude != 0.000000 ) ";
		$clauses['having'] = $wpdb->prepare( "HAVING distance <= %s OR distance IS NULL", $distance );

		if ( ! empty( $clauses['having'] ) ) {

			if ( empty( $clauses['groupby'] ) ) {
				$clauses['groupby'] = $wpdb->prefix . 'posts.ID';
			}
			$clauses['groupby'] .= ' ' . $clauses['having'];
			unset( $clauses['having'] );
		}

		$clauses['orderby'] = "distance ASC";

		return $clauses;

	}
}

add_action( 'plugins_loaded', array( 'xlocateSearchHandler', 'get_instance' ) );