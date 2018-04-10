<?php

namespace NikolayS93\WPAdminForm;

class Input
{
    public static function render( &$field, $active = array(), $for_table = false )
    {
        $defaults = array(
            'type'              => 'text',
            'label'             => '',
            'description'       => isset($field['desc']) ? $field['desc'] : '',
            'placeholder'       => '',
            'maxlength'         => false,
            'required'          => false,
            'autocomplete'      => false,
            'id'                => '',
            'name'              => $field['id'],
            // 'class'             => array(),
            'label_class'       => array('label'),
            'input_class'       => array(),
            'options'           => array(),
            'custom_attributes' => array(),
            // 'validate'          => array(),
            'default'           => '',
            'before'            => '',
            'after'             => '',
            'check_active'      => false,
            'value'             => '',
        );

        $field = wp_parse_args( $field, $defaults );

        if( $field['default'] && ! in_array($field['type'], array('checkbox', 'select', 'radio')) ) {
            $field['placeholder'] = $field['default'];
        }

        $active = is_string($active) ? array($field['id'] => $active) : $active;
        $field['id'] = str_replace('][', '_', $field['id']);
        $entry = self::parse_entry($field, $active, $field['value']);

        return self::_input_template( $field, $entry, $for_table );
    }

    private static function _input_template( $field, $entry, $for_table = false )
    {
        $attributes = array();
        $attributes['name'] = esc_attr( $field['name'] );
        $attributes['id'] = esc_attr( $field['id'] );
        $attributes['class'] = esc_attr( is_array($field['input_class']) ?
            implode(' ', $field['input_class']) : $field['input_class'] );

        if( $field['value'] )
            $attributes['value'] = ('html' == $field['type']) ? $field['value'] : esc_attr( $field['value'] );

        if( $field['placeholder'] )
            $attributes['placeholder'] = esc_attr( $field['placeholder'] );

        if( $field['maxlength'] )
            $attributes['maxlength'] = absint( $field['maxlength'] );

        if( $field['autocomplete'] )
            $attributes['autocomplete'] = esc_attr( $field['autocomplete'] );

        if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
            foreach ( $field['custom_attributes'] as $attribute => $attribute_value ) {
                $attributes[ esc_attr( $attribute ) ] = esc_attr( $attribute_value );
            }
        }

        $label = array('', '');
        if( ! $for_table && $field['label'] ) {
            $label = array(
                sprintf('<label for="%s" class="%s"><span>%s</span>',
                    esc_attr($field['id']),
                    esc_attr(is_array($field['label_class']) ? implode(' ', $field['label_class']) : $field['label_class']),
                    $field['label']
                ),
            '</label>');
        }

        $input = '';
        switch ( $field['type'] ) {
            case 'html':
                $input .= $attributes['value'];
            break;

            case 'textarea':
            case 'checkbox':
            case 'select':
                include( __DIR__ . '/templates/'.$field['type'].'.php' );
            break;

            // @todo:
            case 'radio': break;
            case 'fieldset': break;

            default:
                include( __DIR__ . '/templates/default.php' );
            break;
        }
        return $input;
    }
}
