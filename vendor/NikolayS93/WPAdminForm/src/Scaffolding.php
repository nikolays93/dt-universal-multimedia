<?php

namespace NikolayS93\WPAdminForm;

class Scaffolding
{
    static $clear_value = false;
    protected $inputs, $args, $is_table, $active;
    protected $hiddens = array();

    /**
     * EXPEREMENTAL!
     *
     * @return array installed options
     */
    private function _active()
    {
        if( $this->args['postmeta'] ){
            global $post;

            // do not use instanceof
            if( ! is_a($post, 'WP_Post') ) {
                return false;
            }

            $active = array();
            if( $sub_name = $this->args['sub_name'] ) {
                $active = get_post_meta( $post->ID, $sub_name, true );
            }
            else {
                foreach ($this->fields as $field) {
                    $active[ $field['id'] ] = get_post_meta( $post->ID, $field['id'], true );
                }
            }
        }
        else {
            $active = get_option( $this->args['admin_page'], array() );

            if( $sub_name = $this->args['sub_name'] ) {
                $active = isset($active[ $sub_name ]) ? $active[ $sub_name ] : false;
            }
        }

        /** if active not found */
        if( ! is_array($active) || $active === array() ) {
            return false;
        }

        /**
         * @todo: add recursive handle
         */
        $result = array();
        foreach ($active as $key => $value) {
            if( is_array($value) ){
                foreach ($value as $key2 => $value2) {
                    $result[$key . '_' . $key2] = $value2;
                }
            }
            else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /******************************** Templates *******************************/
    private function _field_template( $field, $input, $for_table )
    {
        // if ( $field['required'] ) {
        //     $field['class'][] = 'required';
        //     $required = ' <abbr class="required" title="' . esc_attr__( 'required' ) . '">*</abbr>';
        // } else {
        //     $required = '';
        // }

        $html = array();

        $desc = '';
        if( $field['description'] ){
            if( isset($this->args['hide_desc']) && $this->args['hide_desc'] === true )
                $desc = "<div class='description' style='display: none;'>{$field['description']}</div>";
            else
                $desc = "<span class='description'>{$field['description']}</span>";
        }

        $template = $field['before'] . $this->args['item_wrap'][0];
        $template.= $input;
        $template.= $this->args['item_wrap'][1] . $field['after'];
        $template.= $desc;

        if( ! $this->is_table ){
            $html[] = '<section id="'.$field['id'].'-wrap">' . $template . '</section>';
        }
        elseif( $field['type'] == 'hidden' ){
            $this->hiddens[] = $input;
        }
        elseif( $field['type'] == 'html' ){
            $html[] = $this->args['form_wrap'][1];
            $html[] = $input;
            $html[] = $this->args['form_wrap'][0];
        }
        else {
            $lc = is_array($field['label_class']) ? implode( ' ', $field['label_class'] ) : $field['label_class'];
            $html[] = "<tr id='{$field['id']}'>";
            // @todo : add required symbol
            $html[] = sprintf('  <%1$s class="label">%2$s</%1$s>', $this->args['label_tag'], $field['label']);

            $html[] = "  <td>";
            $html[] = "    " . $template;
            $html[] = "  </td>";
            $html[] = "</tr>";
        }

        return implode("\n", $html);
    }
}