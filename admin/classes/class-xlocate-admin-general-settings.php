<?php

class xlocate_Admin_General_Settings {
	public $id;
	public $label;
	public $settings;

	public function __construct() {
		$this->id    = 'general';
		$this->label = "General Settings";
		add_filter( 'xlocate_settings_tabs_array', array( $this, 'add_tabs' ) );
		add_action( 'xlocate_settings_' . $this->id, array( $this, 'show_fields' ) );
		add_action( 'xlocate_save_' . $this->id, array( $this, 'save_fields' ) );
	}

	public function add_tabs( $tabs ) {
		$tabs[ $this->id ] = $this->label;

		return $tabs;
	}

	public function show_fields() {
		$this->settings = get_option( 'xlocate_settings' );
		require_once( XLOC_DIR_PATH . '/admin/views/settings.php' );
	}

	public function save_fields() {
		if ( ! isset( $_POST['xLocate_settings_nonce'] ) || ! wp_verify_nonce( $_POST['xLocate_settings_nonce'], 'verify_xLocate_settings_nonce' ) ) {
			return false;
		}

		$config = array();
		if ( isset( $_POST['api-key'] ) && ! empty( $_POST['api-key'] ) ) {
			$config['api_key'] = sanitize_text_field($_POST['api-key']);
		}
		if ( isset( $_POST['default-radius-type'] ) && ! empty( $_POST['default-radius-type'] ) ) {
			$config['default_radius_type'] = sanitize_text_field($_POST['default-radius-type']);
		}
		if ( isset( $_POST['default-radius'] ) && ! empty( $_POST['default-radius'] ) ) {
			$config['default_radius'] = sanitize_text_field(intval($_POST['default-radius']));
		}
		if ( isset( $_POST['default-latitude'] ) && ! empty( $_POST['default-latitude'] ) ) {
			$config['default_latitude'] = sanitize_text_field(floatval($_POST['default-latitude']));
		}
		if ( isset( $_POST['default-longitude'] ) && ! empty( $_POST['default-longitude'] ) ) {
			$config['default_longitude'] = sanitize_text_field(floatval($_POST['default-longitude']));
		}
		if ( isset( $_POST['default-zoom-level'] ) && ! empty( $_POST['default-zoom-level'] ) ) {
			$config['default_zoom_level'] = sanitize_text_field(intval($_POST['default-zoom-level']));
		}
		update_option( 'xlocate_settings', $config );
		xlocate_Admin_Menus::set_message( 'updated', 'Settings Saved' );

	}
}

new xlocate_Admin_General_Settings();