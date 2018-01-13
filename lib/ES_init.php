<?php

namespace ES_LIB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * class ES_init
 */
class ES_init {
	function __construct() {
		add_action( 'plugins_loaded', array( $this, 'es_localize' ) );
		add_action( 'admin_menu', array( $this, 'es_admin_menu' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'es_inc_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'es_admin_scripts' ) );
		add_action( 'admin_init', array( $this, 'register_es_settings' ) );

		add_action( 'wp_ajax_es_get_blocks', array( $this, 'get_tinymce_blocks' ) );
		add_action( 'wp_ajax_nopriv_es_get_blocks', array( $this, 'get_tinymce_blocks' ) );
//Иннициализируем классы
		new ES_ctypes;
		new ES_query;
		new ES_taxonomies;
		new ES_shortcode;
	}

	function es_localize() {
		load_plugin_textdomain( 'easy-start', false, 'easy-start/languages/' );
	}

	public function get_tinymce_blocks() {
		$data  = array();
		$posts = get_posts( array( 'post_type' => 'es_blocks', 'posts_per_page' => '-1' ) );
		if ( $posts ) {
			foreach ( $posts as $key => $post ) {
				$data[] = array(
					'text'  => $post->post_title,
					'value' => $post->post_content
				);
			}
		}
		echo json_encode( $data );
		exit;
	}

	function es_admin_menu() {
		$config = new Es_config();
		if ( $config->data["show_admin_menu"] === true ) {
			add_menu_page( 'Easy Start', 'Easy Start', 'manage_options', 'easy-start', array(
				$this,
				'es_menu_page'
			), 'dashicons-schedule', 61 );
		}
		// add_submenu_page( 'easy-start',__( 'Options Groups', 'easy-start' ),__( 'Options Groups', 'easy-start' ), 'manage_options', 'easy-start-group',array($this,'es_menu_page'));
		add_submenu_page( 'easy-start', __( 'Export', 'easy-start' ), __( 'Export', 'easy-start' ), 'manage_options', 'easy-start-import-export', array(
			$this,
			'es_menu_page'
		) );
		// add_submenu_page( 'easy-start',__( 'Blocks', 'easy-start' ),__( 'Blocks', 'easy-start' ), 'manage_options', 'easy-start-blocks',array($this,'es_menu_page'));
		//add_submenu_page( 'easy-start',__( 'Manual', 'easy-start' ),__( 'Manual', 'easy-start' ), 'manage_options', 'easy-start-manual',array($this,'es_menu_page'));


	}

	function es_menu_page() {

		$page     = $_GET['page'];
		$template = ES_DIR_PATH . "templates/$page.php";

		if ( file_exists( $template ) ) {
			require( $template );
		} else {
			echo "Файл шаблона не найден";
		}
	}

	function es_inc_scripts() {
		wp_enqueue_script('easy-start',ES_DIR_URL."assets/js/easy-start.js",array("jquery"),null,true);
		wp_enqueue_script('es-lightgallery',ES_DIR_URL."assets/plugins/lightGallery/dist/js/lightgallery-all.min.js",array("jquery"),null,true);

		wp_enqueue_style( 'es-lightgallery', ES_DIR_URL. 'assets/plugins/lightGallery/dist/css/lightgallery.min.css' );
		wp_enqueue_style( 'easy-start', ES_DIR_URL. 'assets/css/es-style.css' );
	}

	function es_admin_scripts() {

		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}
		wp_enqueue_style( 'es-admin-css', ES_DIR_URL . 'assets/css/es-admin.css' );

		wp_enqueue_script( 'es-admin-js', ES_DIR_URL . 'assets/js/easy-start-admin.js', array(
			'jquery',
			'jquery-ui-core'
		), false, true );
	}



	function register_es_settings() {
		register_setting( 'es-options-group', 'es-config-json' );

// проверка доступен ли WYSIWYG
		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_filter( "mce_external_plugins", array( $this, "custom_mce_external_plugins" ) );
			add_filter( 'mce_buttons', array( $this, 'custom_mce_buttons' ) );
		}
	}

	function custom_mce_external_plugins( $plugin_array ) {
		$plugin_array['my_custom_button'] = ES_DIR_URL . 'assets/js/easy-tinymce.js';

		return $plugin_array;
	}

	function custom_mce_buttons( $buttons ) {
		array_push( $buttons, "my_custom_button" );

		return $buttons;
	}
} 
