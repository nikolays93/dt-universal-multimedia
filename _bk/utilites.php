<?php

if( ! function_exists('mb_post_meta') ) {
    /**
     * Update or Get post meta with prefix (create if empty)
     *
     * @param  int
     * @param  string meta name (without prefix)
     * @param  string values for update or get
     */
    function mb_post_meta( $post_id = 0, $key = 0, $update_value = false ) {
        // $post_id required
        if( ! $post_id = absint($post_id) ) return false;

        // $key required
        if( ! $key ) return false;

        if( $update_value !== false ) {
            // not empty value
            if( $update_value ) {
                update_post_meta( $post_id, '_'.MB_PREF.$key, $update_value );
            }
            else {
                delete_post_meta( $post_id, '_'.MB_PREF.$key );
            }
        }
        else {
          return get_post_meta( $post_id, '_'.MB_PREF.$key, true );
        }
    }
}

if( ! function_exists('mblock_parse_settings') ) {
    /**
    * Settings & Options
    *
    * Include settings file
    *
    * @param  string settings filename
    * @param  string type returned settings
    * @return array settings
    */
    function mblock_parse_settings( $file = false, $main_type = 'carousel' ) {
        if( ! $file ) return false;

        $path = MBLOCKS_DIR . '/settings/'.$file.'.php';

        if ( is_readable( $path ) ) {
            return include( $path );
        }

        return false;
    }
}

if( ! function_exists('mblock_settings_from_file') ) {
    /**
    * Get or Set values to meta from settings file
    *
    * @param  int    $post_id
    * @param  string $settings_name      settings filename (subtype if ($settings_maintype))
    * @param  string $settings_maintype  main_type settinigs (carousel, gallery..)
    * @param  values $block_values       to record, get installed values if 'false'
    * @return is get (array) else (null)
    */
    function mblock_settings_from_file( $post_id, $settings_name, $settings_maintype = false, $block_values = false ) {
        if( ! $settings_name || ! $post_id = absint( $post_id ) ) {
            return false;
        }

        $filename = ($settings_maintype) ? 'sub/'.$settings_name : 'main/'.$settings_name;
        $settings = mblock_parse_settings( $filename, $settings_maintype );

        if( ! $settings ) return false;

        $result = array();
        $values = ($block_values) ? $block_values : mb_post_meta( $post_id, $settings_name.'_opt' );

        foreach ( $settings as $param ) {
            // Если не указан name принимаем id, иначе '';
            if( !isset($param['name']) ) {
                $param['name'] = isset($param['id']) ? $param['id'] : '';
            }

            // Если не указан default принимаем placeholder, иначе '';
            if( !isset($param['default']) ) {
                $param['default'] = isset($param['placeholder']) ? $param['placeholder'] : '';
            }

            $pn = $param['name'];
            if($settings_maintype !== false) {
                if( isset($values[$pn]) && $values[$pn] != $param['default']){
                    // Пустой checkbox записываем как 'false'
                    if( $values[$pn] == '' && $param['type'] == 'checkbox' ) {
                        $result[$pn] = 'false';
                    }
                    // Принимаем значения если они не пустые, или если это select (Даже пустые)
                    elseif( $values[$pn] != '' || $param['type'] == 'select' ) {
                        $result[$pn] = $values[$pn];
                    }
                }
            }
            else {
                if( isset($values[$pn]) && ($values[$pn] != '' || $param['type'] == 'select') ) {
                  $result[$pn] = $values[$pn];
                }
            }
        }

        if( $block_values ){
            mb_post_meta( $post_id, $settings_name.'_opt', $result );
        }

        return $result;
    }
}

if( ! function_exists('mb_include_file')) {
    function mb_include_file( $arrFiles = array() ) {
        foreach ( $arrFiles as $class_name => $path ) {
            if( is_string( $class_name ) && class_exists( $class_name ) ) continue;

            $path = MBLOCKS_DIR . '/include/' . $path . '.php';
            if ( is_readable( $path ) ) {
                require_once( $path );
            }
        }
    }
}

if( ! function_exists('dash_to_underscore') ){
}