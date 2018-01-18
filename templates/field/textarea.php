<div class="textarea-wrap">
  <textarea name="<?php es_field_name( $name, $args['index'], $args['subfield'] ) ?>"
            class="es_textarea"
	          <?php echo $data ?>
            id="<?php echo esc_attr( $args['id'] ) ?>"
            rows="<?php echo esc_attr( $args['textarea_rows'] ) ?>"
            placeholder="<?php echo esc_attr( $args['placeholder'] ) ?>"
            value="<?php echo esc_attr( $value ) ?>"><?php echo esc_attr( $value ) ?></textarea>
</div>

