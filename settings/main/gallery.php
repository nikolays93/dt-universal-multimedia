<?php
defined( 'ABSPATH' ) or die();

$sizes = get_intermediate_image_sizes();
$view_sizes = array(); // '' => 'Custom'
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
    'id' => 'columns',
    'label' => 'Columns',
    'default' => '4',
    'type' => 'number',
    ),
  array(
    'id' => 'lightbox',
    'label' => 'LightBox class',
    'desc' => 'Add class for lightbox links',
    'type' => 'text',
    'default' => 'zoom',
    ),
  array(
    'id' => 'lazyLoad',
    'label' => 'Enuqueue lazy load',
    'desc' => 'How much images initialized af first',
    'type' => 'number',
    'default' => '4'
    ),
  array(
    'id' => 'masonry',
    'label' => 'Enuqueue masonry',
    'desc' => 'Initialize masonry for gallery images',
    'type' => 'checkbox',
    ),
  );

return $settings;