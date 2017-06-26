<?php
if ( empty( $value ) ||  !is_numeric($value) ) {
	$src = '<img src="' . ES_DIR_URL . '"img/no-image.png">';
} else {
	$src = wp_get_attachment_link( $value, 'thumbnail', false, true, null );
}

?>
<div class="file"><?php echo $src; ?></div>
<div>
    <input type="hidden" name="easy[<?php echo $name ?>]" value="<?php echo $value ?>"/>
    <button type="button" class="upload_image_button button"
            data-type="file"><?php _e( 'Add', 'easy-start' ) ?></button>
    <button type="button" class="remove_image_button button">&times;</button>
</div>