<?php
/**
 * Created by PhpStorm.
 * User: karak
 * Date: 31.08.2016
 * Time: 0:53
 */

function kv_customize_register( $wp_customize ) {
  class ES_Date_Time_Customize_Control extends WP_Customize_Control{
	  public function render_content() {
		  ?>
        <label>
          <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
          <input type="datetime-local" <?php $this->link(); ?> value="<?php echo esc_html($this->value()) ?>">
          <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
        </label>
		  <?php
	  }
  }
  class ES_Checkbox_Customize_Control extends WP_Customize_Control{
	  public function render_content() {
		  ?>
        <label>
          <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
          <input type="checkbox" <?php $this->link(); ?> value="1">
          <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
        </label>
		  <?php
	  }
  }
	class Example_Customize_Textarea_Control extends WP_Customize_Control {


		public $type = 'textarea';

		public function render_content() {
			?>
          <label>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <textarea rows="5"
                      style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
            <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
          </label>
			<?php
		}


	}

	$config   = new \ES_LIB\ES_config();
	$settings = $config->data['customizer'];

	if ( $settings ) {
		foreach ( $settings as $key => $setting ) {
			$wp_customize->add_section( $key, array( 'title' => $setting['title'] ) );
			if ( $setting['settings'] ) {
				foreach ( $setting['settings'] as $sek => $setting ) {
					$wp_customize->add_setting( $sek, array(
						'default'     => $setting['default'],
						'type'        => 'theme_mod',
						'description' => $setting['description'],
						'capability'  => 'edit_theme_options'
					) );
					switch ( $setting['type'] ) {
						case 'text':
							$wp_customize->add_control( $sek, array(
								'label'       => $setting['name'],
								'description' => $setting['description'],
								'section'     => $key,
								'type'        => 'text'
							) );
							break;
						case 'textarea':
							$wp_customize->add_control( new Example_Customize_Textarea_Control( $wp_customize, $sek, array(
								'label'       => $setting['name'],
								'description' => $setting['description'],
								'section'     => $key,
								'settings'    => $sek,
							) ) );
							break;
							case 'checkbox':
							$wp_customize->add_control( new ES_Checkbox_Customize_Control( $wp_customize, $sek, array(
								'label'       => $setting['name'],
								'description' => $setting['description'],
								'section'     => $key,
								'settings'    => $sek,
							) ) );
							break;
						case 'image':
							$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $sek, array(
								'label'       => $setting['name'],
								'description' => $setting['description'],
								'section'     => $key,
								'settings'    => $sek
							) ) );
							break;
						case 'media':
							$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $sek,
								array(
									'settings'      => $sek,
									'label'         => __( 'Default Media Control' ),
									'description'   => $setting['description'],
									'section'       => $key,
									'mime_type'     => 'image',
									// Required. Can be image, audio, video, application, text
									'button_labels' => array( // Optional
										'select'       => __( 'Select File' ),
										'change'       => __( 'Change File' ),
										'default'      => __( 'Default' ),
										'remove'       => __( 'Remove' ),
										'placeholder'  => __( 'No file selected' ),
										'frame_title'  => __( 'Select File' ),
										'frame_button' => __( 'Choose File' ),
									)
								)
							) );
							break;

						case 'date':
							$wp_customize->add_control( new WP_Customize_Date_Time_Control( $wp_customize, $sek,
								array(
									'label'              => $setting['name'],
									'description'        => $setting['description'],
									'section'            => $key,
									'settings'           => $sek,
									'include_time'       => false, // Optional. Default: true
									'allow_past_date'    => false, // Optional. Default: true
									'twelve_hour_format' => false, // Optional. Default: true
								)
							) );
							break;
						case 'datetime':
							$wp_customize->add_control( new ES_Date_Time_Customize_Control( $wp_customize, $sek,
								array(
									'label'              => $setting['name'],
									'description'        => $setting['description'],
									'section'            => $key,
									'settings'           => $sek,
									'include_time'       => true, // Optional. Default: true
									'allow_past_date'    => false, // Optional. Default: true
									'twelve_hour_format' => false, // Optional. Default: true
								)
							) );
							break;

						default:
							$wp_customize->add_control( $sek, array(
								'label'       => $setting['name'],
								'description' => $setting['description'],
								'section'     => $key,
								'type'        => 'text'
							) );
							break;
					}

				}
			}

		}
	}
}

add_action( 'customize_register', 'kv_customize_register' );