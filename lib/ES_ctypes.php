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
		$config     = new Es_config();
		$meta_boxes = isset( $config->data["meta_boxes"] ) ? $config->data["meta_boxes"] : array();

		//Добавляем метабокс
		if ( $meta_boxes ) {
			foreach ( $meta_boxes as $key => $meta_box ) {
				if ( ! $meta_box['display'] ) {
					continue;
				}
				add_meta_box(
					'es_meta_box' . $key,
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

		if ( ! empty( $callback['args']['meta_box']['condition'] ) ) {
			$condition = $callback['args']['meta_box']['condition'];
			if ( ! empty( $condition['template'] ) ) {
				if ( ! in_array( get_page_template_slug( $post->ID ), $condition['template'] ) ) {
					return $post;
				}
			}
		}

		$config      = new Es_config();
		$meta_fields = $callback['args']['post_meta'];

		$box_num   = $callback['args']['box_num'];
		$languages = isset( $config->data["languages"] ) ? $config->data["languages"] : false;
		echo '<input type="hidden" name="easy[es_box_num]" value="' . $box_num . '">';
		if ( $languages ) {
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
			foreach ( $languages as $lang_key => $language ) {

				if ( isset( $language['default'] ) && $language['default'] == 1 ) {
					$class_tab = 'active';
				} else {
					$class_tab = '';
				}
				echo "<div id=\"es-tab-{$language['slug']}\" class=\"es-tab-body {$class_tab}\">";
				if ( $meta_fields ) {
					foreach ( $meta_fields as $key => $field ) {
						$field_name = es_field_prefix( $key, $lang_key );
						$editor_id  = mb_strtolower( str_replace( array( '_' ), array( '-' ), $field_name ) );
						$content    = wp_unslash( get_post_meta( $post->ID, $field_name, 1 ) );
						echo "<div class='es_meta_field'>";
						echo "<div class='es_field_label'><strong>" . $field['name'] . " (" . $language['name'] . ")</strong></div>";
						echo "<div class='es_field_desc'><em>" . $field['desc'] . "</em></div>";

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
								$default  = '';
								$h        = '100';
								$w        = '100';
								$image_id = (int) $content;
								if ( $image_id ) {
									$image_attributes = wp_get_attachment_image_src( $image_id, array( $h, $w ) );
									$src              = $image_attributes[0];
								} else {
									$src = $default;
								}
								echo '
                            <img data-src="' . $default . '" src="' . $src . '" width="' . $w . 'px" height="' . $h . 'px" />
                            <div>
                               <input type="hidden" name="easy[' . $field_name . ']" id="' . $field_name . '" value="' . $image_id . '" />
                               <button type="button" class="upload_image_button button">Загрузить</button>
                               <button type="button" class="remove_image_button button">&times;</button>
                           </div>';
								break;

							case 'textarea':
								echo '<textarea  name="easy[' . $field_name . ']" class="es_textarea" id="' . $editor_id . '" rows="12">' . $content . '</textarea>';
								break;

							case 'text' :
								echo '<input type="text"  name="easy[' . $field_name . ']" id="' . $editor_id . '" class="es_text" value="' . esc_html( $content ). '">';
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
								echo '<input type="text"  name="easy[' . $field_name . ']" id="' . $editor_id . '" class="es_text" value="' . esc_html( $content ) . '">';
								break;


						}
						echo "</div>";

					}
				}
				echo "</div>";
			}


			echo "</div>";
		} else {
			echo "<div id=\"es-meta-block\">";
			if ( $meta_fields ) {
				foreach ( $meta_fields as $key => $field ) {
					if ( ! empty( $field['condition'] ) ) {
						if ( ! empty( $field['condition']['template'] ) ) {
							if ( ! in_array( get_page_template_slug( $post->ID ), $field['condition']['template'] ) ) {
								continue;
							}
						}
					}
					$field_name = $key;
					$editor_id  = mb_strtolower( str_replace( array( '_' ), array( '-' ), $key ) );
					if ( in_array( $field['type'], array( 'gallery', 'accordion' ) ) ) {
						$content = get_post_meta( $post->ID, $key, 0 );
						if ( ! empty( $content[0] ) ) {
							$content = $content[0];
						}
					} else {
						$content = get_post_meta( $post->ID, $key, 1 );
					}


					if ( empty( $content ) && isset( $field['default'] ) ) {
						if ( $field['default'] == 'es_block' ) {
							$content = es_get_block( $field['es_block'] );
						} else {
							$content = $field['default'];
						}

					}

					$content = wp_unslash( $content );

					echo "<div class='es_meta_field es-meta-" . $field['type'] . "'>";
					echo "<div class='es_field_label'><strong>" . $field['name'] . "</strong></div>";
					echo "<div class='es_field_desc'><em>" . $field['desc'] . "</em></div>";

					switch ( $field['type'] ) {
						case 'editor':
							$editor_args    = array();
							$editor_default = array(
								'wpautop'       => false,
								'media_buttons' => 1,
								'textarea_name' => 'easy[' . $key . ']',
								'editor_class'  => $editor_id,
								'tinymce'       => array(
									'verify_html' => false
								),

							);
							if ( ! empty( $field['editor'] ) && is_array( $field['editor'] ) ) {
								$editor_args = $field['editor'];
							}
							$editor_args = wp_parse_args( $editor_args, $editor_default );
							wp_editor( $content, $editor_id, $editor_args );
							break;

						case 'image' :
							es_field_template( $field['type'], $field_name, $content );
							break;
						case 'file' :
							es_field_template( $field['type'], $field_name, $content );
							break;
						case 'taxonomy' :
							es_field_template( $field['type'], $field_name, $content, array( 'taxonomy' => $field['taxonomy'] ) );
							break;

						case 'textarea':
							$content = esc_html__( $content );
							echo '<textarea  name="easy[' . $key . ']" class="es_textarea" id="' . $editor_id . '" rows="12">' . $content . '</textarea>';
							break;

						case 'text' :
							$content = esc_html__( $content );
							echo '<input type="text"  name="easy[' . $key . ']" id="' . $editor_id . '" class="es_text" value="' . esc_html( $content ) . '">';
							break;

						case 'checkbox' :
							echo '<input type="hidden"  name="easy[' . $key . ']" id="hidden-' . $key . '" value="0" />';
							echo '<input type="checkbox"  name="easy[' . $key . ']" id="' . $key . '" value="1" ' . checked( $content, 1, 0 ) . ' class="es_text"/>';
							break;
						case 'select':
							es_field_template( $field['type'], $field_name, $content, array( 'values' => $field['values'] ) );
							break;

						case 'radio' :
							if ( $field['values'] ) {
								foreach ( $field['values'] as $field_key => $value ) {
									echo '<input type="radio"  name="easy[' . $key . ']" id="' . $key . '-' . $field_key . '" value="' . $field_key . '" ' . checked( $content, 1, 0 ) . ' class="es_text"/><label for="' . $key . '-' . $field_key . '">' . $value . '</label>';
								}

							}
							break;
						case 'gallery':
							es_field_template( $field['type'], $field_name, $content );
							break;
						case 'slider':
							es_field_template( $field['type'], $field_name, $content );
							break;
						case 'accordion':
							es_field_template( $field['type'], $field_name, $content );
							break;
						case 'post':
							es_field_template( $field['type'], $field_name, $content, array( 'post_type' => $field['post_type'] ) );
							break;

						case 'date':
							echo '<input type="date"  name="easy[' . $key . ']" id="' . $editor_id . '" class="es_text" value="' . $content . '" style="width:150px">';
							break;

						case 'time':
							echo '<input type="time"  name="easy[' . $key . ']" id="' . $editor_id . '" class="es_text" value="' . $content . '" style="width:150px">';
							break;
						case 'week':
							echo '<input type="week"  name="easy[' . $key . ']" id="' . $editor_id . '" class="es_text" value="' . $content . '" style="width:150px">';
							break;

						default:
							$content = esc_html__( $content );
							echo '<input type="text"  name="easy[' . $key . ']" id="' . $editor_id . '" class="es_text" value="' . esc_html( $content ) . '">';
							break;


					}
					echo "</div>";

				}
			}
			echo "</div>";
		}


	}

// сохраняем наши мета поля
	function es_post_save( $postID ) {
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