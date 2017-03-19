<?php
defined( 'ABSPATH' ) or die();

$Responsive = array(
  array(
    'id' => 'responsive',
    'label' => 'Responsive',
    'desc' => 'Use Owl Carousel on not desktop-only website',
    'type' => 'checkbox',
    'default' => get_theme_mod( 'responsive' ) ? 'on' : '',
    'data-show' => 'itemsDesktop, itemsDesktopSmall, itemsTablet, itemsMobile',
    ),
  array(
    'id' => 'items',
    'label' => 'Items',
    'desc' => 'The number of items you want to see on the screen.',
    'type' => 'number',
    'default' => 5,
    ),
  array(
    'id' => 'itemsDesktop',
    'label' => 'Items Desktop',
    'desc' => 'The number of items on desktop resolutions (1199px)',
    'default' => 4,
    'type' => 'number',
    ),
  array(
    'id' => 'itemsDesktopSmall',
    'label' => 'Items Desktop Small',
    'desc' => 'The number of items on small desktop resolutions (979px)',
    'default' => 3,
    'type' => 'number'
    ),
  array(
    'id' => 'itemsTablet',
    'label' => 'Items Tablet',
    'desc' => 'The number of items on tablet resolutions (768px)',
    'default' => 2,
    'type' => 'number'
    ),
  array(
    'id' => 'itemsMobile',
    'label' => 'Items Mobile',
    'desc' => 'The number of items on mobile resolutions (479px)',
    'default' => 1,
    'type' => 'number',
    ),
  );
$autoPlay   = array(
  array(
    'id' => 'autoPlay' ,
    'label' => 'Auto Play',
    'desc' => 'Change to any integrer for example autoPlay : 5000 to play every 5 seconds, or 0 to disable autoPlay.',
    'default' => 4000,
    'type' => 'number'
    ),
  array(
    'id' => 'stopOnHover' ,
    'label' => 'Stop On Hover',
    'desc' => 'Stop autoplay on mouse hover',
    'type' => 'checkbox',
    'default' => 'on'
    )
  );
$Pagination = array(
  array(
    'id' => 'pagination',
    'label' => 'Pagination',
    'desc' => 'Display pagination.',
    'type' => 'checkbox',
    'data-show' => 'paginationNumbers, paginationSpeed'
    ),
  array(
    'id' => 'paginationNumbers',
    'label' => 'Pagination Numbers',
    'desc' => 'Show numbers inside pagination buttons',
    'type' => 'checkbox',
    ),
  array(
    'id' => 'paginationSpeed',
    'label' => 'Pagination Speed',
    'desc' => 'Pagination speed in milliseconds.',
    'default' => 800,
    'type' => 'number',
    )
  );
$Navigation = array(
  array(
    'id' => 'navigation',
    'label' => 'Navigation',
    'desc' => 'Display "next" and "prev" buttons.',
    'type' => 'checkbox',
    'data-show' => 'navigationTextNext, navigationTextPrev'
    ),
  array(
    'id' => 'navigationTextPrev',
    'label' => 'Navigation "Prev"',
    'desc' => 'Text on "Prev" button',
    'default' => 'Prev',
    'type' => 'text',
    ),
  array(
    'id' => 'navigationTextNext',
    'label' => 'Navigation "Next"',
    'desc' => 'Text on "Next" button',
    'default' => 'Next',
    'type' => 'text',
    ),
  array(
    'id' => 'rewindNav',
    'label' => 'Rewind',
    'desc' => 'Slide to first item.',
    'type' => 'checkbox',
    'default' => 'on',
    'data-show' => 'rewindSpeed'
    ),
  array(
    'id' => 'rewindSpeed',
    'label' => 'Rewind Speed',
    'desc' => 'Rewind speed in milliseconds.',
    'default' => 1000,
    'type' => 'number'
    )
  );
$advanced   = array(
  array(
    'id' => 'scrollPerPage',
    'label' => 'Scroll per Page',
    'desc' => 'Scroll per page not per item. This affect next/prev buttons and mouse/touch dragging.',
    'type' => 'checkbox',
    ),
  array(
    'id' => 'autoHeight',
    'label' => 'Auto Height',
    'desc' => 'Add height to owl-wrapper-outer so you can use diffrent heights on slides. Use it only for one item per page setting.',
    'type' => 'checkbox',
    ),
  array(
    // exclude from sync
    'id' => 'addClassActive',
    'label' => 'Add Class Active',
    'desc' => 'Add "active" classes on visible items. Works with any numbers of items on screen.',
    'type' => 'checkbox',
    ),
  array(
    'id' => 'mouseDrag',
    'label' => 'Mouse Drag',
    'desc' => 'Turn on mouse events.',
    'type' => 'checkbox',
    'default' => 'on'
    ),
  array(
    'id' => 'touchDrag',
    'label' => 'Touch Drag',
    'desc' => 'Turn on touch events.',
    'type' => 'checkbox',
    'default' => 'on'
    ),
  array(
    'id' => 'dragBeforeAnimFinish',
    'label' => 'Drag Before Animation Finishes',
    'desc' => 'Ignore whether a transition is done (only dragging).',
    'type' => 'checkbox',
    'default' => 'on'
    ),
  );

switch ($main_type) {
    case 'slider':
      $settings = array_merge( $autoPlay, $Pagination, $Navigation, $advanced );
      break;

    case 'sync-slider':
      $settings = array_merge( $Responsive, $autoPlay, $Pagination, $Navigation, $advanced );
      break;
    
    default: // 'carousel'
      $settings = array_merge( $Responsive, $autoPlay, $Pagination, $Navigation, $advanced );
      break;
}
// $id, $label, $type, $value - required

return $settings;

// todo:
// lazy load
    // 'lazyLoad' => array(
    //     'label' => 'Lazy Load',
    //     'desc' => 'Delays loading of images. Images outside of viewport won\'t be loaded before user scrolls to them. Great for mobile devices to speed up page loadings. ',
    //     'default' => false,
    //     'type' => 'checkbox',
    //     'type' => 'bool'
    // ),
    // 'lazyFollow' => array(
    //     'label' => 'Lazy Follow',
    //     'desc' => 'When pagination used, it skips loading the images from pages that got skipped. It only loads the images that get displayed in viewport. If set to false, all images get loaded when pagination used. It is a sub setting of the lazy load function.',
    //     'default' => false,
    //     'type' => 'checkbox',
    //     'type' => 'bool'
    // ),

    // 'responsiveRefreshRate' => array(
    //     'label' => 'Responsive Refresh Rate',
    //     'desc' => 'Check window width changes every X ms for responsive actions',
    //     'default' => 200,
    //     'type' => 'text',
    //     'type' => 'number'
    // ),

    // 'itemsScaleUp' => array(
    //     'label' => 'Item Scale Up',
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