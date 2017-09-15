<?php

add_action('init', 'register_media_blocks_type');
function register_media_blocks_type() {
    register_post_type( MBLOCKS_TYPE, array(
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
