<?php

function pre_register_assets( $type = false ){
    $affix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
    $url = MBLOCKS_ASSETS;

    $assets = array(
      'owl-carousel' => array(
        'js' => 'owl.carousel'.$affix.'.js',
        'style' => 'owl.carousel'.$affix.'.css',
        'theme' => 'owl.theme.css',
        'ver' => '1.3.3',
        ),
      'slick' => array(
        'js' => 'slick.js',
        'style' => 'slick.css',
        'theme' => 'slick-theme.css',
        'ver' => '1.6.0',
        ),
      'cloud9carousel' => array(
        'js' => 'jquery.cloud9carousel'.$affix.'.js',
        'ver' => '2.1.0',
        ),
      'waterwheelCarousel' => array(
        'js' => 'jquery.waterwheelCarousel'.$affix.'.js',
        'ver' => '2.3.0',
        ),
      'lazyLoad' => array(
        'js' => 'lazyLoad'.$affix.'.js',
        'ver' => '1.9.0',
        ),
      );

    if( $type )
      return isset($assets[$type]) ? $assets[$type] : false;

    foreach ($assets as $type => $asset) {
      if( !empty($asset['js']) )
        wp_register_script( $type, $url .'/'. $type .'/'. $asset['js'], array('jquery'), $asset['ver'], true );
      if( isset($asset['style']) )
        wp_register_style( $type, $url .'/'. $type .'/'. $asset['style'], array(), $asset['ver'], 'all' );
      if( isset($asset['theme']) )
        wp_register_style( $type.'-theme', $url .'/'. $type .'/'. $asset['theme'],  array(), $asset['ver'], 'all' );
    }

    return true;
  }