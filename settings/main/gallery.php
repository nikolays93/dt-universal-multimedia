<?php
defined( 'ABSPATH' ) or die();

$sizes = get_intermediate_image_sizes();
$view_sizes = array(); // '' => 'Custom'
foreach ($sizes as $value) {
  $view_sizes[$value] = $value;
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
        'id' => 'image_captions',
        'label' => 'Image captions',
        'desc' => '',
        'type' => 'checkbox',
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
        'placeholder' => 'zoom',
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
    array(
        'id' => 'no_paddings',
        'label' => 'Disable paddings',
        'desc' => '',
        'type' => 'checkbox',
        ),
    array(
        'id' => 'exclude_initialize',
        'label' => 'Disable initialize script',
        'desc' => '',
        'type' => 'checkbox',
        )
    );

return $settings;
