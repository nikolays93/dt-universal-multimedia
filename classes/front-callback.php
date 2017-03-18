<?php

/**
 * Media Output
 */
class MediaOutput extends DT_MediaBlocks
{
	function __construct(){

		$this->setup_filters();

		add_shortcode( 'mblock', array($this, 'media_sc') );
	}

	/**
     * Filters
     */
    function setup_filters(){
      add_filter( 'array_options_before_view',  array($this, 'owl_nextprev'), 10, 1 );
      add_filter( 'json_change_values',       array($this, 'bool_to_str'), 10, 1 );
    }

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
    function bool_to_str( $script_options ){
    	$script_options = str_replace('"on"', 'true', $script_options);
    	$script_options = str_replace('"false"', 'false', $script_options);
    	return $script_options;
    }

	function load_assets($type, $template, $style_path){
    	$affix = (is_wp_debug()) ? '' : '.min';

    	if($type == 'owl-carousel'){
    		wp_enqueue_script( 'owl-carousel', DT_MULTIMEDIA_ASSETS_URL.'owl-carousel/owl.carousel'.$affix.'.js', array('jquery'), self::VERSION, true );
    		wp_enqueue_style( 'owl-carousel-core', DT_MULTIMEDIA_ASSETS_URL.'owl-carousel/owl.carousel.css', array(), self::VERSION );
    	}


    	if( $template === "" )
    		return false;

    	switch ($template) {
    		case 'custom':
    		if( $style_path !== false )
    			wp_enqueue_style( $type.'-theme', get_template_directory_uri().'/'.$style_path.'.css', array(), self::VERSION );
    		break;

    		case 'default':
    		$template = 'owl.theme';
    		break;

    		default:
    		$template = 'default.theme';
    		break;
    	}
    	wp_enqueue_style( $type.'-theme', DT_MULTIMEDIA_ASSETS_URL.$type.'/'.$template.'.css',
    		array(), self::VERSION );
    }

    /**
     * $type str sub_type ( fancy, owl, slick.. )
     * $mblock WP_Post 
     * $attachments array() attachemt ids
     */
    function render_carousel( $type, $mblock, $attachments, $non_script = false ){
    	$result = array();
    	$id = $mblock->ID;
    	
    	// parse type[0] settings
    	extract($this->block_meta_settings($id, 'carousel'));
    	$class = (isset($lightbox_class)) ? $lightbox_class : 'fancybox';

        // load assets
    	$template = isset($template) ? $template : false;
    	$style_path = isset($style_path) ? $style_path : false;
    	$this->load_assets($type, $template, $style_path);

        // parse type[1] settings
    	$metas_arr = apply_filters( 'array_options_before_view',
    		$this->block_meta_settings($id, $type) );

    	$script_options = apply_filters( 'json_change_values', cpJsonStr( json_encode($metas_arr) ) );

    	$item = array("<div class='item'>", "</div>");
    	$slider_wrap = array("<div id='mediablock-{$id}' class='media-block carousel {$type}'>", "</div>");
    	$result[] = $slider_wrap[0];
    	switch ( $type ) {
    		case 'owl-carousel':
    		foreach ($attachments as $attachment) {
    			$href = wp_get_attachment_url( $attachment );
    			$link =  (isset($lightbox_links)) ?
    			array('<a rel="group-'.$id.'" href="'.$href.'" class="'.$class.'">', '</a>') : array('', '');

    			$caption = (isset($image_captions)) ? '<p id="caption">'.get_the_excerpt( $attachment ).'</p>' : '';

    			$result[] = $item[0];
    			$result[] = '   '.$link[0];
                $result[] = '       '. wp_get_attachment_image( $attachment, $image_size ); //,null,array(attrs)
                $result[] = '       '.$caption;
                $result[] = '   '.$link[1];
                $result[] = $item[1];
            }
                // $image_meta = wp_get_attachment_metadata( $attachment );

            $script   = array("<script type='text/javascript'>");
            $script[] = " jQuery(function($){";
            $script[] = "     $('#mediablock-".$id."').owlCarousel(".$script_options.");";
            $script[] = " });";
            $script[] = "</script>";
            break;
        }
        $result[] = $slider_wrap[1];
        if(! $non_script )
        	$result[] = implode("\n", $script);
        return implode("\n", $result);
    }
    function render_slider( $type, $mblock, $attachments, $non_script = false ){

    }
    function render_sync_slider( $type, $mblock, $attachments ){
    	$this->render_carousel($type, $mblock, $attachments, true);
    	$this->render_slider(  $type, $mblock, $attachments, true);
	//script
    }
    function render_gallery( $type, $mblock, $attachments, $non_script = false ){
    	echo "<div class='row'>";
    	foreach ($attachments as $attachment_id) {
    		echo '<div class="col-3">';
    		echo wp_get_attachment_image( $attachment_id );
    		echo '</div>';
    	}
    	echo "</div>";
    }

    /**
     * Shortcode
     */
    function media_sc( $atts ) {
    	$result = array();
    	$atts = shortcode_atts( array('id' => false), $atts );
    	$id = intval($atts['id']);

    	$mblock = get_post( $id );
    	if('publish' !== $mblock->post_status){
    		if(is_wp_debug()) echo 'Блок не опубликован';
    		return;
    	}

    	$type = $this->get_media_type($id);
    	if(sizeof($type) == 0)
    		return false;

    	$attachments = explode(',', $this->block_meta_field($id, 'media_imgs') );
    	if( sizeof($attachments) == 0 )
    		return (is_wp_debug()) ? 'Файлов не найдено' : false; 

    	$result[] = '<section id="mblock">';
    	if($this->block_meta_field( $id, 'show_title' ) && $mblock->post_title != '')
    		$result[] = '<h3>'. $mblock->post_title .'</h3>';
    	if($mblock->post_excerpt != '')
    		$result[] = '<div class="excerpt">'.apply_filters('the_content', $mblock->post_excerpt)."</div>";

    	switch ( $type[0] ) {
        	default: // carousel, slider
        	$type[0] = str_replace("-", "_", $type[0]);
        	$func = 'render_' . $type[0];
        	$result[] = $this->$func($type[1], $mblock, $attachments);
        	break;
        } // switch
        $result[] = '</section>';
        return implode("\n", $result);
    }
}