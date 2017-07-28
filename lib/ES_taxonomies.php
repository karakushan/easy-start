<?php
namespace ES_LIB;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Class Es_taxonomies
 */
class Es_taxonomies
{

    function __construct()
    {
        add_action('init', array($this, 'term_meta_create'));
    }

    public function term_meta_create()
    {
        $config = new ES_config;
        $term_meta = $config->data["term_meta"];
        $taxonomies = isset($config->data["taxonomies"]) ? $config->data["taxonomies"] : array();

        //Если задано в настройках доп. поля таксономии то создаём их
        if ($term_meta) {
            foreach ($term_meta as $key => $data) {
                add_action("{$key}_edit_form_fields", array($this, 'edit_new_custom_fields'));
                add_action("create_{$key}", array($this, 'save_custom_taxonomy_meta'));
                add_action("edited_{$key}", array($this, 'save_custom_taxonomy_meta'));
            }
        }

        //Если есть таксономии для регистрации, то создаём их
        if ($taxonomies) {
            foreach ($taxonomies as $key => $taxonomy) {
                register_taxonomy($key, $taxonomy['post_types'], $taxonomy);
            }
        }
    }

    function edit_new_custom_fields($term)
    {
        $config = new Es_config;
        $taxonomy = get_term($term)->taxonomy;
        $fields = $config->data["term_meta"][$taxonomy];
        $languages = isset($config->data["languages"]) ? $config->data["languages"] : array();


        if (count($fields)):
            foreach ($fields as $key => $field): ?>
                <tr class="form-field es_meta_field">
                    <th scope="row" valign="top"><label for="<?php echo $key ?>"><?php echo $field['name'] ?></label>
                    </th>
                    <td>
                        <?php
                        if (!empty($languages)) {
                            echo "<div class=\"es_tax_tabs\"><ul>";
                            $k = 1;
                            foreach ($languages as $lks => $langw) {
                                $field_name = es_field_prefix($key, $lks );
                                $tab_class = $k == 1 ? 'active' : '';
                                echo "<li><a href=\"#es-tab-{$field_name}\" class=\"$tab_class\">{$langw['name']}</a></li>";
                                $k++;
                            }
                            echo "</ul>";
                            unset($k);
                            $i = 1;
                            foreach ($languages as $lk => $lang) {

                                $field_name = es_field_prefix($key, $lk);
                                $tab_class2 = $i == 1 ? 'active' : '';
                                $content = get_term_meta($term->term_id, $field_name, 1);
                                // если значение мета поля пустое
                                if (empty($content)) {
                                    if (isset($field['default']) && $field['default'] == 'es_block') {
                                        $content = !empty($field['es_block']) ? es_get_block($field['es_block']) : '';
                                    } else {
                                        if (!empty($field['default'])) {
                                            $content = $field['default'];
                                        }
                                    }
                                }

                                echo "<div id=\"es-tab-{$field_name}\" class=\"es-tab-body $tab_class2\">";

                                switch ($field['type']) {

                                    case 'editor':
                                        $editor_id = mb_strtolower(str_replace(array('_'), array('-'), $field_name));
                                        $content = wp_unslash(htmlspecialchars_decode(get_term_meta($term->term_id, $field_name, 1)));

                                        wp_editor($content, $editor_id, array(
                                            'wpautop' => false,
                                            'media_buttons' => 1,
                                            'textarea_name' => 'easy[' . $field_name . ']',
                                            'tinymce' => array(
                                                'verify_html' => false
                                            )
                                        ));
                                        break;
                                    case 'checkbox' :
                                        es_field_template($field['type'], $field_name, $content);
                                        break;
                                    case 'select':
                                        es_field_template($field['type'], $field_name, $content, array('values' => $field['values']));
                                        break;
                                    case 'taxonomy' :
                                        es_field_template($field['type'], $field_name, $content, array('taxonomy' => $field['taxonomy']));
                                        break;
                                    case 'image':
                                        es_field_template($field['type'], $field_name, $content);
                                        break;
                                    default:
                                        echo "<input type=\"text\" name=\"easy[" . $field_name . "]\" value=\"" . get_term_meta($term->term_id, $field_name, 1) . "\">";
                                        break;
                                }


                                echo "</div>";
                                $i++;
                            }
                            unset($i);
                            echo "</div>";
                        } else {
                            $field_name = $key;
                            $content = get_term_meta($term->term_id, $field_name, 1);
	                        $content=wp_unslash( $content);
                            // если значение мета поля пустое
                            if (empty($content)) {
                                if (isset($field['default']) && $field['default'] == 'es_block') {
                                    $content = !empty($field['es_block']) ? es_get_block($field['es_block']) : '';
                                } else {
                                    if (!empty($field['default'])) {
                                        $content = $field['default'];
                                    }
                                }
                            }
                            switch ($field['type']) {
                                case 'editor':
                                    $editor_id = mb_strtolower(str_replace(array('-', '_'), array('', ''), $key));
                                    wp_editor($content, $editor_id, array(
                                        'wpautop' => false,
                                        'media_buttons' => 1,
                                        'textarea_name' => 'easy[' . $field_name . ']',
                                        'tinymce' => array(
                                            'verify_html' => false
                                        )
                                    ));
                                    break;
                                case 'taxonomy' :
                                    es_field_template($field['type'], $field_name, $content, array('taxonomy' => $field['taxonomy']));
                                    break;
                                case 'image':
                                    es_field_template($field['type'], $field_name, $content);
                                    break;
	                            case 'gallery':
		                            es_field_template( $field['type'], $field_name, $content );
		                            break;
                                case 'checkbox' :
                                    es_field_template($field['type'], $field_name, $content);
                                    break;
                                case 'select':
                                    es_field_template($field['type'], $field_name, $content, array('values' => $field['values']));
                                    break;
                                case 'taxonomy' :
                                    es_field_template($field['type'], $field_name, $content, array('taxonomy' => $field['taxonomy']));
                                    break;
                                default:
                                    echo "<input type=\"text\" name=\"easy[" . $field_name . "]\" value=\"" . $content . "\">";
                                    break;
                            }
                        }


                        ?>
                        <br/>
                        <span class="description"><?php echo $field['desc'] ?></span>
                    </td>
                </tr>

                <?php
            endforeach;
        endif;
    }

    function save_custom_taxonomy_meta($term_id)
    {
        if (!isset($_POST['easy']))
            return;
        $extra = array_map('trim', $_POST['easy']);
        $config = new Es_config;
        $taxonomy = get_term($term_id)->taxonomy;
        $languages = Es_config::$languages;
        $fields = $config->data["term_meta"][$taxonomy];
        if (count($languages) > 1) {
            foreach ($languages as $lang_key => $lang) {
                foreach ($fields as $m_key => $value) {
                    $meta_key = es_field_prefix($m_key, $lang_key);
                    if (empty($value)) {
                        delete_term_meta($term_id, $meta_key);
                        continue;
                    }
                    update_term_meta($term_id, $meta_key, $extra[$meta_key]);
                }
            }
        } else {
            foreach ($fields as $key => $value) {
                if (empty($_POST['easy'][ $key ])) {
                    delete_term_meta($term_id, $key);
                    continue;
                }
                update_term_meta($term_id, $key,  wp_slash( $_POST['easy'][ $key ] ));
            }
        }


        return $term_id;
    }

}