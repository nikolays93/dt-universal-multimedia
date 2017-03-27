<?php
defined( 'ABSPATH' ) or die();

$sizes = get_intermediate_image_sizes();
$view_sizes = array('' => 'Custom');
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
  array('id' => 'width',
    'label' => 'Width',
    'desc' => '',
    'type' => 'number',
    ),
  array('id' => 'height',
    'label' => 'Height',
    'desc' => '',
    'type' => 'number',
    ),
  array('id' => 'columns',
    'label' => 'Columns',
    'default' => '4',
    'type' => 'number',
    ),
  array('id' => 'lightbox',
    'label' => 'LightBox class',
    'desc' => 'Add class for lightbox links',
    'type' => 'text',
    'default' => 'zoom',
     // 'default' => 'fancybox',
    ),
  // array(
  //   'id'  => 'block_template',
  //   'label' => 'Template',
  //   'desc' => 'include CSS template',
  //   'type' => 'select',
  //   'default' => 'plugin',
  //   'options' => array(
  //     ''=>'Не использовать',
  //     'default'=>'Standart',
  //     'plugin'=>'Changed',
  //     'custom'=>'Personal'
  //     )
  //   ),
  // array(
  //   'id' => 'style_path',
  //   'label' => 'Custom path',
  //   'desc' => '',
  //   'type' => 'text',
  //   ),

  );
  return $settings;