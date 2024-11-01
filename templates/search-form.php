<?php
$page_id      = get_option( 'xloc_search_page' );
$xlocate_opts = get_option( 'xlocate_settings' );
// search form to be used elsewhere
?>
<form id="xlocate-home-search-form" action="<?php echo get_permalink( $page_id ); ?>">
    <input type="text" id="search-address" name="search">
    <input type="hidden" id="lat" name="lat" value="">
    <input type="hidden" id="lng" name="lng" value="">
    <input type="hidden" id="action" name="action" value="xlocate_map">
    <span id="xlocate-frontend-find-location"></span>
    <input type="submit" name="submit" value="Search">
</form>
<script type="text/javascript">
    jQuery(function ($) {
        /*Search Autocomplete and Search*/
        $("#search-address").geocomplete({
            details: "#xlocate-home-search-form",
            //detailsAttribute: "data-geo"
        });
    });
</script>