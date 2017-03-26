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
        return "<script type='text/javascript'><!-- \n jQuery(document).ready(function($) { \n" . $data . "\n });\n --></script>";
    }
    add_filter( 'jQuery_onload_wrapper', 'JScript_jQuery_onload_wrapper', 10, 1 );
}

if(! class_exists('JScript') ){
    class JScript // extends AnotherClass
    {
        protected static $selector;
        protected static $script_name;
        protected static $options;

        // function __construct(){}
        public static function init( $selector, $script_name, $options = '', $open_keys = false ){ // has html
            $selector = sanitize_text_field( $selector );
            $script_name = sanitize_text_field( $script_name );
            
            if( is_array($options) )
                $options = apply_filters( 'jscript_php_to_json', $options );

            if( $open_keys ){
                $options = str_replace('{"', '{', $options);
                $options = str_replace(',"', ',', $options);
                $options = str_replace('":', ':', $options);
            }

            self::$selector = $selector;
            self::$script_name = $script_name; 
            self::$options = $options;
            add_action( 'wp_footer', array('JScript', 'initialize'), 99 );
        }

        static function initialize(){
            $script = "$('".self::$selector."').".self::$script_name."(".self::$options.");";
            echo apply_filters( 'jQuery_onload_wrapper', $script );
        }
    }
}