<?php

/**
 * Media Output
 */
class MediaOutput extends DT_MediaBlocks
{
	function __construct(){
		add_shortcode( 'mblock', array($this, 'media_sc') );
	}

    /**
     * Получить стандартные классы ячейки bootstrap сетки
     */
    function get_column_class( $columns_count="4", $non_responsive=false ){
        switch ($columns_count) {
            case '1': $col = 'col-12'; break;
            case '2': $col = (!$non_responsive) ? 'col-6 col-sm-6 col-md-6 col-lg-6' : 'col-6'; break;
            case '3': $col = (!$non_responsive) ? 'col-12 col-sm-6 col-md-4 col-lg-4' : 'col-4'; break;
            case '4': $col = (!$non_responsive) ? 'col-6 col-sm-4 col-md-3 col-lg-3' : 'col-3'; break;
            case '5': $col = (!$non_responsive) ? 'col-12 col-sm-6 col-md-2-4 col-lg-2-4' : 'col-2-4'; break; // be careful
            case '6': $col = (!$non_responsive) ? 'col-6 col-sm-4 col-md-2 col-lg-2' : 'col-2'; break;
            case '12': $col= (!$non_responsive) ? 'col-4 col-sm-3 col-md-1 col-lg-1' : 'col-1'; break;

            default: $col = false; break;
        }
        return $col;
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
     * Render media blocks
     * 
     * @param  string sub_type ( fancy, owl, slick.. )
     * @param  WP_Post
     * @param  [type]
     * @param  boolean print initialize script
     * @return html output
     */
    function render_carousel( $type, $mblock, $attachments, $not_init_script = false, $sync = false ){
    	$result = array();
    	$id = $mblock->ID;
    	
    	// parse type[0] settings
    	$main_type = ($sync) ? 'sync-slider' : 'carousel';
    	$o = $this->settings_from_file($id, $main_type);
        extract($o);

        // load assets
        _isset_false($template);
        _isset_false($style_path);
    	$this->load_assets($type, $template, $style_path);

    	$slider_wrap = array("<div id='mediablock-{$id}' class='media-block carousel {$type}'>", "</div>");
    	$item = array("<div class='item'>", "</div>");
    	
    	$result[] = $slider_wrap[0];
    	foreach ($attachments as $attachment) {
    		$href = wp_get_attachment_url( $attachment );
    		$link =  (isset($lightbox) && !$sync) ?
    			array('<a rel="group-'.$id.'" href="'.$href.'" class="'.$lightbox.'">', '</a>') : array('', '');

    		$caption = (isset($image_captions)) ? '<p id="caption">'.get_the_excerpt( $attachment ).'</p>' : '';

    		$result[] = $item[0];
    		$result[] = '   '.$link[0];
            $result[] = '   '. wp_get_attachment_image( $attachment, $carousel_size ); //,null,array(attrs)
            $result[] = '   '.$caption;
            $result[] = '   '.$link[1];
            $result[] = $item[1];
        }

        $script = array();
        if(! $not_init_script ){
	        // parse sub_type settings
	    	$php_to_js_params = apply_filters( 'array_options_before_view',
	    		$this->settings_from_file($id, $type) );
	    	$script_options = apply_filters( 'json_change_values', cpJsonStr( json_encode($php_to_js_params) ) );
    		switch ( $type ) {
    			case 'owl-carousel':
	                // $image_meta = wp_get_attachment_metadata( $attachment );
	    			$script[]   = "<script type='text/javascript'>";
	    			$script[] = " jQuery(function($){";
	            	// todo: rewrite it
	            	$script[] = "     $('#mediablock-".$id."').owlCarousel(".$script_options.");";
	    			$script[] = " });";
	    			$script[] = "</script>";
	    			break;
    		}
    	}
        $result[] = $slider_wrap[1];

        $out = implode("\n", $result) . implode("\n", $script);
        return $out;
    }
    function render_slider( $type, $mblock, $attachments, $not_init_script = false, $sync = false ){
    	$result = array();
    	$id = $mblock->ID;
    	
    	// parse type[0] settings
    	$main_type = ($sync) ? 'sync-slider' : 'carousel';
    	$o = $this->settings_from_file($id, $main_type);
        extract($o);
        
        // load assets
        _isset_false( $template );
        _isset_false( $style_path );
    	$this->load_assets($type, $template, $style_path);

        if(! isset($sl_width) )
        	$sl_width = 1110;
        
        if(! isset($sl_height) )
        	$sl_height = 500;

    	$slider_wrap = array("<div id='mediablock-{$id}' class='media-block slider {$type}'>", "</div>");
    	$item = array("<div class='item'>", "</div>");
    	
    	$result[] = $slider_wrap[0];
    	foreach ($attachments as $attachment) {
    		$href = wp_get_attachment_url( $attachment );
    		$link =  ( isset($lightbox) ) ?
    			array('<a rel="group-'.$id.'" href="'.$href.'" class="'.$lightbox.'">', '</a>') : array('', '');

    		$caption = (isset($image_captions)) ? '<p id="caption">'.get_the_excerpt( $attachment ).'</p>' : '';

    		$result[] = $item[0];
    		$result[] = '   '.$link[0];
            $result[] = '       '. wp_get_attachment_image( $attachment, array((int)$sl_width, (int)$sl_height) ); //,null,array(attrs)
            $result[] = '       '.$caption;
            $result[] = '   '.$link[1];
            $result[] = $item[1];
        }

        $script = array();
        if(! $not_init_script ){
	        // parse sub_type settings
	    	$php_to_js_params = apply_filters( 'array_options_before_view',
	    		$this->settings_from_file($id, $type) );
	    	$script_options = apply_filters( 'json_change_values', cpJsonStr( json_encode($php_to_js_params) ) );
    		switch ( $type ) {
    			case 'owl-carousel':
	                // $image_meta = wp_get_attachment_metadata( $attachment );
	    			$script[]   = "<script type='text/javascript'>";
	    			$script[] = " jQuery(function($){";
	            	// todo: rewrite it
	            	//$script[] = "     $('#mediablock-".$id."').owlCarousel(".$script_options.");";
	    			$script[] = " });";
	    			$script[] = "</script>";
	    			break;
    		}
    	}
        $result[] = $slider_wrap[1];

        $out = implode("\n", $result) . implode("\n", $script);
        return $out;
    }
    function render_sync_slider( $type, $mblock, $attachments ){
        $out = $this->render_slider( $type, $mblock, $attachments, true, true );
    	$out .= $this->render_carousel( $type, $mblock, $attachments, true, true );

    	ob_start();

    	$php_to_js_params = apply_filters( 'array_options_before_view',
    		$this->settings_from_file($mblock->ID, $type, 'sync-slider') );

        $o = $this->settings_from_file($mblock->ID, 'sync-slider');
        extract($o);

    	$slider_params = array(
    		'singleItem' => "on",
    		"navigation" => _isset_default( $sl_arrows, 'false' ),
    		"pagination" => "false",
    		"afterAction" => "%position%"
    		);

        if( isset($sl_arr_prev) )
            $slider_params['navigationTextPrev'] = $sl_arr_prev;
        if( isset($sl_arr_next) )
            $slider_params['navigationTextNext'] = $sl_arr_next;

        foreach ($php_to_js_params as $key => $value) {
    		if(in_array( $key, array("autoPlay", "stopOnHover", "rewindNav", "rewindSpeed", "autoHeight") ))
    			$slider_params[$key] = $value;
    	}
    	$php_to_js_params['afterInit'] = '%addFirstActive%';

        $slider_params = apply_filters( 'array_options_before_view', $slider_params );
    	$slider_script_options = apply_filters( 'json_change_values', cpJsonStr( json_encode($slider_params) ) );
    	$script_options = apply_filters( 'json_change_values', cpJsonStr( json_encode($php_to_js_params) ) );
    	?>
    	<script type="text/javascript">
			jq = jQuery.noConflict();
			jq(function( $ ) {
				//on.load
			  $(function(){
			  	var $sync1 = $("#mediablock-<?php echo $mblock->ID; ?>.slider");
			    var sync2Selector = "#mediablock-<?php echo $mblock->ID; ?>.carousel";
			    var $sync2 = $(sync2Selector);
			    var activeClass = "inslide";

			  	function center(number){
			      var sync2visible = $sync2.data("owlCarousel").owl.visibleItems;
			      var num = number;
			      var found = false;
			      for(var i in sync2visible){
			        if(num === sync2visible[i]){
			          var found = true;
			        }
			      }

			      if(found===false){
			        if(num>sync2visible[sync2visible.length-1]){
			          $sync2.trigger("owl.goTo", num - sync2visible.length+2)
			        }else{
			          if(num - 1 === -1){
			            num = 0;
			          }
			          $sync2.trigger("owl.goTo", num);
			        }
			      } else if(num === sync2visible[sync2visible.length-1]){
			        $sync2.trigger("owl.goTo", sync2visible[1])
			      } else if(num === sync2visible[0]){
			        $sync2.trigger("owl.goTo", num-1)
			      }
			    }

			    function position(el){
			      var current = this.currentItem;
			      $(sync2Selector)
			        .find(".owl-item")
			        .removeClass(activeClass)
			        .eq(current)
			        .addClass(activeClass)
			      if( $sync2.data("owlCarousel") !== undefined ){
			        center(current)
			      }
			    }

			    function addFirstActive(el){
			    	el.find(".owl-item").eq(0).addClass(activeClass);
			    }

			    $sync1.owlCarousel(<?php echo $slider_script_options; ?>);

			    $sync2.owlCarousel(<?php echo $script_options; ?>);
			   
			    $(sync2Selector).on("click", ".owl-item", function(e){
			      if(!$(this).hasClass(activeClass)){
			        e.preventDefault();
			        $sync1.trigger("owl.goTo", $(this).data("owlItem") );
			      }
			    });

			  });
			});
		</script>
    	<?php
    	$out .= ob_get_clean();
    	return $out;
    }
    function render_slider_3d( $type, $mblock, $attachments, $not_init_script = false ){

        $o = $this->settings_from_file( $mblock->ID, 'slider-3d' );
        var_dump($o);
    }
    function render_gallery( $type, $mblock, $attachments, $not_init_script = false ){
        $o = $this->settings_from_file($mblock->ID, 'gallery');
        $php_to_js_params = apply_filters( 'array_options_before_view',
            $this->settings_from_file($mblock->ID, $type, 'gallery') );
        extract($o);

        _isset_default( $columns, 4 );
        _isset_default( $pr_width, 300 );
        _isset_default( $pr_height, 300 );
        _isset_default( $lb_class, 'zoom' );

        $column_class = $this->get_column_class($columns) . " mgallery {$type}ed";

        $result = array();
    	$result[] = "<div class='row'>";
    	foreach ($attachments as $attachment_id) {
            $thumb = wp_get_attachment_image_src( $attachment_id, array($pr_width, $pr_height) );
            $image = wp_get_attachment_image_src( $attachment_id, $full_size );

    		$result[] = '<div class="columns-'. $columns .' '. $column_class .'">';
            $result[] = "  <a href='".$image[0]."' rel='gallery-{$mblock->ID}' class='{$lb_class}'>";
            $result[] = "    <img src='".$thumb[0]."' alt=''>";
            $result[] = "  </a>";
            $result[] = "</div>";

    	}
    	$result[] = "</div>";

        $out = implode("\n", $result);
        return $out;
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
    		if( is_wp_debug() ) echo 'Блок не опубликован';
    		return;
    	}

    	if(! $attachments = explode(',', $this->meta_field($id, 'media_imgs') ) )
    		return ( is_wp_debug() ) ? 'Файлов не найдено' : false;
        
    	$result[] = '<section id="mblock">';

    	if( $this->meta_field( $id, 'show_title' ) && $mblock->post_title != '' )
    		$result[] = '<h3>'. $mblock->post_title .'</h3>';
        
    	if( $mblock->post_excerpt )
    		$result[] = '<div class="excerpt">' .apply_filters('the_content', $mblock->post_excerpt). "</div>";

        // Item output
    	$func = 'render_' . apply_filters( 'dash_to_underscore', $this->meta_field( $id, 'main_type' ) );
    	$result[] = $this->$func($this->meta_field( $id, 'type' ), $mblock, $attachments);

        $result[] = '</section>';
        return implode("\n", $result);
    }
}

function render_block(){

}

JSсript::init( '.navbar-brand', 'slideUp', array('key' => array('value', 'value2') ) );

// Get jscript options