<?php

class xlocate_Admin_Settings {

	/**
	 * Setting message.
	 *
	 * @var string
	 */
	private static $messages = array();

	/**
	 * Setting pages.
	 *
	 * @var array
	 */
	private static $settings = array();

	/**
	 * Settings page.
	 *
	 * Handles the display of the main xlocate settings page in admin.
	 */
	public static function xloc_output() {

		global $current_section, $current_tab;

		// enqueue needed scripts here (optional)

		// Include settings pages
		self::xloc_get_settings_pages();

		// Get current tab/section
		$current_tab = empty( $_GET['tab'] ) ? 'general' : sanitize_title( $_GET['tab'] );

		// $current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( $_REQUEST['section'] );\

		$tabs = array(
			'settings'   => 'General Settings',
			'shortcodes' => 'Shortcodes',
			'pages'      => 'Pages',
			'skins'      => 'Skins'
		);


		$tabs = apply_filters( 'xlocate_settings_tabs_array', $tabs );

		include( XLOC_DIR_PATH . '/admin/views/admin.php' );
	}

	/**
	 * Include the settings page classes.
	 */
	public static function xloc_get_settings_pages() {
		if ( empty( self::$settings ) ) {
			$settings = array();

//			include_once( dirname( __FILE__ ) . '/settings/class-wc-settings-page.php' );

			$settings[] = include( 'settings/class-xlocate-settings-general.php' );
//			$settings[] = include( 'settings/class-wc-settings-products.php' );
//			$settings[] = include( 'settings/class-wc-settings-tax.php' );
//			$settings[] = include( 'settings/class-wc-settings-shipping.php' );
//			$settings[] = include( 'settings/class-wc-settings-checkout.php' );
//			$settings[] = include( 'settings/class-wc-settings-accounts.php' );
//			$settings[] = include( 'settings/class-wc-settings-emails.php' );
//			$settings[] = include( 'settings/class-wc-settings-integrations.php' );
//			$settings[] = include( 'settings/class-wc-settings-api.php' );

//			self::$settings = apply_filters( 'xloc_get_settings_pages', $settings );
		}

		return self::$settings;
	}

	public static function get_message( $text = '' ) {
		self::$messages[] = $text;
	}
}