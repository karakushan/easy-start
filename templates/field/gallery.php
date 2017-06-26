<button class="es-add-gallery button" data-name="<?php echo $name ?>"><?php _e( 'Add images', 'easy-start' ) ?></button>
<div class="es-gallery-wrapper">
	<?php if ( ! empty( $value ) ): ?>
		<?php foreach ( $value as $key=>$img ): ?>
			<?php $img_url = wp_get_attachment_image_url( $img, 'full' ) ?>
            <div class="item" style="background-image:url(<?php echo $img_url ?>);">
                <a class="es-image-delete" href="#"
                   aria-label="<?php _e( 'Delete image', 'easy-start' ) ?>"><?php _e( 'remove', 'easy-start' ) ?></a>
                <input type="hidden" name="easy[<?php echo $name ?>][<?php echo $key ?>]"
                       value="<?php echo $img ?>">
            </div>
		<?php endforeach; ?>
	<?php endif; ?>

</div>
