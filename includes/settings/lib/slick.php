<?php
defined( 'ABSPATH' ) or die();

$settings = array(
    array(
        'id'      => 'infinite',
        'label'   => 'Infinite',
        'desc'    => 'Infinite looping',
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'slidesToShow',
        'label'   => 'Slides To Show',
        'desc'    => 'slides to show at a time',
        'default' => '1',
        'type'    => 'number'
        ),
    array(
        'id'      => 'slidesToScroll',
        'label'   => 'Slides To Scroll',
        'desc'    => 'slides to scroll at a time',
        'default' => '1',
        'type'    => 'number'
        ),
    array(
        'id'    => 'autoplay',
        'label' => 'Auto Play',
        'desc'  => 'Enables auto play of slides',
        'type'  => 'checkbox',
        'default' => 'on',
        'data-show' => 'autoplaySpeed'
        ),
    array(
        'id'      => 'autoplaySpeed',
        'label'   => 'Auto Play Speed',
        'desc'    => 'Auto play change interval',
        'default' => '3000',
        'type'    => 'number'
        ),
    array(
        'id'      => 'dots',
        'label'   => 'Dots',
        'desc'    => 'Current slide indicator dots',
        'type'    => 'checkbox',
        'data-show' => 'dotsClass'
        ),
    array(
        'id'      => 'dotsClass',
        'label'   => 'Dots Class',
        'desc'    => 'Class for slide indicator dots container',
        'default' => 'slick-dots',
        'type'    => 'text'
        ),
    array(
        'id'      => 'arrows',
        'label'   => 'Arrows',
        'desc'    => 'Enable Next/Prev arrows',
        'default' => 'on',
        'type'    => 'checkbox',
        'custom_attributes' => array(
            'data-show' => '#prevArrow, #nextArrow',
            ),
        ),
    array(
        'id'      => 'prevArrow',
        'label'   => 'Prev Arrow',
        'desc'    => '(html | jQuery selector) | object (DOM node | jQuery object)   Allows you to select a node or customize the HTML for the "Previous" arrow. (May use %object%)',
        'default' => '<button type="button" class="slick-prev">Previous</button>',
        'type'    => 'text'
        ),
    array(
        'id'      => 'nextArrow',
        'label'   => 'Next Arrow',
        'desc'    => '(html | jQuery selector) | object (DOM node | jQuery object) Allows you to select a node or customize the HTML for the "Next" arrow. (May use %object%)',
        'default' => '<button type="button" class="slick-next">Next</button>',
        'type'    => 'text'
        ),
    array(
        'id'      => 'speed',
        'label'   => 'Speed',
        'desc'    => 'Transition speed',
        'default' => '300',
        'type'    => 'number'
        ),
    array(
        'id'    => 'centerMode',
        'label' => 'Center Mode',
        'desc'  => 'Enables centered view with partial prev/next slides. Use with odd numbered slidesToShow counts.',
        'type'  => 'checkbox',
        'data-show' => 'centerPadding',
        ),
    array(
        'id'      => 'centerPadding',
        'label'   => 'Center Padding',
        'desc'    => 'Side padding when in center mode. (px or %)',
        'default' => '50px',
        'type'    => 'text'
        ),
    array(
        'id'      => 'fade',
        'label'   => 'Fade',
        'desc'    => 'Enables fade',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'variableWidth',
        'label'   => 'Variable Width',
        'desc'    => 'Disables automatic slide width calculation',
        'type'    => 'checkbox'
        ),
    array(
        'id'    => 'adaptiveHeight',
        'label' => 'AdaptiveHeight',
        'desc'  => 'Adapts slider height to the current slide',
        'type'  => 'checkbox'
        ),
    array(
        'id'      => 'cssEase',
        'label'   => 'CSS Ease',
        'desc'    => 'CSS3 easing',
        'default' => 'ease',
        'type'    => 'text'
        ),
    array(
        'id'      => 'accessibility',
        'label'   => 'Accessibility',
        'desc'    => 'Enables tabbing and arrow key navigation',
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'    => 'customPaging',
        'label' => 'Custom Paging',
        'desc'  => '(use %function_name%) Custom paging templates. See source for use example.',
        'type'  => 'text'
        ),
    array(
        'id'      => 'draggable',
        'label'   => 'Draggable',
        'desc'    => 'Enables desktop dragging',
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'easing',
        'label'   => 'Easing',
        'desc'    => 'animate() fallback easing',
        'default' => 'linear',
        'type'    => 'text'
        ),
    array(
        'id'      => 'edgeFriction',
        'label'   => 'Edge Friction',
        'desc'    => 'Resistance when swiping edges of non-infinite carousels',
        'default' => '0.15',
        'type'    => 'number'
        ),
    // array(
    //     'id'      => 'appendArrows',
    //     'label'   => 'appendArrows',
    //     'desc'    => '$(element)  Change where the navigation arrows are attached (Selector, htmlString, Array, Element, jQuery object)',
    //     'default' => 'on',
    //     'type'    => 'text'
    //     ),
    // array(
    //     'id'      => 'appendDots',
    //     'label'   => 'appendDots',
    //     'desc'    => '$(element)  Change where the navigation dots are attached (Selector, htmlString, Array, Element, jQuery object)',
    //     'default' => 'on',
    //     'type'    => 'text'
    //     ),
    array(
        'id'      => 'mobileFirst',
        'label'   => 'Mobile First',
        'desc'    => 'Responsive settings use mobile first calculation',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'initialSlide',
        'label'   => 'Initial Slide',
        'desc'    => 'Slide to start on',
        'default' => '0',
        'type'    => 'number'
        ),
    array(
        'id'      => 'lazyLoad',
        'label'   => 'Lazy Load',
        'desc'    => 'Accepts \'ondemand\' or \'progressive\' for lazy load technique. \'ondemand\' will load the image as soon as you slide to it, \'progressive\' loads one image after the other when the page loads.',
        'default' => 'ondemand',
        'type'    => 'text'
        ),
    array(
        'id'      => 'pauseOnFocus',
        'label'   => 'Pause On Focus',
        'desc'    => 'Pauses autoplay when slider is focussed',
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'pauseOnHover',
        'label'   => 'Pause On Hover',
        'desc'    => 'Pauses autoplay on hover',
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'pauseOnDotsHover',
        'label'   => 'Pause On Dots Hover',
        'desc'    => 'Pauses autoplay when a dot is hovered',
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'respondTo',
        'label'   => 'Respond To',
        'desc'    => 'Width that responsive object responds to. Can be \'window\', \'slider\' or \'min\' (the smaller of the two).
        responsive  array   null    Array of objects containing breakpoints and settings objects (see example). Enables settings at given breakpoint. Set settings to "unslick" instead of an object to disable slick at a given breakpoint.',
        'default' => 'window',
        'type'    => 'text'
        ),
    array(
        'id'      => 'rows',
        'label'   => 'Rows',
        'desc'    => 'Setting this to more than 1 initializes grid mode. Use slidesPerRow to set how many slides should be in each row.',
        'default' => '1',
        'type'    => 'number'
        ),
    array(
        'id'      => 'slide',
        'label'   => 'Slide',
        'desc'    => 'Slide element query',
        'default' => '',
        'type'    => 'text'
        ),
    array(
        'id'      => 'slidesPerRow',
        'label'   => 'Slides Per Row',
        'desc'    => 'With grid mode initialized via the rows option, this sets how many slides are in each grid row.',
        'default' => '1',
        'type'    => 'number'
        ),
    array(
        'id'      => 'swipe',
        'label'   => 'Swipe',
        'desc'    => 'Enables touch swipe',
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'swipeToSlide',
        'label'   => 'Swipe To Slide',
        'desc'    => 'Swipe to slide irrespective of slidesToScroll',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'touchMove',
        'label'   => 'Touch Move',
        'desc'    => 'Enables slide moving with touch',
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'touchThreshold',
        'label'   => 'Touch Threshold',
        'desc'    => 'To advance slides, the user must swipe a length of (1/touchThreshold) * the width of the slider.',
        'default' => '5',
        'type'    => 'number'
        ),
    array(
        'id'      => 'useCSS',
        'label'   => 'Use CSS',
        'desc'    => 'Enable/Disable CSS Transitions',
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'useTransform',
        'label'   => 'Use Transform',
        'desc'    => 'Enable/Disable CSS Transforms',
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'vertical',
        'label'   => 'Vertical',
        'desc'    => 'Vertical slide direction',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'verticalSwiping',
        'label'   => 'Vertical Swiping',
        'desc'    => 'Changes swipe direction to vertical',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'rtl',
        'label'   => 'Right To Left',
        'desc'    => 'Change the slider\'s direction to become right-to-left',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'waitForAnimate',
        'label'   => 'Wait For Animate',
        'desc'    => 'Ignores requests to advance the slide while animating',
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'zIndex',
        'label'   => 'zIndex',
        'desc'    => 'Set the zIndex values for slides, useful for IE9 and lower',
        'default' => '1000',
        'type'    => 'number'
        )
    );

// switch ($main_type) {
//     case 'slider':
//       $settings = array_merge( $autoPlay, $Pagination, $Navigation, $advanced );
//       $settings[] = array(
//         'id' => 'singleItem',
//         'type' => 'hidden',
//         'value' => 'on'
//         );
//       break;

//     case 'sync-slider':
//       $settings = array_merge( $Responsive, $autoPlay, $Pagination, $Navigation, $advanced );
//       break;
    
//     default: // 'carousel'
//       $settings = array_merge( $Responsive, $autoPlay, $Pagination, $Navigation, $advanced );
//       break;
// }
// $id, $label, $type, $value - required

return $settings;