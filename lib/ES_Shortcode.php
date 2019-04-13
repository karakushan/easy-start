<?php
namespace ES_LIB;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Es_config class
 */
class ES_Shortcode
{
    function __construct(){
        add_shortcode('es_terms',array($this,'es_terms_shortcode'));
        add_shortcode('es_posts',array($this,'es_posts_shortcode'));
        add_shortcode('es_block',array($this,'es_block_shortcode'));
        add_shortcode('es_post_meta',array($this,'es_post_meta_shortcode'));
    }

    // шорткод для вывода терминовв, категорий
    public function es_terms_shortcode($atts)
    {
        $atts=shortcode_atts(array(
            'template'=>'es-terms',
            'taxonomy'=>'category',
            'hide_empty'=>0
            ),$atts,'es_terms');
        $terms=get_terms($atts);

        if ($terms) {
           ob_start();
           foreach ($terms as $key => $term) {
            $template=TEMPLATEPATH.DIRECTORY_SEPARATOR .$atts['template'].'.php';
            if (file_exists($template)) {
                include($template);
            }
        }
        $content=ob_get_contents();
        ob_end_clean();

    }

    return $content; 
}  

//  шорткод для вывода постов  
public function es_posts_shortcode($atts)
{
    global $post;
    $atts=shortcode_atts(array(
        'template'=>'es-posts',
        'post_type'=>'post',
        'posts_per_page'=>10
        ),$atts,'es_posts');
    $all_posts=get_posts($atts);

    if ($all_posts) {
       ob_start();
       foreach ($all_posts as $key => $post) { setup_postdata( $post );
        get_template_part($atts['template']);
    }
    wp_reset_postdata();
    $content=ob_get_contents();
    ob_end_clean();
}

return $content; 
}

//  шорткод [es_block] для получения блока 
public function es_block_shortcode($atts)
{
    global $post;
    $atts=shortcode_atts(array(
        'id'=>0,
        ),$atts,'es_block');
    $content=es_get_block($atts['id']);
    return $content; 
}

//  шорткод [es_post_meta] для получения мета поля поста
public function es_post_meta_shortcode($atts)
{
    global $post;
    $atts=shortcode_atts(array(
        'id'=>0,
        'meta_key'=>'',
        'type'=>'text'
        ),$atts,'es_block');
    $content=es_post_meta($atts['meta_key'],$atts['id'],array('type'=>$atts['type'],'echo'=>false));
    return $content; 
}

}