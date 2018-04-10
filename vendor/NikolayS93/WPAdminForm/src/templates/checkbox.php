<?php

if( empty($attributes['value']) ) $attributes['value'] = 'on';
if( empty($attributes['checked']) ) {
    if( ! $attributes['checked'] = checked( $entry, true, false ) )
        unset($attributes['checked']);
}

$attributes['type'] = esc_attr( $field['type'] );
$attributes['class'] .= ' input-checkbox';

// if $clear_value === false dont use defaults (couse default + empty value = true)
if( isset($clear_value) || false !== ($clear_value = self::$clear_value) ) {
    $input .= sprintf('<input type="hidden" name="%s" value="%s">',
        $attributes['name'], $clear_value) . "\n";
}

$input .= '<input ' . self::get_attributes_text( $attributes ) . '/>';
$input .= $label[0] . $label[1];