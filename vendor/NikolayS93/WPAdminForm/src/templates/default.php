<?php

$attributes['type'] = esc_attr( $field['type'] );
$attributes['value'] = $field['value'] ? esc_attr( $field['value'] ) : esc_attr( $entry );
$attributes['class'] .= ' input-' . $attributes['type'];

$input .= $label[0];
$input .= '<input ' . self::get_attributes_text( $attributes ) . '/>';
$input .= $label[1];