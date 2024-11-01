<div class="wrap">
    <h1>xLocate</h1>

    <div class="updated xlocate-plugin-documentation-nag">
        <p>Please check our plugin documentation <a href="http://codemanas.com/docs">here</a>.</p>
    </div>

    <h2 class="nav-tab-wrapper">
		<?php do_action( 'add_settings_tab' ); ?>
		<?php

		foreach ( $this->tabs as $key => $value ) {

			if ( ! isset( $_GET['tab'] ) ) {
				if ( 'general' === $key ) {
					$active_class = 'nav-tab-active';
				} else {
					$active_class = '';
				}

			} else {
				if ( $_GET['tab'] === $key ) {
					$active_class = 'nav-tab-active';
				} else {
					$active_class = '';
				}
			}

			?>

            <a href="<?php echo add_query_arg( array(
				'page' => 'page-xlocate',
				'tab'  => $key
			), admin_url( 'admin.php' ) ); ?>" class="nav-tab <?php echo $active_class; ?>">
				<?php printf( __( '%s', 'xlocate' ), $value ); ?>
            </a>

			<?php
		}
		?>

    </h2>
    <div class="message">
		<?php
		$message = self::get_message();
		if ( isset( $message ) && ! empty( $message ) ) {
			echo $message;
		}
		?>
    </div>
	<?php
	$current_tab = ( isset ( $_GET['tab'] ) ) ? sanitize_title( $_GET['tab'] ) : 'general';
	do_action( 'xlocate_settings_' . $current_tab );
	?>
</div>