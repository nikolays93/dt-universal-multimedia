<?php
/*
Plugin Name: Мультимедия блоки
Plugin URI:
Description: Добавляет возможность создавать медиа блоки (Карусел, слайдер, галарея..)
Version: 1.4.1 alpha
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
  const VERSION = 1.4;
  const CLASSES_DIR = '/classes/';
  
  protected $errors = array();

    function __construct(){
      $this->define_constants();
      $this->setup_filters();
      $this->include_required_classes( array('DTForm' => 'dt-form-render') );
      if( is_admin() ){
      	$this->include_required_classes( array('isAdminView' => 'admin-callback') );
      }
      else {
      	$this->include_required_classes( array('MediaOutput' => 'front-callback') );
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
     * Filters
     */
    function setup_filters(){
      add_filter( 'array_options_before_view',  array($this, 'owl_nextprev'), 10, 1 );
      add_filter( 'json_change_values',       array($this, 'str_to_bool'), 10, 1 );
      add_filter( 'json_change_values',       array($this, 'json_function_names'), 15, 1 );
      add_filter( 'dash_to_underscore',       array($this, 'dash_to_underscore_filter'), 10, 1 );
    }

    function owl_nextprev( $metas_arr ){
      if(isset($metas_arr['navigationTextNext']) || isset($metas_arr['navigationTextPrev'])){
        if(isset($metas_arr['navigationTextPrev'])){
          $prev = $metas_arr['navigationTextPrev'];
          unset($metas_arr['navigationTextPrev']);
        }
        else {
          $prev = 'prev';
        }
        if(isset($metas_arr['navigationTextNext'])){
          $next = $metas_arr['navigationTextNext'];
          unset($metas_arr['navigationTextNext']);
        }
        else {
          $next = 'next';
        }

        $metas_arr['navigationText'] = array($prev, $next);
      }
      return $metas_arr;
    }
    function str_to_bool( $json ){
      $json = str_replace('"on"',  'true',  $json);
      $json = str_replace('"false"', 'false', $json);
      return $json;
    }
    function json_function_names( $json ){
      $json = str_replace( '"%', '', $json );
      $json = str_replace( '%"', '', $json );
      return $json;
    }
    
    function dash_to_underscore_filter( $str ){
      $str = str_replace('-', '_', $str);
      return $str;
    }

    /**
     * Settings & Options
     *
     * Include settings file
     * 
     * @param  string settings filename
     * @param  string type returned settings
     * @return array settings
     */
    protected function get_settings( $file = false, $main_type = 'carousel' ) {
      if( empty($file) )
        return false;

      $path = DT_MULTIMEDIA_PATH . '/settings/'.$file.'.php';

      if ( is_readable( $path ) ) 
        return include( $path );
      else
        $this->set_notice('Файл настроек не найден!');
    }

    /**
     * Update or Get post meta with prefix (create if empty)
     * 
     * @param  int
     * @param  string meta name (without prefix)
     * @param  string values for update or get
     */
    protected function meta_field( $post_id, $var, $record = false ){
      if( !$post_id )
        return false;

      if( $record !== false ){
        if( $record != '' ){
          update_post_meta( $post_id, '_'.DTM_PREFIX.$var, $record );
        }
        else {
          delete_post_meta( $post_id, '_'.DTM_PREFIX.$var );
        }
      }
      else {
        return get_post_meta( $post_id, '_'.DTM_PREFIX.$var, true );
      }
    }

    /**
     * Get or Set values to meta from settings file
     * 
     * @param  int    $post_id
     * @param  string $settings_name      settings filename
     * @param  string $settings_maintype  sub_type settinigs (owl-carousel, slick, fancy..)
     * @param  values $block_values       to record, get installed values if 'false'
     * @return is get (array) else (null)
     */
    protected function settings_from_file( $post_id, $settings_name, $settings_maintype = false, $block_values = false ){
      $post_id = intval( $post_id );
      if( !$settings_name || !$post_id )
        return false;

      $result = array();
      $values = ($block_values) ? $block_values : $this->meta_field( $post_id, $settings_name.'_opt' );
      $settings_type = $this->get_settings( $settings_name, $settings_maintype );

      foreach ( $settings_type as $param ){
        // Если не указан name принимаем id, иначе '';
        if( !isset($param['name']) )
          $param['name'] = isset($param['id']) ? $param['id'] : '';

        // Если не указан default принимаем placeholder, иначе '';
        if( !isset($param['default']) )
          $param['default'] = isset($param['placeholder']) ? $param['placeholder'] : '';

        $pn = $param['name'];
        if($settings_maintype !== false){
          if(isset($values[$pn]) && $values[$pn] != $param['default']){
          // Пустой checkbox записываем как 'false'
            if( $values[$pn] == '' && $param['type'] == 'checkbox' )
              $result[$pn] = 'false';

          // Принимаем значения если они не пустые, или если это select (Даже пустые)
            elseif( $values[$pn] != '' || $param['type'] == 'select' )
              $result[$pn] = $values[$pn];
          }
        }
        else {
          if( isset($values[$pn]) && ($values[$pn] != '' || $param['type'] == 'select') )
              $result[$pn] = $values[$pn];
        }


        // $debug[] = $pn .' => '. $values[$pn] . '(' . $param['type'] . ')';
      }


      if( $block_values ){
        $this->meta_field( $post_id, $settings_name.'_opt', $result );
        // $_debug = print_r($debug, 1) . "\n\n" . print_r($result, 1);
        // file_put_contents(__DIR__ . '/post_result.log', $_debug);
      }
      else{
        return $result;
      }
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