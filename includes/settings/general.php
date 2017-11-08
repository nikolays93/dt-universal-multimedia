<?php
defined( 'ABSPATH' ) or die();

global $post;

$type = wp_parse_args( get_post_meta( $post->ID, 'mtypes', true ), array(
	'grid_type' => '',
	'lib_type'  => '',
) );

$inputs = array(
	array(
		'id'    => 'grid_type',
		'type'  => 'select',
		'input_class' => 'button',
		'default' => $type['grid_type'],
		'options' => array(
			'carousel'    => 'Карусель',
			'slider'      => 'Слайдер',
			//'sync-slider' => 'Синх. слайдер',
			'carousel-3d'   => '3D слайдер',
			// 'gallery'     => 'Галерея',
			),
		),
	array(
		'id'    => 'lib_type',
		'type'  => 'select',
		'input_class' => 'activated carousel slider sync-slider button',
		'options' => array(
			'slick' => 'Скользкий слайдер',
			'owlCarousel' => 'Сова карусель',
			),
		),
	array(
		'id'    => 'lib_type',
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
		'type'  => 'select',
		'input_class' => 'carousel-3d button hidden',
		// 'default' => $type,
		'options' => array(
			'Cloud9carousel' => 'Облачная карусель',
			'waterwheelCarousel' => 'Водяное колесо'
			),
		'custom_attributes' => array(
			'disabled' => 'disable',
			),
		),
	);

return $inputs;