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
    
    function render_attachments( $main_type, $type, $mblock, $attachments, $double = false ){
        $result = array();
        
        // Options
        $id = (int)$mblock->ID;
        $o = $this->settings_from_file( $id, $main_type );
        if( $o )
        extract($o);
        // lightbox
        // items_size
        // width
        // height
        // block_template
        // style_path
        // image_captions
        
        if($main_type == 'slider') $columns = 1;
        _isset_default( $columns, 4 );
        
        if( !isset($items_size) || !$items_size ){
            if( isset($width) || isset($height) ){
                _isset_default($width, 1110);
                _isset_default($height, 450);
                
                $items_size = array( $width, $height );
            }
            else {
                $items_size = 'medium';
            }
        }
        
        $class_type = ($type == 'fancybox') ? 'fancy' : $type;
        $item_wrap = array(
            "<div id='mediablock-{$id}' class='media-block row {$main_type} {$class_type}'>", "</div>");
        $item_class = $this->get_column_class( $columns );
        $item = array("<div class='item {$item_class}'>", "</div>");

        // Load assets
        if( isset($block_template) ){
            _isset_false($style_path);
            $this->load_assets($type, $block_template, $style_path);
        }

        $result[] = $item_wrap[0];
        foreach ($attachments as $attachment) {
            $att = get_post( $attachment );
            
            $caption = ( isset($image_captions) ) ?
                '<p id="caption">'.apply_filters( 'the_content', $att->post_excerpt ).'</p>' : '';

            if( isset($lightbox) && !$double ){
                $href = $att->guid; // wp_get_attachment_url( $attachment )
                $link = array('<a rel="group-'.$id.'" href="'.$href.'" class="'.$lightbox.'">', '</a>');
            }
            else {
                $link = array('', '');
            }

            $result[] = $item[0];
            $result[] = '   '.$link[0];
            $result[] = '   '. wp_get_attachment_image( $attachment, $items_size ); //,null,array(attrs)
            $result[] = '   '.$caption;
            $result[] = '   '.$link[1];
            $result[] = $item[1];
        }
        $result[] = $item_wrap[1];

        $out = implode("\n", $result);
        return $out;
    }
    /**
     * Render media blocks
     * 
     * @param  $type            string sub_type ( fancy, owl, slick.. )
     * @param  $mblock          WP_Post
     * @param  $attachments     string att ids split ","
     * @param  $not_init_script boolean print initialize script
     * @return $sync            html output
     */
    function render_carousel( $type, $mblock, $attachments, $double = false ){
    	$main_type = ($double) ? 'sync-slider' : 'carousel';
        $trigger = '#mediablock-'.$mblock->ID.'.'.$main_type;
    	$php_array_params = $this->settings_from_file( $mblock->ID, $type, $main_type );
		
        switch ( $type ) {
			case 'owl-carousel':
                $init = 'owlCarousel';
    			break;
            default:
                $init = $type;
                break;
        }

        if( ! $double ){
           jScript::init($trigger, 'removeClass', 'row');
           jScript::init($trigger . ' .item', 'attr', 'class", "item');
           jScript::init($trigger, $init, $php_array_params);
        }

        return $this->render_attachments('carousel', $type, $mblock, $attachments, $double);
    }
    function render_slider( $type, $mblock, $attachments, $double = false ){
        $main_type = ($double) ? 'sync-slider' : 'slider';
        $trigger = '#mediablock-'.$mblock->ID.'.'.$main_type;
        $php_array_params = $this->settings_from_file( $mblock->ID, $type, $main_type );

        switch ( $type ) {
            case 'owl-carousel':
                $init = 'owlCarousel';
                break;
            default:
                $init = $type;
                break;
        }

        if( !$double ){
            jScript::init($trigger, 'removeClass', 'row');
            jScript::init($trigger . ' .item', 'attr', 'class", "item');
            jScript::init($trigger, $init, $php_array_params);
        }
        

        return $this->render_attachments('slider', $type, $mblock, $attachments, $double);
    }
    function render_sync_slider( $type, $mblock, $attachments ){
        $out = $this->render_slider( $type, $mblock, $attachments, true );
    	$out .= $this->render_carousel( $type, $mblock, $attachments, true );

    	ob_start();

    	$php_to_js_params = apply_filters( 'array_options_before_view',
    		$this->settings_from_file($mblock->ID, $type, 'sync-slider') );

        $o = $this->settings_from_file($mblock->ID, 'sync-slider');
        extract($o);

    	$slider_params = array(
    		'singleItem' => "on",
    		"navigation" => _isset_default( $arrows, 'false' ),
    		"pagination" => "false",
    		"afterAction" => "%position%"
    		);

        if( isset($arr_prev) )
            $slider_params['navigationTextPrev'] = $arr_prev;
        if( isset($arr_next) )
            $slider_params['navigationTextNext'] = $arr_next;

        foreach ($php_to_js_params as $key => $value) {
    		if(in_array( $key, array("autoPlay", "stopOnHover", "rewindNav", "rewindSpeed", "autoHeight") ))
    			$slider_params[$key] = $value;
    	}
    	$php_to_js_params['afterInit'] = '%addFirstActive%';

        $slider_params = apply_filters( 'array_options_before_view', $slider_params );
    	$slider_script_options = apply_filters( 'jscript_php_to_json', $slider_params );
    	$script_options = apply_filters( 'jscript_php_to_json', $php_to_js_params );
    	?>
    	<script type="text/javascript">
			jq = jQuery.noConflict();
			jq(function( $ ) {
				//on.load
			  $(function(){
                var sync1Selector = "#mediablock-<?php echo $mblock->ID; ?>.slider";
			  	var $sync1 = $(sync1Selector);
			    var sync2Selector = "#mediablock-<?php echo $mblock->ID; ?>.carousel";
			    var $sync2 = $(sync2Selector);
			    var activeClass = "inside";

                $(sync1Selector).removeClass('row');
                $(sync1Selector + ' .item').attr('class', 'item');
                $(sync2Selector).removeClass('row');
                $(sync2Selector + ' .item').attr('class', 'item');

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
    function render_carousel_3d( $type, $mblock, $attachments ){

        echo "<style> .cloud9carousel { min-height:400px; } </style>";

        wp_enqueue_script( 'cloud9carousel', DT_MULTIMEDIA_ASSETS_URL.'cloud9carousel/jquery.cloud9carousel.js', array('jquery'), '', true );

        $init_settings = $this->settings_from_file($mblock->ID, $type, 'carousel_3d');
        JScript::init( "#mediablock-".$mblock->ID, 'Cloud9Carousel', $init_settings );

        return $this->render_attachments('slider-3d', $type, $mblock, $attachments);
    }
    function render_gallery( $type, $mblock, $attachments ){

        return $this->render_attachments('gallery', $type, $mblock, $attachments);
    }

    /**
     * Shortcode
     */
    function media_sc( $atts ) {
    	$result = array();
    	$atts = shortcode_atts( array('id' => false), $atts );
    	$id = intval($atts['id']);

        if( !$id )
            return false;

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
