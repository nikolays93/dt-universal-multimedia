<?php
if( !has_filter( 'jscript_php_to_json', 'json_encode' ) ){
  add_filter( 'jscript_php_to_json', 'json_encode', 10, 1 );
}

if(! function_exists('cpJsonStr') ){
    function cpJsonStr($str){
        $str = preg_replace_callback('/\\\\u([a-f0-9]{4})/i', create_function('$m', 'return chr(hexdec($m[1])-1072+224);'), $str);
        return iconv('cp1251', 'utf-8', $str);
    }
    add_filter( 'jscript_php_to_json', 'cpJsonStr', 15, 1 );
}
if(! function_exists('str_to_bool') ){
  function str_to_bool( $json ){
    $json = str_replace('"true"',  'true',  $json);
    $json = str_replace('"on"',  'true',  $json);
    $json = str_replace('"false"', 'false', $json);
    $json = str_replace('"off"', 'false', $json);
    return $json;
  }
  add_filter( 'jscript_php_to_json', 'str_to_bool', 20, 1 );
}
if(! function_exists('json_function_names') ){
  function json_function_names( $json ){
    $json = str_replace( '"%', '', $json );
    $json = str_replace( '%"', '', $json );
    return $json;
  }
  add_filter( 'jscript_php_to_json', 'json_function_names', 25, 1 );
}

if(! function_exists('JScript_jQuery_onload_wrapper') ){
    function JScript_jQuery_onload_wrapper($data){
        return "<script type='text/javascript'><!-- \n jQuery(document).ready(function($) { \n" . $data . " });\n --></script>\n";
    }
    add_filter( 'jQuery_onload_wrapper', 'JScript_jQuery_onload_wrapper', 10, 1 );
}

if(! class_exists('JScript') ){
    class JScript // extends AnotherClass
    {
        protected static $scripts = array();

        // function __construct(){}
        public static function init( $selector, $script_name, $options = '', $before = '', $after = '' ){ // has html
            $selector = sanitize_text_field( $selector );
            $script_name = sanitize_text_field( $script_name );
            
            if( is_array($options) ){
                $options = apply_filters( 'jscript_php_to_json', $options );
            }
            elseif($options) {
                $options = json_function_names('"'. $options .'"');
            }

            self::$scripts[] = array(
                'selector' => $selector,
                'init' => $script_name,
                'options' =>$options,
                'before' => $before,
                'after' => $after,
                 );


            add_action( 'wp_footer', array('JScript', 'initialize'), 99 );
        }

        static function initialize(){
            $output = '';
            foreach (self::$scripts as $script) {
                $script_code = "$('".$script['selector']."').".$script['init']."(".$script['options'].");";
                $output .= $script['before'] . $script_code . $script['after'] . "\n";
            }
            echo apply_filters( 'jQuery_onload_wrapper', $output );
        }
    }
}