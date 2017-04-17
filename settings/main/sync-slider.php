<?php
defined( 'ABSPATH' ) or die();

$sizes = get_intermediate_image_sizes();
$view_sizes = array();
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
    'label' => 'Carousel size',
    'desc' => '',
    'type' => 'select',
    'options' => $view_sizes
    ),
  array(
    'id' => 'lightbox',
    'label' => 'LightBox class',
    'desc' => 'Add class for lightbox links',
    'type' => 'text',
    'default' => 'zoom',
    ),

  array(
    'id' => 'arr_prev',
    'label' => 'Prev',
    'desc' => '',
    'type' => 'text',
    'default' => 'prev'
    ),
  array(
    'id' => 'arr_next',
    'label' => 'Next',
    'desc' => '',
    'type' => 'text',
    'default' => 'next'
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