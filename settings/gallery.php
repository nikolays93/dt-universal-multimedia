<?php
defined( 'ABSPATH' ) or die();

$sizes = get_intermediate_image_sizes();
$view_sizes = array('' => 'Не использовать');
foreach ($sizes as $value) {
  $view_sizes[$value] = $value;
}

$settings = array(
  array(
    'id' => 'pr_width',
    'label' => 'Width',
    'desc' => '',
    'type' => 'text',
    ),
  array(
    'id' => 'pr_height',
    'label' => 'Height',
    'desc' => '',
    'type' => 'text',
    ),
  array(
    'id' => 'columns',
    'label' => 'Columns',
    'default' => '4',
    'type' => 'number',
    ),
  array(
    'id' => 'full_size',
    'label' => 'Lightbox Size',
    'desc' => '',
    'type' => 'select',
    'default' => 'large',
    'options' => $view_sizes
    ),
  array(
    'id' => 'lb_class',
    'label' => 'Lightbox class',
    'default' => 'zoom',
    'type' => 'text',
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