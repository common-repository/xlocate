<?php

/**
 * Generate Map Meta Box on Post / Post Type
 */
class xlocate_Meta_Box {

	public function __construct() {
		if ( is_admin() ) {
			add_action( 'load-post.php', array( $this, 'xloc_init_metabox' ) );
			add_action( 'load-post-new.php', array( $this, 'xloc_init_metabox' ) );
		}
	}

	/**
	 * Meta box initialization.
	 */
	public function xloc_init_metabox() {
		add_action( 'add_meta_boxes', array( $this, 'xloc_add_metabox' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'xloc_load_scripts' ) );
	}

	/**
	 * Load Neccessary Scripts
	 */
	public function xloc_load_scripts() {
		$screen = get_current_screen();

		$post_types = apply_filters( 'show_xlocate_meta_in_posts', array( 'house' ) );

		foreach ( $post_types as $post_type ) {
			# code...
			if ( $screen->post_type == $post_type ) {

				wp_enqueue_script( 'xloc-google-map' );
				wp_enqueue_script( 'xloc-geocomplete' );
				wp_enqueue_script( 'xloc-map-meta' );

			}
		}
	}

	/**
	 * Adds the meta box.
	 */
	public function xloc_add_metabox() {

		$post_types = apply_filters( 'show_xlocate_meta_in_posts', array( 'house' ) );
		add_meta_box(
			'xlocate-location',
			__( 'Property Location', 'xlocate' ),
			array( $this, 'xloc_render_metabox' ),
			$post_types,
			'normal',
			'high'
		);
	}

	/**
	 * Renders the meta box.
	 */
	public function xloc_render_metabox( $post ) {
		// Add nonce for security and authentication.

		wp_nonce_field( 'verify_xlocate_map_nonce', 'xlocate_map_nonce', true );
		$lat               = get_post_meta( $post->ID, 'xlocate_lat', true );
		$lng               = get_post_meta( $post->ID, 'xlocate_lng', true );
		$formatted_address = get_post_meta( $post->ID, 'xlocate_formatted_address', true );
		?>
        <style type="text/css">
            @font-face {
                font-family: 'fontello';
            <?php $font_url = XLOC_DIR_URL . 'assets/shared/font/'; ?>
                src: url('<?php echo $font_url; ?>fontello.eot?27526776');
                src: url('<?php echo $font_url; ?>fontello.eot?27526776#iefix') format('embedded-opentype'),
                url('<?php echo $font_url; ?>fontello.woff2?27526776') format('woff2'),
                url('<?php echo $font_url; ?>fontello.woff?27526776') format('woff'),
                url('<?php echo $font_url; ?>fontello.ttf?27526776') format('truetype'),
                url('<?php echo $font_url; ?>fontello.svg?27526776#fontello') format('svg');
                font-weight: normal;
                font-style: normal;
            }

            /* Chrome hack: SVG is rendered more smooth in Windozze. 100% magic, uncomment if you need it. */
            /* Note, that will break hinting! In other OS-es font will be not as sharp as it could be */
            /*
			@media screen and (-webkit-min-device-pixel-ratio:0) {
			  @font-face {
				font-family: 'fontello';
				src: url('../font/fontello.svg?27526776#fontello') format('svg');
			  }
			}
			*/
            [class^="icon-"]:before, [class*=" icon-"]:before {
                font-family: "fontello", sans-serif;
                font-style: normal;
                font-weight: normal;
                speak: none;

                display: inline-block;
                text-decoration: inherit;
                width: 1em;
                margin-right: .2em;
                text-align: center;
                /* opacity: .8; */

                /* For safety - reset parent styles, that can break glyph codes*/
                font-variant: normal;
                text-transform: none;

                /* fix buttons height, for twitter bootstrap */
                line-height: 1em;

                /* Animation center compensation - margins should be symmetric */
                /* remove if not needed */
                margin-left: .2em;

                /* you can be more comfortable with increased icons size */
                /* font-size: 120%; */

                /* Font smoothing. That was taken from TWBS */
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;

                /* Uncomment for 3D effect */
                /* text-shadow: 1px 1px 1px rgba(127, 127, 127, 0.3); */
            }

            .icon-location:before {
                content: '\e800';
            }

            .search-location-wrapper {
                position: relative;
            }

            #search-address {
                height: 50px;
            }

            #find-location {
                position: absolute;
                top: 0;
                right: 50px;
                height: 50px;
                width: 50px;
                background: transparent;
                color: red;
                font-size: 0;
                cursor: pointer;
                border: none;
            }

            #clear-location-field {
                position: absolute;
                top: 0;
                right: 0;
                height: 51px;
                width: 50px;
                background: transparent;
                color: red;
                font-size: 0;
                cursor: pointer;
                border: none;
            }

            #find-location:before {
                position: absolute;
                font-family: "fontello";
                content: '\e800';
                left: 50%;
                top: 50%;
                font-size: 20px;
                line-height: 1;
                transform: translate(-50%, -50%);
            }

            #clear-location-field:before {
                position: absolute;
                font-family: "fontello";
                content: '\270e';
                left: 50%;
                top: 50%;
                font-size: 20px;
                line-height: 1;
                transform: translate(-50%, -50%);
            }
        </style>
        <style type="text/css">
            #search-address {
                width: 100%;
            }
        </style>
        <div id="xlocate-map-wrapper" class="wrap">
            <div class="search-location-wrapper">
                <input type="text" id="search-address" name="search-address" value="<?php echo esc_attr( $formatted_address ); ?>">
                <a href="#" id="find-location" title="Use Current Location">Get Current Location</a>
                <a href="javascript:void(0);" id="clear-location-field" title="Clear Location Field" onclick="xloc_cleartext();">Clear Field</a>
            </div>
            <div id="out"></div>
            <input type="hidden" data-geo="lat" id="lat" name="lat" value="<?php echo esc_attr($lat); ?>">
            <input type="hidden" data-geo="lng" id="lng" name="lng" value="<?php echo esc_attr($lng); ?>">
            <input type="hidden" data-geo="formatted_address" id="formatted_address" name="formatted_address"
                   value="<?php echo esc_attr($formatted_address); ?>">

            <div id="map-wrapper" style="height: 400px;">
                <div id="map-canvas" style="height: 100%">
                </div>
                <!-- script needs to go on external file -->
            </div>
        </div>
		<?php
	}

}

new xlocate_Meta_Box();