<?php
defined( 'ABSPATH' ) or die();

global $post;
$type = get_post_meta( $post->ID, '_'.DTM_PREFIX.'type', true );
$inputs = array(
	array(
		'id'    => 'main_type',
		'name'  => 'main_type',
		'type'  => 'select',
		'class' => 'button',
		'default'=> get_post_meta( $post->ID, '_'.DTM_PREFIX.'main_type', true ),
		'options' => array(
			'carousel'    => 'Карусель',
			'slider'      => 'Слайдер',
			'sync-slider' => 'Синх. слайдер',
			'slider-3d'   => '3D слайдер',
			'gallery'     => 'Галерея',
			'query'       => 'Запрос'
				// Преимущества
			),
		),
	array(
		'id'    => 'type',
		'name'  => 'type',
		'type'  => 'select',
		'class' => 'carousel slider sync-slider query button hidden',
		'default' => $type,
		'disabled' => 'disable',
		'options' => array(
			'owl-carousel' => 'Сова карусель',
			'slick-slider' => 'Скользкий слайдер'
			)
		),
	array(
		'id'    => 'type',
		'name'  => 'type',
		'type'  => 'select',
		'class' => 'gallery button hidden',
		'default' => $type,
		'disabled' => 'disable',
		'options' => array(
			'fancybox' => 'Фантастическая коробка',
			)
		),
	array(
		'id'    => 'type',
		'name'  => 'type',
		'type'  => 'select',
		'class' => 'slider-3d button hidden',
		'default' => $type,
		'disabled' => 'disable',
		'options' => array(
			'cloud9carousel' => 'Облачная карусель',
			)
		),
	);

return $inputs;