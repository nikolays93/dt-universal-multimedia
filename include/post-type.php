<?php

namespace MBlocks\post_type;

// $parent_ns = explode( '\\', __NAMESPACE__ );
// $parent_ns = $parent_ns[sizeof($parent_ns) - 1];

add_action('init', __NAMESPACE__ . 'register_media_blocks_type');
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
    ) );
}

/** Удалить стандартные блоки */
add_action( 'add_meta_boxes' , __NAMESPACE__ . 'remove_default_divs', 99 );
function remove_default_divs() {
    remove_meta_box( 'slugdiv', MBLOCKS_TYPE, 'normal' ); // ярлык записи,
    remove_meta_box( 'postcustom', MBLOCKS_TYPE, 'normal' ); // Произвольные поля
    remove_meta_box( 'postexcerpt', MBLOCKS_TYPE, 'normal' );
}

/** Добавить блоки */
add_action( 'edit_form_after_title', __NAMESPACE__ . 'after_title' );
function after_title() {
    global $post, $wp_meta_boxes;

    if($post->post_type !== MBLOCKS_TYPE) {
        return;
    }

    $check = checked( \mb_post_meta($post->ID, '_show_title'), 'on', false );
    ?>
    <div class='wrap-sc'>
        <label>
            <?=__('Показывать заголовок');?>
            <input type='checkbox' id='_show_title' name='_show_title' value='on'{$check}>
        </label>

        Вставьте шорткод в любую запись Вашего сайта
        <input id="shortcode" readonly="readonly" type="text" value='[mblock id="<?=$post->ID;?>"]'>
    </div>
    <?php
}

add_action( 'add_meta_boxes', __NAMESPACE__ . 'blocks_meta_boxes' );
function blocks_meta_boxes( $post_type ) {
    add_meta_box('attachments', 'Мультимедиа', __NAMESPACE__ . 'attachments_callback', MBLOCKS_TYPE, 'normal', 'high');
    add_meta_box('main_settings', 'Настройки', __NAMESPACE__ . 'sub_settings_callback', MBLOCKS_TYPE, 'normal');
    add_meta_box('side_settings', 'Настройки', __NAMESPACE__ . 'side_settings_callback', MBLOCKS_TYPE, 'side');
    add_meta_box('mb_excerpt', 'Контент после заголовка', __NAMESPACE__.'excerpt_box', MBLOCKS_TYPE, 'normal');
}
add_action( 'wp_ajax_main_settings', __NAMESPACE__ . 'sub_settings_callback' );
add_action( 'wp_ajax_main_settings', __NAMESPACE__ . 'side_settings_callback' );

function attachments_callback( $post ) {
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
    //wp_nonce_field( 'dp_addImages_nonce', 'wp_developer_page_nonce' );
    ?>
    <div class="dt-media">
      ?>
      <div class="hide-if-no-js wp-media-buttons">
        <button id="detail_view" class="button" type="button">
          <span class="dashicons dashicons-screenoptions"></span>
          <span class="dashicons dashicons-list-view"></span>
        </button>
        <button id="upload-images" class="button add_media">
          <span class="wp-media-buttons-icon"></span> Добавить медиафайл
        </button>
      </div>
      <label>Тип мультимедия: </label>
      <?php
      // MB\WPForm::render( $this->parse_settings_file('general'), array(
      //  'main_type' => self::meta_field( $post->ID, 'main_type' ),
      //  'type'      => self::meta_field( $post->ID, 'type' )
      //  ), false, array('item_wrap' => array('<span>', '</span>')));
      ?>
      <div class="clear"></div>

      <?php
      $ids = \mb_post_meta( $post->ID, 'media_imgs' );
      $ids_arr = explode( ',', esc_attr($ids) );

      echo '<div class="attachments" id="dt-media">';
      if( $ids ){
        foreach ($ids_arr as $id) {
          $meta = wp_get_attachment_metadata( $id );
          $attrs = ( $meta['image_meta']['orientation'] == 1 ) ? array('class' => 'portrait') : array();

          // wp_get_attachment_metadata( $id )
          $attachment = get_post( $id );
          $image = wp_get_attachment_image($id, 'medium', null, $attrs);
          $link = get_post_meta( $id, 'mb_link', true );
          $link_code = '';
          /**
           * @todo : add link shortcode for developers tool
           */
          if( shortcode_exists( 'link' ) )
            $link_code = '[link id="4"]';
        ?>
        <div class="attachment" data-id="<?php echo $id; ?>">
          <div class="item">
            <span class="dashicons dashicons-no remove"></span>
            <div class="crop"><?php echo $image;?></div>
            <input class="item-excerpt" type="text" name="attachment_excerpt[<?php echo $id; ?>]" value="<?php echo $attachment->  post_excerpt; ?>">
            <textarea class="item-content" name="attachment_content[<?php echo $id; ?>]" id="" cols="90" rows="4"><?php echo $attachment  ->post_content; ?></textarea>
            <input class="item-link" type="text" name="attachment_link[<?php echo $id; ?>]" placeholder="<?php echo $link_code; ?>" value  ="<?php echo $link;?>">
            <input type="hidden" id="dt-ids" name="attachment_id[]" value="<?php echo $id; ?>">
          </div>
        </div>
        <?php
        } // foreach
      } // if
    ?>
      </div><!-- #dt-media -->
      <div class="clear"></div>
    </div>
    <?php
}

/**
 * Показывает настройки библиотеки
 * Обновляется через AJAX
 */
function sub_settings_callback( $post ) {
    $post_id = ( isset($post->ID) ) ? $post->ID : intval( $_POST['post_id'] );
    $main_type = isset($_POST['main_type']) ? $_POST['main_type'] : \mb_post_meta($post_id, 'main_type');
    $type = isset($_POST['type']) ? $_POST['type'] : \mb_post_meta($post_id, 'type');
    if( empty($type) )
        $type = 'owl-carousel';

    echo "<div class='sub-settings-wrp'>";
    MB\WPForm::render( mblock_parse_settings( 'sub/'.$type, $main_type ), \mb_post_meta($post_id, $type.'_opt'), true );
    echo "</div>";
}

/**
 * Показывает под настройки (справа в сайдбаре)
 * Обновляется через AJAX
 */
function side_settings_callback( $post ){
    $post_id = ( isset($post->ID) ) ? $post->ID : intval( $_POST['post_id'] );
    $type = isset($_POST['main_type']) ? $_POST['main_type'] : self::meta_field($post->ID, 'main_type');
    if( empty($type) )
        $type = 'carousel';

    echo "<div class='settings-wrp'>";
    MB\WPForm::render(
        $this->parse_settings_file( 'main/'.$type ),
        self::meta_field($post->ID, $type.'_opt'),
        true,
        array(
          'label_tag' => 'td',
          'clear_value' => false
      )
    );
    echo "</div>";
}

/**
 * Вывод поля "Контент после заголовка" (the_excerpt)
 */
function excerpt_box(){
    global $post;

    echo "<label class='screen-reader-text' for='excerpt'> {_('Excerpt')} </label>
    <textarea rows='1' cols='40' name='excerpt' tabindex='6' id='excerpt'>{$post->post_excerpt}</textarea>";
}

/**
 * Enqueue Assets
 */
add_action( 'load-post.php', __NAMESPACE__ . 'admin_asssets' );
add_action( 'load-post-new.php', __NAMESPACE__ . 'admin_asssets' );
function admin_asssets() {
    $screen = get_current_screen();
    if( $screen->post_type != MBLOCKS_TYPE ) {
        return false;
    }

    if ( ! did_action('wp_enqueue_media') ) {
        wp_enqueue_media();
    }

    wp_enqueue_style(MB_PREF . 'style', MBLOCKS_URL . '/assets/core/style.css', array(), '1.0' );
    wp_enqueue_script( MB_PREF . 'view', MBLOCKS_URL . '/assets/core/view.js', array('jquery'), '1.0', true );
    wp_localize_script(MB_PREF . 'view', 'mb_settings', array(
        'nonce' => wp_create_nonce( 'Secret' ),
    ) );
}

/**
 * Validate Post's Data
 *
 * @todo : set metas with array
 */
function check_security( $post_id ){
    // if ( ! isset( $_POST['wp_developer_page_nonce'] ) )
    // return FALSE;
    // $nonce = $_POST['wp_developer_page_nonce'];
    // if ( ! wp_verify_nonce( $nonce, 'dp_addImages_nonce' ) )
    //  return FALSE;

    // Если это автосохранение ничего не делаем.
    // if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    //  return FALSE;
}

function validate_media_attachments( $post_id ){
    if( !isset($_POST['attachment_id']) || !is_array($_POST['attachment_id']))
        return $post_id;

    $attachment_ids = $_POST['attachment_id'];
    $attachment_ids = implode(',', $attachment_ids);
    self::meta_field( $post_id, 'media_imgs', $attachment_ids );

    if( is_array($_POST['attachment_excerpt']) )
        $s1 = sizeof($_POST['attachment_excerpt']);

    if( is_array($_POST['attachment_content']) )
        $s2 = sizeof($_POST['attachment_content']);

    $each = ($s1 > $s2) ? $_POST['attachment_excerpt'] : $_POST['attachment_content'];

    foreach ($each as $id => $excerpt) {
        $update = array( 'ID' => $id );

        if($_POST['attachment_excerpt'])
          $update['post_excerpt'] = $_POST['attachment_excerpt'][$id];

      if( isset($_POST['attachment_content'][$id]) )
          $update['post_content'] = $_POST['attachment_content'][$id];

      if( sizeof($update > 1) )
          wp_update_post( $update );
  }

  if(is_array($_POST['attachment_link']) && sizeof($_POST['attachment_link']) >= 1 ){
    foreach ($_POST['attachment_link'] as $id => $value) {
      update_post_meta( $id, 'mb_link', $value );
  }
}
}

add_action( 'save_post', __NAMESPACE__ . 'validate_main_settings' );
function validate_main_settings( $post_id ){
    if( FALSE === $this->check_security($post_id) )
        return $post_id;

    $this->validate_media_attachments($post_id);

    /**
     * @todo change it to locate/storage js
     */
    self::meta_field($post_id, self::SHOW_TITLE_NAME, _isset_false($_POST[self::SHOW_TITLE_NAME]) );

    if( !isset($_POST['main_type']) || !isset($_POST['type']) )
        return $post_id;

    $main_type = $_POST['main_type'];
    $type = $_POST['type'];

    self::meta_field($post_id, 'main_type', $main_type);
    self::meta_field($post_id, 'type', $type);

    if(isset($_POST['query']))
        self::meta_field($post_id, 'query', $_POST['query']);

    $this->settings_from_file($post_id, $main_type, false, $_POST );
    $this->settings_from_file($post_id, $type, $main_type, $_POST );

    /**
     * Create TEMP Style File
     */
    $asset = self::pre_register_assets( $type );
    if( ! isset($asset[ $type ]) )
        return false;

    $file = get_template_directory() . 'assets/blocks/block-'.$post_id.'.css';
    if ( ! file_exists( $file ) ) {
        $file = DT_MULTIMEDIA_PATH . 'assets/' . $asset[ $type ]['theme'];
    }

    $out_file = DT_MULTIMEDIA_PATH . 'assets/block-'.$post_id.'.css';

    if ( file_exists( $file ) ){
        $scss = new \scssc();
        $scss->setFormatter('scss_formatter_compressed');
        $compiled = $scss->compile( apply_filters( 'remove_cyrillic', '#mediablock-'.$post_id.' {' . file_get_contents($file) . '}' ) );

        if(!empty($compiled))
            file_put_contents( $out_file, $compiled );
    }
}
