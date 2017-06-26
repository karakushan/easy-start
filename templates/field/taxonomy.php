<?php
$args = array(
	'show_option_all'    => '',
	'show_option_none'   => '',
	'orderby'            => 'ID',
	'order'              => 'ASC',
	'show_last_update'   => 0,
	'show_count'         => 1,
	'hide_empty'         => 0,
	'child_of'           => 0,
	'exclude'            => '',
	'echo'               => 1,
	'selected'           => $value,
	'hierarchical'       => 1,
	'name'               => 'easy['.$name.']',
	'id'                 => 'name',
	'class'              => 'postform',
	'depth'              => 3,
	'tab_index'          => 0,
	'taxonomy'           => $args['taxonomy'],
	'hide_if_empty'      => true,
	'value_field'        => 'term_id', // значение value e option
	'required'           => false,
);

wp_dropdown_categories( $args );