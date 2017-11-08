<?php

namespace CDevelopers\media;

$settings = array(
    array(
        'id'      => 'infinite',
        'label'   => __('Infinite'),
        'desc'    => __('Infinite looping', DOMAIN),
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'slidesToShow',
        'label'   => __('Slides To Show', DOMAIN),
        'desc'    => __('slides to show at a time', DOMAIN),
        'default' => '1',
        'type'    => 'number'
        ),
    array(
        'id'      => 'slidesToScroll',
        'label'   => __('Slides To Scroll', DOMAIN),
        'desc'    => __('slides to scroll at a time', DOMAIN),
        'default' => '1',
        'type'    => 'number'
        ),
    array(
        'id'    => 'autoplay',
        'label' => 'Auto Play',
        'desc'  => __('Enables auto play of slides', DOMAIN),
        'type'  => 'checkbox',
        'default' => 'on',
        'data-show' => 'autoplaySpeed'
        ),
    array(
        'id'      => 'autoplaySpeed',
        'label'   => __('Auto Play Speed', DOMAIN),
        'desc'    => __('Auto play change interval', DOMAIN),
        'default' => '3000',
        'type'    => 'number'
        ),
    array(
        'id'      => 'dots',
        'label'   => __('Dots', DOMAIN),
        'desc'    => __('Current slide indicator dots', DOMAIN),
        'type'    => 'checkbox',
        'data-show' => 'dotsClass'
        ),
    array(
        'id'      => 'dotsClass',
        'label'   => __('Dots Class', DOMAIN),
        'desc'    => __('Class for slide indicator dots container', DOMAIN),
        'default' => 'slick-dots',
        'type'    => 'text'
        ),
    array(
        'id'      => 'arrows',
        'label'   => __('Arrows', DOMAIN),
        'desc'    => __('Enable Next/Prev arrows', DOMAIN),
        'default' => 'on',
        'type'    => 'checkbox',
        'custom_attributes' => array(
            'data-show' => '#prevArrow, #nextArrow',
            ),
        ),
    array(
        'id'      => 'prevArrow',
        'label'   => __('Prev Arrow', DOMAIN),
        'desc'    => __('(html | jQuery selector) | object (DOM node | jQuery object)   Allows you to select a node or customize the HTML for the "Previous" arrow. (May use %object%)', DOMAIN),
        'default' => '<button type="button" class="slick-prev">Previous</button>',
        'type'    => 'text'
        ),
    array(
        'id'      => 'nextArrow',
        'label'   => __('Next Arrow', DOMAIN),
        'desc'    => __('(html | jQuery selector) | object (DOM node | jQuery object) Allows you to select a node or customize the HTML for the "Next" arrow. (May use %object%)', DOMAIN),
        'default' => '<button type="button" class="slick-next">Next</button>',
        'type'    => 'text'
        ),
    array(
        'id'      => 'speed',
        'label'   => __('Speed', DOMAIN),
        'desc'    => __('Transition speed', DOMAIN),
        'default' => '300',
        'type'    => 'number'
        ),
    array(
        'id'    => 'centerMode',
        'label' => 'Center Mode',
        'desc'  => __('Enables centered view with partial prev/next slides. Use with odd numbered slidesToShow counts.', DOMAIN),
        'type'  => 'checkbox',
        'data-show' => 'centerPadding',
        ),
    array(
        'id'      => 'centerPadding',
        'label'   => __('Center Padding', DOMAIN),
        'desc'    => __('Side padding when in center mode. (px or %)', DOMAIN),
        'default' => '50px',
        'type'    => 'text'
        ),
    array(
        'id'      => 'fade',
        'label'   => __('Fade', DOMAIN),
        'desc'    => __('Enables fade', DOMAIN),
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'variableWidth',
        'label'   => __('Variable Width', DOMAIN),
        'desc'    => __('Disables automatic slide width calculation', DOMAIN),
        'type'    => 'checkbox'
        ),
    array(
        'id'    => 'adaptiveHeight',
        'label' => 'AdaptiveHeight',
        'desc'  => __('Adapts slider height to the current slide', DOMAIN),
        'type'  => 'checkbox',
        ),
    array(
        'id'      => 'cssEase',
        'label'   => __('CSS Ease', DOMAIN),
        'desc'    => __('CSS3 easing', DOMAIN),
        'default' => 'ease',
        'type'    => 'text'
        ),
    array(
        'id'      => 'accessibility',
        'label'   => __('Accessibility', DOMAIN),
        'desc'    => __('Enables tabbing and arrow key navigation', DOMAIN),
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'    => 'customPaging',
        'label' => 'Custom Paging',
        'desc'  => __('(use %function_name%) Custom paging templates. See source for use example.', DOMAIN),
        'type'  => 'text'
        ),
    array(
        'id'      => 'draggable',
        'label'   => __('Draggable', DOMAIN),
        'desc'    => __('Enables desktop dragging', DOMAIN),
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'easing',
        'label'   => __('Easing', DOMAIN),
        'desc'    => __('animate() fallback easing', DOMAIN),
        'default' => 'linear',
        'type'    => 'text'
        ),
    array(
        'id'      => 'edgeFriction',
        'label'   => __('Edge Friction', DOMAIN),
        'desc'    => __('Resistance when swiping edges of non-infinite carousels', DOMAIN),
        'default' => '0.15',
        'type'    => 'number'
        ),
    // array(
    //     'id'      => 'appendArrows',
    //     'label'   => __('appendArrows', DOMAIN),
    //     'desc'    => __('$(element)  Change where the navigation arrows are attached (Selector, htmlString, Array, Element, jQuery object)', DOMAIN),
    //     'default' => 'on',
    //     'type'    => 'text'
    //     ),
    // array(
    //     'id'      => 'appendDots',
    //     'label'   => __('appendDots', DOMAIN),
    //     'desc'    => __('$(element)  Change where the navigation dots are attached (Selector, htmlString, Array, Element, jQuery object)', DOMAIN),
    //     'default' => 'on',
    //     'type'    => 'text'
    //     ),
    array(
        'id'      => 'mobileFirst',
        'label'   => __('Mobile First', DOMAIN),
        'desc'    => __('Responsive settings use mobile first calculation', DOMAIN),
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'initialSlide',
        'label'   => __('Initial Slide', DOMAIN),
        'desc'    => __('Slide to start on', DOMAIN),
        'default' => '0',
        'type'    => 'number'
        ),
    array(
        'id'      => 'lazyLoad',
        'label'   => __('Lazy Load', DOMAIN),
        'desc'    => __('Accepts \'ondemand\' or \'progressive\' for lazy load technique. \'ondemand\' will load the image as soon as you slide to it, \'progressive\' loads one image after the other when the page loads.', DOMAIN),
        'default' => 'ondemand',
        'type'    => 'text'
        ),
    array(
        'id'      => 'pauseOnFocus',
        'label'   => __('Pause On Focus', DOMAIN),
        'desc'    => __('Pauses autoplay when slider is focussed', DOMAIN),
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'pauseOnHover',
        'label'   => __('Pause On Hover', DOMAIN),
        'desc'    => __('Pauses autoplay on hover', DOMAIN),
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'pauseOnDotsHover',
        'label'   => __('Pause On Dots Hover', DOMAIN),
        'desc'    => __('Pauses autoplay when a dot is hovered', DOMAIN),
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'respondTo',
        'label'   => __('Respond To', DOMAIN),
        'desc'    => __('Width that responsive object responds to. Can be \'window\', \'slider\' or \'min\', (the smaller of the two).
        responsive  array   null    Array of objects containing breakpoints and settings objects (see example). Enables settings at given breakpoint. Set settings to "unslick" instead of an object to disable slick at a given breakpoint.', DOMAIN),
        'default' => 'window',
        'type'    => 'text'
        ),
    array(
        'id'      => 'rows',
        'label'   => __('Rows', DOMAIN),
        'desc'    => __('Setting this to more than 1 initializes grid mode. Use slidesPerRow to set how many slides should be in each row.', DOMAIN),
        'default' => '1',
        'type'    => 'number'
        ),
    array(
        'id'      => 'slide',
        'label'   => __('Slide', DOMAIN),
        'desc'    => __('Slide element query', DOMAIN),
        'default' => '',
        'type'    => 'text'
        ),
    array(
        'id'      => 'slidesPerRow',
        'label'   => __('Slides Per Row', DOMAIN),
        'desc'    => __('With grid mode initialized via the rows option, this sets how many slides are in each grid row.', DOMAIN),
        'default' => '1',
        'type'    => 'number'
        ),
    array(
        'id'      => 'swipe',
        'label'   => __('Swipe', DOMAIN),
        'desc'    => __('Enables touch swipe', DOMAIN),
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'swipeToSlide',
        'label'   => __('Swipe To Slide', DOMAIN),
        'desc'    => __('Swipe to slide irrespective of slidesToScroll', DOMAIN),
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'touchMove',
        'label'   => __('Touch Move', DOMAIN),
        'desc'    => __('Enables slide moving with touch', DOMAIN),
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'touchThreshold',
        'label'   => __('Touch Threshold', DOMAIN),
        'desc'    => __('To advance slides, the user must swipe a length of (1/touchThreshold) * the width of the slider.', DOMAIN),
        'default' => '5',
        'type'    => 'number'
        ),
    array(
        'id'      => 'useCSS',
        'label'   => __('Use CSS', DOMAIN),
        'desc'    => __('Enable/Disable CSS Transitions', DOMAIN),
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'useTransform',
        'label'   => __('Use Transform', DOMAIN),
        'desc'    => __('Enable/Disable CSS Transforms', DOMAIN),
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'vertical',
        'label'   => __('Vertical', DOMAIN),
        'desc'    => __('Vertical slide direction', DOMAIN),
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'verticalSwiping',
        'label'   => __('Vertical Swiping', DOMAIN),
        'desc'    => __('Changes swipe direction to vertical', DOMAIN),
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'rtl',
        'label'   => __('Right To Left', DOMAIN),
        'desc'    => __('Change the slider\'s direction to become right-to-left', DOMAIN),
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'waitForAnimate',
        'label'   => __('Wait For Animate', DOMAIN),
        'desc'    => __('Ignores requests to advance the slide while animating', DOMAIN),
        'default' => 'on',
        'type'    => 'checkbox'
        ),
    array(
        'id'      => 'zIndex',
        'label'   => __('zIndex', DOMAIN),
        'desc'    => __('Set the zIndex values for slides, useful for IE9 and lower', DOMAIN),
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