<?php

namespace NikolayS93\MBlocks;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

$settings = array(
array(
'id' => 'xOrigin',
'label' => __('xOrigin', DOMAIN),
'desc' => __('Center of the carousel (container width / 2)', DOMAIN),
'type' => 'number',
),
array(
'id' => 'yOrigin',
'label' => __('yOrigin', DOMAIN),
'desc' => __('Center of the carousel (container height / 10)', DOMAIN),
'type' => 'number',
),
array(
'id' => 'xRadius',
'label' => __('xRadius', DOMAIN),
'desc' => __('Half the width of the carousel (container height / 6)', DOMAIN),
'type' => 'number',
),
array(
'id' => 'yRadius',
'label' => __('yRadius', DOMAIN),
'desc' => __('Half the height of the carousel (container height / 6)', DOMAIN),
'type' => 'number',
),
array(
'id' => 'farScale',
'label' => __('farScale', DOMAIN),
'desc' => __('Scale of an item at its farthest point (range: 0 to 1)', DOMAIN),
'type' => 'number',
'default' => '0.5'
),
array(
'id' => 'mirror',
'label' => __('Reflections', DOMAIN),
'desc' => __('Reflection options none', DOMAIN),
'type' => 'number',
'default' => '0.5'
),
array(
'id' => 'transforms',
'label' => __('Transforms', DOMAIN),
'desc' => __('Use native CSS transforms if support for them is detected true', DOMAIN),
'type' => 'text',
),
array(
'id' => 'smooth',
'label' => __('Fps Frames per second', DOMAIN),
'desc' => __('Use maximum effective frame rate via the requestAnimationFrame API if support is detected true. (if smooth animation is turned off)', DOMAIN),
'type' => 'number',
'default' => '30'
),
array(
'id' => 'speed',
'label' => __('Speed', DOMAIN),
'desc' => __('Relative speed factor of the carousel. Any positive number: 1 is slow, 4 is medium, 10 is fast.', DOMAIN),
'type' => 'number',
'default' => '4'
),
array(
'id' => 'autoPlay',
'label' => __('autoPlay', DOMAIN),
'desc' => __('Automatically rotate the carousel by this many items periodically (positive number is clockwise). Auto-play is not performed while the mouse hovers over the carousel container. A value of 0 means auto-play is turned off. See: autoPlayDelay 0', DOMAIN),
'type' => 'number',
'default' => '0'
),
array(
'id' => 'autoPlayDelay',
'label' => __('autoPlayDelay', DOMAIN),
'desc' => __('Delay, in milliseconds, between auto-play spins', DOMAIN),
'type' => 'number',
'default' => '4000'
),
array(
'id' => 'mouseWheel',
'label' => __('mouseWheel', DOMAIN),
'desc' => __('Spin the carousel using the mouse wheel. Requires a "mousewheel" event, provided by this mousewheel plugin. However, see: known issues', DOMAIN),
'type' => 'checkbox',
),
array(
'id' => 'bringToFront',
'label' => __('bringToFront', DOMAIN),
'desc' => __('Clicking an item will rotate it to the front', DOMAIN),
'type' => 'checkbox',
),
array(
'id' => 'buttonLeft',
'label' => __('buttonLeft', DOMAIN),
'desc' => __('jQuery collection of element(s) intended to spin the carousel so as to bring the item to the left of the frontmost item to the front, i.e., spin it counterclockwise, when clicked. E.g., $("#button-left")', DOMAIN),
'type' => 'text',
),
array(
'id' => 'buttonRight',
'label' => __('buttonRight', DOMAIN),
'desc' => __('jQuery collection of element(s) intended to spin the carousel so as to bring the item to the right of the frontmost item to the front, i.e., spin it clockwise, when clicked. E.g., $("#button-right")', DOMAIN),
'type' => 'text',
),
array(
'id' => 'itemClass',
// 'label' => __('itemClass', DOMAIN),
// 'desc' => __('Class attribute of the item elements inside the carousel container', DOMAIN),
'type' => 'hidden',
'value' => 'item'
),
);
// handle  The string handle you can use to interact with the carousel. E.g., $("#carousel").data("carousel").go(1)
return $settings;