<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function get_owl_included_file_settings(){
$settings = array(
// Responsive
    'responsive' => array(
        'name' => 'Non responsive',
        'desc' => 'Use Owl Carousel on desktop-only website',
        'default' => get_theme_mod( 'site-format' ), // get responsive theme
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
    'items' => array(
        'name' => 'Items',
        'desc' => 'The number of items you want to see on the screen.',
        'cmb_type' => 'text',
        'type' => 'number',
        'placeholder' => 3,
    ),
    'itemsDesktop' => array(
        'name' => 'Items Desktop',
        'desc' => 'The number of items on desktop resolutions (1199px)',
        'placeholder' => 4,
        'cmb_type' => 'text',
        'type' => 'number',
        'before_row' => '<div id="responsive-group">'
    ),
    'itemsDesktopSmall' => array(
        'name' => 'Items Desktop Small',
        'desc' => 'The number of items on small desktop resolutions (979px)',
        'placeholder' => 3,
        'cmb_type' => 'text',
        'type' => 'number'
    ),
    'itemsTablet' => array(
        'name' => 'Items Tablet',
        'desc' => 'The number of items on tablet resolutions (768px)',
        'placeholder' => 2,
        'cmb_type' => 'text',
        'type' => 'number'
    ),
    'itemsMobile' => array(
        'name' => 'Items Mobile',
        'desc' => 'The number of items on mobile resolutions (479px)',
        'placeholder' => 1,
        'cmb_type' => 'text',
        'type' => 'number',
        'after_row' => '</div>'
    ),

// autoPlay
    'autoPlay' => array(
        'name' => 'Auto Play',
        'desc' => 'Change to any integrer for example autoPlay : 5000 to play every 5 seconds, or 0 to disable autoPlay.',
        'placeholder' => 0,
        'cmb_type' => 'text',
        'type' => 'number'
    ),
    'stopOnHover' => array(
        'name' => 'Stop On Hover',
        'desc' => 'Stop autoplay on mouse hover',
        'default' => false,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
    'slideSpeed' => array(
        'name' => 'Slide Speed',
        'desc' => 'Slide speed in milliseconds.',
        'placeholder' => 200,
        'cmb_type' => 'text',
        'type' => 'number'
    ),
    
// Pagination
    'pagination' => array(
        'name' => 'Pagination',
        'desc' => 'Hide pagination.',
        'default' => false,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
    'paginationNumbers' => array(
        'name' => 'Pagination Numbers',
        'desc' => 'Show numbers inside pagination buttons',
        'default' => false,
        'cmb_type' => 'checkbox',
        'type' => 'bool',
        'before_row' => '<div id="pagination-group">'
    ),
    'paginationSpeed' => array(
        'name' => 'Pagination Speed',
        'desc' => 'Pagination speed in milliseconds.',
        'placeholder' => 800,
        'cmb_type' => 'text',
        'type' => 'number',
        'after_row' => '</div>'
    ),

// Navigation
    'navigation' => array(
        'name' => 'Navigation',
        'desc' => 'Display "next" and "prev" buttons.',
        'default' => false,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
    'navigationTextNext' => array(
        'name' => 'Navigation "Next"',
        'desc' => 'Text on "Next" button',
        'placeholder' => 'Next ',
        'cmb_type' => 'text',
        'type' => 'string',
        'before_row' => '<div id="navigation-group">'
    ),
    'navigationTextPrev' => array(
        'name' => 'Navigation "Prev"',
        'desc' => 'Text on "Prev" button',
        'placeholder' => 'Prev ',
        'cmb_type' => 'text',
        'type' => 'string',
        'after_row' => '</div>'
    ),
    'rewindNav' => array(
        'name' => 'Not rewind',
        'desc' => 'Not! slide to first item.',
        'default' => false,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
    'rewindSpeed' => array(
        'name' => 'Rewind Speed',
        'desc' => 'Rewind speed in milliseconds.',
        'placeholder' => 1000,
        'cmb_type' => 'text',
        'type' => 'number'
    ),
    'scrollPerPage' => array(
        'name' => 'Scroll per Page',
        'desc' => 'Scroll per page not per item. This affect next/prev buttons and mouse/touch dragging.',
        'default' => false,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),

    'autoHeight' => array(
        'name' => 'Auto Height',
        'desc' => 'Add height to owl-wrapper-outer so you can use diffrent heights on slides. Use it only for one item per page setting.',
        'default' => false,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
    'addClassActive' => array(
        'name' => 'Add Class Active',
        'desc' => 'Add "active" classes on visible items. Works with any numbers of items on screen.',
        'default' => false,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),

    'mouseDrag' => array(
        'name' => 'Mouse Drag',
        'desc' => 'Turn off mouse events.',
        'default' => false,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
    'touchDrag' => array(
        'name' => 'Touch Drag',
        'desc' => 'Turn off touch events.',
        'default' => false,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
    'dragBeforeAnimFinish' => array(
        'name' => 'Drag Before Animation Finishes',
        'desc' => 'Not! Ignore whether a transition is done (only dragging).',
        'default' => false,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
);

return $settings;
}

// todo:
// lazy load
    // 'lazyLoad' => array(
    //     'name' => 'Lazy Load',
    //     'desc' => 'Delays loading of images. Images outside of viewport won\'t be loaded before user scrolls to them. Great for mobile devices to speed up page loadings. ',
    //     'default' => false,
    //     'cmb_type' => 'checkbox',
    //     'type' => 'bool'
    // ),
    // 'lazyFollow' => array(
    //     'name' => 'Lazy Follow',
    //     'desc' => 'When pagination used, it skips loading the images from pages that got skipped. It only loads the images that get displayed in viewport. If set to false, all images get loaded when pagination used. It is a sub setting of the lazy load function.',
    //     'default' => false,
    //     'cmb_type' => 'checkbox',
    //     'type' => 'bool'
    // ),

    // 'responsiveRefreshRate' => array(
    //     'name' => 'Responsive Refresh Rate',
    //     'desc' => 'Check window width changes every X ms for responsive actions',
    //     'placeholder' => 200,
    //     'cmb_type' => 'text',
    //     'type' => 'number'
    // ),

    // 'itemsScaleUp' => array(
    //     'name' => 'Item Scale Up',
    //     'desc' => 'Option to not stretch items when it is less than the supplied items.',
    //     'default' => false,
    //     'cmb_type' => 'checkbox',
    //     'type' => 'bool'
    // ),