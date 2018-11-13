<?php

namespace NikolayS93\MBlocks;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

class Utils extends Plugin
{
    private function __construct() {}
    private function __clone() {}

    /**
     * Получает настройку из parent::$options || из кэша || из базы данных
     * @param  mixed  $default Что вернуть если опции не существует
     * @return mixed
     */
    private static function get_option( $default = array() )
    {
        if( ! parent::$options )
            parent::$options = get_option( parent::get_option_name(), $default );

        return apply_filters( "get_{DOMAIN}_option", parent::$options );
    }

    /**
     * Получает url (адресную строку) до плагина
     * @param  string $path путь должен начинаться с / (по аналогии с __DIR__)
     * @return string
     */
    public static function get_plugin_url( $path = '' )
    {
        $url = plugins_url( basename(PLUGIN_DIR) ) . $path;

        return apply_filters( "get_{DOMAIN}_plugin_url", $url, $path );
    }

    public static function get_template( $template, $slug = false, $data = array() )
    {
        if ($slug) $templates[] = PLUGIN_DIR . '/' . $template . '-' . $slug;
        $templates[] = PLUGIN_DIR . '/' . $template;

        if ($tpl = locate_template($templates)) {
            return $tpl;
        }

        return false;
    }

    public static function get_admin_template( $tpl = '', $data = array(), $include = false )
    {
        $filename = PLUGIN_DIR . '/admin/template/' . $tpl;
        if( !file_exists($filename) ) $filename = false;

        if( $filename && $include ) {
            include $filename;
        }

        return $filename;
    }

    /**
     * Получает параметр из опции плагина
     * @todo Добавить фильтр
     *
     * @param  string  $prop_name Ключ опции плагина или 'all' (вернуть опцию целиком)
     * @param  mixed   $default   Что возвращать, если параметр не найден
     * @return mixed
     */
    public static function get( $prop_name, $default = false )
    {
        $option = self::get_option();
        if( 'all' === $prop_name ) {
            if( is_array($option) && count($option) ) {
                return $option;
            }

            return $default;
        }

        return isset( $option[ $prop_name ] ) ? $option[ $prop_name ] : $default;
    }

    /**
     * Установит параметр в опцию плагина
     * @todo Подумать, может стоит сделать $autoload через фильтр, а не параметр
     *
     * @param mixed  $prop_name Ключ опции плагина || array(параметр => значение)
     * @param string $value     значение (если $prop_name не массив)
     * @param string $autoload  Подгружать опцию автоматически @see update_option()
     * @return bool             Совершились ли обновления @see update_option()
     */
    public static function set( $prop_name, $value = '', $autoload = null )
    {
        $option = self::get_option();
        if( ! is_array($prop_name) ) $prop_name = array($prop_name => $value);

        foreach ($prop_name as $prop_key => $prop_value) {
            $option[ $prop_key ] = $prop_value;
        }

        return update_option( parent::get_option_name(), $option, $autoload );
    }

    /**
     * Получить настройки из файла
     * @param  string $filename Название файла в папке настроек ex. 'main.php'
     * @param  array  $args     Параметры что нужно передать в файл настроек
     * @return mixed
     */
    public static function get_settings( $filename, $args = array() ) {
        include PLUGIN_DIR . '/admin/settings/' . $filename . '.php';
    }

    public static function dash_to_underscore( $str ){
        return str_replace('-', '_', $str);
    }

    public static function _post_meta( $post_id, $key = '', $value = false, $hidden = '_', $default = false )
    {
        $answer = $default;

        $key = DOMAIN . '_' . $key;
        if( $hidden ) {
            $key = $hidden . $key;
        }

        if( false === $value ) {
            if( $value ) {
                if( ! $result = get_post_meta( $post_id, $key, true ) ) {
                    $answer = $result;
                }
            }
            else {
                delete_post_meta( $post_id, $key );
            }
        }
        else {
            update_post_meta( $post_id, $key, true );
        }

        return $answer;
    }

    /**
     * Получить стандартные классы ячейки bootstrap сетки
     */
    public static function get_column_class( $columns_count = 4, $responsive = false ) {
        $xs = ( $need_xs = apply_filters('bootstrap3_columns', false) ) ? '-xs' : '';
        switch ($columns_count) {
            case 1: $col = 'col-12'; break;
            case 2: $col = ($responsive) ? 'col'.$xs.'-6 col-sm-6 col-md-6 col-lg-6' : 'col'.$xs.'-6'; break;
            case 3: $col = ($responsive) ? 'col'.$xs.'-12 col-sm-6 col-md-4 col-lg-4' : 'col'.$xs.'-4'; break;
            case 4: $col = ($responsive) ? 'col'.$xs.'-6 col-sm-4 col-md-3 col-lg-3' : 'col'.$xs.'-3'; break;
            // be careful
            case 5: $col = ($responsive) ? 'col'.$xs.'-12 col-sm-6 col-md-2-4 col-lg-2-4' : 'col'.$xs.'-2-4'; break;
            case 6: $col = ($responsive) ? 'col'.$xs.'-6 col-sm-4 col-md-2 col-lg-2' : 'col'.$xs.'-2'; break;
            case 12: $col= ($responsive) ? 'col'.$xs.'-4 col-sm-3 col-md-1 col-lg-1' : 'col'.$xs.'-1'; break;

            default: $col = false; break;
        }
        return $col;
    }

    public static function get_library_list() {

        return apply_filters( DOMAIN . '_library_list', array() );
    }
}
