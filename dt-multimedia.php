<?php
/*
 * DT_MultiMedia
 *
 * Plugin Name: MultiMedia
 * Version:     0.3
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function cpJsonStr($str){
    $str = preg_replace_callback('/\\\\u([a-f0-9]{4})/i', create_function('$m', 'return chr(hexdec($m[1])-1072+224);'), $str);
    return iconv('cp1251', 'utf-8', $str);
}

class DT_MultiMedia
{
	public $version = 0.3;
	
	function __construct()
	{
        $this->define_constants();
        $this->add_required_classes();
        $this->setup_actions();
        // $this->setup_filters();
        $this->setup_shortcode();
        

        if(is_admin()){
            if(class_exists('isAdminView'))
                new isAdminView();
        }
	}
    /**
     * SETUP global methods
     */
    function is_debug(){
        if( WP_DEBUG ){
            if( defined(WP_DEBUG_DISPLAY) && WP_DEBUG_DISPLAY){
                return true;
            }
        }
        return false;
    }
	private function define_constants() {
        define( 'DT_PREFIX', 'dtmm_');
        define( 'DT_MULTIMEDIA_MAIN_TYPE', 'multimedia-base');

        // define( 'DT_MULTIMEDIA_VERSION',    $this->version );
        define( 'DT_MULTIMEDIA_BASE_URL',   trailingslashit( plugins_url( 'DT-universal-multimedia' ) ) );
        define( 'DT_MULTIMEDIA_ASSETS_URL', trailingslashit( DT_MULTIMEDIA_BASE_URL . 'assets' ) );
		define( 'DT_MULTIMEDIA_PATH',       plugin_dir_path( __FILE__ ) );
	}
	private function required_classes(){
		return array(
            'isAdminView'             => DT_MULTIMEDIA_PATH . '/classes/admin-edit.php',
        );
	}
	private function add_required_classes(){
		foreach ( $this->required_classes() as $id => $path ) {
			if ( is_readable( $path ) && ! class_exists( $id ) ) {
				require_once( $path );
			}
            else {
                add_action( 'admin_notices', function(){
                    echo '
                    <div id="message" class="error notice is-dismissible">
                        <p>Обнаружен поврежденный класс!</p>
                    </div>';
                });
            }
		}
	}
    function get_settings($type=false){
        if($type === false)
            return;

        require_once(DT_MULTIMEDIA_PATH.'/settings/'.$type.'.php');
        if(function_exists('get_dt_multimedia_settings'))
            return get_dt_multimedia_settings();

        return array();
    }
    /**
     * Media output
     */
    protected function get_media_type($media_id){
        return get_post_meta( $media_id, '_'.DT_PREFIX.'type', true );
    }  
    protected function get_options($post_id=false, $type=false, $update=false){
        if(!$type || !$post_id)
            return false;

        $result = array();
        $settings = $this->get_settings($type);
        $params = array_keys($settings);

        if($update){
            update_post_meta( $post_id, '_'.DT_PREFIX.'type', $type );
            $val = $_POST;
        }
        else {
            $val = get_post_meta($post_id, '_'.DT_PREFIX.'options', true);
        }

        foreach ($params as $param){
            $default = isset($settings[$param]['default']) ? $settings[$param]['default'] : '';

            if(isset($val[$param]) && $val[$param] != $default){
                if($val[$param] == '') $val[$param] = 'false';
                $result[$param] = $val[$param];
            }
        }
        
        // file_put_contents(DT_MULTIMEDIA_PATH.'/debug.log', print_r($result, 1) );
        if($update)
            update_post_meta( $post_id, '_'.DT_PREFIX.'options', $result );
        else
            return $result;
    }

    // render shortcode
    function media_sc( $atts ) {
        $atts = shortcode_atts( array(
            'id' => false
            ), $atts );
        $id = intval($atts['id']);
        $type = $this->get_media_type($id);

        $attachments = get_post_meta( $id, DT_PREFIX.'media_imgs', true );
        $attachments = explode(',', $attachments);
        if( $attachments[0] == '' )
            if($this->is_debug()) return 'Файлов не найдено'; else return false; 

        switch ( $type ) {
            case 'owl-carousel':
                // load assets
                $affix = (WP_DEBUG) ? '' : '.min';
                wp_enqueue_script( 'owl-carousel', DT_MULTIMEDIA_ASSETS_URL.'/owl-carousel/owl.carousel'.$affix.'.js', array('jquery'), $this->version );

                wp_enqueue_style( 'owl-carousel-core', DT_MULTIMEDIA_ASSETS_URL.'/owl-carousel/owl.carousel.css', array(), $this->version );
                wp_enqueue_style( 'owl-carousel-theme', DT_MULTIMEDIA_ASSETS_URL.'/owl-carousel/owl.theme.css', array(), $this->version );

                $metas_arr = $this->get_options($id, $type, false );

                $metas_arr = apply_filters( 'array_options_from_view', $metas_arr );
                var_dump($metas_arr);

                $metas = cpJsonStr( json_encode($metas_arr) );
                $metas = str_replace('"on"', 'true', $metas);
                $metas = str_replace('"false"', 'false', $metas);

                $html = array("<div id='slider'>");
                foreach ($attachments as $attachment) {
                    $html[] = '   <div>' . wp_get_attachment_image( $attachment ) . '</div>';
                }
                $html[] = "</div>";

                $html[] = "<script type='text/javascript'>";
                $html[] = " jQuery(function($){";
                $html[] = "     $('#slider').owlCarousel(".$metas.");";
                $html[] = " });";
                $html[] = "</script>";
                break;
            
            default:
                # code...
                break;
        }
        
        return implode("\n", $html);
    }
    private function setup_shortcode(){

        add_shortcode( 'media', array($this, 'media_sc') );
    }

    /**
     * Set global actions
     */
	function register_post_types(){
		register_post_type( DT_MULTIMEDIA_MAIN_TYPE, array(
                'query_var' => false,
                'rewrite' => false,
                'public' => false,
                'exclude_from_search' => true,
                'publicly_queryable' => false,
                'show_in_nav_menus' => false,
                'show_ui' => true,
                'supports' => array('title', 'custom-fields'),
                'labels' => array(
                    'name' => 'МультиМедиа'
                )
            )
        );
	}
    function setup_actions(){

        add_action('init', array($this, 'register_post_types'));
    }
}
new DT_MultiMedia();

function owl_nextprev( $metas_arr ){
    if(isset($metas_arr['navigationTextNext']) || isset($metas_arr['navigationTextPrev'])){
        if(isset($metas_arr['navigationTextPrev'])){
            $prev = $metas_arr['navigationTextPrev'];
            unset($metas_arr['navigationTextPrev']);
        }
        else {
            $prev = 'prev';
        }
        if(isset($metas_arr['navigationTextNext'])){
            $next = $metas_arr['navigationTextNext'];
            unset($metas_arr['navigationTextNext']);
        }
        else {
            $next = 'next';
        }

        $metas_arr['navigationText'] = array($prev, $next);
    }
    return $metas_arr;
}
add_filter( 'array_options_from_view', 'owl_nextprev', 10 );

// function rewrite_flush() {
//     DT_MultiMedia::register_post_types();
//     flush_rewrite_rules();
// }
// register_activation_hook( __FILE__, 'rewrite_flush' );