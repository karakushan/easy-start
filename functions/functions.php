<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Функция выводит доступный для редактирования в фронтэнде блок из меню "Блоки"
 *
 * @param  integer $block_id - id блока
 * @param  array $args - массив аргументов
 *
 * @return string выводит текст блока
 */
function es_block( $block_id = 0, $args = array() ) {
	$args = wp_parse_args( $args, array(
		'type'   => 'text',
		'class'  => 'es-block',
		'filter' => 'the_content'
	) );

	$es_post = get_post( $block_id, OBJECT, $args['raw'], 'display' );

	switch ( $args['type'] ) {
		case 'text':
			$edit_link = esc_url( get_edit_post_link( $block_id ) );
			break;
		case 'slider':
			$edit_link = esc_url( get_edit_post_link( $block_id ) );
			break;

		default:
			$edit_link = esc_url( get_edit_post_link( $block_id ) );
			break;
	}

	$class = ! empty( $args['class'] ) ? 'class="' . esc_attr( $args['class'] ) . '"' : "";

	if ( current_user_can( 'edit_posts' ) ) {
		printf( '<div data-es-type="es_blocks" data-es-block="%1$s" %2$s>', esc_attr( $block_id ), esc_attr( $class ) );
		printf( '<div data-es-type="es_editor"><a href="%1$s" target="_blank">%2$s</a></div>', esc_attr( $edit_link ), esc_attr__( 'edit block', 'easy-start' ) );
	}


	$content = sanitize_post_field( 'post_content', $es_post->post_content, $block_id, 'display' );

	if ( $args['filter'] ) {
		$content = apply_filters( $args['filter'], $content );
	}

	echo $content;

	if ( current_user_can( 'edit_posts' ) ) {
		echo "</div>";
	}


}

function es_get_block( $block_id ) {

	$query    = new WP_Query;
	$es_posts = $query->query( array(
		'p'         => (int) $block_id,
		'post_type' => 'es_blocks'
	) );

	foreach ( $es_posts as $es_post ) {
		return $es_post->post_content;
	}
}

/**
 * Выводит заголовок блока
 *
 * @param int $block_id
 */
function es_block_title( $block_id = 0 ) {
	if ( empty( $block_id ) ) {
		return;
	}
	echo get_the_title( $block_id );
}

function es_transliteration( $str ) {
	$trans = array(
		"а" => "a",
		"б" => "b",
		"в" => "v",
		"г" => "g",
		"д" => "d",
		"е" => "e",
		"ё" => "yo",
		"ж" => "j",
		"з" => "z",
		"и" => "i",
		"й" => "i",
		"к" => "k",
		"л" => "l",
		"м" => "m",
		"н" => "n",
		"о" => "o",
		"п" => "p",
		"р" => "r",
		"с" => "s",
		"т" => "t",
		"у" => "y",
		"ф" => "f",
		"х" => "h",
		"ц" => "c",
		"ч" => "ch",
		"ш" => "sh",
		"щ" => "shh",
		"ы" => "i",
		"э" => "e",
		"ю" => "u",
		"я" => "ya",
		"ї" => "i",
		"'" => "",
		"ь" => "",
		"Ь" => "",
		"ъ" => "",
		"Ъ" => "",
		"і" => "i",
		"А" => "A",
		"Б" => "B",
		"В" => "V",
		"Г" => "G",
		"Д" => "D",
		"Е" => "E",
		"Ё" => "Yo",
		"Ж" => "J",
		"З" => "Z",
		"И" => "I",
		"Й" => "I",
		"К" => "K",
		"Л" => "L",
		"М" => "M",
		"Н" => "N",
		"О" => "O",
		"П" => "P",
		"Р" => "R",
		"С" => "S",
		"Т" => "T",
		"У" => "Y",
		"Ф" => "F",
		"Х" => "H",
		"Ц" => "C",
		"Ч" => "Ch",
		"Ш" => "Sh",
		"Щ" => "Sh",
		"Ы" => "I",
		"Э" => "E",
		"Ю" => "U",
		"Я" => "Ya",
		"Ї" => "I",
		"І" => "I"
	);
	$res   = str_replace( " ", "-", strtr( strtolower( $str ), $trans ) );
	//если надо, вырезаем все кроме латинских букв, цифр и дефиса (например для формирования логина)
	$res = mb_strtolower( preg_replace( "|[^a-zA-Z0-9-]|", "", $res ) );

	return $res;
}

/**
 * Функция возвращает значение мета поля термина с учётом локали
 *
 * @param  [string] $key     -   название поля
 * @param  [int] $term_id    -   id термина
 * @param  [string] $default -   значение отображаемое по умолчанию если нечего возвращать
 *
 * @return [string]          -   строковое значение мета поля
 */
function es_term_meta( $key, $term_id = 0, $args = array() ) {
	global $wp_query;
	// Задаём id термина
	$term_id = $term_id == 0 ? $wp_query->queried_object_id : (int) $term_id;
	$args    = wp_parse_args( $args, array(
		'type'     => 'text',
		'default'  => '',
		'raw'      => true,
		'echo'     => true,
		'size'     => 'large',
		'es_block' => 0
	) );

	$lang_key = es_field_prefix( $key );

	//отдаём данные в зависимости от параметра $type
	switch ( $args['type'] ) {
		case 'image':

			$term_meta = wp_get_attachment_url( get_term_meta( $term_id, $lang_key, 1 ) );
			$term_meta = empty( $term_meta ) ? wp_get_attachment_url( get_term_meta( $term_id, $key, 1 ) ) : $term_meta;
			break;
		case "taxonomy":
			$term_id      = get_term_meta( $term_id, $lang_key, 1 );
			$term_meta    = get_term_by( 'term_taxonomy_id', $term_id );
			$args['echo'] = false;
			break;
		case "gallery":
			$post_meta      = get_term_meta( $term_id, $lang_key, 0 );
			$gallery_images = ! empty( $post_meta[0] ) ? $post_meta[0] : array();
			$meta_img       = array();
			foreach ( $gallery_images as $k => $images ) {
				$meta_img[ $k ] = wp_get_attachment_image_url( $images, $args['size'] );
			}
			$term_meta    = $meta_img;
			$args['echo'] = 0;
			break;
		default:
			$term_meta = get_term_meta( $term_id, $lang_key, 1 );
			$term_meta = empty( $term_meta ) ? get_term_meta( $term_id, $key, 1 ) : $term_meta;
			if ( ! $args['raw'] ) {
				$term_meta = apply_filters( 'the_content', $term_meta );
			}
			break;
	}
	//Если пустое значение мета поля или равно false возвращаем значение заданное в $default
	if ( empty( $term_meta ) ) {
		if ( $args['default'] == 'es_block' ) {
			$term_meta = es_get_block( $args['es_block'] );
		} else {
			$term_meta = $args['default'];
		}

	}
	$term_meta = wp_unslash( $term_meta );

	if ( $args['echo'] ) {
		echo $term_meta;
	} else {
		return $term_meta;
	}


}

/**
 * Выводит обработанное мета поле поста
 *
 * @param  string $meta_key - ключ поля
 * @param  int $post_id - id записи
 * @param  array $args - масив доп. настроек
 *
 * @return string $post_meta - обработтаное значение поля
 */
function es_post_meta( $meta_key, $post_id = 0, $args = array() ) {
	// Задаём id поста если не указан параметр $post_id
	global $post;
	$post_id = empty( $post_id ) ? $post->ID : (int) $post_id;

	$defaults = array(
		'default'     => '',
		'type'        => 'text',
		'es_block'    => '',
		'echo'        => true,
		'filter'      => false,
		'date_format' => 'd.m.Y'
	);
	$args     = wp_parse_args( $args, $defaults );

	$meta_value = get_post_meta( $post_id, es_field_prefix( $meta_key ), 1 );

//    если значения метаполя пусто ищем сначала в дефолтном значении а потом в поле без префикса языка
	if ( empty( $meta_value ) ) {
		if ( $args['default'] == 'es_block' ) {
			$meta_value = es_get_block( (int) $args['es_block'] );
		} else {
			if ( ! empty( $args['default'] ) ) {
				$meta_value = $args['default'];
			} else {
				$meta_value = get_post_meta( $post_id, $meta_key, 1 );
			}

		}
	}

	$meta_value = wp_unslash( $meta_value ); // убираем слеши
	//Если пустое значение мета поля или равно false возвращаем значение заданное в $default
	if ( empty( $meta_value ) ) {
		$meta_value = $args['default'];
	}

	//отдаём данные в зависимости от параметра $type
	switch ( $args['type'] ) {

		case 'image':
			if ( is_numeric( $meta_value ) ) {
				$post_meta = wp_get_attachment_url( $meta_value );
			} else {
				$post_meta = $meta_value;
			}
			break;
		case 'file':
			$post_meta = wp_get_attachment_url( $meta_value );
			break;
		case "taxonomy":
			$post_meta    = get_term_by( 'term_taxonomy_id', $meta_value );
			$args['echo'] = 0;
			break;
		case "post":
			$post_meta    = get_post( $meta_value );
			$args['echo'] = 0;
			break;
		case "date":
			$post_meta = date( $args['date_format'], strtotime( $meta_value ) );
			break;
		case "accordion":
			$post_meta    = get_post_meta( $post_id, es_field_prefix( $meta_key ), 0 );
			$post_meta    = ! empty( $post_meta[0] ) ? $post_meta[0] : array();
			$args['echo'] = 0;
			break;
		case "multiple":
			$post_meta    = get_post_meta( $post_id, es_field_prefix( $meta_key ), 0 );
			$post_meta    = ! empty( $post_meta[0] ) ? $post_meta[0] : array();
			$args['echo'] = 0;
			break;
		case "slider":
			$post_meta    = get_post_meta( $post_id, es_field_prefix( $meta_key ), 0 );
			$post_meta    = wp_unslash( $post_meta );
			$post_meta    = ! empty( $post_meta[0] ) ? $post_meta[0] : array();
			$args['echo'] = 0;
			break;
		case "gallery":
			$post_meta      = get_post_meta( $post_id, es_field_prefix( $meta_key ), 0 );
			$gallery_images = ! empty( $post_meta[0] ) ? $post_meta[0] : array();
			$meta_img       = array();
			foreach ( $gallery_images as $k => $images ) {
				$meta_img[ $k ] = wp_get_attachment_image_url( $images, 'full' );
			}
			$post_meta    = $meta_img;
			$args['echo'] = 0;
			break;
		default:
			$post_meta = $meta_value;
			break;
	}

	if ( $args['filter'] ) {
		$post_meta = apply_filters( $args['filter'], $post_meta );
	}

	if ( $args['echo'] ) {
		echo $post_meta;
	} else {
		return $post_meta;
	}
}

/**
 * возвращает название мета поля с префиксом для мультиязыков
 *
 * @param  string $meta_key название метаполя
 * @param  string $lang код локали язык в формате ru_RU
 *
 * @return string            название поля с префиксом
 */
function es_field_prefix( $meta_key, $lang = '' ) {
	$languages = \ES_LIB\ES_config::$languages;
	if ( count( $languages ) ) {
		if ( empty( $lang ) ) {
			$lang = get_locale();
		}
		if ( ! empty( $lang ) && ! isset( $languages[ $lang ]['default'] ) ) {
			$meta_key = $meta_key . '-' . $languages[ $lang ]['slug'];
		}
	}

	return $meta_key;
}

/**
 * Возвращает или отдаёт опцию сайта
 *
 * @param $key - ключ или название опции
 * @param array $args - параметры
 *
 * @return mixed|string|void
 */
function es_options( $key, $args = array() ) {
	$args = wp_parse_args( $args, array(
		'type'    => 'theme_mod',
		'default' => '',
		'format'  => 'text',
		'echo'    => true

	) );
	// отдаём настройку в зависимости от типа
	switch ( $args['type'] ) {
		case 'theme_mod':
			$options_value = get_theme_mod( $key, $args['default'] );
			break;
		case 'blog_options':
			$options_value = get_option( $key, $args['default'] );
			break;
	}

	//придаём настройкам соответствующий формат
	switch ( $args['format'] ) {
		case 'number':
			$options_value = preg_replace( "/[^0-9]/", '', $options_value );
			break;
		case 'email':
			$options_value = antispambot( $options_value );
			break;
	}


	if ( $args['echo'] == true ) {
		echo esc_html( $options_value );
	} else {
		return $options_value;
	}
}

/**
 * подключает поле в метабоксе редактирования записи, категории
 *
 * @param  string $type тип поля
 * @param  смешанный $name атрибут name у поля формы
 * @param  смешанный $value значения поля value у элемента формы
 * @param  array $args дополнительные аргументы
 *
 * @return [type]        html код поля формы
 */
function es_field_template( $type = 'text', $name, $value, $args = array() ) {
	$template = ES_DIR_PATH . "templates/field/" . $type . ".php";
	$default  = array(
		'style'         => '',
		'taxonomy'      => 'category',
		'post_type'     => 'page',
		'values'        => array(),
		'textarea_rows' => 8,
		'id'            => sanitize_title( $name ),
		'placeholder'   => '',
		'name'          => '',
		'subfield'      => '',
		'index'         => 0,
		'data'          => array( 'data-name' => $name ),
		'first_option'  => __( 'Please Select', 'easy-start' )
	);
	if ( ! empty( $args['subfield'] ) ) {
		$args['data']['data-name'] = $args['subfield'];
	}
	$args = wp_parse_args( $args, $default );

	$data = es_parse_atts( $args['data'] );
	switch ( $type ) {
		case 'post':
			$args['values'] = get_posts( array( 'post_type' => $args['post_type'], 'posts_per_page' => - 1 ) );
			break;
	}
	if ( file_exists( $template ) ) {
		include $template;
	}
}

/**
 * Создает атрибут name
 *
 * @param $name
 * @param int $index
 * @param string $subfield_name
 */
function es_field_name( $name, $index = 0, $subfield_name = '' ) {
	$field_name = sprintf( 'easy[%s]', $name );
	if ( ! empty( $subfield_name ) ) {
		$field_name = sprintf( 'easy[%s][%d][%s]', $name, $index, $subfield_name );
	}

	echo esc_attr( $field_name );
}

/**
 * парсит атрибуты из массива в строку
 *
 * @param array $atts
 *
 * @return string
 */
function es_parse_atts( $atts = array(), $prefix_data = false ) {
	if ( empty( $atts ) || ! is_array( $atts ) ) {
		return '';
	}
	$attribute = array();
	foreach ( $atts as $key => $att ) {
		if ( $prefix_data ) {
			$attribute[] = 'data-' . sanitize_key( $key ) . '="' . $att . '"';
		} else {
			$attribute[] = sanitize_key( $key ) . '="' . esc_attr( $att ) . '"';
		}
	}
	$atts_text = implode( ' ', $attribute );

	return $atts_text;
}

/**
 * функция возвращает текущие настройки из конфиг файла es-config.json
 * @return mixed
 */
function es_get_config() {
	$config = new \ES_LIB\ES_config();

	return $config->data;
}

/**
 * Получает настройки мета поля
 *
 * @param $meta_box id метабокса
 * @param $post_meta ключ поля
 *
 * @return mixed
 */
function es_get_config_post_meta( $meta_box = 0, $post_meta = '' ) {
	$config    = new \ES_LIB\ES_config();
	$post_meta = $config->data['meta_boxes'][ $meta_box ]['post_meta'][ $post_meta ];

	return $post_meta;
}
