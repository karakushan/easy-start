<?php
$editor_id  = mb_strtolower( str_replace( array( '_' ), array( '-' ), $name ) );
wp_editor( $value, $editor_id, array(
	'wpautop'       => false,
	'media_buttons' => 1,
	'textarea_name' => 'easy[' . $name . ']',
	'textarea_rows' => $args['textarea_rows'],
	'tinymce'       => array(
		'verify_html' => false
	),

) );

