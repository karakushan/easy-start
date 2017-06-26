<?php
/**
 * Created by PhpStorm.
 * User: karak
 * Date: 31.08.2016
 * Time: 0:53
 */

function kv_customize_register( $wp_customize ) {
    class Example_Customize_Textarea_Control extends WP_Customize_Control {


        public $type = 'textarea';
        public function render_content() {
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
            </label>
            <?php
        }
    }

    $config=new \ES_LIB\ES_config();
    $settings=$config->data['customizer'];

    if ($settings){
        foreach ($settings as $key=>$setting) {
            $wp_customize->add_section( $key, array('title' => $setting['title']) );
            if ($setting['settings']){
                foreach ($setting['settings'] as $sek=>$setting){
                    $wp_customize->add_setting( $sek, array( 'default' =>$setting[ 'default'], 'type' => 'theme_mod', 'capability' => 'edit_theme_options' ) );
                    switch ($setting['type']){
                        case 'text':
                            $wp_customize->add_control( $sek, array(
                                'label' =>$setting['name'],
                                'section' =>$key,
                                'type' => 'text'
                            ) );
                            break;
                        case 'textarea':
                            $wp_customize->add_control( new Example_Customize_Textarea_Control( $wp_customize, $sek, array(
                                'label' =>$setting['name'],
                                'section' =>$key,
                                'settings' => $sek,
                            ) ) );
                            break;
                        case 'image':
                            $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, $sek , array(
                                'label' =>$setting['name'],
                                'section' =>$key,
                                'settings' =>$sek
                            )));
                            break;
                        default:
                            $wp_customize->add_control( $sek, array(
                                'label' =>$setting['name'],
                                'section' =>$key,
                                'type' => 'text'
                            ) );
                            break;
                    }

                }
            }

        }
    }
}
add_action( 'customize_register', 'kv_customize_register' );