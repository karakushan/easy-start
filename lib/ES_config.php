<?php
namespace ES_LIB;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Es_config class
 */
class ES_config
{
    public $data=array();
    public static $languages=array();
    function __construct()
    {
        // определяем json файлы из которых будем читать онфигурационные установки и превращать в массив
        $config_file =  get_template_directory().'/es-config.json';
        $config_file_plugin = ES_DIR_PATH.'templates/es-config.json';
        // проверяем наличие файла es-config.json в корне активной темы
        if (file_exists($config_file)) {
            $config_ready = file_get_contents($config_file);
            $config=json_decode($config_ready,true);

            if (!empty($config) && is_array($config)) {
               $this->data = $config;
           }
       } else {
        // если конф. файл не найден берём конфигурацию из файла es-config.json плагина
         $config_ready_plugin = file_get_contents($config_file_plugin);
         $config=json_decode($config_ready_plugin,true);
     }
     $this->data=$config;
     if (!empty($config['languages'])) {
         self::$languages=$config['languages'];
     }
 }
}