<select name="<?php es_field_name( $name, $args['index'], $args['subfield'] ) ?>" id="es-field-<?php echo esc_attr( $name ) ?>">
  <option value=""><?php echo $args['first_option'] ?></option>
	<? foreach ( $args['values'] as $key => $val ) {
		echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $value, 0 ) . '>' . esc_attr( $val ) . '</option>';
	} ?>
</select>