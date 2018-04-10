<?php

if( !empty( $field['custom_attributes']['rows'] ) ) $attributes['rows'] = 5;
if( !empty( $field['custom_attributes']['cols'] ) ) $attributes['cols'] = 40;

$input .= $label[0];
$input .= '<textarea ' . self::get_attributes_text( $attributes ) . '>';
$input .= esc_textarea( $entry );
$input .= '</textarea>';
$input .= $label[1];