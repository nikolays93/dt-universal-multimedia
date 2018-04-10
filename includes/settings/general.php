<?php

namespace NikolayS93\MediaBlocks;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

global $post;

$type = wp_parse_args( get_post_meta( $post->ID, 'mtypes', true ), array(
	'grid_type' => '',
	'lib_type'  => '',
) );


$list = Utils::get_library_list();

$options = array();
foreach ($list as $key => $item) {
	$options[ $item->label ] = array();
	if( isset($item->child) && is_array($item->child) ) {
		foreach ($item->child as $handle => $child) {
			$options[ $item->label ][ $handle ] = $item->label . ': ' . $child->label;
		}
	}
}

$inputs = array(
	'id' => '_type',
	'type' => 'select',
	'input_class' => 'button',
	'options' => $options,
);

return $inputs;
