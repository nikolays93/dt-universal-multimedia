<?php

namespace NikolayS93\WPAdminForm;

class Form extends Scaffolding
{
    public function __construct($data = null, $is_table = true, $args = null)
    {
        if( ! is_array($data) )
            $data = array();

        if( ! is_array($args) )
            $args = array();

        if( isset($data['id']) || isset($data['name']) )
            $data = array($data);

        $args = self::parse_defaults($args, $is_table);
        if( $args['admin_page'] || $args['sub_name'] ) {
            foreach ($data as &$field) {
                if ( ! isset($field['id']) && ! isset($field['name']) )
                    continue;

                if( $args['admin_page'] ) {
                    if( isset($field['name']) ) {
                        $field['name'] = ($args['sub_name']) ?
                        "{$args['admin_page']}[{$args['sub_name']}][{$field['name']}]" : "{$args['admin_page']}[{$field['name']}]";
                    }
                    else {
                        $field['name'] = ($args['sub_name']) ?
                        "{$args['admin_page']}[{$args['sub_name']}][{$field['id']}]" : "{$args['admin_page']}[{$field['id']}]";
                    }

                    if( !isset($field['check_active']) )
                        $field['check_active'] = 'id';
                }
            }
        }

        $this->fields = $data;
        $this->args = $args;
        $this->is_table = $is_table;
    }

    public function render( $return=false )
    {
        $this->get_active();

        $html = $this->args['form_wrap'][0];
        foreach ($this->fields as $field) {
            if ( ! isset($field['id']) && ! isset($field['name']) )
                continue;

            // &$field
            $input = self::render_input( $field, $this->active, $this->is_table );
            $html .= self::_field_template( $field, $input, $this->is_table );
        }
        $html .= $this->args['form_wrap'][1];
        $result = $html . "\n" . implode("\n", $this->hiddens);
        if( $return )
            return $result;

        echo $result;
    }
}
