<?php

if ( ! empty( $field['options'] ) ) {
    if ( '' === current($field['options']) ) {
        if ( empty($attributes['placeholder']) )
            $attributes['placeholder'] = $text ? $text : __( 'Choose an option' );
    }

    $input .= $label[0];
    $input .= '<select ' . self::get_attributes_text( $attributes ) . '>';
    $input .= self::get_select_options($field['options'], $entry);
    $input .= '</select>';
    $input .= $label[1];
}