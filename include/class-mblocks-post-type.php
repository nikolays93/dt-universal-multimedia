<?php

/**
 * static class MBlocks_Post_Type
 */
class MBlocks_Post_Type
{
    const post_type = 'm-block';
    const security  = 'Secret';

    static function init()
    {
        /**
         * Регистрация нового типа
         */
        add_action('init', array(__CLASS__, 'register_media_blocks_type') );

        /**
         * Изменение вывода блоков
         */
        add_action( 'edit_form_after_title', array(__CLASS__, 'after_title') );
        add_action( 'add_meta_boxes', array(__CLASS__, 'blocks_meta_boxes') );
        add_action( 'add_meta_boxes' , array(__CLASS__, 'remove_default_divs'), 99 );

        /**
         * Ajax обновление новых блоков
         */
        add_action( 'wp_ajax_main_settings', array(__CLASS__, 'sub_settings_callback') );
        add_action( 'wp_ajax_main_settings', array(__CLASS__, 'side_settings_callback') );

        /**
         * Добавить стили и скрипты на страницу создания и редактирования типа записи
         */
        add_action( 'load-post.php', array(__CLASS__, 'enqueue_admin_assets') );
        add_action( 'load-post-new.php', array(__CLASS__, 'enqueue_admin_assets') );

        /**
         * Сохранить изменения в записи типа медиаблок
         */
        add_action( 'save_post', array(__CLASS__, 'validate') );
    }

    /**
     * Регистрация типа записей "Медиа блоки"
     */
    static function register_media_blocks_type()
    {
        register_post_type( self::post_type, array(
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

    /**
     * Удалить стандартные блоки
     */
    static function remove_default_divs()
    {
        // ярлык записи,
        remove_meta_box( 'slugdiv', self::post_type, 'normal' );
        // Произвольные поля,
        remove_meta_box( 'postcustom', self::post_type, 'normal' );
        // Цитата (Краткое содержимое).
        remove_meta_box( 'postexcerpt', self::post_type, 'normal' );
    }

    /**
     * Добавить новые
     */
    static function blocks_meta_boxes()
    {
        add_meta_box('attachments', 'Мультимедиа', array(__CLASS__, 'attachments_callback'), self::post_type, 'normal', 'high');
        add_meta_box('main_settings', 'Настройки', array(__CLASS__, 'sub_settings_callback'), self::post_type, 'normal');
        add_meta_box('side_settings', 'Настройки', array(__CLASS__, 'side_settings_callback'), self::post_type, 'side');
        add_meta_box('mb_excerpt', 'Контент после заголовка', array(__CLASS__, 'excerpt_box'), self::post_type, 'normal');
    }

    /**
     * Чекбокс после заголовка "Показывать заголовок"
     */
    static function after_title()
    {
        global $post, $wp_meta_boxes;

        if($post->post_type !== self::post_type) return;

        $check = checked( mb_post_meta($post->ID, '_show_title'), 'on', false );
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

    static function attachments_callback( $post )
    {
        if ( ! did_action( 'wp_enqueue_media' ) ) wp_enqueue_media();
        ?>
        <div class="dt-media">
            <div class="hide-if-no-js wp-media-buttons">
                <button id="detail_view" class="button" type="button">
                    <span class="dashicons dashicons-screenoptions"></span>
                    <span class="dashicons dashicons-list-view"></span>
                </button>
                <button id="upload-images" class="button add_media">
                    <span class="wp-media-buttons-icon"></span> Добавить медиафайл
                </button>
            </div><!-- .wp-media-buttons -->
            <label>Тип мультимедия: </label>
            <?php
            $form = new DT_Form(
                mblock_parse_settings('general'),
                array(
                    'main_type' => mb_post_meta( $post->ID, 'main_type' ),
                    'type'      => mb_post_meta( $post->ID, 'type' ),
                ),
                false,
                array( 'item_wrap' => array('<span>', '</span>') )
            );
            $form->render();
            ?>
            <div class="clear"></div>

            <div class="attachments" id="dt-media">
            <?php
            $ids = mb_post_meta( $post->ID, 'media_imgs' );
            $ids_arr = $ids ? explode( ',', esc_attr($ids) ) : array();

            foreach ($ids_arr as $id) :
                $meta = wp_get_attachment_metadata( absint($id) );
                $attrs = ( $meta['image_meta']['orientation'] == 1 ) ? array('class' => 'portrait') : array();

                // wp_get_attachment_metadata( $id )
                $attachment = get_post( $id );
                $image = wp_get_attachment_image($id, 'medium', null, $attrs);
                $link = get_post_meta( $id, MB_PREF . 'link', true );
                $link_code = '';

                /**
                 * @todo : add link shortcode for developers tool
                 */
                if( shortcode_exists( 'link' ) ) $link_code = '[link id="4"]';
            ?>
                <div class="attachment" data-id="<?=$id;?>">
                    <div class="item">
                        <span class="dashicons dashicons-no remove"></span>
                        <div class="crop"><?php echo $image;?></div>
                        <input
                            type="text"
                            class="item-excerpt"
                            name="attachment_excerpt[<?php echo $id; ?>]"
                            value="<?php echo $attachment->post_excerpt; ?>"
                        >
                        <textarea
                            class="item-content"
                            name="attachment_content[<?php echo $id; ?>]"
                            id=""
                            cols="90"
                            rows="4"
                        ><?php echo $attachment->post_content; ?></textarea>
                        <input
                            type="text"
                            class="item-link"
                            name="attachment_link[<?php echo $id; ?>]"
                            placeholder="<?php echo $link_code; ?>"
                            value="<?php echo $link;?>"
                        >
                        <input type="hidden" id="dt-ids" name="attachment_id[]" value="<?php echo $id; ?>">
                    </div>
                </div><!-- .attachment -->
            <?php endforeach; ?>
            </div><!-- #dt-media -->
            <div class="clear"></div>
        </div>
        <?php
        wp_nonce_field( self::security, __CLASS__ );
    }

    /**
     * Показывает настройки библиотеки
     * Обновляется через AJAX
     */
    static function sub_settings_callback( $post )
    {
        // wp_parse_args( $args, $defaults );
        $post_id = ( isset($post->ID) ) ? $post->ID : intval( $_POST['post_id'] );
        $main_type = isset($_POST['main_type']) ? $_POST['main_type'] : mb_post_meta($post_id, 'main_type');
        $type = isset($_POST['type']) ? $_POST['type'] : mb_post_meta($post_id, 'type');
        if( empty($type) )
            $type = 'owl-carousel';

        echo "<div class='sub-settings-wrp'>";
        $form = new DT_Form(
            mblock_parse_settings( 'sub/'.$type, $main_type ),
            mb_post_meta($post_id, $type.'_opt'),
            true
        );
        $form->render();
        echo "</div>";
    }

    /**
     * Показывает под настройки (справа в сайдбаре)
     * Обновляется через AJAX
     */
    static function side_settings_callback( $post )
    {
        $post_id = ( isset($post->ID) ) ? $post->ID : intval( $_POST['post_id'] );
        $type = isset($_POST['main_type']) ? $_POST['main_type'] : mb_post_meta($post->ID, 'main_type');
        if( empty($type) )
            $type = 'carousel';

        echo "<div class='settings-wrp'>";
        $form = new DT_Form(
            mblock_parse_settings( 'main/'.$type ),
            true,
            true,
            array()
        );
        $form->render();

        // \MB\WPForm::render(
        //     ,
        //     mb_post_meta($post->ID, $type.'_opt'),
        //     true,
        //     array(
        //       'label_tag' => 'td',
        //       'clear_value' => false
        //   )
        // );
        echo "</div>";
    }

    /**
     * Вывод поля "Контент после заголовка" (the_excerpt)
     */
    static function excerpt_box()
    {
        global $post;

        echo "<label class='screen-reader-text' for='excerpt'> {_('Excerpt')} </label>
        <textarea rows='1' cols='40' name='excerpt' tabindex='6' id='excerpt'>{$post->post_excerpt}</textarea>";
    }

    /**
     * Enqueue Assets
     */
    static function enqueue_admin_assets()
    {
        $screen = get_current_screen();
        if( $screen->post_type != self::post_type ) {
            return false;
        }

        if ( ! did_action('wp_enqueue_media') ) {
            wp_enqueue_media();
        }

        $core_url = '/assets/core/';
        wp_enqueue_style(MB_PREF . 'style', MBLOCKS_URL . $core_url . 'style.css', array(), '1.0' );
        wp_enqueue_script( MB_PREF . 'view', MBLOCKS_URL . $core_url . 'view.js', array('jquery'), '1.0', true );
        wp_localize_script(MB_PREF . 'view', 'mb_settings', array(
            'nonce' => wp_create_nonce( self::security ),
        ) );
        $lib = 'jquery.data-actions';
        wp_enqueue_script( 'easy-actions', MBLOCKS_URL.$core_url.$lib.'/'.$lib.'.min.js', array('jquery'), '1.0', true );
    }

    /**
     * Validate Post's Data
     *
     * @todo : set metas with array
     */

    /**
     * Записываем данные для каждого изображения
     */
    static function write_attachments_post_meta( $post_id )
    {
        $args = wp_parse_args( array_filter($_POST, 'sanitize_text_field'), array(
            'attachment_id' => 0,
            'attachment_excerpt' => array(),
            'attachment_content' => array(),
            'attachment_link'    => array(),
        ) );

        extract($args);

        if( ! is_array($attachment_id) ) {
            mb_post_meta( $post_id, 'media_imgs', '' );
            return $post_id;
        }

        mb_post_meta( $post_id, 'media_imgs', implode(',', $attachment_id) );

        foreach ($attachment_id as $aid) {
            $update = array( 'ID' => $aid );

            if( isset($attachment_excerpt[$aid]) )
                $update['post_excerpt'] = $attachment_excerpt[$aid];

            if( isset($attachment_content[$aid]) )
                $update['post_content'] = $attachment_content[$aid];

            if( sizeof($update) > 1 )
                wp_update_post( $update );

            if( isset($attachment_link[$aid]) )
                update_post_meta( $aid, MB_PREF .'link', $attachment_link[$aid] );
        }
    }

    static function validate( $post_id ){
        if ( ! isset( $_POST[__CLASS__] ) || ! wp_verify_nonce( $_POST[__CLASS__], self::security ) )
            return false;

        self::write_attachments_post_meta($post_id);

        $args = wp_parse_args( array_filter($_POST, 'sanitize_text_field'), array(
            '_show_title' => false,
            'main_type'   => false,
            'type'        => false,
        ) );

        extract($args);

        if( ! $main_type || ! $type ) return $post_id;

        mb_post_meta($post_id, '_show_title', $_show_title );
        mb_post_meta($post_id, 'main_type', $main_type);
        mb_post_meta($post_id, 'type', $type);

        // if(isset($_POST['query']))
        //     mb_post_meta($post_id, 'query', $_POST['query']);

        mblock_settings_from_file($post_id, $main_type, false, $args );
        mblock_settings_from_file($post_id, $type, $main_type, $args );

        /**
         * Create TEMP Style File
         */
        // $asset = self::pre_register_assets( $type );
        // if( ! isset($asset[ $type ]) )
        //     return false;

        // $file = get_template_directory() . 'assets/blocks/block-'.$post_id.'.css';
        // if ( ! file_exists( $file ) ) {
        //     $file = DT_MULTIMEDIA_PATH . 'assets/' . $asset[ $type ]['theme'];
        // }

        // $out_file = DT_MULTIMEDIA_PATH . 'assets/block-'.$post_id.'.css';

        // if ( file_exists( $file ) ){
        //     $scss = new \scssc();
        //     $scss->setFormatter('scss_formatter_compressed');
        //     $compiled = $scss->compile( apply_filters( 'remove_cyrillic', '#mediablock-'.$post_id.' {' . file_get_contents($file) . '}' ) );

        //     if(!empty($compiled))
        //         file_put_contents( $out_file, $compiled );
        // }
    }
}
