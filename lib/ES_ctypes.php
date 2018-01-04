<?php

namespace ES_LIB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * class ES_init
 */
class ES_ctypes {
	function __construct() {
		add_action( 'init', array( $this, 'es_post_types' ) );
		add_action( 'add_meta_boxes', array( $this, 'es_post_metaboxes' ) );
		add_action( 'save_post', array( $this, 'es_post_save' ), 10, 2 );
	}

	function es_post_types() {
		$config      = new ES_config();
		$custom_post = isset( $config->data["custom_post"] ) ? $config->data["custom_post"] : array();

		$args = array(
			'label'               => __( 'Blocks', 'easy-start' ),
			'labels'              => array(
				'name'               => __( 'Blocks', 'easy-start' ),
				// основное название для типа записи
				'singular_name'      => __( 'Block', 'easy-start' ),
				// название для одной записи этого типа
				'add_new'            => __( 'Add Block', 'easy-start' ),
				// для добавления новой записи
				'add_new_item'       => __( 'Add Block', 'easy-start' ),
				// заголовка у вновь создаваемой записи в админ-панели.
				'edit_item'          => __( 'Edit Block', 'easy-start' ),
				// для редактирования типа записи
				'new_item'           => __( 'Block title', 'easy-start' ),
				// текст новой записи
				'view_item'          => __( 'View Block', 'easy-start' ),
				// для просмотра записи этого типа.
				'search_items'       => '',
				// для поиска по этим типам записи
				'not_found'          => '',
				// если в результате поиска ничего не было найдено
				'not_found_in_trash' => '',
				// если не было найдено в корзине
				'parent_item_colon'  => '',
				// для родительских типов. для древовидных типов
				'menu_name'          => __( 'Blocks', 'easy-start' ),
				// название меню
			),
			'description'         => '',
			'public'              => false,
			'publicly_queryable'  => null,
			'exclude_from_search' => null,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 62,
			'menu_icon'           => null,
			//'capability_type'   => 'post',
			//'capabilities'      => 'post', // массив дополнительных прав для этого типа записи
			'map_meta_cap'        => true, // Ставим true чтобы включить дефолтный обработчик специальных прав
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor' ),
			'taxonomies'          => array(),
			'has_archive'         => false,
			'rewrite'             => true,
			'query_var'           => true,
			'show_in_nav_menus'   => null,
		);

		register_post_type( 'es_blocks', $args );

		//Регистрируем новые типы постов
		if ( $custom_post ) {
			foreach ( $custom_post as $key => $cpost ) {
				register_post_type( $key, $cpost );
			}
		}


		register_taxonomy( 'blocks_category', array( 'es_blocks' ), array(
			'label'             => __( 'Categories blocks', 'easy-start' ),
			'hierarchical'      => true,
			'show_admin_column' => true,
		) );
	}

	function es_post_metaboxes() {
		$config = new Es_config();
		global $post;
		$meta_boxes = isset( $config->data["meta_boxes"] ) ? $config->data["meta_boxes"] : array();

		//Добавляем метабокс
		if ( $meta_boxes ) {
			foreach ( $meta_boxes as $key => $meta_box ) {
				//=== НАЧАЛО ПРОВЕРОК ПО УСЛОВИЮ  === //
				// проверяем свойство "display" если оно пустое, равно 0 или false , то выходим из цикла
				if ( empty( $meta_box['display'] ) ) {
					continue;
				}
				// если указано условие "template" (шаблон) и оно является массивом
				if ( ! empty( $meta_box['condition'] ['template'] ) ) {
					// если шаблон текущей страницы не входит в массив указанных в условии то выходим из цикла
					if ( is_array( $meta_box['condition']['template'] ) && ! in_array( get_page_template_slug( $post->ID ), $meta_box['condition'] ['template'] ) ) {
						continue;
					}
				}
				// если указано условие "post_id" (ID страницы, поста) и оно является массивом
				if ( ! empty( $meta_box['condition']['post_id'] ) ) {
					// если ID текущего поста не входит  в массив ID указанных в условии, то выходим из цикла
					if ( is_array( $meta_box['condition']['post_id'] ) && ! in_array( $post->ID, $meta_box['condition']['post_id'] ) ) {
						continue;
						// если в качестве аргумента указано число а не массив и оно не равно номеру поста, то выходим из цикла
					} elseif ( is_numeric( $meta_box['condition']['post_id'] ) && $meta_box['condition']['post_id'] != $post->ID ) {
						continue;
					}
				}
				// Добавляем метабокс
				add_meta_box(
					'es_meta_box_' . $key,
					$meta_box['name'],
					array( $this, 'es_meta_block' ),
					$meta_box['post_types'],
					'normal',
					'high', array( 'box_num' => $key, 'post_meta' => $meta_box['post_meta'], 'meta_box' => $meta_box )
				);
			}
		}
	}

	function es_meta_block( $post, $callback ) {


		$config      = new Es_config();
		$meta_fields = $callback['args']['post_meta'];
		$box_num     = $callback['args']['box_num'];
		$languages   = ! empty( $config->data["languages"] ) ? $config->data["languages"] : array(
			'ru_RU' => array(
				'slug'    => 'ru',
				'name'    => 'Русский',
				'default' => 1
			)
		);

		echo '<input type="hidden" name="easy[es_box_num]" value="' . esc_attr( $box_num ) . '">';
		wp_nonce_field( plugin_basename( __FILE__ ),'es_nonce' );
		if ( ! empty( $languages ) ) {
			// если больше одного языка в конфиге, то создаём табы, переключатели языков
			if ( count( $languages ) > 1 ) {
				echo "<div class=\"es_tax_tabs\"><ul>";

				foreach ( $languages as $lks => $lang ) {
					if ( isset( $lang['default'] ) && $lang['default'] == 1 ) {
						$class = 'active';
					} else {
						$class = '';
					}
					echo "<li><a href=\"#es-tab-{$lang['slug']}\" class=\"{$class}\">{$lang['name']}</a></li>";
				}
				echo "</ul>";
			}
			foreach ( $languages as $lang_key => $language ) {

				$lang_name = count( $languages ) > 1 ? sprintf( '(%s)', $language['name'] ) : '';

				if ( count( $languages ) > 1 ) {
					if ( isset( $language['default'] ) && $language['default'] == 1 ) {
						$class_tab = 'active';
					} else {
						$class_tab = '';
					}
					echo "<div id=\"es-tab-{$language['slug']}\" class=\"es-tab-body {$class_tab}\">";
				}
				if ( ! empty( $meta_fields ) ) {
					foreach ( $meta_fields as $key => $field ) {
						$field_name = es_field_prefix( $key, $lang_key );
						$editor_id  = mb_strtolower( str_replace( array( '_' ), array( '-' ), $field_name ) );
						$content    = wp_unslash( get_post_meta( $post->ID, $field_name, 1 ) );

						echo "<div class='es_meta_field'>";
						echo "<label for=\"" . esc_attr( $field_name ) . "\">" . $field['name'] . " {$lang_name}</label>";
						if ( ! empty( $field['before'] ) ) {
							echo '<div class="input-group">';
							echo '<span class="input-group-addon">' . $field['before'] . '</span>';
						}
						switch ( $field['type'] ) {

							case 'editor':
								wp_editor( $content, $editor_id, array(
									'wpautop'       => false,
									'media_buttons' => 1,
									'textarea_name' => 'easy[' . $field_name . ']',

									'tinymce' => array(
										'verify_html' => false
									),

								) );
								break;

							case 'image' :
								es_field_template( $field['type'], $field_name, $content );
								break;

							case 'text' :
								echo '<input type="text"  name="easy[' . $field_name . ']" id="' . $editor_id . '" class="es_text" value="' . esc_html( $content ) . '">';
								break;

							case 'taxonomy' :
								es_field_template( $field['type'], $field_name, $content, array( 'taxonomy' => $field['taxonomy'] ) );
								break;
							case 'checkbox' :
								echo '<input type="hidden"  name="easy[' . $field_name . ']" id="hidden-' . $field_name . '" value="0" />';
								echo '<input type="checkbox"  name="easy[' . $field_name . ']" id="' . $field_name . '" value="1" ' . checked( $content, 1, 0 ) . ' class="es_text"/>';
								break;
							case 'select':
								echo '<select name="easy[' . $key . ']">';
								echo '<option value="">выбрать из списка</option>';
								foreach ( $field['values'] as $sel_key => $sel ) {
									echo '<option value="' . $sel_key . '" ' . selected( $sel_key, $content, 0 ) . '>' . $sel . '</option>';
								}
								echo '</select>';
								break;
							case 'date':
								echo '<input type="date"  name="easy[' . $field_name . ']" id="' . $editor_id . '" class="es_text" value="' . $content . '" style="width:150px">';
								break;

							case 'time':
								echo '<input type="time"  name="easy[' . $field_name . ']" id="' . $editor_id . '" class="es_text" value="' . $content . '" style="width:150px">';
								break;
							case 'week':
								echo '<input type="week"  name="easy[' . $field_name . ']" id="' . $editor_id . '" class="es_text" value="' . $content . '" style="width:150px">';
								break;
							case 'file' :
								es_field_template( $field['type'], $field_name, $content );
								break;

							default:
								es_field_template( $field['type'], $field_name, $content, $field );
								break;


						}
						if ( ! empty( $field['after'] ) ) {
							echo '<span class="input-group-addon">' . esc_attr( $field['after'] ) . '</span>';
						}
						if ( ! empty( $field['before'] ) || ! empty( $field['after'] ) ) {
							echo '</div>';
						}
						echo "<div class='es_field_desc'><em>" . esc_attr( $field['desc'] ) . "</em></div>";
						echo "</div>";


					}
				}

				if ( count( $languages ) > 1 ) {
					echo "</div>";
				}
			}


			echo "</div>";
		}


	}

// сохраняем наши мета поля
	function es_post_save( $postID ) {
		// проверяем nonce нашей страницы, потому что save_post может быть вызван с другого места.
		if ( ! wp_verify_nonce( $_POST['es_nonce'], plugin_basename( __FILE__ ) ) ) {
			return;
		}


		if ( ! isset( $_POST['easy'] ) || wp_is_post_revision( $postID ) || ( isset( $_POST['action'] ) && $_POST['action'] == 'autosave' ) ) {
			return;
		}
		$config     = new Es_config();
		$meta_boxes = isset( $config->data["meta_boxes"] ) ? $config->data["meta_boxes"] : array();
		$box_num    = $_POST['easy']['es_box_num'];
		$post_meta  = $meta_boxes[ $box_num ]['post_meta'];
		$languages  = ES_config::$languages;

		if ( count( $languages ) ) {
			foreach ( $languages as $lang_key => $lang ) {
				foreach ( $post_meta as $meta_key => $meta_value ) {
					$field_name = es_field_prefix( $meta_key, $lang_key );
					if ( ( empty( $_POST['easy'][ $meta_key ] ) || ! isset( $_POST['easy'][ $field_name ] ) )
					     && $post_meta[ $meta_key ]['type'] != 'checkbox'
					) {
						delete_post_meta( $postID, $field_name );
					} else {
						$content = wp_slash( $_POST['easy'][ $field_name ] );
						if ( $post_meta[ $meta_key ]['type'] == "text" ) {
							$content = wp_specialchars_decode( $_POST['easy'][ $field_name ], ENT_QUOTES );
						}
						update_post_meta( $postID, $field_name, $content );
					}
				}
			}
		} else {
			foreach ( $post_meta as $meta_key => $meta_value ) {
				if ( ( empty( $_POST['easy'][ $meta_key ] ) || ! isset( $_POST['easy'][ $meta_key ] ) )
				     && $post_meta[ $meta_key ]['type'] != 'checkbox'
				) {
					delete_post_meta( $postID, $meta_key );
				} else {
					$content = wp_slash( $_POST['easy'][ $meta_key ] );
					if ( $post_meta[ $meta_key ]['type'] == "text" ) {
						$content = wp_specialchars_decode( $_POST['easy'][ $meta_key ], ENT_QUOTES );
					}
					update_post_meta( $postID, $meta_key, $content );
				}
			}
		}
	}

}