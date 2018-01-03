<?php
if ( empty( $value ) && ! is_numeric( $value ) ) {
	$src = '<img src="' . esc_url( ES_NO_IMAGE_URL ) . '">';
} else {
	$src = '<img src="' . esc_url( wp_get_attachment_image_url( $value, 'thumbnail' ) ) . '">';
}

?>
<div class="file"><?php echo $src; ?></div>
<div>
  <input type="hidden" name="easy[<?php echo $name ?>]" value="<?php echo $value ?>"/>
  <button type="button" class="upload_image_button button button-primary" data-type="file" title="добавить изображение"
          style="width: 126px;">
	  <?php _e( 'Add', 'easy-start' ) ?>
  </button>
  <button type="button" class="remove_image_button button" title="удалить изображение" data-no-image="<?php echo esc_url( ES_NO_IMAGE_URL ) ?>">&times;</button>
</div>