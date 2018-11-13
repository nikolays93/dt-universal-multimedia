<?php

namespace NikolayS93\MBlocks;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

$settings = array(
  array(
    'id'      => 'startingItem',
    'type'    => 'number',
    'label'   => __('Starting Item', DOMAIN),
    'desc'    => __('Determines which image will appear in the center when the carousel loads. Set to 0 to start with the middle item.', DOMAIN),
    'default' => '0'
    ),
  array(
    'id'      => 'separation',
    'type'    => 'number',
    'label'   => __('Separation', DOMAIN),
    'desc'    => __('The amount if pixels to separate each item from one another.', DOMAIN),
    'default' => '150'
    ),
  array(
    'id'      => 'separationMultiplier',
    'type'    => 'text',
    'label'   => __('Separation Multiplier', DOMAIN),
    'desc'    => __('Multiplied by \'separation\' exponentially to determine item separation for all items (a value of 0.5 will reduce the distance by half for each item as they span out).', DOMAIN),
    'default' => '0.6'
    ),
  array(
    'id'      => 'horizonOffset',
    'type'    => 'number',
    'label'   => __('Horizon Offset', DOMAIN),
    'desc'    => __('The amount of pixels to separate each item from the horizon. Can be negative or positive to have the items fall above or below horizon. Set to \'0\' to keep the center of each item aligned with the horizon.', DOMAIN),
    'default' => '0'
    ),
  array(
    'id'      => 'horizonOffsetMultiplier',
    'type'    => 'text',
    'label'   => __('Horizon Offset Multiplier', DOMAIN),
    'desc'    => __('Multiplied by \'horizonOffset\' exponentially to determine horizon offset for all items.', DOMAIN),
    'default' => '0.7'
    ),
  array(
    'id'      => 'sizeMultiplier',
    'type'    => 'text',
    'label'   => __('Size Multiplier', DOMAIN),
    'desc'    => __('How much the items should increase/decrease by as they span out (a value of 0.5 will reduce each items size by half).', DOMAIN),
    'default' => '0.7'
    ),
  array(
    'id'      => 'opacityMultiplier',
    'type'    => 'text',
    'label'   => __('Opacity Multiplier', DOMAIN),
    'desc'    => __('How drastically the opacity of each item decreases. Applied exponentially. ', DOMAIN),
    'default' => '0.8'
    ),
  array(
    'id'      => 'horizon',
    'type'    => 'number',
    'label'   => __('Horizon', DOMAIN),
    'desc'    => __('How "far in" the horizon should be set from either the top of the container (horizontal orientation) or left of the container (vertical orientation). By default, it will be centered based on the container dimensions.', DOMAIN),
    'default' => '0'
    ),
  array(
    'id'      => 'flankingItems',
    'type'    => 'number',
    'label'   => __('Flanking Items', DOMAIN),
    'desc'    => __('The amount of visible images on either side of the center item at any time.', DOMAIN),
    'default' => '3'
    ),
  array(
    'id'      => 'speed',
    'type'    => 'number',
    'label'   => __('Speed', DOMAIN),
    'desc'    => __('Time in milliseconds it takes to rotate the carousel.', DOMAIN),
    'default' => '300'
    ),
  array(
    'id'      => 'animationEasing',
    'type'    => 'text',
    'label'   => __('Animation Easing', DOMAIN),
    'desc'    => __('The easing effect used to animate the features in the carousel. jQuery has two built in effects \'swing\' and \'linear\'. There are many more transition effects available with this plugin.', DOMAIN),
    'default' => 'linear'
    ),
  array(
    'id'      => 'quickerForFurther',
    'type'    => 'checkbox',
    'label'   => __('Quicker For Further', DOMAIN),
    'desc'    => __('Will animate the carousel faster depending on how far away an item was when it was clicked to move to center.', DOMAIN),
    'default' => 'on'
    ),
  array(
    'id'      => 'edgeFadeEnabled',
    'type'    => 'checkbox',
    'label'   => __('Edge Fade Enabled', DOMAIN),
    'desc'    => __('When true, items fade off into nothingness when reaching the edge. Otherwise, they will move to a hidden position behind the center item.', DOMAIN),
    ),
  array(
    'id'      => 'linkHandling',
    'type'    => 'number',
    'label'   => __('Link Handling', DOMAIN),
    'desc'    => __('Determines behavior of links that are placed around the images. 1 to disable all (if you want to use the callback functions to do something special with the links), 2 to disable all but center (to link images out)', DOMAIN),
    'default' => '2'
    ),
  array(
    'id'      => 'autoPlay',
    'type'    => 'number',
    'label'   => __('Auto Play', DOMAIN),
    'desc'    => __('The speed in milliseconds to wait before auto-rotating. Positive value for a left to right movement, negative for a right to left. Zero to turn off.', DOMAIN),
    'default' => '0'
    ),
  array(
    'id'      => 'orientation',
    'type'    => 'text',
    'label'   => __('Orientation', DOMAIN),
    'desc'    => __('Controls whether or not the carousel spans out horizontally or vertically. The default options are optimized for a horizontal orientation.', DOMAIN),
    'default' => 'horizontal'
    ),
  array(
    'id'      => 'activeClassName',
    'type'    => 'text',
    'label'   => __('Active Class Name', DOMAIN),
    'desc'    => __('The class name to assign to the item currently in the center position.', DOMAIN),
    'default' => 'carousel-center'
    ),
  array(
    'id'      => 'keyboardNav',
    'type'    => 'checkbox',
    'label'   => __('Keyboard Nav', DOMAIN),
    'desc'    => __('Set to true to allow the user to use the arrow keys to move the carousel.', DOMAIN),
    ),
  array(
    'id'      => 'keyboardNavOverride',
    'type'    => 'checkbox',
    'label'   => __('Keyboard Nav Override', DOMAIN),
    'desc'    => __('Set to true to to override the normal functionality of the arrow keys on the browser window (prevents scrolling of the window). False to allow normal functionality of the keys as well as controlling the carousel.', DOMAIN),
    'default' => 'on'
    ),
  array(
    'id'      => 'imageNav',
    'type'    => 'checkbox',
    'label'   => __('Image Nav', DOMAIN),
    'desc'    => __('When true, clicking an image that is not in the center position will rotate the image to the center. False to disable that functionality, which is commonly paired with navigational buttons instead.', DOMAIN),
    'default' => 'on'
    ),
  array(
    'id'      => 'preloadImages',
    'type'    => 'checkbox',
    'label'   => __('Preload Images', DOMAIN),
    'desc'    => __('The carousel will attempt to preload all images before initializing. This is known to have some issues in certain browsers. The main reason for the preloader is too be able to determine the dimensions for each image before running calculations. If you run into issues, disable this and use the forced proportions below (or set your image dimensions using CSS).', DOMAIN),
    'default' => 'on'
    ),
  array(
    'id'      => 'forcedImageWidth',
    'type'    => 'number',
    'label'   => __('Forced Image Width', DOMAIN),
    'desc'    => __('Set a global width that should be applied to all images in the carousel.', DOMAIN),
    'default' => '0'
    ),
  array(
    'id'      => 'forcedImageHeight',
    'type'    => 'number',
    'label'   => __('Forced Image Height', DOMAIN),
    'desc'    => __('Set a global height that should be applied to all images in the carousel.', DOMAIN),
    'default' => '0'
    ),
  // movingToCenter, movedToCenter, clickedCenter, movingFromCenter, movedFromCenter
);
return $settings;