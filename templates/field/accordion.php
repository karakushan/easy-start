<div class="es-rows">
	<?php if ( ! empty( $value ) ): ?>
		<?php foreach ( $value as $key => $val ): ?>
            <div class="es-form-row">
                <a href="#" class="remove">&#9746; <?php _e('remove','easy-start') ?></a>
                <div class="es-section-id"><?php _e( 'Section', 'easy-start' ) ?> #<span><?php echo $key+1 ?></span></div>
                <div class="acc-title">
                    <input type="text" name="easy[<?php echo $name ?>][<?php echo $key ?>][title]"
                           class="es-input es-acc-title"
                           placeholder="<?php _e( 'Section title', 'easy-start' ) ?>" value="<?php echo $val['title'] ?>">
                </div>
                <div class="acc-subtitle">
                    <input type="text" name="easy[<?php echo $name ?>][<?php echo $key ?>][subtitle]"
                           class="es-input es-acc-subtitle"
                           placeholder="<?php _e( 'Section subtitle', 'easy-start' ) ?>" value="<?php echo $val['subtitle'] ?>">
                </div>

                <div class="acc-body">
            <textarea name="easy[<?php echo $name ?>][<?php echo $key ?>][text]" rows="8"
                      placeholder="<?php _e( 'Section text', 'easy-start' ) ?>" class="es-acc-desc"><?php echo $val['text'] ?></textarea>
                </div>
            </div>
		<?php endforeach; ?>
	<?php else: ?>
        <div class="es-form-row">
            <div class="es-section-id"><?php _e( 'Section', 'easy-start' ) ?> #<span>1</span></div>
            <a href="#" class="remove"><?php _e('remove','easy-start') ?></a>
			<?php _e( 'Section title', 'easy-start' ) ?>
            <div class="acc-title">
                <input type="text" name="easy[<?php echo $name ?>][0][title]" class="es-input es-acc-title"
                       placeholder="<?php _e( 'Section title', 'easy-start' ) ?>">
            </div>
            <div class="acc-subtitle">
                <input type="text" name="easy[<?php echo $name ?>][0][subtitle]"
                       class="es-input es-acc-subtitle"
                       placeholder="<?php _e( 'Section subtitle', 'easy-start' ) ?>" value="<?php echo $val['subtitle'] ?>">
            </div>

            <div class="acc-body">
            <textarea name="easy[<?php echo $name ?>][0][text]" rows="8"
                      placeholder="<?php _e( 'Section text', 'easy-start' ) ?>" class="es-acc-desc"></textarea>
            </div>
        </div>
	<?php endif; ?>


</div>
<div class="es-button-row">
    <button type="button" class="button"
            data-action="es-add-field"><?php _e( 'Add new section', 'easy-start' ) ?></button>
</div>