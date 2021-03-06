<?php

namespace NikolayS93\MediaBlocks;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

add_filter( UTILS::PREF .'carousel__child-list', __NAMESPACE__ . '\add_slick_slider', 10, 1 );
add_filter( UTILS::PREF .'slider__child-list', __NAMESPACE__ . '\add_slick_slider', 10, 1 );
add_filter( UTILS::PREF .'double__child-list', __NAMESPACE__ . '\add_slick_slider', 10, 1 );
function add_slick_slider( $list ) {
    $list[ 'slick' ] = (object) array(
        'label'   => __('Slick Slider', DOMAIN),
        'settings' => Utils::get_settings( 'lib/slick.php' ),
        'library' => (object) array(
            'js' => 'slick.js',
            'style' => 'slick.css',
            'theme' => 'slick-theme.css',
            'ver' => '1.6.0',
        ),
    );

    return $list;
}

add_filter( UTILS::PREF .'carousel__child-list', __NAMESPACE__ . '\add_owl_carousel', 10, 1 );
add_filter( UTILS::PREF .'slider__child-list', __NAMESPACE__ . '\add_owl_carousel', 10, 1 );
add_filter( UTILS::PREF .'double__child-list', __NAMESPACE__ . '\add_owl_carousel', 10, 1 );
function add_owl_carousel( $list ) {
    $affix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

    $list[ 'owl-carousel' ] = (object) array(
        'label'   => __('Owl Carousel', DOMAIN),
        'settings' => Utils::get_settings( 'lib/owlCarousel.php' ),
        'library' => (object) array(
            'js' => 'owl.carousel'.$affix.'.js',
            'style' => 'owl.carousel'.$affix.'.css',
            'theme' => 'owl.theme.css',
            'ver' => '1.3.3',
        ),
    );

    return $list;
}

add_filter( UTILS::PREF .'3d__child-list', __NAMESPACE__ . '\add_cloud9carousel', 10, 1 );
function add_cloud9carousel( $list ) {
    $affix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

    $list[ 'cloud9carousel' ] = (object) array(
        'label' => __('Cloud 9 Carousel', DOMAIN),
        'settings' => Utils::get_settings( 'lib/Cloud9carousel.php' ),
        'library' => (object) array(
            'js' => 'jquery.cloud9carousel'.$affix.'.js',
            'ver' => '2.1.0',
        ),
    );

    return $list;
}

add_filter( UTILS::PREF .'3d__child-list', __NAMESPACE__ . '\add_waterwheelCarousel', 10, 1 );
function add_waterwheelCarousel( $list ) {
    $affix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

    $list[ 'waterwheelCarousel' ] = (object) array(
        'label' => __('Waterwheel Carousel', DOMAIN),
        'settings' => Utils::get_settings( 'lib/waterwheelCarousel.php' ),
        'library' => (object) array(
            'js' => 'jquery.waterwheelCarousel'.$affix.'.js',
            'ver' => '2.3.0',
        ),
    );

    return $list;
}

add_filter( UTILS::PREF .'gallery__child-list', __NAMESPACE__ . '\add_masonry_gallery', 10, 1 );
function add_masonry_gallery( $list ) {
    $list[ 'masonry' ] = (object) array(
        'label' => __('Masonry Gallery', DOMAIN),
        'settings' => Utils::get_settings( 'lib/masonry.php' ),
        'library' => (object) array(
            // 'js' => 'jquery.waterwheelCarousel'.$affix.'.js',
            // 'ver' => '2.3.0',
        ),
    );

    return $list;
}


add_filter( UTILS::PREF .'library_list', __NAMESPACE__ . '\add_carousel');
function add_carousel( $libraries ) {
    $libraries[ 'carousel' ] = (object) array(
        'label' => __('Carousel', DOMAIN),
        'options' => Utils::get_settings('grid/carousel.php'),
        'child' => apply_filters( UTILS::PREF .'carousel__child-list', array() ),
    );

    return $libraries;
}

add_filter( UTILS::PREF .'library_list', __NAMESPACE__ . '\add_slider');
function add_slider( $libraries ) {
    $libraries[ 'slider' ] = (object) array(
        'label' => __('Slider', DOMAIN),
        'options' => Utils::get_settings('grid/slider.php'),
        'child' => apply_filters( UTILS::PREF .'slider__child-list', array() ),
    );

    return $libraries;
}

add_filter( UTILS::PREF .'library_list', __NAMESPACE__ . '\add_double_slider');
function add_double_slider( $libraries ) {
    $libraries[ 'double-slider' ] = (object) array(
        'label' => __('Double Slider', DOMAIN),
        'options' => Utils::get_settings('grid/double.php'),
        'child' => apply_filters( UTILS::PREF .'double__child-list', array() ),
    );

    return $libraries;
}

add_filter( UTILS::PREF .'library_list', __NAMESPACE__ . '\add_carousel_3d');
function add_carousel_3d( $libraries ) {
    $libraries[ 'carousel-3d' ] = (object) array(
        'label' => __('Carousel 3D', DOMAIN),
        'options' => Utils::get_settings('grid/carousel-3d.php'),
        'child' => apply_filters( UTILS::PREF .'3d__child-list', array() ),
    );

    return $libraries;
}

add_filter( UTILS::PREF .'library_list', __NAMESPACE__ . '\add_gallery');
function add_gallery( $libraries ) {
    $libraries[ 'gallery' ] = (object) array(
        'label' => __('Gallery', DOMAIN),
        'options' => Utils::get_settings('grid/gallery.php'),
        'child' => apply_filters( UTILS::PREF .'gallery__child-list', array() ),
    );

    return $libraries;
}
