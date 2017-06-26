<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
/**
 * пропускает данные мета поля записи через фильтр, оставляя только нужные данные
 *
 * @param $meta_value - значение мета поля которое фильтрируется
 * @param $type - тип фильтра
 */
add_filter( 'es_meta_filter', 'es_meta_filter_action', 10, 2 );
function es_meta_filter_action( $meta_value, $type ) {
	switch ( $type ) {
		case 'domain':
			//оставляет только домен из ссылки
			$meta_value = parse_url( $meta_value, PHP_URL_HOST );
			break;
		default:
			break;
	}

	return $meta_value;
}

add_filter( 'the_content', 'fs_content_filter' );
function fs_content_filter( $content ) {
	if ( get_post_type() != 'es_blocks' ) {
		return $content;
	}
	global $post;
	$block_id    = $post->ID;
	$edit_link   = get_edit_post_link( $block_id );
	$wrap_before = '';
	$wrap_after  = '';


	switch ( get_post_type() ) {
		case 'post':
			$edit_text = __( 'edit post', 'easy-start' );
			break;
		case 'page':
			$edit_text = __( 'edit page', 'easy-start' );
			break;
		case 'es_blocks':
			$edit_text = __( 'edit block', 'easy-start' );
			break;
		case 'product':
			$edit_text = __( 'edit product', 'easy-start' );
			break;
		default:
			$edit_text = __( 'edit ' . get_post_type(), 'easy-start' );
			break;
	}
	if ( current_user_can( 'edit_posts' ) ) {
		$wrap_before = "<div data-es-type='es_blocks' data-es-block=\"$block_id\" data-type=\"post\"><div data-es-type='es_editor'><a href=\"$edit_link\" target=\"_blank\">" . $edit_text . "</a></div>";
	}
	if ( current_user_can( 'edit_posts' ) ) {
		$wrap_after = "</div>";
	}
	$content = $wrap_before . $content . $wrap_after;

	return $content;
}