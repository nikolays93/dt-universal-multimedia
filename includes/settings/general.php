<?php
defined( 'ABSPATH' ) or die();

global $post;
$type = get_post_meta( $post->ID, '_type', true );
$inputs = array(
	array(
		'id'    => 'grid_type',
		'type'  => 'select',
		'input_class' => 'button',
		// 'default'=> get_post_meta( $post->ID, '_main_type', true ),
		'options' => array(
			'carousel'    => 'Карусель',
			'slider'      => 'Слайдер',
			//'sync-slider' => 'Синх. слайдер',
			'carousel-3d'   => '3D слайдер',
			'gallery'     => 'Галерея',
			),
		),
	array(
		'id'    => 'lib_type',
		'name'  => 'lib_type',
		'type'  => 'select',
		'input_class' => 'carousel slider sync-slider query button',
		'options' => array(
			'slick' => 'Скользкий слайдер',
			'owl-carousel' => 'Сова карусель',
			),
		),
	array(
		'id'    => 'lib_type',
		'name'  => 'lib_type',
		'type'  => 'select',
		'input_class' => 'gallery button hidden',
		// 'default' => $type,
		'options' => array(
			'fancybox' => 'Фантастическая коробка',
			),
		'custom_attributes' => array(
			'disabled' => 'disable',
			),
		),
	array(
		'id'    => 'lib_type',
		'name'  => 'lib_type',
		'type'  => 'select',
		'input_class' => 'carousel-3d button hidden',
		// 'default' => $type,
		'options' => array(
			'cloud9carousel' => 'Облачная карусель',
			'waterwheelCarousel' => 'Водяное колесо'
			),
		'custom_attributes' => array(
			'disabled' => 'disable',
			),
		),
	);

return $inputs;