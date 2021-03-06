<?php
/**
 * Class ES_Ctypes
 * @package ES_LIB
 */

namespace ES_LIB;
class ES_Ctypes {
	function __construct() {
		add_action( 'init', array( $this, 'es_post_types' ) );
		add_action( 'add_meta_boxes', array( $this, 'es_post_metaboxes' ) );
		add_action( 'save_post', array( $this, 'es_post_save' ), 10, 2 );
	}

	/**
	 * Регистрируем кастомные типы записей
	 */
	function es_post_types() {

		// Регистрация типа записи "Блоки"
		register_post_type( 'es_blocks', array(
			'label'               => __( 'Blocks', 'easy-start' ),
			'description'         => null,
			'public'              => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => null,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-edit',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor' ),
			'taxonomies'          => array(),
			'has_archive'         => false,
			'rewrite'             => true,
			'query_var'           => true,
			'show_in_nav_menus'   => null,
			'show_in_rest'        => true,
		) );

		register_taxonomy( 'blocks_category', array( 'es_blocks' ), array(
			'label'             => __( 'Categories blocks', 'easy-start' ),
			'hierarchical'      => true,
			'show_admin_column' => true,
		) );


		$custom_posts = ES_Start::get_config( 'custom_post' );

		// Просто выходим из ф-ции если в конфиге нет новых типов постов
		if ( empty( $custom_posts ) || ! is_array( $custom_posts ) ) {
			return;
		}

		//Регистрируем новые типы записей из конфигурационного файла
		foreach ( $custom_posts as $key => $custom_posts ) {
			$args = array(
				'label'               => ! empty( $custom_posts['label'] ) ? $custom_posts['label'] : $key,
				'description'         => null,
				'public'              => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_icon'           => null,
				'map_meta_cap'        => true,
				'hierarchical'        => false,
				'supports'            => array( 'title', 'editor' ),
				'taxonomies'          => array(),
				'has_archive'         => false,
				'rewrite'             => true,
				'query_var'           => true,
				'show_in_rest'        => true,
				'show_in_nav_menus'   => true,

			);
			register_post_type( $key, wp_parse_args( $custom_posts, $args ) );
		}


	}

	/**
	 * Отвечает за регистрацию метабоксов
	 */
	function es_post_metaboxes() {
		global $post;

		$meta_boxes = ES_Start::get_config( 'meta_boxes' );

		if ( empty( $meta_boxes ) || ! is_array( $meta_boxes ) ) {
			return;
		}

		//Добавляем метабокс
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

	/**
	 * Отвечает за вывод метабокса вместе с доп. полями
	 *
	 * @param $post
	 * @param $callback
	 */
	function es_meta_block( $post, $callback ) {
		$meta_fields = $callback['args']['post_meta'];
		$box_num     = $callback['args']['box_num'];
		$languages   = ES_Start::get_languages();

		echo '<input type="hidden" name="easy[es_box_num]" value="' . esc_attr( $box_num ) . '">';
		wp_nonce_field( plugin_basename( __FILE__ ), 'es_nonce' );
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
					echo '<li><a href="#es-tab-' . esc_attr( $lang['slug'] ) . '" class="' . esc_attr( $class ) . '">' . esc_attr( $lang['name'] ) . '</a></li>';
				}
				echo "</ul>"; // end tag <ul>
			}
			foreach ( $languages as $lang_key => $language ) {
				if ( count( $languages ) > 1 ) {
					if ( isset( $language['default'] ) && $language['default'] == 1 ) {
						$class_tab = 'active';
					} else {
						$class_tab = '';
					}
					echo '<div id="es-tab-' . esc_attr( $language['slug'] ) . '" class="es-tab-body ' . esc_attr( $class_tab ) . '">';
				}
				if ( ! empty( $meta_fields ) ) {
					foreach ( $meta_fields as $key => $field ) {
						$field_name = es_field_prefix( $key, $lang_key );
						$content    = wp_unslash( get_post_meta( $post->ID, $field_name, 1 ) );

						echo "<div class='es_meta_field'>";
						echo '<label for=" ' . esc_attr( $field_name ) . '">' . esc_attr( $field['name'] ) . '</label>';
						if ( ! empty( $field['before'] ) ) {
							echo '<div class="es_meta_input-before">';
							echo esc_attr( $field['before'] );
							echo '</div>';// end of class es_meta_input-before
						}
						// Прессет поля типа слайдер
						if ( $field['type'] == 'slider' ) {
							$field['type']   = 'multiple';
							$field['fields'] = array(
								'title' => array( 'type' => 'text', 'name' => __( 'Title', 'easy-start' ) ),
								'image' => array( 'type' => 'image', 'name' => __( 'Image', 'easy-start' ) ),
								'text'  => array( 'type' => 'textarea', 'name' => __( 'Text', 'easy-start' ) ),
								'link'  => array( 'type' => 'text', 'name' => __( 'Link', 'easy-start' ), )
							);
						} elseif ( $field['type'] == 'accordion' ) {
							$field['type']   = 'multiple';
							$field['fields'] = array(
								'title' => array( 'type' => 'text', 'name' => __( 'Title', 'easy-start' ) ),
								'text'  => array( 'type' => 'textarea', 'name' => __( 'Text', 'easy-start' ) )
							);
						}


						es_field_template( $field['type'], $field_name, $content, $field );
						if ( ! empty( $field['after'] ) ) {
							echo '<span class="input-group-addon">' . esc_attr( $field['after'] ) . '</span>';
						}
						echo "<div class='es_field_desc'><em>" . esc_attr( $field['desc'] ) . "</em></div>";
						echo "</div>"; // end class es_meta_field
					}
				}
				echo '</div>'; // end class es-tab-body

			}
			if ( count( $languages ) > 1 ) {
				echo "</div>"; // end .es_tax_tabs
			}
		}


	}


	/**
	 * Save meta fields values
	 *
	 * @param $postID
	 */
	function es_post_save( $postID ) {
		// проверяем nonce нашей страницы, потому что save_post может быть вызван с другого места.
		if ( ! wp_verify_nonce( $_POST['es_nonce'], plugin_basename( __FILE__ ) ) ) {
			return;
		}

		if ( wp_is_post_revision( $postID ) || ( isset( $_POST['action'] ) && $_POST['action'] == 'autosave' ) ) {
			return;
		}
		$config     = ES_Start::get_config();
		$meta_boxes = isset( $config["meta_boxes"] ) ? $config["meta_boxes"] : array();
		$languages  = ES_Start::get_languages();

		if ( $meta_boxes ) {
			foreach ( $meta_boxes as $metabox_id => $meta_box ) {
				$post_meta = $meta_boxes[ $metabox_id ]['post_meta'];
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
			}
		}

	}

}