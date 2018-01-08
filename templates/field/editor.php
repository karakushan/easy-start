<?php
$editor_id = mb_strtolower( str_replace( array( '_' ), array( '-' ), $name ) );
if ( empty( $value ) && $args['default'] == 'es_block' ) {
	$value = es_get_block( intval( $args['es_block'] ) );
}
wp_editor( $value, $editor_id, array(
	'wpautop'       => false,
	'media_buttons' => 1,
	'textarea_name' => esc_attr( 'easy[' . $name . ']' ),
	'textarea_rows' => intval( $args['textarea_rows'] ),
	'tinymce'       => array(
		'verify_html' => false
	),

) );

