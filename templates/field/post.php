
<?php if($args['post_type']=='page'): ?>
	<?php wp_dropdown_pages(array('selected'=>$value,'name'=>'easy['.$name.']')); ?>
    <?php else: ?>
    <select name="easy[<?php echo $name ?>]" id="es-field-<?php echo $name ?>">
        <option value=""><?php echo $args['first_option'] ?></option>
		<? foreach ( $args['values'] as $key => $post ) {
			echo '<option value="' . $post->ID . '" ' . selected( $post->ID, $value, 0 ) . '>' . $post->post_title . '</option>';
		} ?>
    </select>
<?php endif; ?>

