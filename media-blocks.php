<?php

/*
Plugin Name: Медиаблоки
Plugin URI: https://github.com/nikolays93/mediablocks
Description: Добавляет возможность создавать медиа блоки (Карусел, слайдер, галарея..)
Version: 1.1.0 alpha
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if(!function_exists('_isset_default')){
  function _isset_default(&$var, $default, $unset = false){
    $result = $var = isset($var) ? $var : $default;
    if($unset)
      $var = FALSE;
    return $result;
  }
}
if( !function_exists('_isset_false') ){
  function _isset_false(&$var, $unset = false){ return _isset_default( $var, false, $unset ); }
}
if( !function_exists('_isset_false') ){
  function _isset_empty(&$var, $unset = false){ return _isset_default( $var, '', $unset ); }
}


/**
 * Получить стандартные классы ячейки bootstrap сетки
 */
if( ! function_exists('get_column_class') ){
  function get_column_class( $columns_count="4", $non_responsive=false ){
    $xs = ( $need_xs = apply_filters('bootstrap3_columns', false) ) ? '-xs' : '';
    switch ($columns_count) {
        case '1': $col = 'col-12'; break;
        case '2': $col = (!$non_responsive) ? 'col'.$xs.'-6 col-sm-6 col-md-6 col-lg-6' : 'col'.$xs.'-6'; break;
        case '3': $col = (!$non_responsive) ? 'col'.$xs.'-12 col-sm-6 col-md-4 col-lg-4' : 'col'.$xs.'-4'; break;
        case '4': $col = (!$non_responsive) ? 'col'.$xs.'-6 col-sm-4 col-md-3 col-lg-3' : 'col'.$xs.'-3'; break;
        case '5': $col = (!$non_responsive) ? 'col'.$xs.'-12 col-sm-6 col-md-2-4 col-lg-2-4' : 'col'.$xs.'-2-4'; break; // be careful
        case '6': $col = (!$non_responsive) ? 'col'.$xs.'-6 col-sm-4 col-md-2 col-lg-2' : 'col'.$xs.'-2'; break;
        case '12': $col= (!$non_responsive) ? 'col'.$xs.'-4 col-sm-3 col-md-1 col-lg-1' : 'col'.$xs.'-1'; break;

        default: $col = false; break;
    }
    return $col;
  }
}

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

add_action( 'plugins_loaded', function(){ new DT_MediaBlocks(); });
register_activation_hook( __FILE__, array( 'DT_MediaBlocks', 'activate' ) );
register_uninstall_hook( __FILE__, array( 'DT_MediaBlocks', 'uninstall' ) );

class DT_MediaBlocks {
  const SETTINGS = 'MediaBlocks';
  const POST_TYPE = 'mediablocks';
  const PREFIX = 'mb_';
  const CLASSES_DIR = 'include/';
  const VERSION = '1.1.8';

  const SHOW_TITLE_NAME = 'show_title';
  const VIEW_MODE_NAME = 'view_mode';

  public $settings = array();

  private function __clone() {}
  private function __wakeup() {}

  public static function activate(){ add_option( self::SETTINGS, array() ); }
  public static function uninstall(){ delete_option(self::SETTINGS); }

  function __construct() {
    self::define_constants();
    self::include_required_classes();
    $this->settings = get_option( self::SETTINGS, array() );

    add_action( 'wp_enqueue_scripts', array( __CLASS__, 'pre_register_assets'), 50 );
    add_action('init', array($this, 'register_post_types'));

    if( is_admin() )
      new isAdminView();
  }

  private static function define_constants(){
    define('MBLOCKS_DIR', plugin_dir_path( __FILE__ ) );
    define('MBLOCKS_ASSETS',  plugins_url( 'assets', __FILE__ ) );
  }
  
  function register_post_types(){
    register_post_type( self::POST_TYPE, array(
      'query_var' => false,
      'rewrite' => false,
      'public' => false,
      'exclude_from_search' => true,
      'publicly_queryable' => false,
      'show_in_nav_menus' => false,
      'show_ui' => true,
      'menu_icon' => 'dashicons-images-alt2',
      'supports' => array('title', 'custom-fields', 'excerpt'),
      'labels' => array(
        'name' => 'Медиаблоки',
        'singular_name'      => 'Медиаблок',
        'add_new'            => 'Добавить блок',
        'add_new_item'       => 'Добавление блок',
        'edit_item'          => 'Редактирование блока',
        'new_item'           => 'Новый блок',
        'view_item'          => 'Смотреть МультиБлок',
        'search_items'       => 'Искать МультиБлок',
        'menu_name'          => 'Медиаблоки',
        )
      )
    );
  }

  private static function include_required_classes(){
    $required_classes = array(
      'admin' => array(
        'MB\JQScript'    => 'class-wp-jqscript',
        // 'MB\queries'     => 'queries',
        'scssc'          => 'scss.inc',
        'MB\WPForm'      => 'class-wp-form-render',
        'MB\WPPostBoxes' => 'class-wp-post-boxes',
        'MB\isAdminView' => 'is-admin-callback',
        ),
      'public' => array(
        'MB\JQScript'    => 'class-wp-jqscript',
        // 'MB\queries'     => 'queries',
        'MB\MediaBlock'  => 'front-callback',
        ),
      );

    foreach ($required_classes as $type => $classes) {
      foreach ( $classes as $class_name => $path ) {
        if( ($type == 'admin' && !is_admin()) || ($type == 'public' && is_admin()) )
          continue;

        $path = MBLOCKS_DIR . self::CLASSES_DIR . $path . '.php';

        if ( is_readable( $path ) && ! class_exists( $class_name ) ) 
          require_once( $path );
      }
    }
  }

  static function pre_register_assets( $type = false ){
    $affix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
    $url = MBLOCKS_ASSETS;

    $assets = array(
      'owl-carousel' => array(
        'js' => 'owl.carousel'.$affix.'.js',
        'style' => 'owl.carousel'.$affix.'.css',
        'theme' => 'owl.theme.css',
        'ver' => '1.3.3',
        ),
      'slick' => array(
        'js' => 'slick.js',
        'style' => 'slick.css',
        'theme' => 'slick-theme.css',
        'ver' => '1.6.0',
        ),
      'cloud9carousel' => array(
        'js' => 'jquery.cloud9carousel'.$affix.'.js',
        'ver' => '2.1.0',
        ),
      'waterwheelCarousel' => array(
        'js' => 'jquery.waterwheelCarousel'.$affix.'.js',
        'ver' => '2.3.0',
        ),
      'lazyLoad' => array(
        'js' => 'lazyLoad'.$affix.'.js',
        'ver' => '1.9.0',
        ),
      );

    if( $type )
      return isset($assets[$type]) ? $assets[$type] : false;

    foreach ($assets as $type => $asset) {
      if( !empty($asset['js']) )
        wp_register_script( $type, $url .'/'. $type .'/'. $asset['js'], array('jquery'), $asset['ver'], true );
      if( isset($asset['style']) )
        wp_register_style( $type, $url .'/'. $type .'/'. $asset['style'], array(), $asset['ver'], 'all' );
      if( isset($asset['theme']) )
        wp_register_style( $type.'-theme', $url .'/'. $type .'/'. $asset['theme'],  array(), $asset['ver'], 'all' );
    }

    return true;
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
  protected function parse_settings_file( $file = false, $main_type = 'carousel' ){
    if( empty($file) )
      return false;

    $path = MBLOCKS_DIR . 'settings/'.$file.'.php';

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
  public static function meta_field( $post_id, $key, $value = false ){
    if( !$post_id )
      return false;

    if( $value !== false ){
      if( $value != '' ){
        update_post_meta( $post_id, '_'.self::PREFIX.$key, $value );
      }
      else {
        delete_post_meta( $post_id, '_'.self::PREFIX.$key );
      }
    }
    else {
      return get_post_meta( $post_id, '_'.self::PREFIX.$key, true );
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
    $settings = $this->parse_settings_file( $filename, $settings_maintype );

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
      self::meta_field( $post_id, $settings_name.'_opt', $result );
      // $_debug = print_r($debug, 1) . "\n\n" . print_r($result, 1);
      // file_put_contents(__DIR__ . '/post_result.log', $_debug);
    }
    else{
      return $result;
    }
  }
}