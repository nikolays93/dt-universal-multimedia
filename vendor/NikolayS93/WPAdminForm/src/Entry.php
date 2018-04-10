<?php

namespace NikolayS93\WPAdminForm;

class Entry
{
    private static function is_checked( $field, $active )
    {
        // if( $active === false && $value )
          // return true;

        $checked = ( $active === false ) ? false : true;
        if( $active === 'false' || $active === 'off' || $active === '0' )
            return false;

        if( $active === 'true'  || $active === 'on'  || $active === '1' )
            return true;

        if( $active || $field['default'] ){
            if( $field['value'] ){
                if( is_array($active) ){
                    if( in_array($field['value'], $active) )
                        return true;
                }
                else {
                    if( $field['value'] == $active || $field['value'] === true )
                        return true;
                }
            }
            else {
                if( $active || (!$checked && $field['default']) )
                    return true;
            }
        }

        return false;
    }

    private static function parse_entry($field, $active)
    {
        if( ! is_array($active) || sizeof($active) < 1 )
            return false;

        $active_key = $field['check_active'] ? $field[$field['check_active']] : str_replace('[]', '', $field['name']);
        $active_value = isset($active[$active_key]) ? $active[$active_key] : false;

        if($field['type'] == 'checkbox' || $field['type'] == 'radio'){
            $entry = self::is_checked( $field, $active_value );
        }
        elseif($field['type'] == 'select'){
            $entry = ($active_value) ? $active_value : $field['default'];
        }
        else {
            // if text, textarea, number, email..
            $entry = $active_value;
        }
        return $entry;
    }
}
