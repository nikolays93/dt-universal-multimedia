<?php
namespace MB;
/*
Plugin Name: Мульти блоки
Plugin URI:
Description: Добавляет возможность создавать медиа блоки (Карусел, слайдер, галарея..)
Version: 1.5.1 alpha
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

define( 'DTM_PREFIX', 'dtmm_');
define( 'DTM_TYPE', 'multimedia-base');

define( 'DT_MULTIMEDIA_BASE_URL',   trailingslashit( plugins_url( basename( __DIR__ ) ) ) );
define( 'DT_MULTIMEDIA_ASSETS_URL', trailingslashit( DT_MULTIMEDIA_BASE_URL . 'assets' ) );
define( 'DT_MULTIMEDIA_PATH',       plugin_dir_path( __FILE__ ) );

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
if(!function_exists('_isset_default')){
  function _isset_default(&$var, $default, $unset = false){
    $result = $var = isset($var) ? $var : $default;
    if($unset)
      $var = FALSE;
    return $result;
  }
  function _isset_false(&$var, $unset = false){ return _isset_default( $var, false, $unset ); }
  function _isset_empty(&$var, $unset = false){ return _isset_default( $var, '', $unset ); }
}

class DT_MediaBlocks {
  const VERSION = 1.5;
  const CLASSES_DIR = 'include/';
  
  function __construct(){
    add_action('init', array($this, 'register_post_types'));

    $this->include_required_classes( array('MB\JQScript' => 'class-wp-jqscript') );
    
    if( is_admin() ){
    	$this->include_required_classes( array(
        'scssc'       => 'scss.inc',
        'MB\WPForm'   => 'class-wp-form-render',
        'isAdminView' => 'admin-callback'
        ) );
      new isAdminView();
    }
    else {
    	$this->include_required_classes( array('MediaOutput' => 'front-callback') );
      new MediaOutput();
    }
  }

  /**
   * Global Methods
   */
  private function include_required_classes( $classes = array() ){
      if(! is_array($classes) )
        return false;

      foreach ( $classes as $class_name => $path ) {
        $path = DT_MULTIMEDIA_PATH . self::CLASSES_DIR . $path . '.php';

        if ( is_readable( $path ) && ! class_exists( $class_name ) ) 
          require_once( $path );
        else
          wp_die( 'NOT READBLE : '.$path );
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
        'name' => 'Мульти Блоки',
        'singular_name'      => 'Мульти Блок',
        'add_new'            => 'Добавить блок',
        'add_new_item'       => 'Добавление блок',
        'edit_item'          => 'Редактирование блока',
        'new_item'           => 'Новый блок',
        'view_item'          => 'Смотреть МультиБлок',
        'search_items'       => 'Искать МультиБлок',
        'menu_name'          => 'Мульти Блоки',
        )
      )
    );
  }
  static protected function get_assets_list(){
    $affix = (is_wp_debug()) ? '' : '.min';

    $assets = array(
      'owl-carousel' => array(
        'js' => 'owl-carousel/owl.carousel'.$affix.'.js',
        'core' => 'owl-carousel/owl.carousel'.$affix.'.css',
        'theme' => 'owl-carousel/owl.theme.css',
        'ver' => '1.3.3'
        ),
      'slick' => array(
        'js' => 'slick/slick.js',
        'core' => 'slick/slick.css',
        'theme' => 'slick/slick-theme.css',
        'ver' => '1.6.0'
        )
    );
    return $assets;
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
  protected function get_settings_file( $file = false, $main_type = 'carousel' ){
    if( empty($file) )
      return false;

    $path = DT_MULTIMEDIA_PATH . 'settings/'.$file.'.php';

    if ( is_readable( $path ) ) 
      return include( $path );

    return false;
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
   * @param  string $settings_name      settings filename (subtype if ($settings_maintype))
   * @param  string $settings_maintype  main_type settinigs (carousel, gallery..)
   * @param  values $block_values       to record, get installed values if 'false'
   * @return is get (array) else (null)
   */
  protected function settings_from_file( $post_id, $settings_name, $settings_maintype = false, $block_values = false ){
    $post_id = intval( $post_id );
    if( !$settings_name || !$post_id )
      return false;

    $result = array();
    $values = ($block_values) ? $block_values : $this->meta_field( $post_id, $settings_name.'_opt' );
    $filename = ($settings_maintype) ? 'sub/'.$settings_name : 'main/'.$settings_name;
    $settings = $this->get_settings_file( $filename, $settings_maintype );

    if( ! $settings )
      return false;
    
    foreach ( $settings as $param ){
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
      // $debug[] = $pn .' => '. $values[$pn] . ' (' . $param['type'] . ')';
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

/**
 * Filters
 */
// function setup_filters(){
//   add_filter( 'array_options_before_view', 'split_array', 10, 3 );
//   add_filter( 'json_change_values',        'str_to_bool', 10, 1 );
//   add_filter( 'json_change_values',        'json_function_names', 15, 1 );
  add_filter( 'dash_to_underscore',        'MB\dash_to_underscore', 10, 1 );
// }

// function split_array( $arr, $keys = array('navigationTextPrev', 'navigationTextNext'), $result_key =  'navigationText'){
//   if( sizeof($arr) ){
//     if(isset($arr[ $keys[0] ])){
//       $prev = $arr[ $keys[0] ];
//       unset($arr[ $keys[0] ]);
//     }
//     else {
//       $prev = 'prev';
//     }

//     if(isset($arr[ $keys[1] ])){
//       $next = $arr[ $keys[1] ];
//       unset($arr[ $keys[1] ]);
//     }
//     else {
//       $next = 'next';
//     }

//     $arr[$result_key] = array($prev, $next);
//   }

//   return $arr;
// }
// function str_to_bool( $json ){
//   $json = str_replace('"true"',  'true',  $json);
//   $json = str_replace('"on"',  'true',  $json);
//   $json = str_replace('"false"', 'false', $json);
//   $json = str_replace('"off"', 'false', $json);
//   return $json;
// }
// function json_function_names( $json ){
//   $json = str_replace( '"%', '', $json );
//   $json = str_replace( '%"', '', $json );
//   return $json;
// }
function dash_to_underscore( $str ){
  $str = str_replace('-', '_', $str);
  return $str;
}

new DT_MediaBlocks();

// function rewrite_flush() {
//     DT_MultiMedia::register_post_types();
//     flush_rewrite_rules();
// }
// register_activation_hook( __FILE__, 'rewrite_flush' );
// 
//