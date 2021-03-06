<?php

namespace ES_LIB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class Es_taxonomies
 */
class ES_Taxonomies {

	function __construct() {
		add_action( 'init', array( $this, 'term_meta_create' ) );
	}

	public function term_meta_create() {
		$config     = ES_Start::get_config();
		$term_meta  = $config['term_meta'];
		$taxonomies = isset( $config["taxonomies"] ) ? $config["taxonomies"] : array();

		//Если задано в настройках доп. поля таксономии то создаём их
		if ( $term_meta ) {
			foreach ( $term_meta as $key => $data ) {
				add_action( "{$key}_edit_form_fields", array( $this, 'edit_new_custom_fields' ) );
				add_action( "create_{$key}", array( $this, 'save_custom_taxonomy_meta' ) );
				add_action( "edited_{$key}", array( $this, 'save_custom_taxonomy_meta' ) );
			}
		}

		//Если есть таксономии для регистрации, то создаём их
		if ( $taxonomies ) {
			foreach ( $taxonomies as $key => $taxonomy ) {
				$taxonomy = wp_parse_args( $taxonomy, array(
					'description'           => '',
					'public'                => true,
					'publicly_queryable'    => true,
					'show_in_nav_menus'     => true,
					'show_ui'               => true,
					'show_in_menu'          => true,
					'show_tagcloud'         => true,
					'show_in_rest'          => true,
					'rest_base'             => $key,
					'hierarchical'          => false,
					'update_count_callback' => '_update_post_term_count',
					'rewrite'               => true,
					'query_var'             => $key,
					'capabilities'          => array(),
					'meta_box_cb'           => null,
					'show_admin_column'     => false,
					'_builtin'              => false,
					'show_in_quick_edit'    => null,
				) );
				register_taxonomy( $key, $taxonomy['post_types'], $taxonomy );
			}
		}
	}

	function edit_new_custom_fields( $term ) {

		$taxonomy  = get_term( $term )->taxonomy;
		$fields    = ES_Start::get_config( 'term_meta' )[ $taxonomy ];
		$languages = ES_Start::get_languages();

		if ( count( $fields ) ):
			foreach ( $fields as $key => $field ): ?>
                <tr class="form-field es_meta_field">
                    <th scope="row" valign="top">
                        <label for="<?php echo esc_attr( $key ) ?>"><?php echo esc_attr( $field['name'] ) ?></label>
                    </th>
                    <td>
						<?php

						if ( count( $languages ) > 1 ) {
							$k = 1;
							echo "<div class=\"es_tax_tabs\"><ul>";
							foreach ( $languages as $locale => $language ) {
								$field_name = es_field_prefix( $key, $locale );
								$tab_class  = $k == 1 ? 'active' : '';
								echo "<li><a href=\"#es-tab-" . esc_attr( $field_name ) . "\" class=\"" . esc_attr( $tab_class ) . "\">" . esc_attr( $language['name'] ) . "</a></li>";
								$k ++;
							}
							echo "</ul>";
							unset( $k );
						}

						$i = 1;
						foreach ( $languages as $locale => $language ) {
							$field_name = es_field_prefix( $key, $locale );
							$tab_class2 = $i == 1 ? 'active' : '';
							$content    = get_term_meta( $term->term_id, $field_name, 1 );
							// если значение мета поля пустое
							if ( empty( $content ) ) {
								if ( isset( $field['default'] ) && $field['default'] == 'es_block' ) {
									$content = ! empty( $field['es_block'] ) ? es_get_block( $field['es_block'] ) : '';
								} else {
									if ( ! empty( $field['default'] ) ) {
										$content = $field['default'];
									}
								}
							}

							echo '<div id="es-tab-' . esc_attr( $field_name ) . '" class="es-tab-body ' . esc_attr( $tab_class2 ) . '">';

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


							echo "</div>";
							$i ++;
						}
						unset( $i );
						echo "</div>";

						?>
                        <br/>
                        <span class="description"><?php echo esc_html( $field['desc'] ) ?></span>
                    </td>
                </tr>

			<?php
			endforeach;
		endif;
	}

	function save_custom_taxonomy_meta( $term_id ) {
		if ( ! isset( $_POST['easy'] ) && ! wp_verify_nonce( $_POST['_wpnonce'] ) ) {
			return;
		}
		$extra     = array_map( 'trim', $_POST['easy'] );
		$config    = ES_Start::get_config();
		$taxonomy  = get_term( $term_id )->taxonomy;
		$languages = ES_Start::get_languages();
		$fields    = $config["term_meta"][ $taxonomy ];
		if ( count( $languages ) > 1 ) {
			foreach ( $languages as $lang_key => $lang ) {
				foreach ( $fields as $m_key => $value ) {
					$meta_key = es_field_prefix( $m_key, $lang_key );
					if ( empty( $value ) ) {
						delete_term_meta( $term_id, $meta_key );
						continue;
					}
					update_term_meta( $term_id, $meta_key, $extra[ $meta_key ] );
				}
			}
		} else {
			foreach ( $fields as $key => $value ) {
				if ( empty( $_POST['easy'][ $key ] ) ) {
					delete_term_meta( $term_id, $key );
					continue;
				}
				update_term_meta( $term_id, $key, wp_slash( $_POST['easy'][ $key ] ) );
			}
		}


		return $term_id;
	}

}