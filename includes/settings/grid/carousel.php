<?php
/**
 * @global  $args
 *
 * as like slider
 */

$sizes = get_intermediate_image_sizes();
$view_sizes = array();
foreach ($sizes as $value) {
  $view_sizes[ $value ] = $value;
}

$settings = array(
  array(
    "id" => "set_size",
    "type" => "html",
    "value" => "<p>Укажите размер вручную</p>",
    ),
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
    "id" => "set_size2",
    "type" => "html",
    "value" => "<p>Или выберите предустановленный</p>",
    ),
  array(
    'id' => 'items_size',
    'label' => 'Image size',
    'desc' => '',
    'type' => 'select',
    'options' => $view_sizes
    ),
  array(
    "id" => "set_size3",
    "type" => "html",
    "value" => "<p>Дополнительные настройки</p>",
    ),
  array(
    'id' => 'lightbox',
    'label' => 'LightBox class',
    'desc' => 'Add class for lightbox links',
    'type' => 'text',
    'placeholder' => 'zoom',
    ),
  array(
    'id' => 'image_captions',
    'label' => 'Image captions',
    'desc' => '',
    'type' => 'select',
    'options' => array(
        ''       => 'Не отображать',
        'top'    => 'Сверху',
        'bottom' => 'Снизу'
        )
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