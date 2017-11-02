<?php

namespace CDevelopers\media;

/**
 * Регистрация типа записей "Медиа блоки"
 */
add_action('init', __NAMESPACE__ . '\register_mediablocks_type' );
function register_mediablocks_type() {
    register_post_type( Utils::OPTION, array(
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
            'name' => __( 'Mediablocks', MB_LANG ),
            'singular_name' => __( 'Mediablock', MB_LANG ),
            'add_new'       => __( 'Add block', MB_LANG ),
            'add_new_item'  => __( 'Add new block', MB_LANG ),
            'edit_item'     => __( 'Edit block', MB_LANG ),
            'new_item'      => __( 'New block', MB_LANG ),
            'view_item'     => __( 'View block', MB_LANG ),
            'search_items'  => __( 'Search media block', MB_LANG ),
        )
    ) );
}

/**
 * Добавить стили и скрипты на страницу создания и редактирования типа записи
 */
add_action( 'load-post.php',     __NAMESPACE__ . '\enqueue_admin_assets' );
add_action( 'load-post-new.php', __NAMESPACE__ . '\enqueue_admin_assets' );
function enqueue_admin_assets() {
    if( ($screen = get_current_screen()) && isset($screen->post_type) && $screen->post_type != Utils::OPTION ) {
        return false;
    }

    if ( ! did_action('wp_enqueue_media') ) {
        wp_enqueue_media();
    }

    $core_url = Utils::get_plugin_url( 'assets/core' );

    wp_enqueue_style(  Utils::PREF . 'style', $core_url . '/style.css', array(), '1.0' );
    wp_enqueue_script( Utils::PREF . 'admin', $core_url . '/admin.js', array('jquery'), '1.0', true );
    wp_localize_script( Utils::PREF . 'admin', 'mb_settings', array(
        'nonce' => wp_create_nonce( Utils::SECURITY ),
    ) );

    $min = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
    wp_enqueue_script( 'easy-actions', $core_url . '/jquery.data-actions'.$min.'.js', array('jquery'), '1.0', true );
}

/**
 * Шорткод и
 * Чекбокс после заголовка "Показывать заголовок"
 */
add_action( 'edit_form_after_title', __NAMESPACE__ . '\after_title' );
function after_title() {
    global $post, $wp_meta_boxes;

    if( $post->post_type == Utils::OPTION ) {
    ?>
    <div class='shortcode-wrap wrap-sc'>
        <label>
            <?php
                // _e('Show title', MB_LANG);
                // echo sprintf('<input id="_show_title" name="_show_title" type="checkbox" value="on"%s>',
                //     checked( Utils::_post_meta($post->ID, '_show_title'), 'on', true ))
            ?>
        </label>

        <?php
            _e('Insert short code in any post', MB_LANG);
            echo sprintf('<input readonly="readonly" id="shortcode" type="text" value="%s" onclick="%s">',
                '[mblock id=&quot;'.$post->ID.'&quot;]',
                'this.focus();return false;'
            );
        ?>
    </div>
<!--     <div class="media-modal wp-core-ui">
        <div class="media-modal-content">
            <div class="edit-attachment-frame mode-select hide-menu hide-router">
                <div class="media-frame-content">
                    <?php // echo get_media_item( 297 ); ?>
                </div>
            </div>
        </div>
    </div> -->
    <?php
    }
}

/**
 * Добавить метабоксы
 */
add_action( 'add_meta_boxes', __NAMESPACE__ . '\blocks_meta_boxes', 20 );
function blocks_meta_boxes() {
    add_meta_box('attachments', 'Мультимедиа', __NAMESPACE__ . '\attachments_grid', Utils::OPTION, 'normal', 'high');
    add_meta_box('json_options', __('JSON options'), __NAMESPACE__ . '\json_options', Utils::OPTION, 'normal');
    add_meta_box('grid_options', 'Настройки', __NAMESPACE__ . '\grid_options', Utils::OPTION, 'side');
}

/**
 * Ajax обновление метабоксов
 */
add_action( 'wp_ajax_main_settings', 'json_options' );
add_action( 'wp_ajax_main_settings', 'grid_options' );

add_action( 'mb_attachments_tool_bar', __NAMESPACE__ . '\attachments_media_buttons', 50 );
function attachments_media_buttons() {
    ?>
    <div class="hide-if-no-js wp-media-buttons">
        <button id="detail_view" class="button" type="button">
            <span class="dashicons dashicons-screenoptions"></span>
            <span class="dashicons dashicons-list-view"></span>
        </button>
        <button id="upload-images" class="button add_media">
            <span class="wp-media-buttons-icon"></span> Добавить медиафайл
        </button>
    </div><!-- .wp-media-buttons -->
    <?php
}

add_action( 'mb_attachments_tool_bar', __NAMESPACE__ . '\determine_type', 50 );
function determine_type() {
    // echo sprintf('<label>%s</label>', __('', MB_LANG) );

    $form = new WP_Admin_Forms( Utils::get_settings( 'general' ), false, array(
        'form_name'  => 'mtypes',
        'mode'       => 'post',
        // Template:
        'item_wrap'   => array('<span>', '</span>'),
        // 'form_wrap'   => array('', ''),
        // 'label_tag'   => 'th',
        // 'hide_desc'   => false,
    ) );

    echo $form->render();
}

function attachments_grid( $post ) {
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
    ?>

        <?php do_action('mb_attachments_tool_bar'); ?>
        <div class="clear"></div>

        <ul tabindex="-1" id="dt-media" class="attachments">
        <?php
            $arrAttachments = array();
            if( $attachments = get_post_meta( $post->ID, '_attachments', true ) ) {
                $arrAttachments = explode(',', $attachments);
            }

            foreach ($arrAttachments as $attachment_id) :
                $attachment_id = absint($attachment_id);
                $attachment = get_post( $attachment_id );
                if( ! $attachment ) continue;

                $attachment_metadata = wp_get_attachment_metadata( $attachment_id );

                $image = wp_get_attachment_image($attachment_id, 'medium');
                $file = explode('.', basename($attachment_metadata['file']));

                $attachmentOrientation = ( $attachment_metadata['height'] > $attachment_metadata['width'] ) ? 'portrait' : 'landscape';

                $attachmentClass = array('attachment-preview');
                $attachmentClass[] = $attachmentOrientation;
                if( isset($attachment_metadata['sizes']['thumbnail']['mime-type']) ) {
                    $types = explode('/', $attachment_metadata['sizes']['thumbnail']['mime-type']);
                    $attachmentClass[] = 'type-' . $types[0];
                    if( isset($types[1]) ) {
                        $attachmentClass[] =  'subtype-' . $types[1];
                    }
                }

                $attachment_link = get_post_meta( $attachment_id, 'link', true );
                $attachment_blank = get_post_meta( $attachment_id, '_blank', true );
                ?>
                <li tabindex="0" aria-label="<?php echo $file[0] ?>" data-id="<?php echo $attachment_id ?>" class="attachment">
                    <div class="thumbnail-wrap">
                        <div class="<?php echo implode(' ', $attachmentClass); ?>">
                            <div class="thumbnail">
                                <div class="centered">
                                    <?php echo $image; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        echo sprintf('<input type="text" class="item-excerpt" name="%s" value="%s">',
                            esc_attr( "attachment_excerpt[ $attachment_id ]" ),
                            esc_attr( $attachment->post_excerpt ) );
                        ?>
                    </div>
                    <?php
                    echo sprintf('<textarea class="item-content" name="%s" cols="75" rows="7" placeholder="The some contents..">%s</textarea>',
                        "attachment_content[ $attachment_id ]",
                        $attachment->post_content );
                    ?>
                    <div class="item-link-wrap">
                        <?php
                        echo sprintf('<input type="text" class="item-link" name="%s" value="%s">',
                            "attachment_link[ $attachment_id ]",
                            esc_attr( $attachment_link ) );
                        ?>
                        <label class="open-blank">
                            <?php
                            // _e('Target blank');
                            // echo sprintf('<input type="checkbox" class="item-blank" name="%s" value="1">',
                            //     "attachment_blank[ $attachment_id ]",
                            //     checked( '1', $attachment_blank, false )
                            // );
                                ?>
                        </label>
                    </div>

                    <button type="button" class="check remove" tabindex="-1">
                        <span class="media-modal-icon"></span>
                        <!-- <span class="screen-reader-text">Убрать</span> -->
                    </button>
                    <input type="hidden" id="attachments" name="attachment_id[]" value="<?php echo $attachment_id; ?>">
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="clear"></div>
        <?php do_action('mb_after_attachments'); ?>
    <?php
    wp_nonce_field( Utils::SECURITY, 'mediablocks' );
}

/**
 * Показывает настройки библиотеки
 * Обновляется через AJAX
 */
function json_options( $post, $default = '' )
{
    //$_POST

    $atts = wp_parse_args( get_post_meta(get_the_ID(), 'mtypes', true), array(
        'grid_type' => 'carousel',
        'lib_type'  => 'slick',
    ) );

    echo '<div class="inner">';

    $form = new WP_Admin_Forms( Utils::get_settings( 'lib/'.$atts['lib_type'], $atts ), $is_table = true, $args = array(
        'form_name'  => '_json_options',
        'mode'       => 'post',
        // template:
        // 'item_wrap'   => array('<p>', '</p>'),
        // 'form_wrap'   => array('', ''),
        // 'label_tag'   => 'th',
        // 'hide_desc'   => false,
    ) );

    echo $form->render();

    echo '</div>';
}

/**
 * Показывает под настройки (справа в сайдбаре)
 * Обновляется через AJAX
 */
function grid_options( $post )
{
    $atts = wp_parse_args( get_post_meta(get_the_ID(), 'mtypes', true), array(
        'grid_type' => 'carousel',
        'lib_type'  => 'slick',
    ) );


    echo "<div class='inner'>";

    $form = new WP_Admin_Forms( Utils::get_settings( 'grid/'.$atts['grid_type'], $atts ), $is_table = true, $args = array(
        'form_name'  => '_grid_options',
        'mode'       => 'post',
        // template:
        // 'item_wrap'   => array('<p>', '</p>'),
        // 'form_wrap'   => array('', ''),
        // 'label_tag'   => 'th',
        // 'hide_desc'   => false,
    ) );

    echo $form->render();

    echo '</div>';
}

add_action( 'add_meta_boxes', __NAMESPACE__ . '\change_excerpt_box', 10 );
function change_excerpt_box() {
    add_meta_box('mb_excerpt', 'Контент после заголовка', __NAMESPACE__ . '\excerpt_box', Utils::OPTION, 'normal');
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
    remove_meta_box( 'slugdiv', Utils::OPTION, 'normal' );
    // Произвольные поля
    remove_meta_box( 'postcustom', Utils::OPTION, 'normal' );
    // Цитата (Краткое содержимое).
    remove_meta_box( 'postexcerpt', Utils::OPTION, 'normal' );
}

/**
 * Записываем данные для каждого изображения
 */
function write_attachments_post_meta( $post_id ) {
    $args = wp_parse_args( $_POST, array(
        'attachment_id' => 0,
        'attachment_excerpt' => array(),
        'attachment_content' => array(),
        'attachment_link'    => array(),
        'attachment_blank'   => array(),
    ) );

    extract($args);

    if( ! is_array( $attachment_id ) ) {
        update_post_meta( $post_id, '_attachments', '' );
        return $post_id;
    }

    /** Записываем новые изображения */
    update_post_meta( $post_id, '_attachments', implode(',', $attachment_id) );

    foreach ($attachment_id as $attach_id) {
        $update = array( 'ID' => $attach_id );

        if( isset($attachment_excerpt[ $attach_id ]) )
            $update['post_excerpt'] = $attachment_excerpt[ $attach_id ];

        if( isset($attachment_content[ $attach_id ]) )
            $update['post_content'] = $attachment_content[ $attach_id ];

        if( sizeof( $update ) > 1 )
            wp_update_post( $update );

        if( isset($attachment_link[ $attach_id ]) )
            update_post_meta( $attach_id, 'link', $attachment_link[ $attach_id ] );

        if( ! empty($attachment_blank[ $attach_id ]) )
            update_post_meta( $attach_id, '_blank', '1' );
    }
}

/**
 * Сохранить изменения в записи типа медиаблок
 *
 * @todo : set metas with array
 */
add_action( 'save_post', __NAMESPACE__ . '\validate' );
function validate( $post_id ) {
    if ( ! isset( $_POST['mediablocks'] ) || ! wp_verify_nonce( $_POST['mediablocks'], Utils::SECURITY ) ) {
        return false;
    }

    write_attachments_post_meta( $post_id );

    file_put_contents(__DIR__ . '/debug.log', print_r(array($post_id, $_POST), 1));

    if( isset($_POST['mtypes']) )
        update_post_meta( $post_id, 'mtypes', $_POST['mtypes'] );

    if( // isset($_POST['mtypes']['grid_type']) &&
        isset($_POST['_grid_options']) && is_array($_POST['_grid_options']) ) {
        // $defaults = WP_Admin_Forms::defaults(
        //     Utils::get_settings( 'grid/'.$_POST['mtypes']['grid_type'], $_POST['mtypes'] ) );

        // foreach ($defaults as $field_id => $default) {
        //     if( isset($_POST['_grid_options'][ $field_id ]) && $_POST['_grid_options'][ $field_id ] != $default )
        //         $result[ $field_id ] = $_POST['_grid_options'][ $field_id ];
        // }
        update_post_meta( $post_id, '_grid_options', array_filter($_POST['_grid_options']) );
    }

    if( isset($_POST['mtypes']['lib_type']) && isset($_POST['_json_options']) && is_array($_POST['_json_options']) ) {
        $result = array();
        $defaults = WP_Admin_Forms::defaults(
            Utils::get_settings( 'lib/' . $_POST['mtypes']['lib_type'], $_POST['mtypes'] ) );

        foreach ($defaults as $field_id => $default) {
            if( isset($_POST['_json_options'][ $field_id ]) && $_POST['_json_options'][ $field_id ] != $default )
                $result[ $field_id ] = $_POST['_json_options'][ $field_id ];
        }

        update_post_meta( $post_id, '_json_options', $result );
    }

        return $post_id;
    //     $args = wp_parse_args( array_filter($_POST, 'sanitize_text_field'), array(
    //         '_show_title' => false,
    //         'main_type'   => false,
    //         'type'        => false,
    //     ) );

    //     extract($args);

    //     if( ! $main_type || ! $type ) return $post_id;

    //     mb_post_meta($post_id, '_show_title', $_show_title );
    //     mb_post_meta($post_id, 'main_type', $main_type);
    //     mb_post_meta($post_id, 'type', $type);

    //     // if(isset($_POST['query']))
    //     //     mb_post_meta($post_id, 'query', $_POST['query']);

    //     mblock_settings_from_file($post_id, $main_type, false, $args );
    //     mblock_settings_from_file($post_id, $type, $main_type, $args );

    //     /**
    //      * Create TEMP Style File
    //      */
    //     // $asset = self::pre_register_assets( $type );
    //     // if( ! isset($asset[ $type ]) )
    //     //     return false;

    //     // $file = get_template_directory() . 'assets/blocks/block-'.$post_id.'.css';
    //     // if ( ! file_exists( $file ) ) {
    //     //     $file = DT_MULTIMEDIA_PATH . 'assets/' . $asset[ $type ]['theme'];
    //     // }

    //     // $out_file = DT_MULTIMEDIA_PATH . 'assets/block-'.$post_id.'.css';

    //     // if ( file_exists( $file ) ){
    //     //     $scss = new \scssc();
    //     //     $scss->setFormatter('scss_formatter_compressed');
    //     //     $compiled = $scss->compile( apply_filters( 'remove_cyrillic', '#mediablock-'.$post_id.' {' . file_get_contents($file) . '}' ) );

    //     //     if(!empty($compiled))
    //     //         file_put_contents( $out_file, $compiled );
    //     // }
}
