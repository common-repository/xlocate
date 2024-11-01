<?php

class xlocate_Location {
	public static $instance = null;
	private $table_name = null;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . "xloc_locations";
		add_action( 'save_post_house', array( $this, 'on_xlocate_marker_update' ) );
		add_action( 'delete_post', array( $this, 'delete_location' ) );
	}

	//check if post_id has already been saved
	//might need another field for wpml
	public function get_product_location_id( $post_id ) {
		global $wpdb;
		$table_name = $this->table_name;
		$post_id    = (int) $post_id;
		if ( ! $post_id ) {
			return false;
		}
		$sql    = $wpdb->prepare( "SELECT post_id FROM $table_name WHERE `post_id` = %d LIMIT 1", $post_id );
		$result = $wpdb->get_row( $sql );

		return $result->post_id;
	}

	//add a location with corresponding post id;
	public function add_location( $post_id, $latitude, $longitude ) {
		global $wpdb;
		$post_id = (int) $post_id;
		if ( ! $post_id ) {
			return false;
		}

		if ( ( ! isset( $latitude ) || empty( $latitude ) ) || ( ! isset( $longitude ) || empty( $longitude ) ) ) {
			return false;
		}
		$table_name = $this->table_name;
		$data       = array(
			'post_id'   => $post_id,
			'latitude'  => $latitude,
			'longitude' => $longitude,

		);

		return $wpdb->insert( $table_name, $data );
	}

	public function update_location( $post_id, $latitude, $longitude ) {
		global $wpdb;
		$table_name = $this->table_name;
		$post_id    = (int) $post_id;
		if ( ! $post_id ) {
			return false;
		}

		if ( ( ! isset( $latitude ) || empty( $latitude ) ) || ( ! isset( $longitude ) || empty( $longitude ) ) ) {
			return false;
		}
		$where  = array( 'post_id' => $post_id );
		$update = $wpdb->update( $table_name,
			array(
				'latitude'  => $latitude,
				'longitude' => $longitude,
			),
			$where
		);
	}

	function delete_location( $post_id ) {
		global $wpdb;
		$post_id    = (int) $post_id;
		$table_name = $this->table_name;
		$sql        = $wpdb->prepare( "SELECT * FROM $table_name WHERE post_id = %d", $post_id );
		if ( ! $wpdb->get_row( $sql ) ) {
			return false;
		}
		$result = $wpdb->delete( $table_name, array( 'post_id' => $post_id ) );
	}

	function product_saved( $post_id, $latitude, $longitude ) {
		$prev_id = $this->get_product_location_id( $post_id );

		if ( ! $prev_id ) {
			$result = $this->add_location( $post_id, $latitude, $longitude );
		} else {
			$result = $this->update_location( $post_id, $latitude, $longitude );
		}
	}

	public function on_xlocate_marker_update( $post_id ) {

		$nonce = filter_input( INPUT_POST, 'xlocate_map_nonce' );
		if ( $nonce && wp_verify_nonce( $nonce, 'verify_xlocate_map_nonce' ) ) {

			if ( wp_is_post_revision( $post_id ) ) {
				return;
			}

			$latitude          = sanitize_text_field( number_format( filter_input( INPUT_POST, 'lat' ), 6 ) );
			$longitude         = sanitize_text_field( number_format( filter_input( INPUT_POST, 'lng' ), 6 ) );
			$formatted_address = sanitize_text_field( filter_input( INPUT_POST, 'formatted_address' ) );

			/*Update Post Meta Needs Validations*/
			update_post_meta( $post_id, 'xlocate_lat', $latitude );
			update_post_meta( $post_id, 'xlocate_lng', $longitude );
			update_post_meta( $post_id, 'xlocate_formatted_address', $formatted_address );

			if ( $latitude && $longitude ) {
				$this->product_saved( $post_id, $latitude, $longitude );
			}

		}

	}

}

add_action( 'plugins_loaded', array( 'xlocate_Location', 'get_instance' ) );