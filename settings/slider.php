<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function get_owl_included_file_settings(){
$settings = array(
    'singleItem' => array(
        'default' => 'on',
        'cmb_type' => 'hidden',
    ),
    'slideSpeed' => array(
        'name' => 'Slide Speed',
        'desc' => 'Slide speed in milliseconds.',
        'placeholder' => '200',
        'cmb_type' => 'text',
        'type' => 'number'
    ),
    'paginationSpeed' => array(
        'name' => 'Pagination Speed',
        'desc' => 'Pagination speed in milliseconds.',
        'placeholder' => 800,
        'cmb_type' => 'text',
        'type' => 'number'
    ),
    'rewindSpeed' => array(
        'name' => 'Rewind Speed',
        'desc' => 'Rewind speed in milliseconds.',
        'placeholder' => 1000,
        'cmb_type' => 'text',
        'type' => 'number'
    ),
    'rewindNav' => array(
        'name' => 'Rewind Nav',
        'desc' => 'Slide to first item.',
        'default' => true,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
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
        'default' => true,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
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
        'placeholder' => 'Next',
        'cmb_type' => 'text',
        'type' => 'string'
    ),
    'navigationTextPrev' => array(
        'name' => 'Navigation "Prev"',
        'desc' => 'Text on "Prev" button',
        'placeholder' => 'Prev',
        'cmb_type' => 'text',
        'type' => 'string'
    ),
    'pagination' => array(
        'name' => 'Pagination',
        'desc' => 'Show pagination.',
        'default' => true,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
    'paginationNumbers' => array(
        'name' => 'Pagination Numbers',
        'desc' => 'Show numbers inside pagination buttons',
        'default' => false,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
    'itemsScaleUp' => array(
        'name' => 'Item Scale Up',
        'desc' => 'Option to not stretch items when it is less than the supplied items.',
        'default' => false,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
    'responsive' => array(
        'name' => 'Responsive',
        'desc' => 'You can use Owl Carousel on desktop-only websites too! Just change that to "false" to disable resposive capabilities',
        'default' => true,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
    'responsiveRefreshRate' => array(
        'name' => 'Responsive Refresh Rate',
        'desc' => 'Check window width changes every X ms for responsive actions',
        'placeholder' => 200,
        'cmb_type' => 'text',
        'type' => 'number'
    ),
    'lazyLoad' => array(
        'name' => 'Lazy Load',
        'desc' => 'Delays loading of images. Images outside of viewport won\'t be loaded before user scrolls to them. Great for mobile devices to speed up page loadings. ',
        'default' => false,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
    'lazyFollow' => array(
        'name' => 'Lazy Follow',
        'desc' => 'When pagination used, it skips loading the images from pages that got skipped. It only loads the images that get displayed in viewport. If set to false, all images get loaded when pagination used. It is a sub setting of the lazy load function.',
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
    'mouseDrag' => array(
        'name' => 'Mouse Drag',
        'desc' => 'Turn off/on mouse events.',
        'default' => true,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
    'touchDrag' => array(
        'name' => 'Touch Drag',
        'desc' => 'Turn off/on touch events.',
        'default' => true,
        'cmb_type' => 'checkbox',
        'type' => 'bool'
    ),
    'dragBeforeAnimFinish' => array(
        'name' => 'Drag Before Animation Finishes',
        'desc' => 'Ignore whether a transition is done or not (only dragging).',
        'default' => true,
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
);

return $settings;
}

// function add_js(){

// }
// add_action('admin_init', 'add_js');