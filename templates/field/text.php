<input type="text"
       name="<?php es_field_name( $name, $args['index'], $args['subfield'] ) ?>" <?php echo esc_attr( $data ) ?>
       id="<?php echo esc_attr( $args['id'] ) ?>"
	<?php if ( ! empty( $args['placeholder'] ) ): ?>
      placeholder="<?php echo esc_attr( $args['placeholder'] ) ?>"
	<?php endif; ?>
       class="es_text"
       value="<?php if ( ! empty( $value ) )
		   echo esc_attr( $value ) ?>">
