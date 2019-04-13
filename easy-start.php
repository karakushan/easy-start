<?php
/*
   Plugin Name: Easy start
   Plugin URI: http://wordpress.org/extend/plugins/easy-start/
   Version: 0.1
   Author: Vitaliy Karakushan
   Description: Create your own post types, taxonomies, additional fields, topic settings using the json file.
   Text Domain: easy-start
   License: GPLv3
  */

/*
    "WordPress Plugin Template" Copyright (C) 2016 Vitaliy Karakushan  (email : karakushan@gmail.com)

    This following part of this file is part of WordPress Plugin Template for WordPress.

    WordPress Plugin Template is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WordPress Plugin Template is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database Extension.
    If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

define( 'ES_DIR_PATH', plugin_dir_path( __FILE__ ) ); // системный путь к плагину
define( 'ES_DIR_URL', plugin_dir_url( __FILE__ ) ); // урл к плагину относительно домена
define( 'ES_NO_IMAGE_URL', plugin_dir_url( __FILE__ ) . 'assets/img/no-image.png' ); // путь к заглушке изображения
require_once 'vendor/autoload.php';
new ES_LIB\ES_Start;


