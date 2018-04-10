<?php

namespace NikolayS93\MediaBlocks;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

/**
 * Регистрация типа записей "Медиа блоки"
 */
add_action('init', __NAMESPACE__ . '\register_mediablocks_type' );
function register_mediablocks_type() {
    register_post_type( Utils::get_option_name(), array(
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

add_action( 'add_meta_boxes', __NAMESPACE__ . '\change_excerpt_box', 10 );
function change_excerpt_box() {
    add_meta_box('mb_excerpt', __('Контент после заголовка', DOMAIN),
        __NAMESPACE__ . '\excerpt_box', Utils::get_option_name(), 'normal');
}

/**
 * Вывод поля "Контент после заголовка" (the_excerpt)
 */
function excerpt_box() {
    global $post;

    echo "<label class='screen-reader-text' for='excerpt'> {_('Excerpt')} </label>
    <textarea rows='1' cols='40' name='excerpt' tabindex='6' id='excerpt'>{$post->post_excerpt}</textarea>";
}

/**
 * Удалить стандартные блоки
 */
add_action( 'add_meta_boxes', __NAMESPACE__ . '\remove_default_divs', 99 );
function remove_default_divs() {
    // ярлык записи
    remove_meta_box( 'slugdiv', Utils::get_option_name(), 'normal' );
    // Произвольные поля
    remove_meta_box( 'postcustom', Utils::get_option_name(), 'normal' );
    // Цитата (Краткое содержимое).
    remove_meta_box( 'postexcerpt', Utils::get_option_name(), 'normal' );
}
