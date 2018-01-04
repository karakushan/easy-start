<div class="es-slider-wrapper">
  <div class="es-slides es-sort" data-name="<?php echo esc_attr( $name ) ?>">
	  <?php if ( ! empty( $value ) ): ?>
		  <?php $count = 0; ?>
		  <?php foreach ( $value as $k => $val ): ?> 
          <section data-index='<?php echo $count ?>'>
            <header>
              <span class="dashicons dashicons-plus" data-action='open-section'></span>
              <span class='count'><?php echo $count ?></span>
              <input name='easy[<?php echo esc_attr( $name ) ?>][<?php echo $count ?>][title]' class='title' value="<?php echo esc_attr($val['title']) ?>">
              <span class="dashicons dashicons-dismiss" data-action='remove-section'></span>
            </header>
            <div class='bottom'>
              <div class='image' title='добавить/заменить изображение'
                   style="background-image: url(<?php echo wp_get_attachment_image_url( $val['image'] ) ?>);"
                   data-action="select-image">
                <input type='hidden' name='easy[<?php echo esc_attr( $name ) ?>][<?php echo $count ?>][image]' class="image-input" value="<?php echo esc_attr($val['image']) ?>">
                <span class="dashicons dashicons-plus-alt"></span>
              </div>
              <textarea name='easy[<?php echo esc_attr( $name ) ?>][<?php echo $count ?>][text]' class='text'
                        placeholder='Ваш текст или HTMl код'><?php echo $val['text'] ?></textarea>
            </div>
          </section>
			  <?php $count ++; ?>
		  <?php endforeach; ?>
	  <?php endif; ?>


  </div>
  <button type="button" data-action="add-slide" class="button button-primary">добавить секцию</button>
</div>

