<div class="es-slider-wrapper">
	<?php
	$title = ! empty( $args['fields']['title']['placeloder'] ) ? $args['fields']['title']['placeloder'] : __( 'Title', 'easy-start' );
	// если нет секций, то создаём пустую, так как остальные создаются методом клонирования
	if ( empty( $value ) ) {
		foreach ( $args['fields'] as $sk => $field_start ) {
			$value[0][ $sk ] = '';
		}
	}
	?>
  <div class="es-slides es-sort" data-name="<?php echo esc_attr( $name ) ?>">
	  <?php if ( ! empty( $value ) ): ?>
		  <?php $count = 0; ?>
		  <?php foreach ( $value as $k => $val ): ?>
          <section data-index='<?php echo esc_attr( $count ) ?>' data-name="<?php echo esc_attr( $name ) ?>">
            <header>
              <span class="dashicons dashicons-plus" data-action='open-section'></span>
              <span class='count'><?php echo esc_attr( $count ) ?></span>
              <input name='easy[<?php echo esc_attr( $name ) ?>][<?php echo esc_attr( $count ) ?>][title]' class='title'
                     placeholder="<?php echo esc_attr( $title ) ?>" value="<?php echo esc_attr( $val['title'] ) ?>">
              <span class="dashicons dashicons-dismiss" data-action='remove-section'></span>
            </header>
            <div class='bottom'>
				<?php if ( ! empty( $args['fields'] ) ) {

					foreach ( $args['fields'] as $key => $field ) {
						if ( $key == 'title' || empty( $field['type'] ) ) {
							continue;
						}
						$field_name = $name . '[' . $count . '][' . $key;
						echo '<div class="sub-field full">';
						if ( ! empty( $field['name'] ) ) {
							echo '<label for="' . esc_attr( $field_name ) . '">' . esc_attr( $field['name'] ) . '</label>';
						}
						$field['subfield'] = $key;
						$field['index']    = $count;
						es_field_template( $field['type'], $name, $val[ $key ], $field );
						echo "<div class='es_field_desc'><em>" . esc_attr( $field['desc'] ) . "</em></div>";
						echo '</div>';
					}
				} ?>
            </div>
          </section>
			  <?php $count ++; ?>
		  <?php endforeach; ?>
	  <?php endif; ?>
  </div>
  <button type="button" data-action="add-slide" class="button button-primary">добавить секцию</button>
</div>

