<?php if ( $args['post_type'] == 'page' ): ?>
	<?php wp_dropdown_pages( array(
		'selected' => esc_attr( $value ),
		'name'     => 'easy[' . esc_attr( $name ) . ']'
	) ); ?>
<?php else: ?>
  <select name="easy[<?php echo esc_attr( $name ) ?>]" id="es-field-<?php echo esc_attr( $name ) ?>">
    <option value=""><?php echo esc_attr( $args['first_option'] ) ?></option>
	  <? foreach ( $args['values'] as $key => $post ) {
		  echo '<option value="' . esc_attr( $post->ID ) . '" ' . selected( $post->ID, $value, 0 ) . '>' . esc_html( $post->post_title ) . '</option>';
	  } ?>
  </select>
<?php endif; ?>

