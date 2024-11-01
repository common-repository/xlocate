<?php

class xlocate_Admin_Shortcodes_Settings {
	public $id;
	public $label;

	public function __construct() {
		$this->id    = 'shortcodes';
		$this->label = "Shortcodes";
		add_filter( 'xlocate_settings_tabs_array', array( $this, 'add_tabs' ) );
		add_action( 'xlocate_settings_' . $this->id, array( $this, 'show_fields' ) );
	}

	public function add_tabs( $tabs ) {
		$tabs[ $this->id ] = $this->label;

		return $tabs;
	}

	public function show_fields() {
		require_once( XLOC_DIR_PATH . '/admin/views/shortcodes.php' );
	}
}

new xlocate_Admin_Shortcodes_Settings();