<?php

namespace NikolayS93\MBlocks;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

$settings = array(
    array(
        'id' => 'padding',
        'label' => __('Padding', DOMAIN),
        'type' => 'text',
        'default' => '15',
        'desc' => __(' Space inside fancyBox around content. Can be set as array - [top, right, bottom, left]. ', DOMAIN)
        ),
    array(
        'id' => 'margin',
        'label' => __('Margin', DOMAIN),
        'type' => 'text',
        'default' => '20',
        'desc'  => __('Minimum space between viewport and fancyBox. Can be set as array - [top, right, bottom, left]. Right and bottom margins are ignored if content dimensions exceeds viewport', DOMAIN)
        ),
    array(
        'id' => 'width',
        'label' => __('Width', DOMAIN),
        'type' => 'text',
        'default' => '800',
        'desc' => "Default width for 'iframe' and 'swf' content. Also for 'inline', 'ajax' and 'html' if 'autoSize' is set to 'false'. Can be numeric or 'auto'."
        ),
    array(
        'id' => 'height',
        'label' => __('Height', DOMAIN),
        'default' => '600',
        'type' => 'text',
        'desc' => "Default height for 'iframe' and 'swf' content. Also for 'inline', 'ajax' and 'html' if 'autoSize' is set to 'false'. Can be numeric or 'auto'"
        ),
    array(
        'id' => 'minWidth',
        'label' => __('Minimum Width', DOMAIN),
        'default' => '100',
        'type' => 'number',
        'desc' => __('Minimum width fancyBox should be allowed to resize to    ', DOMAIN)
        ),
    array(
        'id' => 'minHeight',
        'label' => __('Minimum Height', DOMAIN),
        'default' => '100',
        'type' => 'number',
        'desc' => __('Minimum height fancyBox should be allowed to resize to   ', DOMAIN)
        ),
    array(
        'id' => 'maxWidth',
        'label' => __('Maximum Width', DOMAIN),
        'default' => '9999',
        'type' => 'number',
        'desc' => __('Maximum width fancyBox should be allowed to resize to    ', DOMAIN)
        ),
    array(
        'id' => 'maxHeight',
        'label' => __('Maximum Height', DOMAIN),
        'default' => '9999',
        'type' => 'number',
        'desc' => __('Maximum height fancyBox should be allowed to resize to   ', DOMAIN)
        ),
    array(
        'id' => 'autoSize',
        'label' => __('Auto Size', DOMAIN),
        'default' => 'on',
        'type' => 'checkbox',
        'desc' => __('Maximum height fancyBox should be allowed to resize to', DOMAIN),
        'data-show' => 'autoWidth, autoHeight'
        ),
    array(
        'id' => 'autoWidth',
        'label' => __('Auto Width', DOMAIN),
        'type' => 'checkbox',
        'desc' => "If set to true, for 'inline', 'ajax' and 'html' type content width is auto determined. If no dimensions set this may give unexpected results"
        ),
    array(
        'id' => 'autoHeight',
        'label' => __('Auto Height', DOMAIN),
        'type' => 'checkbox',
        'desc' => "If set to true, for 'inline', 'ajax' and 'html' type content height is auto determined. If no dimensions set this may give unexpected results",
        ),
    array(
        'id' => 'autoResize', // default: !isTouch
        'label' => __('Auto Resize', DOMAIN),
        'type' => 'checkbox',
        'desc' => "If set to true, the content will be resized after window resize event"
        ),
    array(
        'id' => 'autoCenter', // default: !isTouch
        'label' => __('Auto Center', DOMAIN),
        'type' => 'checkbox',
        "desc" => "If set to true, the content will always be centered"
        ),
    array(
        'id' => 'fitToView',
        'label' => __('Fit To View', DOMAIN),
        'default' => 'on',
        'type' => 'checkbox',
        'desc' => __('If set to true, fancyBox is resized to fit inside viewport before opening', DOMAIN)
        ),
    array(
        'id' => 'scrolling',
        'label' => __('Scrolling', DOMAIN),
        'default' => 'auto',
        'type' => 'text',
        'desc' => "Set the overflow CSS property to create or hide scrollbars. Can be set to 'auto', 'yes', 'no' or 'visible'"
        ),
    array(
        'id' => 'wrapCSS',
        'label' => __('Wrap CSS', DOMAIN),
        'default' => '',
        'type' => 'text',
        'desc' => __('Customizable CSS class for wrapping element (useful for custom styling)', DOMAIN)
        ),
    array(
        'id' => 'arrows',
        'label' => __('Display Arrows', DOMAIN),
        'default' => 'on',
        'type' => 'checkbox',
        'desc' => __('If set to true, navigation arrows will be displayed', DOMAIN)
        ),
    array(
        'id' => 'closeBtn',
        'label' => __('Display Close Button', DOMAIN),
        'default' => 'on',
        'type' => 'checkbox',
        'desc' => __('If set to true, close button will be displayed', DOMAIN)
        ),
    array(
        'id' => 'closeClick',
        'label' => __('Close Click', DOMAIN),
        'type' => 'checkbox',
        'desc' => __('If set to true, fancyBox will be closed when user clicks the content', DOMAIN)
        ),
    array(
        'id' => 'nextClick',
        'label' => __('Next Click', DOMAIN),
        'type' => 'checkbox',
        'desc' => __('If set to true, will navigate to next gallery item when user clicks the content', DOMAIN)
        ),
    array(
        'id' => 'mouseWheel',
        'label' => __('Mouse Wheel', DOMAIN),
        'default' => 'on',
        'type' => 'checkbox',
        'desc' => __('If set to true, you will be able to navigate gallery using the mouse wheel', DOMAIN)
        ),
    array(
        'id' => 'autoPlay',
        'label' => __('Auto Play', DOMAIN),
        'type' => 'checkbox',
        'desc' => __('If set to true, slideshow will start after opening the first gallery item', DOMAIN),
        'data-show' => 'playSpeed'
        ),
    array(
        'id' => 'playSpeed',
        'label' => __('Play Speed', DOMAIN),
        'default' => '3000',
        'type' => 'number',
        'desc' => __('Slideshow speed in milliseconds  ', DOMAIN)
        ),
    array(
        'id' => 'preload',
        'label' => __('Preload', DOMAIN),
        'default' => '3',
        'type' => 'number',
        'desc' => __('Number of gallery images to preload  ', DOMAIN)
        ),
    array(
        'id' => 'modal',
        'label' => __('Modal', DOMAIN),
        'type' => 'checkbox',
        'desc' => __('If set to true, will disable navigation and closing  ', DOMAIN)
        ),
    array(
        'id' => 'loop',
        'label' => __('Loop', DOMAIN),
        'default' => 'on',
        'type' => 'checkbox',
        'desc' => __('If set to true, enables cyclic navigation. This means, if you click "next" after you reach the last element, first element will be displayed (and vice versa).   ', DOMAIN)
        ),
    array(
        'id' => 'scrollOutside',
        'label' => __('Scroll Outside', DOMAIN),
        'default' => 'on',
        'type' => 'checkbox',
        'desc' => __('If true, the script will try to avoid horizontal scrolling for iframes and html content', DOMAIN)
        ),
    array(
        'id' => 'index',
        'label' => __('Index', DOMAIN),
        'default' => '0',
        'type' => 'number',
        'desc' => __('Overrides group start index', DOMAIN)
        ),
    );
return $settings;

// array(
//     'id' => 'aspectRatio',
//     'label' => __('aspectRatio', DOMAIN),
//     'type' => 'checkbox'
//     ),
// array(
//     'id' => 'topRatio',
//     'label' => __('topRatio', DOMAIN),
//     'default' => '',
//     'type' => 'checkbox'
//     ),
// array(
//     'id' => 'leftRatio',
//     'label' => __('leftRatio', DOMAIN),
//     'default' => '',
//     'type' => 'checkbox'
//     ),

// array(
//     'id' => 'type',
//     'label' => __('type', DOMAIN),
//     'default' => '',
//     'type' => 'checkbox'
//     ),
// array(
//     'id' => 'href',
//     'label' => __('href', DOMAIN),
//     'default' => '',
//     'type' => 'checkbox'
//     ),
// array(
//     'id' => 'content',
//     'label' => __('content', DOMAIN),
//     'default' => '',
//     'type' => 'checkbox'
//     ),
// array(
//     'id' => 'title',
//     'label' => __('title', DOMAIN),
//     'default' => '',
//     'type' => 'checkbox'
//     ),