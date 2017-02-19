<?php
defined( 'ABSPATH' ) or die();
 
// $id, $name, $type, $value - required
function get_dt_multimedia_settings(){
$settings = array(
// Responsive
    'responsive' => array(
        'name' => 'Non responsive',
        'desc' => 'Use Owl Carousel on desktop-only website',
        'type' => 'checkbox',
        'hide' => 'itemsDesktop, itemsDesktopSmall, itemsTablet, itemsMobile',
    ),
    'items' => array(
        'name' => 'Items',
        'desc' => 'The number of items you want to see on the screen.',
        'type' => 'number',
        'placeholder' => 5,
    ),
    'itemsDesktop' => array(
        'name' => 'Items Desktop',
        'desc' => 'The number of items on desktop resolutions (1199px)',
        'placeholder' => 4,
        'type' => 'number',
    ),
    'itemsDesktopSmall' => array(
        'name' => 'Items Desktop Small',
        'desc' => 'The number of items on small desktop resolutions (979px)',
        'placeholder' => 3,
        'type' => 'number'
    ),
    'itemsTablet' => array(
        'name' => 'Items Tablet',
        'desc' => 'The number of items on tablet resolutions (768px)',
        'placeholder' => 2,
        'type' => 'number'
    ),
    'itemsMobile' => array(
        'name' => 'Items Mobile',
        'desc' => 'The number of items on mobile resolutions (479px)',
        'placeholder' => 1,
        'type' => 'number',
    ),

// autoPlay
    'autoPlay' => array(
        'name' => 'Auto Play',
        'desc' => 'Change to any integrer for example autoPlay : 5000 to play every 5 seconds, or 0 to disable autoPlay.',
        'placeholder' => 4000,
        'type' => 'number'
    ),
    'stopOnHover' => array(
        'name' => 'Stop On Hover',
        'desc' => 'Stop autoplay on mouse hover',
        'type' => 'checkbox',
        'default' => 'on'
    ),
    'slideSpeed' => array(
        'name' => 'Slide Speed',
        'desc' => 'Slide speed in milliseconds.',
        'placeholder' => 200,
        'type' => 'number'
    ),
    
// Pagination
    'pagination' => array(
        'name' => 'Pagination',
        'desc' => 'Hide pagination.',
        'type' => 'checkbox',
        'default' => 'on',
        'hide' => 'paginationNumbers, paginationSpeed'
    ),
    'paginationNumbers' => array(
        'name' => 'Pagination Numbers',
        'desc' => 'Show numbers inside pagination buttons',
        'type' => 'checkbox',
    ),
    'paginationSpeed' => array(
        'name' => 'Pagination Speed',
        'desc' => 'Pagination speed in milliseconds.',
        'placeholder' => 800,
        'type' => 'number',
    ),

// Navigation
    'navigation' => array(
        'name' => 'Navigation',
        'desc' => 'Display "next" and "prev" buttons.',
        'type' => 'checkbox',
        'show' => 'navigationTextNext, navigationTextPrev'
    ),
    'navigationTextPrev' => array(
        'name' => 'Navigation "Prev"',
        'desc' => 'Text on "Prev" button',
        'placeholder' => 'Prev',
        'type' => 'text',
    ),
    'navigationTextNext' => array(
        'name' => 'Navigation "Next"',
        'desc' => 'Text on "Next" button',
        'placeholder' => 'Next',
        'type' => 'text',
    ),
    'rewindNav' => array(
        'name' => 'Rewind',
        'desc' => 'Slide to first item.',
        'type' => 'checkbox',
        'default' => 'on',
        'show' => 'rewindSpeed'
    ),
    'rewindSpeed' => array(
        'name' => 'Rewind Speed',
        'desc' => 'Rewind speed in milliseconds.',
        'placeholder' => 1000,
        'type' => 'number'
    ),
    'scrollPerPage' => array(
        'name' => 'Scroll per Page',
        'desc' => 'Scroll per page not per item. This affect next/prev buttons and mouse/touch dragging.',
        'type' => 'checkbox',
    ),

    'autoHeight' => array(
        'name' => 'Auto Height',
        'desc' => 'Add height to owl-wrapper-outer so you can use diffrent heights on slides. Use it only for one item per page setting.',
        'type' => 'checkbox',
    ),
    'addClassActive' => array(
        'name' => 'Add Class Active',
        'desc' => 'Add "active" classes on visible items. Works with any numbers of items on screen.',
        'type' => 'checkbox',
    ),

    'mouseDrag' => array(
        'name' => 'Mouse Drag',
        'desc' => 'Turn on mouse events.',
        'type' => 'checkbox',
        'default' => 'on'
    ),
    'touchDrag' => array(
        'name' => 'Touch Drag',
        'desc' => 'Turn on touch events.',
        'type' => 'checkbox',
        'default' => 'on'
    ),
    'dragBeforeAnimFinish' => array(
        'name' => 'Drag Before Animation Finishes',
        'desc' => 'Ignore whether a transition is done (only dragging).',
        'type' => 'checkbox',
        'default' => 'on'
    ),
);

if( get_theme_mod( 'site-format' ) )
    $settings['responsive']['default'] = 'on';

return $settings;
}

// todo:
// lazy load
    // 'lazyLoad' => array(
    //     'name' => 'Lazy Load',
    //     'desc' => 'Delays loading of images. Images outside of viewport won\'t be loaded before user scrolls to them. Great for mobile devices to speed up page loadings. ',
    //     'default' => false,
    //     'type' => 'checkbox',
    //     'type' => 'bool'
    // ),
    // 'lazyFollow' => array(
    //     'name' => 'Lazy Follow',
    //     'desc' => 'When pagination used, it skips loading the images from pages that got skipped. It only loads the images that get displayed in viewport. If set to false, all images get loaded when pagination used. It is a sub setting of the lazy load function.',
    //     'default' => false,
    //     'type' => 'checkbox',
    //     'type' => 'bool'
    // ),

    // 'responsiveRefreshRate' => array(
    //     'name' => 'Responsive Refresh Rate',
    //     'desc' => 'Check window width changes every X ms for responsive actions',
    //     'placeholder' => 200,
    //     'type' => 'text',
    //     'type' => 'number'
    // ),

    // 'itemsScaleUp' => array(
    //     'name' => 'Item Scale Up',
    //     'desc' => 'Option to not stretch items when it is less than the supplied items.',
    //     'default' => false,
    //     'type' => 'checkbox',
    //     'type' => 'bool'
    // ),

    // CSS Styles
    // baseClass : "owl-carousel",
    // theme : "owl-theme",
 
    // //Lazy load
    // lazyLoad : false,
    // lazyFollow : true,
    // lazyEffect : "fade",