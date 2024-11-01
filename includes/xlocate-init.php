<?php

/**
 * Hello Main Class Here
 * I cannot be extended nor overridden
 *
 * @created by R2-D2
 * @since  1.0.0
 */
final class xlocate_Init {

	public function __construct() {
		add_action( 'init', array( $this, 'xloc_register_scripts' ) );
		add_filter( 'plugin_action_links_' . XLOC_BASENAME, array( $this, 'xloc_add_action_links' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'xloc_register_admin_scripts' ) );
	}

	protected static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			$className      = __CLASS__;
			self::$instance = new $className;
		}

		return self::$instance;
	}

	/**
	 * Run when plugin is activated
	 *
	 * @created by DarthVader
	 * @modified R2-D2
	 * @since  1.0.0
	 */
	public static function xloc_activate_plugin() {
		/* Register Post Type First */
		xloc_register_post_types();
		flush_rewrite_rules();

		/*create location table*/
		global $wpdb;
		$table_name      = $wpdb->prefix . "xloc_locations";
		$charset_collate = $wpdb->get_charset_collate();
		$sql             = "CREATE TABLE $table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    post_id bigint(20) NOT NULL,
    latitude float(10,6) NOT NULL,
    longitude float(10,6) NOT NULL,
    PRIMARY KEY id (id)
    ) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		/**
		 * create page with xlocate-shortcode in content
		 * @referrer Umesh
		 */
		$check_page_exists = get_option( 'xloc_search_page' );
		if ( ! $check_page_exists ) {
			$postarr = array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Search Results',
				'post_content' => '[xlocate_map]'
			);
			$post_id = wp_insert_post( $postarr );
			update_option( 'xlocate_settings_pages', array( 'search_result_page' => $post_id ) );
			update_option( 'xloc_search_page', $post_id );
		}

		$settings_exists = get_option('xlocate_settings');
		if( !empty($settings_exists) ) {
			$config = array();
			$config['default_radius_type'] = 'miles';
			$config['default_radius'] = 50;
			$config['default_latitude'] = '27.7090319';
			$config['default_longitude'] = '85.2911132';
			$config['default_zoom_level'] = 10;
			update_option( 'xlocate_settings', $config );
		}
	}

	/**
	 * Register scripts in front
	 * @created by DarthVader
	 * @modified R2-D2
	 * @since  1.0.0
	 */
	public function xloc_register_scripts() {
		$settings = get_option( 'xlocate_settings' );
		$key      = isset( $settings['api_key'] ) ? $settings['api_key'] : '';

		$src = 'https://maps.googleapis.com/maps/api/js?key=' . $key . '&libraries=places';

		wp_register_script( 'xloc-google-map', $src );
		wp_register_script( 'xloc-geocomplete', XLOC_DIR_URL . 'assets/shared/js/jquery.geocomplete.min.js', array(
			'jquery',
			'xloc-google-map'
		), XLOC_VERSION, true );
		wp_register_script( 'xloc-map-meta', XLOC_DIR_URL . 'assets/admin/js/admin-script.js', array(
			'jquery',
			'xloc-google-map',
			'xloc-geocomplete'
		), XLOC_VERSION, true );
		// Localize the script with new data
		wp_localize_script( 'xloc-map-meta', 'settings_data', $settings );

		wp_register_script( 'xloc-marker-clusterer', XLOC_DIR_URL . 'assets/frontend/js/markerclusterer.js', array( 'xloc-google-map' ), XLOC_VERSION, true );

		wp_register_script( 'xloc-map', XLOC_DIR_URL . 'assets/frontend/js/frontend-script.js', array(
			'jquery',
			'xloc-geocomplete',
			'xloc-marker-clusterer'
		), '1.0.0', true );

		wp_localize_script( 'xloc-map', 'settings_data', $settings );

		wp_register_style( 'xloc-map-css', XLOC_DIR_URL . 'assets/frontend/css/style.css', '', '', 'ALL' );

		wp_register_style( 'xloc-admin-general', XLOC_DIR_URL . '/assets/admin/css/general.css', '', '', 'ALL' );
		wp_register_style( 'xloc-admin-general-help', XLOC_DIR_URL . '/assets/admin/css/help.css', '', '', 'ALL' );
		wp_register_style( 'xloc-admin-skin', XLOC_DIR_URL . 'assets/admin/css/skin.css', '', '', 'ALL' );

		$xlocate_settings_skins = get_option( 'xlocate_settings_skins' );
		$skin                   = ( isset( $xlocate_settings_skins['skin'] ) ) ? $xlocate_settings_skins['skin'] : 'default';
		if ( 'default' != $skin ) {
			wp_register_style( 'xloc-skin', XLOC_DIR_URL . 'assets/frontend/css/skins/' . $skin . '.css', '', '', 'ALL' );
		}
	}

	/**
	 * Add Settings link in the plugins page
	 * @created by DarthVader
	 * @modified R2-D2
	 * @since  1.0.0
	 */
	public function xloc_add_action_links( $links ) {
		$mylinks = array(
			'<a href="' . admin_url( 'admin.php?page=page-xlocate' ) . '">Settings</a>',
		);

		return array_merge( $links, $mylinks );
	}

	/**
	 * Unistalling the plugin
	 * @created by DarthVader
	 * @modified R2-D2
	 * @since  1.0.0
	 */
	public static function xloc_uninstall_plugin() {
		global $wpdb;
		$table_name = $wpdb->prefix . "xloc_locations";
		$sql        = "DROP TABLE $table_name";
	}

	/**
	 * Registering and enqueing admin scripts
	 * @author anakin
	 * @since 1.0.0
	 */
	public static function xloc_register_admin_scripts( $hook ) {

		// Load only on ?page=mypluginname
		if ( $hook != 'toplevel_page_page-xlocate' ) {
			return;
		}

		// get xlocate settings tab
		if ( isset( $_GET['tab'] ) && 'skins' == $_GET['tab'] ) {
			wp_enqueue_style( 'xloc-admin-skin' );
		}
		
		wp_enqueue_style( 'xloc-admin-general' );
	}
}