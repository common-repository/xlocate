<?php

class xlocate_Admin_Pages_Settings {
	public $id;
	public $label;
	public $settings_pages;

	public function __construct() {
		$this->id    = 'pages';
		$this->label = "Pages";
		add_filter( 'xlocate_settings_tabs_array', array( $this, 'add_tabs' ) );
		add_action( 'xlocate_settings_' . $this->id, array( $this, 'show_fields' ) );
		add_action( 'xlocate_save_' . $this->id, array( $this, 'save_fields' ) );
	}

	public function add_tabs( $tabs ) {
		$tabs[ $this->id ] = $this->label;

		return $tabs;
	}

	public function show_fields() {
		$this->settings_pages = get_option( 'xlocate_settings_pages' );
		require_once( XLOC_DIR_PATH . '/admin/views/pages.php' );
	}

	public function save_fields() {
		if ( isset( $_POST['xLocate_settings_pages_nonce'] ) && wp_verify_nonce( $_POST['xLocate_settings_pages_nonce'], 'verify_xLocate_settings_pages_nonce' ) ) {
			$pages_config = array();
			if ( isset( $_POST['search-result-page'] ) && ! empty( $_POST['search-result-page'] ) ) {
				$pages_config['search_result_page'] = sanitize_text_field($_POST['search-result-page']);
			}

			update_option( 'xlocate_settings_pages', $pages_config );
			xlocate_Admin_Menus::set_message( 'updated', 'Pages Saved' );
		}

	}
}

new xlocate_Admin_Pages_Settings();