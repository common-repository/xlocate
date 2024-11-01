<?php
//Load Data
$api_key = '';
if ( isset( $this->settings['api_key'] ) && ! empty( $this->settings['api_key'] ) ) {
	$api_key = $this->settings['api_key'];
}
$default_radius_type = '';
if ( isset( $this->settings['default_radius_type'] ) && ! empty( $this->settings['default_radius_type'] ) ) {
	$default_radius_type = $this->settings['default_radius_type'];
}
$default_radius = '';
if ( isset( $this->settings['default_radius'] ) && ! empty( $this->settings['default_radius'] ) ) {
	$default_radius = $this->settings['default_radius'];
}
$default_latitude = '27.7090319';
if ( isset( $this->settings['default_latitude'] ) && ! empty( $this->settings['default_latitude'] ) ) {
	$default_latitude = $this->settings['default_latitude'];
}
$default_longitude = '85.2911132';
if ( isset( $this->settings['default_longitude'] ) && ! empty( $this->settings['default_longitude'] ) ) {
	$default_longitude = $this->settings['default_longitude'];
}
$default_zoom_level = '10';
if ( isset( $this->settings['default_zoom_level'] ) && ! empty( $this->settings['default_zoom_level'] ) ) {
	$default_zoom_level = $this->settings['default_zoom_level'];
}
$skin = '';
if ( isset( $this->settings['skin'] ) && ! empty( $this->settings['skin'] ) ) {
	$skin = $this->settings['skin'];
}
?>

<form id="xloc-general-settings-form" class="" action="" method="post" data-xloc-validate="true">
	<?php wp_nonce_field( 'verify_xLocate_settings_nonce', 'xLocate_settings_nonce' ); ?>
    <table class="form-table">
        <tr>
            <th><label for="api-key">API Key</label></th>
            <td>
                <input type="password" id="api-key" name="api-key" class="regular-text" value="<?php echo esc_attr($api_key); ?>"/> <a href="javascript:void(0);" onclick="toggle_password_api('api-key');" id="showhide">Show</a><?php xlocate_the_tool_tip( esc_html__( 'You can get your api key from developer console', 'xlocate' ) ); ?><p class="description" id="tagline-description">You can get your API key from developer console <a href="https://developers.google.com/maps/documentation/javascript/adding-a-google-map#step_3_get_an_api_key">Here</a>. Please remember, you need to enable both the “Google Maps JavaScript API” and “Google Places API Web Service” under Enable APIs.</p>
            </td>
        </tr>
        <tr>
            <th><label for="default-radius-type">Search Radius Unit</label></th>
            <td>
                <select id="default-radius-type" name="default-radius-type">
                    <option value="kms" <?php echo ( ! empty( $default_radius_type ) && $default_radius_type == 'kms' ) ? 'selected' : false; ?>>Kilometer</option>
                    <option value="miles" <?php echo ( ! empty( $default_radius_type ) && $default_radius_type == 'miles' ) ? 'selected' : false; ?>>Miles</option>
                </select>
                <p class="description" id="tagline-description">The search radius unit in Kilometer or Miles.</p>
            </td>
        </tr>
        <tr>
            <th><label for="default-radius">Default Search Radius</label></th>
            <td>
                <select id="default-radius" name="default-radius">
					<?php
					for ( $i = 5; $i <= 100; $i = $i + 5 ) {
						$selected = ( ! empty( $default_radius ) && $default_radius == $i ) ? 'selected' : false;
						echo '<option ' . $selected . ' value="' . $i . '">' . $i . '</option>';
					}
					?>
                </select>
                <p class="description" id="tagline-description">Specify the default search radius to search within.</p>
            </td>
        </tr>
        <tr>
            <th><label for="center-map">Center</label></th>
            <td>
                <input type="text" id="default-latitude" name="default-latitude" class="regular-text" value="<?php echo esc_attr($default_latitude); ?>" placeholder="Latitude"/>
                <input type="text" id="default-longitude" name="default-longitude" class="regular-text" value="<?php echo esc_attr($default_longitude); ?>" placeholder="Longitude"/>
                <p class="description" id="tagline-description">Center point for when the initial map loads.</p>
            </td>
        </tr>
        <tr>
            <th><label for="default-zoom-level">Default Zoom Level</label></th>
            <td>
                <input type="number" id="default-zoom-level" name="default-zoom-level" class="regular-text" value="<?php echo esc_attr($default_zoom_level); ?>"/>
                <p class="description" id="tagline-description">Set default map zoom level. From 0 to 20. Note: Zoom level 0 is the most zoomed out zoom level available and each integer step in zoom level halves the X and Y extents of the view and doubles the linear resolution.</p>
            </td>
        </tr>
    </table>
    <p class="submit">
        <input type="submit" class="button button-primary" value="save"/>
    </p>
</form>
