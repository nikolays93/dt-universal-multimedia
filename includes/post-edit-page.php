<?php

namespace NikolayS93\MediaBlocks;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

/**
 * На странице создания и редактирования типа записи
 */
class mediablock_load_post_page
{
    function __construct()
    {
        /**
         * Шорткод и Чекбокс после заголовка "Показывать заголовок"
         */
        add_action( 'edit_form_after_title', array(__CLASS__, 'after_title') );

        /**
         * Добавить метабоксы
         */
        add_action( 'add_meta_boxes', array(__CLASS__, 'blocks_meta_boxes'), 20 );

        /**
         * Ajax обновление метабоксов
         */
        add_action( 'wp_ajax_mblock_options', array(__CLASS__, 'json_options') );
        add_action( 'wp_ajax_mblock_options', array(__CLASS__, 'grid_options') );

        /**
         * Добавить upload и sort кнопку
         */
        add_action( 'mediablocks_before_attachments',
            array(__CLASS__, 'attachments_media_buttons'), 50 );

        /**
         * Выбор типа блока
         */
        add_action( 'mediablocks_before_attachments',
            array(__CLASS__, 'determine_type'), 50 );

        /**
         * Запись параметров
         */
        add_action( 'save_post', array(__CLASS__, 'validate') );
    }

    static function after_title() {
        global $post, $wp_meta_boxes;

        if( $post->post_type !== Utils::get_option_name() ) {
            return;
        } ?>
        <div class='shortcode-wrap wrap-sc'>
            <label>
                <?php
                    _e('Show title', DOMAIN);
                    echo sprintf('<input id="_show_title" name="_show_title" type="checkbox" value="on"%s>',
                        checked( Utils::_post_meta($post->ID, '_show_title'), 'on', true ))
                ?>
            </label>

            <?php
                _e('Insert short code in any post', DOMAIN);
                echo sprintf('<input readonly="readonly" id="shortcode" type="text" value="%s" onclick="%s">',
                    '[mblock id=&quot;'.$post->ID.'&quot;]',
                    'this.focus();return false;'
                );
            ?>
        </div>
        <?php
    }

    static function blocks_meta_boxes() {
        add_meta_box('attachments', __('Multimedia', DOMAIN),
            array(__CLASS__, 'attachments_grid'), Utils::get_option_name(), 'normal', 'high');

        /** With AJAX */
        add_meta_box('json_options', __('JSON options', DOMAIN),
            array(__CLASS__, 'json_options'), Utils::get_option_name(), 'normal');
        add_meta_box('grid_options', __('Settings', DOMAIN),
            array(__CLASS__, 'grid_options'), Utils::get_option_name(), 'side');
    }

    /******************************* Attachments ******************************/
    static function attachments_media_buttons()
    {
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

    static function determine_type()
    {
        $form = new WP_Admin_Forms(
            Utils::get_settings( '/general.php' ),
            $table = false,
            array(
                'postmeta' => true,
                'admin_page' => 'settings',
                // 'form_name'  => 'mtypes',
                // 'mode'       => 'post',
                'item_wrap'   => array('<span>', '</span>'),
            ) );

        echo $form->render();
        echo '<div class="clear"></div>';
    }

    static function get_attachment_class( $attachment_metadata = false )
    {
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

        return implode(' ', $attachmentClass);
    }

    static function get_attachment_size( $attachment_metadata = false )
    {
        $upload = wp_upload_dir();
        $dir = dirname( $attachment_metadata['file'] ) . '/';

        if( isset( $attachment_metadata['sizes']['medium'] ) ) {
            $filename = $dir . $attachment_metadata['sizes']['medium']['file'];
        }
        elseif( isset( $attachment_metadata['sizes']['large'] ) ) {
            $filename = $dir . $attachment_metadata['sizes']['large']['file'];
        }
        elseif( isset( $attachment_metadata['sizes']['thumbnail'] ) ) {
            $filename = $dir . $attachment_metadata['sizes']['thumbnail']['file'];
        }
        elseif( isset( $attachment_metadata['sizes']['full'] ) ) {
            $filename = $dir . $attachment_metadata['sizes']['full']['file'];
        }
        else {
            $filename = $attachment_metadata['file'];
        }

        return $upload['baseurl'] . '/' . $filename;
    }

    static function attachments_grid( $post )
    {
        do_action('mediablocks_before_attachments');
        ?>
        <ul tabindex="-1" id="mediablocks-media" class="attachments">
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
                $file = explode('.', basename($attachment_metadata['file']));

                // Init template engine
                $m = new \Mustache_Engine;
                $tpl = Utils::get_plugin_dir('includes/templates/admin_attachment.tpl');
                echo $m->render( file_get_contents( $tpl ), array(
                    'filename' => $file[0],
                    'attachment_id' => $attachment_id,
                    'attachment_class' => self::get_attachment_class( $attachment_metadata ),
                    'attachment_url' => self::get_attachment_size( $attachment_metadata ),
                    'attachment_excerpt_value' => esc_attr( $attachment->post_excerpt ),
                    'attachment_content_value' => $attachment->post_content,
                    'attachment_link_value' => esc_attr( get_post_meta( $attachment_id, 'link', true ) ),
                    'attachment_blank_label' => __('Target blank', DOMAIN),
                    'attachment_blank_checked' => checked( '1',
                        get_post_meta( $attachment_id, '_blank', true ), false ),
                    'excerpt_description' => __( 'Caption' ),
                    'content_description' => __( 'Detail caption' ),
                    'link_description' => __( 'Link' ),
                ) );
            endforeach;
            ?>
        </ul>
        <?php do_action('mediablocks_after_attachments');

        wp_nonce_field( Utils::SECURITY, 'mediablocks' );
    }

    /**
     * Показывает настройки библиотеки
     * Обновляется через AJAX
     */
    static function json_options( $post, $default = '' )
    {
        $atts = wp_parse_args( get_post_meta(get_the_ID(), 'mtypes', true), array(
            'grid_type' => isset($_POST['grid_type']) ? $_POST['grid_type'] : 'carousel',
            'lib_type'  => isset($_POST['lib_type'])  ? $_POST['lib_type']  : 'slick',
        ) );

        $form = new WP_Admin_Forms(
            Utils::get_settings( '/lib/'.$atts['lib_type'], $atts ),
            $is_table = true, array(
                'form_name'  => '_json_options',
                'mode'       => 'post',
            ) );

        echo '<div class="inner">';
        echo $form->render();
        echo '</div>';
    }

    /**
     * Показывает под настройки (справа в сайдбаре)
     * Обновляется через AJAX
     */
    static function grid_options( $post )
    {
        $atts = wp_parse_args( get_post_meta(get_the_ID(), 'mtypes', true), array(
            'grid_type' => isset($_POST['grid_type']) ? $_POST['grid_type'] : 'carousel',
            'lib_type'  => isset($_POST['lib_type'])  ? $_POST['lib_type']  : 'slick',
        ) );

        $list = Utils::get_library_list();
        $form = new WP_Admin_Forms(
            $list['carousel']->options,
            $is_table = true,
            array(
                'form_name' => '_grid_options',
                'mode'      => 'post',
            ) );

        echo "<div class='inner'>";
        echo $form->render();
        echo '</div>';
    }

    /**
     * Записываем данные для каждого изображения
     */
    private static function write_attachments_post_meta( $post_id ) {
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

    static function validate( $post_id ) {
        if ( ! isset( $_POST['mediablocks'] ) || ! wp_verify_nonce( $_POST['mediablocks'], Utils::SECURITY ) ) {
            return false;
        }

        self::write_attachments_post_meta( $post_id );

        if( isset($_POST['mtypes']) )
            update_post_meta( $post_id, 'mtypes', $_POST['mtypes'] );

        if( isset($_POST['_grid_options']) && is_array($_POST['_grid_options']) )
            update_post_meta( $post_id, '_grid_options', array_filter($_POST['_grid_options']) );

        if( isset($_POST['_json_options']) ) {
            $result = array();

            // exclude defaults
            $defaults = WP_Admin_Forms::defaults(
                Utils::get_settings( 'lib/' . $_POST['mtypes']['lib_type'] . '.php', $_POST['mtypes'] ) );
            foreach ($defaults as $field_id => $default) {
                if( isset($_POST['_json_options'][ $field_id ]) && $_POST['_json_options'][ $field_id ] != $default )
                    $result[ $field_id ] = $_POST['_json_options'][ $field_id ];
            }

            update_post_meta( $post_id, '_json_options', $result );
        }

        return $post_id;
    }
}
new mediablock_load_post_page();
