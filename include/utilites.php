<?php

if(!function_exists('_isset_default')){
  function _isset_default(&$var, $default, $unset = false){
    $result = $var = isset($var) ? $var : $default;
    if($unset)
      $var = FALSE;
    return $result;
  }
}
if( !function_exists('_isset_false') ){
  function _isset_false(&$var, $unset = false){ return _isset_default( $var, false, $unset ); }
}
if( !function_exists('_isset_false') ){
  function _isset_empty(&$var, $unset = false){ return _isset_default( $var, '', $unset ); }
}


/**
 * Получить стандартные классы ячейки bootstrap сетки
 */
if( ! function_exists('get_column_class') ){
  function get_column_class( $columns_count="4", $non_responsive=false ){
    $xs = ( $need_xs = apply_filters('bootstrap3_columns', false) ) ? '-xs' : '';
    switch ($columns_count) {
        case '1': $col = 'col-12'; break;
        case '2': $col = (!$non_responsive) ? 'col'.$xs.'-6 col-sm-6 col-md-6 col-lg-6' : 'col'.$xs.'-6'; break;
        case '3': $col = (!$non_responsive) ? 'col'.$xs.'-12 col-sm-6 col-md-4 col-lg-4' : 'col'.$xs.'-4'; break;
        case '4': $col = (!$non_responsive) ? 'col'.$xs.'-6 col-sm-4 col-md-3 col-lg-3' : 'col'.$xs.'-3'; break;
        case '5': $col = (!$non_responsive) ? 'col'.$xs.'-12 col-sm-6 col-md-2-4 col-lg-2-4' : 'col'.$xs.'-2-4'; break; // be careful
        case '6': $col = (!$non_responsive) ? 'col'.$xs.'-6 col-sm-4 col-md-2 col-lg-2' : 'col'.$xs.'-2'; break;
        case '12': $col= (!$non_responsive) ? 'col'.$xs.'-4 col-sm-3 col-md-1 col-lg-1' : 'col'.$xs.'-1'; break;

        default: $col = false; break;
    }
    return $col;
  }
}