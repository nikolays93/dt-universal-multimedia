<?php
defined( 'ABSPATH' ) or die();
// has a $main_type 'gallery'
// $id, $label, $type - required

$settings = array(
    array(
        'id' => 'padding',
        'label' => 'Padding',
        'type' => 'text',
        'default' => '15',
        'desc' => ' Space inside fancyBox around content. Can be set as array - [top, right, bottom, left]. '
        ),
    array(
        'id' => 'margin',
        'label' => 'Margin',
        'type' => 'text',
        'default' => '20',
        'desc' => 'Minimum space between viewport and fancyBox. Can be set as array - [top, right, bottom, left]. Right and bottom margins are ignored if content dimensions exceeds viewport'
        ),
    array(
        'id' => 'width',
        'label' => 'Width',
        'type' => 'text',
        'default' => '800',
        'desc' => "Default width for 'iframe' and 'swf' content. Also for 'inline', 'ajax' and 'html' if 'autoSize' is set to 'false'. Can be numeric or 'auto'."
        ),
    array(
        'id' => 'height',
        'label' => 'Height',
        'default' => '600',
        'type' => 'text',
        'desc' => "Default height for 'iframe' and 'swf' content. Also for 'inline', 'ajax' and 'html' if 'autoSize' is set to 'false'. Can be numeric or 'auto'"
        ),
    array(
        'id' => 'minWidth',
        'label' => 'Minimum Width',
        'default' => '100',
        'type' => 'number',
        'desc' => 'Minimum width fancyBox should be allowed to resize to    '
        ),
    array(
        'id' => 'minHeight',
        'label' => 'Minimum Height',
        'default' => '100',
        'type' => 'number',
        'desc' => 'Minimum height fancyBox should be allowed to resize to   '
        ),
    array(
        'id' => 'maxWidth',
        'label' => 'Maximum Width',
        'default' => '9999',
        'type' => 'number',
        'desc' => 'Maximum width fancyBox should be allowed to resize to    '
        ),
    array(
        'id' => 'maxHeight',
        'label' => 'Maximum Height',
        'default' => '9999',
        'type' => 'number',
        'desc' => 'Maximum height fancyBox should be allowed to resize to   '
        ),
    array(
        'id' => 'autoSize',
        'label' => 'Auto Size',
        'default' => 'on',
        'type' => 'checkbox',
        'desc' => 'Maximum height fancyBox should be allowed to resize to',
        'data-show' => 'autoWidth, autoHeight'
        ),
    array(
        'id' => 'autoWidth',
        'label' => 'Auto Width',
        'type' => 'checkbox',
        'desc' => "If set to true, for 'inline', 'ajax' and 'html' type content width is auto determined. If no dimensions set this may give unexpected results"
        ),
    array(
        'id' => 'autoHeight',
        'label' => 'Auto Height',
        'type' => 'checkbox',
        'desc' => "If set to true, for 'inline', 'ajax' and 'html' type content height is auto determined. If no dimensions set this may give unexpected results",
        ),
    array(
        'id' => 'autoResize', // default: !isTouch
        'label' => 'Auto Resize',
        'type' => 'checkbox',
        'desc' => "If set to true, the content will be resized after window resize event"
        ),
    array(
        'id' => 'autoCenter', // default: !isTouch
        'label' => 'Auto Center',
        'type' => 'checkbox',
        "desc" => "If set to true, the content will always be centered"
        ),
    array(
        'id' => 'fitToView',
        'label' => 'Fit To View',
        'default' => 'on',
        'type' => 'checkbox',
        'desc' => 'If set to true, fancyBox is resized to fit inside viewport before opening'
        ),
    array(
        'id' => 'scrolling',
        'label' => 'Scrolling',
        'default' => 'auto',
        'type' => 'text',
        'desc' => "Set the overflow CSS property to create or hide scrollbars. Can be set to 'auto', 'yes', 'no' or 'visible'"
        ),
    array(
        'id' => 'wrapCSS',
        'label' => 'Wrap CSS',
        'default' => '',
        'type' => 'text',
        'desc' => 'Customizable CSS class for wrapping element (useful for custom styling)'
        ),
    array(
        'id' => 'arrows',
        'label' => 'Display Arrows',
        'default' => 'on',
        'type' => 'checkbox',
        'desc' => 'If set to true, navigation arrows will be displayed'
        ),
    array(
        'id' => 'closeBtn',
        'label' => 'Display Close Button',
        'default' => 'on',
        'type' => 'checkbox',
        'desc' => 'If set to true, close button will be displayed'
        ),
    array(
        'id' => 'closeClick',
        'label' => 'Close Click',
        'type' => 'checkbox',
        'desc' => 'If set to true, fancyBox will be closed when user clicks the content'
        ),
    array(
        'id' => 'nextClick',
        'label' => 'Next Click',
        'type' => 'checkbox',
        'desc' => 'If set to true, will navigate to next gallery item when user clicks the content'
        ),
    array(
        'id' => 'mouseWheel',
        'label' => 'Mouse Wheel',
        'default' => 'on',
        'type' => 'checkbox',
        'desc' => 'If set to true, you will be able to navigate gallery using the mouse wheel'
        ),
    array(
        'id' => 'autoPlay',
        'label' => 'Auto Play',
        'type' => 'checkbox',
        'desc' => 'If set to true, slideshow will start after opening the first gallery item',
        'data-show' => 'playSpeed'
        ),
    array(
        'id' => 'playSpeed',
        'label' => 'Play Speed',
        'default' => '3000',
        'type' => 'number',
        'desc' => 'Slideshow speed in milliseconds  '
        ),
    array(
        'id' => 'preload',
        'label' => 'Preload',
        'default' => '3',
        'type' => 'number',
        'desc' => 'Number of gallery images to preload  '
        ),
    array(
        'id' => 'modal',
        'label' => 'Modal',
        'type' => 'checkbox',
        'desc' => 'If set to true, will disable navigation and closing  '
        ),
    array(
        'id' => 'loop',
        'label' => 'Loop',
        'default' => 'on',
        'type' => 'checkbox',
        'desc' => 'If set to true, enables cyclic navigation. This means, if you click "next" after you reach the last element, first element will be displayed (and vice versa).   '
        ),
    array(
        'id' => 'scrollOutside',
        'label' => 'Scroll Outside',
        'default' => 'on',
        'type' => 'checkbox',
        'desc' => 'If true, the script will try to avoid horizontal scrolling for iframes and html content'
        ),
    array(
        'id' => 'index',
        'label' => 'Index',
        'default' => '0',
        'type' => 'number',
        'desc' => 'Overrides group start index'
        ),
    );
return $settings;

// array(
//     'id' => 'aspectRatio',
//     'label' => 'aspectRatio',
//     'type' => 'checkbox'
//     ),
// array(
//     'id' => 'topRatio',
//     'label' => 'topRatio',
//     'default' => '',
//     'type' => 'checkbox'
//     ),
// array(
//     'id' => 'leftRatio',
//     'label' => 'leftRatio',
//     'default' => '',
//     'type' => 'checkbox'
//     ),

// array(
//     'id' => 'type',
//     'label' => 'type',
//     'default' => '',
//     'type' => 'checkbox'
//     ),
// array(
//     'id' => 'href',
//     'label' => 'href',
//     'default' => '',
//     'type' => 'checkbox'
//     ),
// array(
//     'id' => 'content',
//     'label' => 'content',
//     'default' => '',
//     'type' => 'checkbox'
//     ),
// array(
//     'id' => 'title',
//     'label' => 'title',
//     'default' => '',
//     'type' => 'checkbox'
//     ),