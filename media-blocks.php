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
  public $version = 1.3;
  
  protected $errors = array();

    function __construct(){
      $this->define_constants();
      $this->add_required_classes();
      $this->setup_actions();
      $this->setup_filters();
      $this->setup_shortcode();

      if(is_admin())
        new isAdminView();
    }

    /**
     * SETUP global methods
     */
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

    private function define_constants() {
      define( 'DTM_PREFIX', 'dtmm_');
      define( 'DT_MULTIMEDIA_MAIN_TYPE', 'multimedia-base');

        // define( 'DT_MULTIMEDIA_VERSION',    $this->version );
      define( 'DT_MULTIMEDIA_BASE_URL',   trailingslashit( plugins_url( basename( __DIR__ ) ) ) );
      define( 'DT_MULTIMEDIA_ASSETS_URL', trailingslashit( DT_MULTIMEDIA_BASE_URL . 'assets' ) );
      define( 'DT_MULTIMEDIA_PATH',       plugin_dir_path( __FILE__ ) );
    }

    private function add_required_classes(){
      if(is_admin())
        $classes = array('isAdminView' => DT_MULTIMEDIA_PATH . '/classes/admin-edit.php');

      if(!isset($classes))
        return false;

      foreach ( $classes as $id => $path ) {
        if ( is_readable( $path ) && ! class_exists( $id ) ) 
          require_once( $path );
        else
          $this->set_notice('Обнаружен поврежденный класс!');
      }
    }

    /**
     * Set global actions
     */
    function register_post_types(){
      register_post_type( DT_MULTIMEDIA_MAIN_TYPE, array(
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
    function setup_actions(){

      add_action('init', array($this, 'register_post_types'));
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
    function bool_replace($script_options){
    	$script_options = str_replace('"on"', 'true', $script_options);
    	$script_options = str_replace('"false"', 'false', $script_options);
    	return $script_options;
    }

    function setup_filters(){
		add_filter( 'array_options_before_view', 	array($this, 'owl_nextprev'), 10, 1 );
		add_filter( 'json_change_values', 			array($this, 'bool_replace'), 10, 1 );
    }

    protected function get_settings($file=false) {
      if($file === false)
        return false;

      $path = DT_MULTIMEDIA_PATH.'/settings/'.$file.'.php';

      if ( is_readable( $path ) ) 
        return include( $path );
      else
        $this->set_notice('Обнаружен поврежденный файл настроек!');
    }
    protected function set_post_meta($post_id, $var, $is_record=false){
      if( !isset($post_id) || !isset($var) )
        return false;

      if($is_record){
        if(isset($_POST[$var]))
          update_post_meta( $post_id, '_'.DTM_PREFIX.$var, $_POST[$var] );
      }
      else {
        return get_post_meta( $post_id, '_'.DTM_PREFIX.$var, true );
      }
    }
    protected function set_meta_settings($post_id, $settings_name, $post_values){
      if(!$settings_name || !$post_id)
        return false;

      if($post_values){
        $in_set = $post_values;
        $is_record = true;
      }
      else {
        $in_set = $this->get_post_meta($post_id,$settings_name.'_opt');
        $is_record = false;
      }

      $settings = $this->get_settings($settings_name);

      $result = array();
      foreach ($settings as $param){
        if( isset($param['id']) )
          $param_name = $param['id'];
        else
          $param_name = (isset($param['name'])) ? $param['name'] : '';

        if(isset($param['default']))
          $default = $param['default'];
        else
          $default = isset($param['placeholder']) ? $param['placeholder'] : '';

        if(isset($in_set[$param_name]) && $in_set[$param_name] != $default){
          if($in_set[$param_name] == '' && $param['type'] == 'checkbox')
              $in_set[$param_name] = 'false';

          if( $in_set[$param_name] != '' || $param['type'] == 'select' )
            $result[$param_name] = $in_set[$param_name];
        }

      }
      
      // if($settings_name == 'carousel')
      	// file_put_contents(DT_MULTIMEDIA_PATH . 'debug2.log', print_r($result, 1) );

      if($is_record)
        update_post_meta( $post_id, '_'.DTM_PREFIX.$settings_name.'_opt', $result );
      else
        return $result;
    }

    protected function get_media_type($media_id){
      $main_type = get_post_meta( $media_id, '_'.DTM_PREFIX.'main_type', true );
      $type = get_post_meta( $media_id, '_'.DTM_PREFIX.'type', true );
      
      return array($main_type, $type);
    }
    protected function get_meta_settings($post_id, $settings_name){

      return $this->set_meta_settings($post_id, $settings_name, false);
    }
    protected function get_post_meta($post_id, $var){

      return $this->set_post_meta($post_id, $var);
    }

    /**
     * Media output
     */
    function load_assets($type, $template, $style_path){
    	$affix = (is_wp_debug()) ? '' : '.min';

    	if($type == 'owl-carousel'){
    		wp_enqueue_script( 'owl-carousel', DT_MULTIMEDIA_ASSETS_URL.'owl-carousel/owl.carousel'.$affix.'.js', array('jquery'), $this->version, true );
    		wp_enqueue_style( 'owl-carousel-core', DT_MULTIMEDIA_ASSETS_URL.'owl-carousel/owl.carousel.css', array(), $this->version );
    	}


    	if( $template === "" )
    		return false;

    	switch ($template) {
    		case 'custom':
	    		if( $style_path !== false )
	    			wp_enqueue_style( $type.'-theme', get_template_directory_uri().'/'.$style_path.'.css', array(), $this->version );
    			break;

    		case 'default':
	    		$template = 'owl.theme';
    			break;

    		default:
    			$template = 'default.theme';
    			break;
    	}
    	wp_enqueue_style( $type.'-theme', DT_MULTIMEDIA_ASSETS_URL.$type.'/'.$template.'.css',
    		array(), $this->version );
    }
    function carousel($type, $mblock, $attachments, $non_script = false){
    	$result = array();
    	$id = $mblock->ID;
    	
    	// parse type[0] settings
        extract($this->get_meta_settings($id, 'carousel'));
        $class = (isset($lightbox_class)) ? $lightbox_class : 'fancybox';

        // load assets
        $template = isset($template) ? $template : false;
        $style_path = isset($style_path) ? $style_path : false;
        $this->load_assets($type, $template, $style_path);
        
        // parse type[1] settings
        $metas_arr = apply_filters( 'array_options_before_view',
        	$this->get_meta_settings($id, $type) );

        $script_options = apply_filters( 'json_change_values', cpJsonStr( json_encode($metas_arr) ) );
        
        $item = array("<div class='item'>", "</div>");
        $slider_wrap = array("<div id='mediablock-{$id}' class='media-block carousel {$type}'>", "</div>");
        $result[] = $slider_wrap[0];
    	switch ( $type ) {
            case 'owl-carousel':
                foreach ($attachments as $attachment) {
                    $href = wp_get_attachment_url( $attachment );
                    $link =  (isset($lightbox_links)) ?
                        array('<a rel="group-'.$id.'" href="'.$href.'" class="'.$class.'">', '</a>') : array('', '');

                    $caption = (isset($image_captions)) ? '<p id="caption">'.get_the_excerpt( $attachment ).'</p>' : '';

                    $result[] = $item[0];
                    $result[] = '   '.$link[0];
                    $result[] = '       '.wp_get_attachment_image( $attachment, $image_size ); //,null,array(attrs)
                    $result[] = '       '.$caption;
                    $result[] = '   '.$link[1];
                    $result[] = $item[1];
                }
                    // $image_meta = wp_get_attachment_metadata( $attachment );

                $script   = array("<script type='text/javascript'>");
                $script[] = " jQuery(function($){";
                $script[] = "     $('#mediablock-".$id."').owlCarousel(".$script_options.");";
                $script[] = " });";
                $script[] = "</script>";
                break;
        }
        $result[] = $slider_wrap[1];
        if(! $non_script )
          $result[] = implode("\n", $script);
        return implode("\n", $result);
    }
    function slider($type, $mblock, $attachments, $non_script = false){

    }
    function sync_slider($type, $mblock, $attachments){
    	$this->carousel($type, $mblock, $attachments, true);
    	$this->slider(  $type, $mblock, $attachments, true);
    	//script
    }
    // render shortcode
    function media_sc( $atts ) {
    	$result = array();
        $atts = shortcode_atts( array('id' => false), $atts );
        $id = intval($atts['id']);

        $mblock = get_post( $id );
        if('publish' !== $mblock->post_status){
            if(is_wp_debug()) echo 'Блок не опубликован';
            return;
        }

        if(sizeof($type = $this->get_media_type($id)) == 0)
        	return false;

        $attachments = explode(',', $this->get_post_meta($id, 'media_imgs'));
        if( sizeof($attachments) == 0 )
            return (is_wp_debug()) ? 'Файлов не найдено' : false; 

        $result[] = '<section id="mblock">';
        if($this->get_post_meta( $id, 'show_title' ) && $mblock->post_title != '')
        		$result[] = '<h3>'. $mblock->post_title .'</h3>';
    	if($mblock->post_excerpt != '')
    		$result[] = '<div class="excerpt">'.apply_filters('the_content', $mblock->post_excerpt)."</div>";

        switch ( $type[0] ) {
        	default: // carousel, slider
        	$type[0] = str_replace("-", "_", $type[0]);
        	$result[] = $this->$type[0]($type[1], $mblock, $attachments);
        		break;
        } // switch
        $result[] = '</section>';
        return implode("\n", $result);
    }
    private function setup_shortcode(){

        add_shortcode( 'mblock', array($this, 'media_sc') );
    }
}
new DT_MediaBlocks();

// function rewrite_flush() {
//     DT_MultiMedia::register_post_types();
//     flush_rewrite_rules();
// }
// register_activation_hook( __FILE__, 'rewrite_flush' );