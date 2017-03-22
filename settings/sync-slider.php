<?php
defined( 'ABSPATH' ) or die();

$sizes = get_intermediate_image_sizes();
$view_sizes = array();
foreach ($sizes as $value) {
  $view_sizes[$value] = $value;
}

$settings = array(
  array(
    'id' => 'sl_width',
    'label' => 'Width',
    'desc' => '',
    'type' => 'number',
    ),
  array(
    'id' => 'sl_height',
    'label' => 'Height',
    'desc' => '',
    'type' => 'number',
    ),
  array(
    'id' => 'sl_arrows',
    'label' => 'Use slider arrows',
    'desc' => '',
    'type' => 'checkbox',
    'data-show' => 'sl_arr_prev, sl_arr_next'
    ),
  array(
    'id' => 'sl_arr_prev',
    'label' => 'Prev',
    'desc' => '',
    'type' => 'text',
    ),
  array(
    'id' => 'sl_arr_next',
    'label' => 'Next',
    'desc' => '',
    'type' => 'text',
    ),
  array(
    'id' => 'lightbox',
    'label' => 'Use lightbox',
    'desc' => 'Add class for lightbox links',
    'type' => 'text',
    'default' => 'zoom',
     // 'default' => 'fancybox',
    ),
  array(
    'id' => 'carousel_size',
    'label' => 'Carousel size',
    'desc' => '',
    'type' => 'select',
    'options' => $view_sizes
    ),
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
  );
  return $settings;