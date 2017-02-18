<?php
/*
 * DT_MultiMedia
 *
 * Plugin Name: MultiMedia
 * Version:     0.1
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
* 
*/
class DT_MultiMedia
{
	public $version = 0.1;
	
	function __construct()
	{
		$this->define_constants();
        $this->add_required_classes();
        // $this->setup_actions();
        // $this->setup_filters();
        // $this->setup_shortcode();
        $this->register_post_types();

        if(is_admin()){
            if(class_exists('isAdminView'))
                new isAdminView();
        }
	}

	private function define_constants() {
        define( 'DT_MULTIMEDIA_MAIN_TYPE', 'multimedia-base');
        // define( 'DT_MULTIMEDIA_VERSION',    $this->version );
        define( 'DT_MULTIMEDIA_BASE_URL',   trailingslashit( plugins_url( 'DT-universal-multimedia' ) ) );
        define( 'DT_MULTIMEDIA_ASSETS_URL', trailingslashit( DT_MULTIMEDIA_BASE_URL . 'assets' ) );
		define( 'DT_MULTIMEDIA_PATH',       plugin_dir_path( __FILE__ ) );
	}

	private function required_classes(){
		return array(
            'admin-edit'             => DT_MULTIMEDIA_PATH . '/classes/admin-edit.php',
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
}

new DT_MultiMedia();