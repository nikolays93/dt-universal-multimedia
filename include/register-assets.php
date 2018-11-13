<?php

namespace NikolayS93\MBlocks;

add_action('wp_enqueue_scripts', __NAMESPACE__ . '\register_assets');
function register_assets() {
    $affix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
    $url = Utils::get_plugin_url('/assets');

    /**
     * Register global public js
     */
    wp_register_script( 'mediablocks', "{$url}/public.js", array('jquery'), '1.0', true );

    /**
     * @todo repait it? where is assets list?
     */
    $assets = array(
        'lazyLoad' => array(
            'js' => 'lazyLoad'.$affix.'.js',
            'ver' => '1.9.0',
        ),
    );

    foreach ($assets as $type => $asset) {
        if( !empty($asset['js']) ) {
            wp_register_script( $type . '-js', "{$url}/{$type}/{$asset['js']}", array('jquery'), $asset['ver'], true );
        }

        if( isset($asset['style']) ) {
            wp_register_style( $type . '-css', "{$url}/{$type}/{$asset['style']}", array(), $asset['ver'] );
        }

        if( isset($asset['theme']) ) {
            wp_register_style( $type . '-th', "{$url}/{$type}/{$asset['theme']}", array(), $asset['ver'] );
        }
    }
}

add_action( 'load-post.php',     __NAMESPACE__ . '\enqueue_admin_assets' );
add_action( 'load-post-new.php', __NAMESPACE__ . '\enqueue_admin_assets' );
function enqueue_admin_assets() {
    $screen = get_current_screen();
    $min = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

    if( !empty($screen->post_type) && Plugin::get_option_name() == $screen->post_type ) {
        if ( ! did_action('wp_enqueue_media') ) {
            wp_enqueue_media();
        }

        $core_url = Utils::get_plugin_url( '/admin/assets' );

        wp_enqueue_style(  DOMAIN . '_style', $core_url . '/post-type.css', array(), '1.0' );
        wp_enqueue_script( DOMAIN . '_admin', $core_url . '/admin.js', array('jquery'), '1.0', true );
        wp_localize_script( DOMAIN . '_admin', 'mb_settings', array(
            'nonce' => wp_create_nonce( Plugin::SECURITY ),
        ) );

        wp_enqueue_script( 'data-actions', $core_url . '/jquery.data-actions/jquery.data-actions'.$min.'.js', array('jquery'), '1.0', true );
        wp_enqueue_script('mustache', $core_url . '/mustache/mustache'.$min.'.js', array(), null, true);
    }
}

add_action( 'admin_head', function() {
    $tpl = PLUGIN_DIR . '/admin/template/admin_attachment.tpl';
    if( is_file($tpl) ) {
        echo "<script id='attachment-tpl' type='x-tmpl-mustache'>";
        echo file_get_contents($tpl);
        echo "</script>";
    }
} );
