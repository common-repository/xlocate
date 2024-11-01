<?php

class xlocate_Admin_Menus {

	public $tabs = array();
	public static $message = '';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
	}

	public function admin_menu_page() {
		$plugin_hook_suffix = add_menu_page(
			'xLocate',
			'xLocate',
			'manage_options',
			'page-xlocate',
			array( $this, 'generate_admin_page' ),
			'dashicons-location-alt',
			50
		);
	}

	/**
	 * Init the settings page.
	 */
	public function generate_admin_page() {
		$this->get_settings_page();
		$this->save_settings();
		$this->tabs = apply_filters( 'xlocate_settings_tabs_array', $this->tabs );
		require_once( XLOC_DIR_PATH . '/admin/views/admin.php' );
	}

	/**
	 * Save the settings page.
	 */
	public function save_settings() {
		$current_tab = ( isset( $_GET['tab'] ) ) ? sanitize_title( $_GET['tab'] ) : 'general';
		do_action( 'xlocate_save_' . $current_tab );

	}

	public function get_settings_page() {
		$settings[] = require_once( XLOC_DIR_PATH . '/admin/classes/class-xlocate-admin-general-settings.php' );
		$settings[] = require_once( XLOC_DIR_PATH . '/admin/classes/class-xlocate-admin-help-settings.php' );
		$settings[] = require_once( XLOC_DIR_PATH . '/admin/classes/class-xlocate-admin-pages-settings.php' );
		$settings[] = require_once( XLOC_DIR_PATH . '/admin/classes/class-xlocate-admin-shortcodes-settings.php' );
		$settings[] = require_once( XLOC_DIR_PATH . '/admin/classes/class-xlocate-admin-skins-settings.php' );
	}


	/**
	 * Add contextual help tag to the screen.
	 * @return string Path to contextual-help.php
	 */
	public function generate_contextual_help_page() {
		require_once( XLOC_DIR_PATH . '/admin/views/contextual-help.php' );
	}

	public static function get_message() {
		return self::$message;
	}

	public static function set_message( $class, $message ) {
		self::$message = '<div class=' . $class . '><p>' . $message . '</p></div>';
	}

	public function load_scripts( $hook ) {
		if ( 'toplevel_page_page-xlocate' == $hook ) {
			wp_enqueue_script( 'xloc-map-meta' );
		}
	}
}

new xlocate_Admin_Menus();