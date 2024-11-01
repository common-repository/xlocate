<?php

class xlocate_Admin_Skins_Settings {
	public $id;
	public $label;
	public $settings_skins;

	public function __construct() {
		$this->id    = 'skins';
		$this->label = "Skins";
		add_filter( 'xlocate_settings_tabs_array', array( $this, 'add_tabs' ) );
		add_action( 'xlocate_settings_' . $this->id, array( $this, 'show_fields' ) );
		add_action( 'xlocate_save_' . $this->id, array( $this, 'save_fields' ) );
	}

	public function add_tabs( $tabs ) {
		$tabs[ $this->id ] = $this->label;

		return $tabs;
	}

	public function show_fields() {
		$this->settings_skins = get_option( 'xlocate_settings_skins' );
		require_once( XLOC_DIR_PATH . '/admin/views/skins.php' );
	}

	public function save_fields() {
		if ( isset( $_POST['xLocate_skin_nonce'] ) && wp_verify_nonce( $_POST['xLocate_skin_nonce'], 'verify_xLocate_skin_nonce' ) ) {
			$config_skin = array();
			if ( isset( $_POST['skin'] ) && ! empty( $_POST['skin'] ) ) {
				$config_skin['skin'] = sanitize_text_field($_POST['skin']);
			}

			update_option( 'xlocate_settings_skins', $config_skin );
			xlocate_Admin_Menus::set_message( 'updated', 'Skins Saved' );
		}
	}
}

new xlocate_Admin_Skins_Settings();