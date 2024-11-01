<?php
$pages_arr = array(
	'sort_order'  => 'asc',
	'sort_column' => 'post_title',
	'post_status' => 'publish'
);
$pages     = get_pages( $pages_arr );
?>

<form id="xloc-pages-settings-form" class="" action="" method="post" data-xloc-validate="true">
	<?php wp_nonce_field( 'verify_xLocate_settings_pages_nonce', 'xLocate_settings_pages_nonce' ); ?>
    <table class="form-table">
        <tr>
            <th><label for="search-result-page">Search Result Page</label></th>
            <td>
                <select name="search-result-page" id="search-result-page">
					<?php
					foreach ( $pages as $page ) {
					    $selected = !empty($this->settings_pages['search_result_page']) && $this->settings_pages['search_result_page'] == $page->ID ? 'selected' : false;
						$option = '<option ' . $selected . ' value="' . $page->ID . '">';
						$option .= $page->post_title;
						$option .= '</option>';
						echo $option;
					}
					?>
                </select>
                <p class="description" id="tagline-description">Insert [xlocate_map] in the choosen page if you have selected a different page than the default one.</p>
            </td>
        </tr>
    </table>
    <p class="submit">
        <input type="submit" class="button button-primary" value="Save"/>
    </p>
</form>