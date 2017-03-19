<?php
defined( 'ABSPATH' ) or die();

$sizes = get_intermediate_image_sizes();
$view_sizes = array();
foreach ($sizes as $value) {
  $view_sizes[$value] = $value;
}

$settings = array(
  array(
    'id'  => 'block_template',
    'label' => 'Template',
    'desc' => 'include CSS template',
    'type' => 'select',
    'default' => 'plugin',
    'options' => array(
      ''=>'Не использовать',
      'default'=>'Standart',
      'plugin'=>'Changed',
      'custom'=>'Personal'
      )
    ),
  array(
    'id' => 'style_path',
    'label' => 'Custom path',
    'desc' => '',
    'type' => 'text',
    ),
  array(
    'id' => 'carousel_size',
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
    'id' => 'lightbox',
    'label' => 'Use lightbox',
    'desc' => 'Add class for lightbox links',
    'type' => 'text',
    'default' => 'zoom',
     // 'default' => 'fancybox',
    )
  );
  return $settings;