<?php
defined( 'ABSPATH' ) or die();

$settings = array(
  array(
    'id'      => 'startingItem',
    'type'    => 'number',
    'label'   => 'Starting Item',
    'desc'    => 'Determines which image will appear in the center when the carousel loads. Set to 0 to start with the middle item.',
    'default' => '0'
    ),
  array(
    'id'      => 'separation',
    'type'    => 'number',
    'label'   => 'Separation',
    'desc'    => 'The amount if pixels to separate each item from one another.',
    'default' => '150'
    ),
  array(
    'id'      => 'separationMultiplier',
    'type'    => 'text',
    'label'   => 'Separation Multiplier',
    'desc'    => 'Multiplied by \'separation\' exponentially to determine item separation for all items (a value of 0.5 will reduce the distance by half for each item as they span out).',
    'default' => '0.6'
    ),
  array(
    'id'      => 'horizonOffset',
    'type'    => 'number',
    'label'   => 'Horizon Offset',
    'desc'    => 'The amount of pixels to separate each item from the horizon. Can be negative or positive to have the items fall above or below horizon. Set to \'0\' to keep the center of each item aligned with the horizon.',
    'default' => '0'
    ),
  array(
    'id'      => 'horizonOffsetMultiplier',
    'type'    => 'text',
    'label'   => 'Horizon Offset Multiplier',
    'desc'    => 'Multiplied by \'horizonOffset\' exponentially to determine horizon offset for all items.',
    'default' => '0.7'
    ),
  array(
    'id'      => 'sizeMultiplier',
    'type'    => 'text',
    'label'   => 'Size Multiplier',
    'desc'    => 'How much the items should increase/decrease by as they span out (a value of 0.5 will reduce each items size by half).',
    'default' => '0.7'
    ),
  array(
    'id'      => 'opacityMultiplier',
    'type'    => 'text',
    'label'   => 'Opacity Multiplier',
    'desc'    => 'How drastically the opacity of each item decreases. Applied exponentially. ',
    'default' => '0.8'
    ),
  array(
    'id'      => 'horizon',
    'type'    => 'number',
    'label'   => 'Horizon',
    'desc'    => 'How "far in" the horizon should be set from either the top of the container (horizontal orientation) or left of the container (vertical orientation). By default, it will be centered based on the container dimensions.',
    'default' => '0'
    ),
  array(
    'id'      => 'flankingItems',
    'type'    => 'number',
    'label'   => 'Flanking Items',
    'desc'    => 'The amount of visible images on either side of the center item at any time.',
    'default' => '3'
    ),
  array(
    'id'      => 'speed',
    'type'    => 'number',
    'label'   => 'Speed',
    'desc'    => 'Time in milliseconds it takes to rotate the carousel.',
    'default' => '300'
    ),
  array(
    'id'      => 'animationEasing',
    'type'    => 'text',
    'label'   => 'Animation Easing',
    'desc'    => 'The easing effect used to animate the features in the carousel. jQuery has two built in effects \'swing\' and \'linear\'. There are many more transition effects available with this plugin.',
    'default' => 'linear'
    ),
  array(
    'id'      => 'quickerForFurther',
    'type'    => 'checkbox',
    'label'   => 'Quicker For Further',
    'desc'    => 'Will animate the carousel faster depending on how far away an item was when it was clicked to move to center.',
    'default' => 'on'
    ),
  array(
    'id'      => 'edgeFadeEnabled',
    'type'    => 'checkbox',
    'label'   => 'Edge Fade Enabled',
    'desc'    => 'When true, items fade off into nothingness when reaching the edge. Otherwise, they will move to a hidden position behind the center item.',
    ),
  array(
    'id'      => 'linkHandling',
    'type'    => 'number',
    'label'   => 'Link Handling',
    'desc'    => 'Determines behavior of links that are placed around the images. 1 to disable all (if you want to use the callback functions to do something special with the links), 2 to disable all but center (to link images out)',
    'default' => '2'
    ),
  array(
    'id'      => 'autoPlay',
    'type'    => 'number',
    'label'   => 'Auto Play',
    'desc'    => 'The speed in milliseconds to wait before auto-rotating. Positive value for a left to right movement, negative for a right to left. Zero to turn off.',
    'default' => '0'
    ),
  array(
    'id'      => 'orientation',
    'type'    => 'text',
    'label'   => 'Orientation',
    'desc'    => 'Controls whether or not the carousel spans out horizontally or vertically. The default options are optimized for a horizontal orientation.',
    'default' => 'horizontal'
    ),
  array(
    'id'      => 'activeClassName',
    'type'    => 'text',
    'label'   => 'Active Class Name',
    'desc'    => 'The class name to assign to the item currently in the center position.',
    'default' => 'carousel-center'
    ),
  array(
    'id'      => 'keyboardNav',
    'type'    => 'checkbox',
    'label'   => 'Keyboard Nav',
    'desc'    => 'Set to true to allow the user to use the arrow keys to move the carousel.',
    ),
  array(
    'id'      => 'keyboardNavOverride',
    'type'    => 'checkbox',
    'label'   => 'Keyboard Nav Override',
    'desc'    => 'Set to true to to override the normal functionality of the arrow keys on the browser window (prevents scrolling of the window). False to allow normal functionality of the keys as well as controlling the carousel.',
    'default' => 'on'
    ),
  array(
    'id'      => 'imageNav',
    'type'    => 'checkbox',
    'label'   => 'Image Nav',
    'desc'    => 'When true, clicking an image that is not in the center position will rotate the image to the center. False to disable that functionality, which is commonly paired with navigational buttons instead.',
    'default' => 'on'
    ),
  array(
    'id'      => 'preloadImages',
    'type'    => 'checkbox',
    'label'   => 'Preload Images',
    'desc'    => 'The carousel will attempt to preload all images before initializing. This is known to have some issues in certain browsers. The main reason for the preloader is too be able to determine the dimensions for each image before running calculations. If you run into issues, disable this and use the forced proportions below (or set your image dimensions using CSS).',
    'default' => 'on'
    ),
  array(
    'id'      => 'forcedImageWidth',
    'type'    => 'number',
    'label'   => 'Forced Image Width',
    'desc'    => 'Set a global width that should be applied to all images in the carousel.',
    'default' => '0'
    ),
  array(
    'id'      => 'forcedImageHeight',
    'type'    => 'number',
    'label'   => 'Forced Image Height',
    'desc'    => 'Set a global height that should be applied to all images in the carousel.',
    'default' => '0'
    ),
  // movingToCenter, movedToCenter, clickedCenter, movingFromCenter, movedFromCenter
);
return $settings;