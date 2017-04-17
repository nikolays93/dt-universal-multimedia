<?php
defined( 'ABSPATH' ) or die();

$sizes = get_intermediate_image_sizes();
$view_sizes = array(); // array('' => 'Custom');
foreach ($sizes as $value) {
  $view_sizes[$value] = $value;
}

$settings = array(
  array(
    'id' => 'width',
    'label' => 'Width',
    'desc' => '',
    'type' => 'number',
    ),
  array(
    'id' => 'height',
    'label' => 'Height',
    'desc' => '',
    'type' => 'number',
    ),
  array(
    'id' => 'items_size',
    'label' => 'Image size',
    'desc' => '',
    'type' => 'select',
    'options' => $view_sizes
    ),
  array(
    'id' => 'image_captions',
    'label' => 'Show image captions',
    'desc' => '',
    'type' => 'checkbox',
    ),

  array(
    'id' => 'exclude_styles',
    'label' => 'Exclude Template',
    'desc' => '',
    'type' => 'checkbox',
    ),
  array(
    'id' => 'exclude_assets',
    'label' => 'Exclude Assets',
    'desc' => '',
    'type' => 'checkbox',
    )

  );

return $settings;