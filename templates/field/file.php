<?php
if ( empty( $value ) ||  !is_numeric($value) ) {
	$src = '<img src="'.esc_url(ES_NO_IMAGE_URL).'">';
} else {
	$src = wp_get_attachment_image($value,'thumbnail',true);
}

?>
<div class="file"><?php echo $src; ?></div>
<div>
    <input type="hidden" name="easy[<?php echo $name ?>]" value="<?php echo $value ?>"/>
    <button type="button" class="upload_image_button button button-primary" data-action="select-file"><?php _e( 'Add', 'easy-start' ) ?></button>
    <button type="button" class="remove_image_button button" data-no-image="<?php echo esc_url(ES_NO_IMAGE_URL) ?>" data-action='remove-atachment'>&times;</button>
</div>