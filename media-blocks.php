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

/**
 * @todo: include utilites
 * @todo: include post-type
 * @todo: include register assets
 */

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

define('MBLOCKS_DIR', plugin_dir_path( __FILE__ ) );
define('MBLOCKS_ASSETS',  plugins_url( 'assets', __FILE__ ) );
define('MBLOCKS_TYPE', 'mediablocks' );

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

  public $settings = array();

  private function __clone() {}
  private function __wakeup() {}

  public static function activate(){ add_option( self::SETTINGS, array() ); }
  public static function uninstall(){ delete_option(self::SETTINGS); }

  function __construct() {
    self::include_required_classes();
    $this->settings = get_option( self::SETTINGS, array() );

    add_action( 'wp_enqueue_scripts', array( __CLASS__, 'pre_register_assets'), 50 );

    if( is_admin() )
      new isAdminView();
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
}
