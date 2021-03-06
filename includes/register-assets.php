<?php

namespace NikolayS93\MediaBlocks;

add_action('wp_enqueue_scripts', __NAMESPACE__ . '\register_assets');
function register_assets() {
    $affix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
    $url = Utils::get_plugin_url('assets');

    $assets = array(
        'lazyLoad' => array(
            'js' => 'lazyLoad'.$affix.'.js',
            'ver' => '1.9.0',
        ),
    );

    wp_register_script( 'mediablocks', "{$url}/public.js", array('jquery'), '1.0', true );

    foreach ($assets as $type => $asset) {
        if( !empty($asset['js']) )
            wp_register_script( $type, "{$url}/{$type}/{$asset['js']}", array('jquery'), $asset['ver'], true );

        if( isset($asset['style']) )
            wp_register_style( $type, "{$url}/{$type}/{$asset['style']}", array(), $asset['ver'] );

        if( isset($asset['theme']) )
            wp_register_style( "{$type}-theme", "{$url}/{$type}/{$asset['theme']}", array(), $asset['ver'] );
    }
}

add_action( 'load-post.php',     __NAMESPACE__ . '\enqueue_admin_assets' );
add_action( 'load-post-new.php', __NAMESPACE__ . '\enqueue_admin_assets' );
function enqueue_admin_assets() {
    if( ($screen = get_current_screen()) && isset($screen->post_type) && $screen->post_type != Utils::get_option_name() ) {
        return false;
    }

    if ( ! did_action('wp_enqueue_media') ) {
        wp_enqueue_media();
    }

    $core_url = Utils::get_plugin_url( '/includes/assets' );

    wp_enqueue_style(  Utils::PREF . 'style', $core_url . '/post-type.css', array(), '1.0' );
    wp_enqueue_script( Utils::PREF . 'admin', $core_url . '/admin.js', array('jquery'), '1.0', true );
    wp_localize_script( Utils::PREF . 'admin', 'mb_settings', array(
        'nonce' => wp_create_nonce( Utils::SECURITY ),
    ) );

    $min = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
    wp_enqueue_script( 'data-actions', $core_url . '/jquery.data-actions/jquery.data-actions'.$min.'.js', array('jquery'), '1.0', true );

    wp_enqueue_script('mustache', $core_url . '/mustache/mustache'.$min.'.js', array(), null, true);
    echo "<script id='attachment-tpl' type='x-tmpl-mustache'>";
    echo file_get_contents(Utils::get_plugin_dir('/includes/templates/admin_attachment.tpl'));
    echo "</script>";
}