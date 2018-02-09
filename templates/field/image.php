<div class="file">
	<?php
	if ( empty( $value ) ) {
		echo '<img src="' . esc_attr( ES_NO_IMAGE_URL ) . '">';
	} elseif ( ! is_numeric( $value ) ) {
		echo '<img src="' . esc_attr( $value ) . '">';
	} else {
		echo wp_get_attachment_image( $value, 'thumbnail' );
	}
	?>
</div>
<div>
  <input type="hidden" name="<?php es_field_name( $name, $args['index'], $args['subfield'] ) ?>" <?php echo $data ?>
         value="<?php echo esc_attr( $value ) ?>"/>
  <button type="button" class="upload_image_button button button-primary" data-action="select-image"
          title="добавить изображение"
          style="width: 126px;">
	  <?php esc_attr_e( 'Add', 'easy-start' ) ?>
  </button>
  <button type="button" class="remove_image_button button" title="удалить изображение"
          data-no-image="<?php echo esc_attr( ES_NO_IMAGE_URL ) ?>" data-action='remove-atachment'>&times;
  </button>
</div>