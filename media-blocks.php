<?php
/*
Plugin Name: Мультимедия блоки
Plugin URI:
Description: Добавляет возможность создавать медиа блоки (Карусел, слайдер, галарея..)
Version: 1.3.2 alpha
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
*/
/*  Copyright 2017  NikolayS93  (email: NikolayS93@ya.ru)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!function_exists('is_wp_debug')){
  function is_wp_debug(){
    if( WP_DEBUG ){
      if( defined(WP_DEBUG_DISPLAY) && ! WP_DEBUG_DISPLAY){
        return false;
      }
      return true;
    }
    return false;
  }
}
if(!function_exists('cpJsonStr')){
  function cpJsonStr($str){
    $str = preg_replace_callback('/\\\\u([a-f0-9]{4})/i', create_function('$m', 'return chr(hexdec($m[1])-1072+224);'), $str);
    return iconv('cp1251', 'utf-8', $str);
  }
}

class DT_MediaBlocks
{
  const VERSION = 1.3;
  const CLASSES_DIR = '/classes/';
  
  protected $errors = array();

    function __construct(){
      $this->define_constants();
      if( is_admin() ){
      	$this->include_required_classes( array(
      		'isAdminView' => 'admin-callback',
      		'DTForm' => 'dt-form-render',
      		) );
      }
      else {
      	$this->include_required_classes( array(
      		'MediaOutput' => 'front-callback',
      		) );
      }
      
      add_action('init', array($this, 'register_post_types'));
      
      if(is_admin()){
      	new isAdminView();
      }
      else {
      	new MediaOutput();
      }
        
    }

    /**
     * Global Methods
     */
    private function define_constants() {
    	define( 'DTM_PREFIX', 'dtmm_');
    	define( 'DTM_TYPE', 'multimedia-base');

    	define( 'DT_MULTIMEDIA_BASE_URL',   trailingslashit( plugins_url( basename( __DIR__ ) ) ) );
    	define( 'DT_MULTIMEDIA_ASSETS_URL', trailingslashit( DT_MULTIMEDIA_BASE_URL . 'assets' ) );
    	define( 'DT_MULTIMEDIA_PATH',       plugin_dir_path( __FILE__ ) );
    }

    function show_admin_notice(){
      if(sizeof($this->errors) == 0)
        return;

      foreach ($this->errors as $error) {
        $type = (isset($error['type'])) ? $error['type'] . ' ' : ' ';
        $msg = (isset($error['msg'])) ? apply_filters('the_content', $error['msg']) : false;
        if($msg)
          echo '
        <div id="message" class="'.$type.'notice is-dismissible">
          '.$msg.'
        </div>';
        else
          echo '
        <div id="message" class="'.$type.'notice is-dismissible">
          <p>Обнаружена неизвестная ошибка!</p>
        </div>';
      }
    }
    protected function set_notice($msg=false, $type='error'){
      $this->errors[] = array('type' => $type, 'msg' => $msg);

      add_action( 'admin_notices', array($this, 'show_admin_notice') );
      return false;
    }

    private function include_required_classes( $classes = array() ){
        if(! is_array($classes) )
          return false;

        foreach ( $classes as $class_name => $path ) {
          $path = DT_MULTIMEDIA_PATH . self::CLASSES_DIR . $path . '.php';

          if ( is_readable( $path ) && ! class_exists( $class_name ) ) 
            require_once( $path );
        }
    }

    function register_post_types(){
      register_post_type( DTM_TYPE, array(
        'query_var' => false,
        'rewrite' => false,
        'public' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'show_in_nav_menus' => false,
        'show_ui' => true,
        'supports' => array('title', 'custom-fields', 'excerpt'),
        'labels' => array(
          'name' => 'Медиа блоки'
          )
        )
      );
    }

    /**
     * Settings & Options
     */
    // include settings file
    protected function get_settings( $file = false ) {
      if( empty($file) )
        return false;

      $path = DT_MULTIMEDIA_PATH . '/settings/'.$file.'.php';

      if ( is_readable( $path ) ) 
        return include( $path );
      else
        $this->set_notice('Файл настроек не найден!');
    }

    // main settings
    protected function block_meta_settings( $post_id, $settings_name, $block_values = false ){
      if(!$settings_name || !$post_id)
        return false;

      $result = array();
      $values = ($block_values) ? $block_values : $this->block_meta_field( $post_id, $settings_name.'_opt' );
      $settings_type = $this->get_settings( $settings_name );
      
      foreach ( $settings_type as $param ){
        // p_name - это ID или NAME, иначе '';
        if( isset($param['id']) )
          $p_name = $param['id'];
        else
          $p_name = (isset($param['name'])) ? $param['name'] : '';

        // Если не указан default принимаем placeholder, иначе '';
        if(isset($param['default']))
          $default = $param['default'];
        else
          $default = isset($param['placeholder']) ? $param['placeholder'] : '';

        if(isset($values[$p_name]) && $values[$p_name] != $default){
          // Если пришел пустой checkbox присваиваем ему 'false'
          if( $values[$p_name] == '' && $param['type'] == 'checkbox' )
              $values[$p_name] = 'false';

          // принимаем значения если они не пустые, или если это select (Даже пустые)
          if( $values[$p_name] != '' || $param['type'] == 'select' )
            $result[$p_name] = $values[$p_name];
        }
      }
      
      if( $block_values )
        update_post_meta( $post_id, '_'.DTM_PREFIX.$settings_name.'_opt', $result );
      else
        return $result;
    }

    // side settings
    protected function block_meta_field( $post_id, $var, $is_record = false ){
      if( !$post_id || !$var )
        return false;

      if($is_record){
        if(isset($_POST[$var]))
          update_post_meta( $post_id, '_'.DTM_PREFIX.$var, $_POST[$var] );
      }
      else {
        return get_post_meta( $post_id, '_'.DTM_PREFIX.$var, true );
      }
    }

    // carousel, slider, gallery..
    protected function get_media_type( $media_id ){
      $main_type = get_post_meta( $media_id, '_' . DTM_PREFIX . 'main_type', true );
      $type = get_post_meta( $media_id, '_' . DTM_PREFIX . 'type', true );
      
      return array( $main_type, $type );
    }
}
new DT_MediaBlocks();

// function rewrite_flush() {
//     DT_MultiMedia::register_post_types();
//     flush_rewrite_rules();
// }
// register_activation_hook( __FILE__, 'rewrite_flush' );
// 
//