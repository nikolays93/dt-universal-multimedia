<?php
defined( 'ABSPATH' ) or die();

$sizes = get_intermediate_image_sizes();
$view_sizes = array();
foreach ($sizes as $value) {
  $view_sizes[$value] = $value;
}

$settings = array(
  array('id' => 'items_size',
    'label' => 'Image size',
    'desc' => '',
    'type' => 'select',
    'options' => $view_sizes
    ),
  // array(
  //   'id' => 'image_captions',
  //   'label' => 'Show image captions',
  //   'desc' => '',
  //   'type' => 'checkbox',
  //   ),
  );
  return $settings;