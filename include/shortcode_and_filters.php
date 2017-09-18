<?php

add_shortcode( 'mblock', 'mblock_sc' );
function mblock_sc( $atts ){
    if( ! isset( $atts['id'] ) || ! $id = absint( $atts['id'] ) ) {
        return false;
    }

    $mblock = new MediaBlock( $id, apply_filters( 'custom_mblock_attributs', $id, $atts ) );
    return $mblock->render();
}

add_filter( MB_PREF . 'default_columns', 'slider_default_columns', 10, 2 );
function slider_default_columns( $columns, $main_type ){
    if($main_type == 'slider') {
        return 1;
    }

    return $columns;
}

add_filter( 'type_to_lib', 'type_to_lib', 10, 1 );
function type_to_lib( $type ){
    switch ( $type ) {
        case 'owl-carousel':
            $type = 'owlCarousel';
            break;
        case 'cloud9carousel':
            $type = 'Cloud9Carousel';
            break;
    }

    return $type;
}
