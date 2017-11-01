<?php

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

/**
 * mblock_html > render_$type > render_attachments
 */
class MediaBlock {
    public $id;
    public $post;
    public $atts;

    // default type
    public $main_type = 'carousel';
    public $sub_type  = 'slick';

    public $settings = array();

    public $attach_ids = array();
    public $default_att_width = 1110;
    public $default_att_height = 450;
    public $default_att_size = 'medium';

    public $default_columns = 4;

    // use for double block
    public $double = false;

    function __construct( $post_id = false, $atts = array() )
    {
        $this->atts = wp_parse_args( $atts, array(
            'status' => 'publish',
            'tag_title' => 'h3',
            ) );

        $this->id = $post_id;
        $this->post = get_post( $this->id );

        $this->main_type = self::meta_field( $this->id, 'main_type' );
        $this->sub_type  = self::meta_field( $this->id, 'type' );
    }

    /********************************* Assets *********************************/
    function initialize_scripts()
    {
        $trigger = "#mediablock-{$this->id}.{$this->main_type}";

        if( $this->main_type != 'gallery' ) {
            MB\JQScript::init($trigger, 'removeClass', 'row');
            MB\JQScript::init($trigger . ' .item', 'attr', 'class", "item');
        }

        /***************************** Initialize *****************************/
        if( isset($this->settings['not_initialize']) ) {
            return false;
        }

        if( $this->main_type != 'gallery' ) {
            if( $this->main_type != 'sync-slider' ){
                $init = apply_filters('type_to_lib', $this->sub_type);
                $filename = dash_to_underscore($this->main_type);
                $init_settings = $this->settings_from_file($this->id, $this->sub_type, $filename);

                MB\JQScript::init($trigger, $init, $init_settings );
            }
            else {
                MB\JQScript::custom($this->double_script());
            }
        }
        else {
        }

        $script = '';
        $is_lazy = isset($this->settings['lazyLoad']) && $this->settings['lazyLoad'] != 'false';
        $is_masonry = isset($this->settings['masonry']) && $this->settings['masonry'] != 'false';

        $id = $this->id;

        if( $is_lazy || $is_masonry ){
            $script  = "var \$container{$id} = \$('{$trigger}');";
            $script .= "var itemSelector{$id} = '{$trigger} > .item';";

            if( $is_masonry ) {
                $script .= '$container'.$id.'.masonry({ itemSelector: itemSelector'.$id.' });';
            }

            if($is_lazy && $is_masonry) {
                $script .= '$container'.$id.'.imagesLoaded(function(){';
            }

            if($is_lazy) {
                $script .= '
                $(itemSelector'.$id.' +" img").addClass("not-loaded");
                $(itemSelector'.$id.' +" img.not-loaded").lazyload({
                    effect: "fadeIn",
                    load: function() {
                        $(this).removeClass("not-loaded");
                        $(this).addClass("loaded");

                        if( typeof $container'.$id.'.masonry !== "undefined" ) {
                            $container'.$id.'.masonry("reload");
                        }
                    }
                });
                $(itemSelector'.$id.' +" img.not-loaded").trigger("scroll");';
            }

            if($is_lazy && $is_masonry) {
                $script .= '});';
            }

            MB\JQScript::custom($script);
        }
    }

    /**
     * Load Assets
     * @todo : previosly register assets
     * @todo : add style for id ( from scss )
     */
    function load_assets()
    {
        if( $asset = self::pre_register_assets( $this->sub_type ) ){
            if( isset($asset['js']) ) {
                wp_enqueue_script( $this->sub_type );
            }

            if( isset($asset['style']) ) {
                wp_enqueue_style ( $this->sub_type );
            }

            if( isset($asset['theme']) ) {
                wp_enqueue_style ( $this->sub_type . '-theme' );
            }
        }

        if( !empty($this->settings['lazyLoad']) ) {
            wp_enqueue_script( 'lazyLoad' );
        }

        if( isset($this->settings['masonry']) && $this->settings['masonry'] != 'false' ) {
            wp_enqueue_script( 'jquery-masonry' );
        }
    }

    /**
     * set attachment ids to settings
     */
    function set_attachment_ids()
    {
        $query_options = self::meta_field($this->id, 'query');

        if(!empty($query_options['enable'])){
            $query = new \WP_Query( array(
                'post_type' => $query_options['type'],
                'posts_per_page' => $query_options['qty'],
                'tax_query' => array( array(
                    'taxonomy' => $query_options['tax'],
                    'terms'    => $query_options['term'],
                    ), ),
                'order'   => $query_options['sort'],
                ) );

            $attachments = array();
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) { $query->the_post();
                    $attachments[] = get_post_thumbnail_id( get_the_id() );
                }
            }
            wp_reset_postdata();

            $this->attachments = $attachments;
        }
        else {
            $this->attachments = explode(',', self::meta_field($this->id, 'media_imgs') );
        }

        if( ! is_array($this->attachments) || sizeof($this->attachments) < 1 ) {
            return false;
        }

        return true;
    }

    protected function parse_items_size( $width, $height, $items_size )
    {
        if( $width || $height ) {
            if( ! $width ) {
                $width = apply_filters( 'default_att_width', $this->default_att_width);
            }

            if( ! $height ) {
                $height = apply_filters( 'default_att_height', $this->default_att_height);
            }

            $items_size = array( $width, $height );
        }
        else {
            $items_size = apply_filters('default_att_size', $this->default_att_size);
        }

        reset($items_size);
        $height = current($items_size);

        if( $this->main_type == 'carousel-3d' ) {
            echo "<style> #mediablock-{$this->id} { height:{$height}px; } </style>";
        }

        return $items_size;
    }

    /********************************* Output *********************************/
    public function render()
    {
        if( ! $this->post instanceof WP_Post ) {
            return;
        }

        if($this->post->post_status !== $this->atts['status'] ) {
            return false;
        }

        if( false === $this->set_attachment_ids() ) {
            return;
        }

        $this->settings = $this->settings_from_file( $this->id, $this->main_type );
        $settings = wp_parse_args( $this->settings_from_file( $this->id, $this->main_type ), array(
            'exclude_assets' => false,
            'exclude_styles' => false,
            'width' => false,
            'height' => false,
            'items_size' => false,
            'columns' => apply_filters( MB_PREF.'default_columns', $this->default_columns, $this->main_type ),
            'no_paddings' => false,
            'image_captions' => false,
            'lightbox' => false,
            ) );

        extract( $settings );

        // if( ! $exclude_styles ) {
        //     $this->load_style();
        // }

        if( ! $exclude_assets ) {
            $this->load_assets();
            $this->initialize_scripts();
        }

        $settings['items_size'] = $this->parse_items_size( $width, $height, $items_size );

        $result = array();
        $result[] = "<section id='mblock-{$this->id}'>";

        if( self::meta_field( $this->id, '_show_title' ) && $this->post->post_title !== '' ) {
            sprintf('<%1$s>%2$s</%1$s>',
                $this->atts['tag_title'],
                $this->post->post_title
                );
        }

        if( $this->post->post_excerpt !== '' ) {
            $result[] = '<div class="excerpt">' .apply_filters('the_content', $this->post->post_excerpt). "</div>";
        }

        $result[] = $this->render_attachments( $settings );

        $result[] = '</section>';

        return implode("\n", $result);
    }

    protected function render_attachments( $settings )
    {
        // need for gallery or "no js"
        $item_class = get_column_class( $settings['columns'] );
        if( $settings['no_paddings'] ) $item_class .= ' no-paddings';

        // .fancybox usually use for modal trigger
        $class_type = ($this->sub_type == 'fancybox') ? 'fancy' : $this->sub_type;

        $item_wrap = array(
          "<div id='mediablock-{$this->id}' class='media-block row {$this->main_type} {$class_type}'>",
          "</div>");
        $item = array("<div class='item {$item_class}'>", "</div>");

        $result = array();
        $result[] = $item_wrap[0];
        foreach ($this->attachments as $i => $attachment) {
            $att = get_post( $attachment );

            if( $settings['image_captions'] ) {
                /** Get Caption */
                $caption = sprintf('<div class="caption">%s %s</div>',
                    ($att->post_excerpt !== '') ? "<h4>" . $att->post_excerpt . "</h4>" : '',
                    ($att->post_content !== '') ? apply_filters( 'the_content', $att->post_content ) : ''
                    );
            }

            /** Get Link */
            $link = array('', '');
            if( $settings['lightbox'] && ($settings['columns'] == 1 || ! $this->double) ) {
                $link = array("<a rel='group-{$this->id}' href='{$att->guid}' class='{$settings['lightbox']}'>", "</a>");
            }
            elseif( $metalink = esc_attr( get_post_meta( $attachment, 'mb_link', true ) ) ) {
                if( preg_match("/\[link id=\"([0-9]{1,40})\"\]/i", $metalink, $output) && isset($output[1]) ) {
                    $url = get_permalink( (int)$output[1] );
                }
                else {
                    $url = $metalink;
                }

                $link = array("<a href='{$url}' class='mediablock-link'>", "</a>");
            }

            $image = wp_get_attachment_image( $attachment, $settings['items_size'] );
            if(!empty( $this->settings['lazyLoad']) && $i > 3 ) {
                $image = str_replace(' src', ' data-original', $image);
            }

            /** Set Template */
            $result[] = $item[0];
            $result[] = '   '.$link[0];
            $result[] = '     <div class="wrap">';

            if( $settings['image_captions'] == 'top' )
                $result[] = '       ' . $caption;

            $result[] = '       ' . $image;

            if( $settings['image_captions'] == 'bottom' )
                $result[] = '       ' . $caption;

            $result[] = '     </div>';
            $result[] = '   '.$link[1];
            $result[] = $item[1];
        }
        $result[] = $item_wrap[1];

        return implode("\n", $result);
    }
}

 /**
   * @todo : get variables from class props
   */
  /*
  function render_sync_slider(){
    $this->main_type = 'sync-slider';

    $out = $this->render_slider();
    $out .= $this->render_carousel();

    return $out;
  }

  function double_script(){
    ob_start();

    $php_to_js_params = apply_filters( 'array_options_before_view',
      $this->settings_from_file($this->id, $this->sub_type, 'sync-slider') );

    $o = $this->settings_from_file($this->id, 'sync-slider');
    extract($o);
    ?>
            $(function(){
              var sync1Selector = "#mediablock-<?php echo $this->id; ?>";
              var $sync1 = $(sync1Selector);
              var sync2Selector = sync1Selector +" + "+ sync1Selector;
              var $sync2 = $(sync2Selector);

              $(sync1Selector).removeClass('row');
              $(sync1Selector + ' .item').attr('class', 'item');
              $(sync2Selector).removeClass('row');
              $(sync2Selector + ' .item').attr('class', 'item');
              <?php
              switch ($this->sub_type) {
                case 'slick':
                ?>
                $(sync1Selector).slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                fade: true,
                asNavFor: sync2Selector
              });
              $(sync2Selector).slick({
              slidesToShow: 3,
              slidesToScroll: 1,
              asNavFor: sync1Selector,
              dots: true,
              centerMode: true,
              focusOnSelect: true
            });
              <?php
              break;

              default:
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
              var activeClass = "inside";

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
<?php
break;
}
?>
});
<?php
return ob_get_clean();
}
// */