<?php

namespace NikolayS93\MediaBlocks;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class WP_List_Table extends \WP_List_Table {

    private $columns = array(),
            $sortable = array(),
            $values = array(),
            $actions = array();

    public function __construct($args = array())
    {
        $args = wp_parse_args( $args, array(
            'singular' => '',
            'plural'   => '',
            'ajax'     => false,
        ) );

        parent::__construct( $args );
    }

    /**
     * Set: Head Row
     */
    public function set_columns( $columns = array() )
    {
        $this->columns = wp_parse_args( $columns, array(
            'cb'     => '<input type="checkbox" />', //Render a checkbox instead of text
            'post_title'   => __( 'Title', DOMAIN ),
        ) );

        return $this->columns;
    }

    /**
     * required WP_List_Table method
     */
    public function get_columns() {

        return $this->columns;
    }

    public function set_sortable_columns( $sortable )
    {
        $this->sortable = wp_parse_args( $sortable, array(
            'title'  => array( 'title', false ),
        ) );

        return $this->sortable;
    }

    protected function get_sortable_columns() {

        return $this->sortable;
    }

    /**
     * Set: Body Row
     */
    public function set_value( $values )
    {
        $this->values[] = wp_parse_args( $values, array(
            'ID'    => '',
            'title' => '',
        ) );
    }

    /********************************* Columns ********************************/
    /**
     * Render: Callbacks checkbox
     */
    function column_cb( $item )
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],  // Let's simply repurpose the table's singular label ("modal").
            $item['ID']                // The value of the checkbox should be the record's ID.
        );
    }

    /**
     * Render: Row Title
     */
    function column_post_title($item)
    {
        /**
         * @todo repair it
         */
        $actions = array(
            'edit' => sprintf('<a href="/wp-admin/post.php?post=%d&action=edit">%s</a>',
                $item['ID'],
                esc_attr( __('Edit') )
            ),
            'delete' => sprintf('<a href="%s">%s</a>',
                get_delete_post_link( $item['ID'], '', true ),
                __('Delete')
            ),
        );

        return $item['post_title'] . $this->row_actions($actions);
    }

    protected function column_shortcode( $item )
    {
        $onclick = 'this.select();return 0;';
        $styles = '
            font-size: 12px;
            max-width: 100%;';

        return sprintf('<input type="text" value=\'[%1$s id="%2$d" title="%3$s"]%4$s[/%1$s]\' onclick="%5$s" style="%6$s">',
            Utils::get_shortcode_name(),
            $item['ID'],
            $item['post_title'],
            __( 'Open', DOMAIN ),
            $onclick,
            $styles );
    }

    protected function column_post_author( $item ) 
    {
        $_user = get_user_by( 'id', $item['post_author'] );
        if( empty($_user->ID) ) return '';

        return sprintf('<a href="%s">%s</a>', get_edit_user_link( $_user->ID ), $_user->data->user_nicename );
    }

    protected function column_post_date( $item )
    {
        if( empty($item['post_date']) ) return '';

        $format = get_option('time_format') . ' ' . get_option('date_format');

        return date( $format, strtotime($item['post_date']) );
    }

    /**
     * Render: Columns Data
     */
    function column_default($item, $column_name)
    {
        if( '_count' === $column_name && empty($item[ $column_name ]) ) {
            return 0;
        }

        if( isset($item[ $column_name ]) )
            return $item[ $column_name ];

        return false;
    }

    public function single_row( $item )
    {
        printf('<tr class="%s">',
            !empty( $item['classrow'] ) ? $item['classrow'] : '');
        $this->single_row_columns( $item );
        echo '</tr>';
    }

    /****************************** Bulk Actions ******************************/
    public function set_bulk_actions( $actions = array() )
    {
        $this->actions = wp_parse_args( $actions, array(
            'delete' => __( 'Delete' ),
        ) );

        return $actions;
    }

    protected function get_bulk_actions() {

        return $this->actions;
    }

    protected function process_bulk_action() {
        if ( 'delete' === $this->current_action() ) {
            /**
             * @todo set hooks
             */
            wp_die( 'Items deleted (or they would be if we had items to delete)!' );
        }
    }

    /******************************** Sortable ********************************/
    protected function usort_reorder( $a, $b )
    {
        // If no sort, default to title.
        $orderby = ! empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : 'post_title'; // WPCS: Input var ok.

        // If no order, default to asc.
        $order = ! empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : 'asc'; // WPCS: Input var ok.

        // Determine sort order.
        $result = strcmp( $a[ $orderby ], $b[ $orderby ] );

        return ( 'asc' === $order ) ? $result : - $result;
    }

    /**
     * Prepares the list of items for displaying.
     *
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     */
    public function prepare_items()
    {
        if( !count( $this->columns ) )
            $this->set_columns();

        $per_page = 20;

        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array( $columns, $hidden, $sortable );

        $this->process_bulk_action();

        $data = $this->values;

        usort( $data, array( $this, 'usort_reorder' ) );

        $current_page = $this->get_pagenum();

        $total_items = count( $data );

        $data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

        $this->items = $data;

        if( $total_items > $per_page ) {
            $this->set_pagination_args( array(
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil( $total_items / $per_page ),
            ) );
        }
    }
}
