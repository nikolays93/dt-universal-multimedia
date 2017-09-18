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
define('MB_PREF', 'mb_');

define('MBLOCKS_DIR', rtrim(plugin_dir_path( __FILE__ ), '/') );
define('MBLOCKS_URL', rtrim(plugins_url( '', __FILE__ ), '/') );

register_activation_hook( __FILE__, 'activate_media_blocks' );
function activate_media_blocks() {
    add_option( MBLOCKS, array() );
}

register_uninstall_hook( __FILE__, 'uninstall_media_blocks' );
function uninstall_media_blocks() {
    delete_option( MBLOCKS );
}

function init_media_blocks() {
    require_once MBLOCKS_DIR . '/include/utilites.php';

    if( is_admin() ) {
        mb_include_file( array(
            'scssc'          => 'class/scss.inc',
            'DT_Form'        => 'class/class-dt-form',
            'MB\WPPostBoxes' => 'class/class-wp-post-boxes',
            'MBlocks_Post_Type' => 'class-mblocks-post-type',
        ) );
    }
    else {
        mb_include_file( array(
            'register-assets',
        ) );
    }

    // mb_include_file( array(
    //     'mblock_class',
    //     // 'front' => 'front-callback',
    // ) );
}

require_once MBLOCKS_DIR . '/include/class-mblocks-post-type.php';

add_action( 'plugins_loaded', 'init_media_blocks');
add_action( 'plugins_loaded', array('MBlocks_Post_Type', 'init') );

// class DT_MediaBlocks {
//   function __construct() {
//     add_action( 'wp_enqueue_scripts', array( __CLASS__, 'pre_register_assets'), 50 );
//   }
// }
