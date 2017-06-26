<div class="es-slider-wrapper">
    <button data-es-action="add-slider-row" data-name="<?php echo $name ?>" class="button"><?php _e('Add slider section','easy-start') ?></button>

    <?php if ( ! empty( $value ) ): ?>
		<?php foreach ( $value as $key => $img ): ?>
			<?php $img_url = wp_get_attachment_image_url( $img['image'], 'full' );
			$style         = 'background-image:url(' . $img_url . ');background-size: cover;';
			if ( empty( $img_url ) ) {
				$img_url = ES_DIR_URL . 'img/image-add-button.svg';
				$style   = 'background-image:url(' . $img_url . ');background-size: 63px;';
			}

			$editor_id = 'image_' . $key . '_text';
			?>
            <div class="item es-form-row">
                <a class="es-image-delete" data-es-action="row-remove" href="#"
                   aria-label="<?php _e( 'Delete image', 'easy-start' ) ?>"><?php _e( 'remove', 'easy-start' ) ?></a>

                <input type="hidden" name="easy[<?php echo $name ?>][<?php echo $key ?>][image]"
                       value="<?php echo $img['image'] ?>" class="image-id" data-type="image">
                <div class="item-left">

                    <div class="image-add" data-es-action="add-image" title="<?php _e( 'Add/edit image' ) ?>"
                         style="<?php echo $style ?>" >

                    </div>

                </div>
                <div class="item-right">

                    <p>

                        <label for=""><?php _e('Title','easy-start') ?></label>
                        <input type="text" name="easy[<?php echo $name ?>][<?php echo $key ?>][title]"
                               value="<?php echo $img['title'] ?>" data-type="title">
                    </p>
                    <p>
                        <label for="<?php echo $editor_id ?>"><?php _e('Description','easy-start') ?></label>
                        <textarea name="easy[<?php echo $name ?>][<?php echo $key ?>][text]"
                                  id="<?php echo $editor_id ?>"
                                  rows="8" data-type="text"><?php echo $img['text'] ?></textarea>
                    </p>
                </div>
                <div class="clear"></div>
            </div>
		<?php endforeach; ?>
	<?php endif; ?>

</div>

