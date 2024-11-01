<?php
// Ensure that ThickBox is loaded
add_thickbox();
$skin = '';
if ( isset( $this->settings_skins['skin'] ) && ! empty( $this->settings_skins['skin'] ) ) {
	$skin = $this->settings_skins['skin'];
}
$skin_nonce = wp_create_nonce( 'verify_xLocate_skin_nonce' );
?>

<table class="skins form-table">
    <tr>
        <th><label for="skin">Skins</label></th>
        <td>

            <ul>
                <li>
                    <a href="#TB_inline?width=auto&height=auto&inlineId=skin-default" title="Default" class="thickbox">

                        <label for="default">
                            <img src="<?php echo XLOC_DIR_URL; ?>assets/admin/images/skin-default.png" width="300px">
                            <span><?php echo ( 'default' == $skin ) ? '<b>Active</b> : ' : ''; ?>Default</span>
                        </label>

                        <div id="skin-default" style="display:none;">
                            <img src="<?php echo XLOC_DIR_URL; ?>assets/admin/images/skin-default.png">
                            <form id="" class="skins" action="" method="post">
								<?php // wp_nonce_field( 'verify_xLocate_skin_nonce', 'xLocate_skin_nonce' ); ?>
                                <input type="radio" name="skin" value="default" id="default" checked>
                                <input type="hidden" name="xLocate_skin_nonce" value="<?php echo $skin_nonce; ?>">
                                <input type="submit" class="button button-primary set-skin" value="Set Skin">
                            </form>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#TB_inline?width=auto&height=auto&inlineId=skin-aliceblue" title="AliceBlue"
                       class="thickbox">
                        <label for="aliceblue">
                            <img src="<?php echo XLOC_DIR_URL; ?>assets/admin/images/skin-aliceblue.png" width="300px">
                            <span><?php echo ( 'aliceblue' == $skin ) ? '<b>Active</b> : ' : ''; ?>AliceBlue </span>
                        </label>
                        <div id="skin-aliceblue" style="display:none;">
                            <img src="<?php echo XLOC_DIR_URL; ?>assets/admin/images/skin-aliceblue.png">
                            <form id="" class="skins" action="" method="post">
                                <input type="radio" name="skin" value="aliceblue" id="aliceblue" checked>
                                <input type="hidden" name="xLocate_skin_nonce" value="<?php echo $skin_nonce; ?>">
                                <input type="submit" class="button button-primary set-skin" value="Set Skin">
                            </form>
                        </div>
                    </a>
                </li>
            </ul>
        </td>
    </tr>
</table>
