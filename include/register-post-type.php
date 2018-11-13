<?php

namespace NikolayS93\MBlocks;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

/**
 * Регистрация типа записей "Медиа блоки"
 */
add_action('init', __NAMESPACE__ . '\register_mediablocks_type' );
function register_mediablocks_type() {
    register_post_type( Plugin::get_option_name(), array(
        'query_var' => false,
        'rewrite' => false,
        'public' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'show_in_nav_menus' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-images-alt2',
        'menu_position' => 10,
        'supports' => array('title', 'custom-fields', 'excerpt'),
        'labels' => array(
            'name' => __( 'Mediablocks', DOMAIN ),
            'singular_name' => __( 'Mediablock', DOMAIN ),
            'add_new'       => __( 'Add block', DOMAIN ),
            'add_new_item'  => __( 'Add new block', DOMAIN ),
            'edit_item'     => __( 'Edit block', DOMAIN ),
            'new_item'      => __( 'New block', DOMAIN ),
            'view_item'     => __( 'View block', DOMAIN ),
            'search_items'  => __( 'Search media block', DOMAIN ),
        )
    ) );
}
