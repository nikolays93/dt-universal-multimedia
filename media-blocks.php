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

define('MBLOCKS', 'MediaBlocks');

define('MBLOCKS_DIR', rtrim(plugin_dir_path( __FILE__ ), '/') );
define('MBLOCKS_ASSETS',  rtrim(plugins_url( 'assets', __FILE__ ), '/') );
define('MBLOCKS_TYPE', 'mediablocks' );

register_activation_hook( __FILE__, function() { add_option( MBLOCKS, array() ); } );
register_uninstall_hook( __FILE__, function() { delete_option( MBLOCKS ); } );
add_action( 'plugins_loaded', 'init_media_blocks');
function init_media_blocks() {
    require_once MBLOCKS_DIR . '/include/utilites.php';
    require_once MBLOCKS_DIR . '/include/class-mblock.php';
    require_once MBLOCKS_DIR . '/include/';
    require_once MBLOCKS_DIR . '/include/';
}

class DT_MediaBlocks {
  const SETTINGS = 'MediaBlocks';
  const POST_TYPE = 'mediablocks';
  const PREFIX = 'mb_';
  const CLASSES_DIR = 'include/';
  const VERSION = '1.1.8';

  const SHOW_TITLE_NAME = 'show_title';

  public $settings = array();

  function __construct() {
    self::include_required_classes();
    $this->settings = get_option( self::SETTINGS, array() );

    add_action( 'wp_enqueue_scripts', array( __CLASS__, 'pre_register_assets'), 50 );

    if( is_admin() )
      new isAdminView();
  }
}
