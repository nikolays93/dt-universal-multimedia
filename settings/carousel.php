<?php
defined( 'ABSPATH' ) or die();

$sizes = get_intermediate_image_sizes();
$view_sizes = array();
foreach ($sizes as $value) {
  $view_sizes[$value] = $value;
}

$settings = array(
  array(
    'id' => 'template',
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
    'id' => 'image_size',
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
    'id' => 'lightbox_links',
    'label' => 'Use lightbox',
    'desc' => 'Add links w/ class "zoomin" for lightbox',
    'type' => 'checkbox',
    ),
  array(
    'id' => 'lightbox_class',
    'label' => 'Links class',
    'desc' => 'Add classes for lightbox links',
    'type' => 'text',
    'default' => 'fancybox',
     // 'default' => 'fancybox',
    )
  );
  return $settings;