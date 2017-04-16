<?php
namespace MB;
/**
 * Class Name: WP-JQScript
 * Class URI: https://github.com/nikolays93/JQScript
 * Description: initializer and enqueuer for jQuery WordPress scripts and styles
 * Version: 1.0
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !has_filter( 'jscript_php_to_json', 'json_encode' ) ){

  add_filter( 'jscript_php_to_json', 'json_encode', 10, 1 );
}
if(! function_exists('cpJsonStr') ){
    function cpJsonStr($str){
        $str = preg_replace_callback('/\\\\u([a-f0-9]{4})/i', create_function('$m', 'return chr(hexdec($m[1])-1072+224);'), $str);
        return iconv('cp1251', 'utf-8', $str);
    }
    add_filter( 'jscript_php_to_json', 'cpJsonStr', 15, 1 );
}
if(! function_exists('str_to_bool') ){
  function str_to_bool( $json ){
    $json = str_replace('"true"',  'true',  $json);
    $json = str_replace('"on"',  'true',  $json);
    $json = str_replace('"false"', 'false', $json);
    $json = str_replace('"off"', 'false', $json);
    return $json;
  }
  add_filter( 'jscript_php_to_json', 'str_to_bool', 20, 1 );
}
if(! function_exists('json_function_names') ){
  function json_function_names( $json ){
    $json = str_replace( '"%', '', $json );
    $json = str_replace( '%"', '', $json );
    return $json;
  }
  add_filter( 'jscript_php_to_json', 'json_function_names', 25, 1 );
}
if(! function_exists('JScript_jQuery_onload_wrapper') ){
    function JScript_jQuery_onload_wrapper($data){
        return "<script type='text/javascript'><!-- \n jQuery(document).ready(function($) { \n" . $data . " });\n --></script>\n";
    }
    add_filter( 'jQuery_onload_wrapper', 'JScript_jQuery_onload_wrapper', 10, 1 );
}

class JQScript {
    public    static $assets_dir = '';
    protected static $common = array();
    protected static $custom = '';
    public static $scripts = array();
    protected static $styles = array();

    static function include_scripts(){
      foreach (self::$scripts as $asset) {
        wp_enqueue_script($asset['handle'], $asset['path'], array('jquery'), $asset['ver'], $asset['footer']);
      }
    }
    static function include_styles(){
      foreach (self::$styles as $asset) {
        wp_enqueue_style( $asset['handle'], $asset['src'], array(), $asset['ver'], $asset['media'] );
      }
    }
    static function initialize(){
        $output = '';
        foreach (self::$common as $script) {
            $script_code = "$('".$script['selector']."').".$script['init']."(".$script['options'].");";
            $output .= $script['before'] . $script_code . $script['after'] . "\n";
        }
        $output .= self::$custom;
        echo apply_filters( 'jQuery_onload_wrapper', $output );
    }

    public static function script( $handle, $src, $ver = null, $footer = true ){
      self::$scripts[] = array(
        'handle' => $handle,
        'path'   => (preg_match('/^http/', $src)) ? $src : JQScript::$assets_dir . $src,
        'ver' => $ver,
        'footer' => $footer
        );
    }

    public static function style( $handle, $src, $ver = null, $media = null ){
     self::$styles[] = array(
        'handle' => $handle,
        'src'   => (preg_match('/^http/', $src)) ? $src : JQScript::$assets_dir . $src,
        'ver' => $ver,
        'media' => $media
        );
    }

    public static function init( $selector, $script_name, $options = '', $before = '', $after = '' ){
      $selector = sanitize_text_field( $selector );
      $script_name = sanitize_text_field( $script_name );

      if( is_array($options) ){
        $options = apply_filters( 'jscript_php_to_json', $options );
      }
      elseif($options) {
        $options = json_function_names('"'. $options .'"');
      }

      self::$common[] = array(
        'selector' => $selector,
        'init' => $script_name,
        'options' =>$options,
        'before' => $before,
        'after' => $after,
        );
    }
    public static function custom( $script ){

      self::$custom .= $script;
    }
}

if( ! is_admin() ){
  add_action( 'wp_enqueue_scripts', function(){
    JQScript::include_styles();
    JQScript::include_scripts();
  }, 666 );
  add_action( 'wp_footer', array('MB\JQScript', 'initialize'), 99 );
}
else {
  add_action( 'admin_enqueue_scripts', function(){
    JQScript::include_styles();
    JQScript::include_scripts();
  }, 666 );
  add_action( 'admin_footer', array('MB\JQScript', 'initialize'), 99 );
}
