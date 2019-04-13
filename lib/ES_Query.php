<?php
namespace ES_LIB;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
/**
 * class ES_query		
 */

class ES_Query
{
	function __construct()
	{
		add_action( 'init',array($this,'es_saved_group'));
	}
	public function es_saved_group()
	{
		if (!isset($_GET['page'])) return;
		
		if($_GET['page']!='easy-start-group' || !wp_verify_nonce( $_GET['_wpnonce'])) return;
		if ($_GET['action']==='add-group') {
			$es_options=array();
			
			$opt_post=array(
				'name'=>$_REQUEST['es_group_name'],
				'slug'=>'es-'.es_transliteration($_REQUEST['es_group_slug']),
				'type'=>$_REQUEST['es_group_type']
				);
			$es_options=get_option('_es_options_group');

			if ($es_options){
				if (count($es_options)) {
					foreach ($es_options as $key => $es_option) {
						if ($es_option['name']=='') continue;
						$opt_res[$key]['name']= $es_option['name'];
						$opt_res[$key]['slug']= $es_option['slug'];
						$opt_res[$key]['type']= $es_option['type'];

					}
					$opt_res[]=$opt_post;
				} else {
					$opt_res[]=$opt_post;
				}
			}else{
				$opt_res[]=$opt_post;
			}

			
			
			$opt_update=update_option('_es_options_group',$opt_res);
			if ($opt_update) {
				add_action('admin_notices', function(){
					echo '<div id="message" class="updated"><p>Группа добавлена</p></div>';
				});
			} else {
				add_action('admin_notices', function(){
					echo '<div id="message" class="error"><p>Ошибка добавления группы опций</p></div>';
				});
			}

		}
	}
} 
