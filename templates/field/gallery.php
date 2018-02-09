<div class="es-meta-gallery">
  <p><?php _e( 'You can drag and drop the images changing their position.', 'easy-start' ) ?></p>
  <button class="es-add-gallery button"
          data-name="<?php echo $name ?>"><?php _e( 'Add images', 'easy-start' ) ?></button>
  <div class="es-gallery-wrapper">
	  <?php if ( ! empty( $value ) ): ?>
		  <?php foreach ( $value as $key => $img ): ?>
			  <?php $img_url = wp_get_attachment_image_url( $img, 'full' ) ?>
          <div class="item" style="background-image:url(<?php echo esc_attr( $img_url ) ?>);">
            <a class="es-image-delete" href="#"
               aria-label="<?php esc_attr_e( 'Delete image', 'easy-start' ) ?>"><?php esc_html_e( 'remove', 'easy-start' ) ?></a>
            <input type="hidden" name="easy[<?php echo esc_attr( $name ) ?>][]"
                   value="<?php echo esc_attr( $img ) ?>">
          </div>
		  <?php endforeach; ?>
	  <?php endif; ?>

  </div>
</div>