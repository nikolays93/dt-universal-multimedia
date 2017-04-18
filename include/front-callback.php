<?php
namespace MB;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! has_filter( 'remove_cyrillic' )){
  add_filter( 'remove_cyrillic', 'MB\remove_cyrillic_filter', 10, 1 );
  function remove_cyrillic_filter($str){
    $pattern = "/[\x{0410}-\x{042F}]+.*[\x{0410}-\x{042F}]+/iu";
    $str = preg_replace( $pattern, "", $str );

    return $str;
  }
}

/**
 * Media Output
 */
class MediaOutput extends DT_MediaBlocks
{
  public $type = 'slick';
  public $main_type = 'carousel';
  public $mblock;
  public $attachments;

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

  function load_style(){
    $act = 'wp_head'; // 'wp_admin';
  }

  function load_assets( $type = null ){
    $url = DT_MULTIMEDIA_ASSETS_URL;
    $dir = $this->type . '/';
    
    $asset = $this->get_assets_list( $this->type );
    if( $asset ){
      if( isset($asset['js']) )
        wp_enqueue_script( $this->type, $url.$dir.$asset['js'], array('jquery'), $asset['ver'], true );
      if( isset($asset['core']) )
        wp_enqueue_style ( $this->type.'-core', $url.$dir.$asset['core'], array(), $asset['ver'], 'all' );
    }
  }
    
  function render_attachments( $main_type, $double = false ){
    $result = array();
    $id = (int)$this->mblock->ID;
    
    // width / height / items_size / columns / lightbox / image_captions / exclude_styles / exclude_assets /
    $settings = $this->settings_from_file( $id, $main_type );
    if( $settings )
      extract( $settings );

    $default_height = 450;
    if( ! isset($items_size) || ! $items_size ){
      if( isset($width) || isset($height) ){
        _isset_default($width, 1110);
        _isset_default($height, $default_height);

        $items_size = array( $width, $height );
      }
      else {
        $items_size = 'medium';
      }
    }
    _isset_default($height, $default_height);

    $tag_id = "mediablock-" . $this->mblock->ID;
    if( $main_type == 'slider' ){
      $columns = 1;
    }
    elseif( $main_type == 'carousel-3d' ){
      echo "<style> #{$tag_id} { height:{$height}px; } </style>";
    }
    _isset_default( $columns, 4 );

    // need for gallery or "no js"
    $item_class = $this->get_column_class( $columns );

    // .fancybox usually use for modal trigger
    $class_type = str_replace('fancybox', 'fancy', $this->type);
    
    $item_wrap = array("<div id='{$tag_id}' class='media-block row {$main_type} {$class_type}'>", "</div>");
    $item = array("<div class='item {$item_class}'>", "</div>");

    if( empty($exclude_styles) )
      $this->load_style();
    
    if( empty($exclude_assets) )
      $this->load_assets( $this->type );

    /**
     * Output Attachments
     */
    $result[] = $item_wrap[0];
    foreach ($this->attachments as $attachment) {
        $att = get_post( $attachment );
        
        $caption = '';
        if( isset($image_captions) )
          $caption = '<div id="caption">'.apply_filters( 'the_content', $att->post_excerpt ).'</div>';
        
        $link = array('', '');
        if( ! empty($lightbox) && ($columns == 1 || ! $double) )
            $link = array('<a rel="group-'.$id.'" href="'.$att->guid.'" class="'.$lightbox.'">', '</a>');

        $result[] = $item[0];
        $result[] = '   '.$link[0];
        $result[] = '   '. wp_get_attachment_image( $attachment, $items_size ); //,null,array(attrs)
        $result[] = '   '.$caption;
        $result[] = '   '.$link[1];
        $result[] = $item[1];
    }
    $result[] = $item_wrap[1];

    return implode("\n", $result);
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
  function render_carousel( $double = false ){
    $main_type = ($double) ? 'sync-slider' : 'carousel';
    $trigger = '#mediablock-'.$this->mblock->ID.'.'.$main_type;
    $php_array_params = $this->settings_from_file( $this->mblock->ID, $this->type, $main_type );

    switch ( $this->type ) {
      case 'owl-carousel':
        $init = 'owlCarousel';
        break;
      default:
        $init = $this->type;
        break;
    }

    if( ! $double ){
     JQScript::init($trigger, 'removeClass', 'row');
     // clear class to 'item'
     JQScript::init($trigger . ' .item', 'attr', 'class", "item');
     JQScript::init($trigger, $init, $php_array_params);
   }

   return $this->render_attachments('carousel', $double);
  }
  function render_slider( $double = false ){
      $main_type = ($double) ? 'sync-slider' : 'slider';
      $trigger = '#mediablock-'.$this->mblock->ID.'.'.$main_type;
      $php_array_params = $this->settings_from_file( $this->mblock->ID, $this->type, $main_type );

      switch ( $this->type ) {
          case 'owl-carousel':
              $init = 'owlCarousel';
              break;
          default:
              $init = $this->type;
              break;
      }

      if( !$double ){
          JQScript::init($trigger, 'removeClass', 'row');
          JQScript::init($trigger . ' .item', 'attr', 'class", "item');
          JQScript::init($trigger, $init, $php_array_params);
      }

      return $this->render_attachments('slider', $double);
  }
  # todo : get variables from class props
  function render_sync_slider(){
    $out = $this->render_slider( true );
    $out .= $this->render_carousel( true );

    ob_start();

    $php_to_js_params = apply_filters( 'array_options_before_view',
      $this->settings_from_file($this->mblock->ID, $this->type, 'sync-slider') );

    $o = $this->settings_from_file($this->mblock->ID, 'sync-slider');
    extract($o);

    var_dump($o);
    echo "<hr>";
    var_dump($php_to_js_params);
    ?>
    <script type="text/javascript">
          jq = jQuery.noConflict();
          jq(function( $ ) {
            //on.load
            $(function(){
              var sync1Selector = "#mediablock-<?php echo $this->mblock->ID; ?>.slider";
              var $sync1 = $(sync1Selector);
              var sync2Selector = "#mediablock-<?php echo $this->mblock->ID; ?>.carousel";
              var $sync2 = $(sync2Selector);

              $(sync1Selector).removeClass('row');
              $(sync1Selector + ' .item').attr('class', 'item');
              $(sync2Selector).removeClass('row');
              $(sync2Selector + ' .item').attr('class', 'item');
    <?php
    switch ($this->type) {
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
          });
          </script>
    <?php
    $out .= ob_get_clean();
    return $out;
  }
  # todo : REWRITE CODE!
  function render_carousel_3d(){
    $init_settings = $this->settings_from_file($this->mblock->ID, $this->type, 'carousel_3d');
    $trigger = '#mediablock-'.$this->mblock->ID.'.carousel-3d';

    JQScript::init($trigger, 'removeClass', 'row');
    JQScript::init($trigger . ' .item', 'attr', 'class", "item');
    
    switch ($this->type) {
      case 'cloud9carousel':
        $init = 'Cloud9Carousel';
        break;
      default:
        $init = $this->type;
        break;
    }

    JQScript::init($trigger, $init, $init_settings );

    return $this->render_attachments('carousel-3d');
  }
  function render_gallery(){

    return $this->render_attachments('gallery');
  }

  /**
   * Shortcode
   */
  function media_sc( $atts ) {
    /**
     * Get & Set Options
     */
    $atts = shortcode_atts( array('id' => false), $atts );
    if( ! $id = intval($atts['id']) )
      return false;

    $this->type = $this->meta_field( $id, 'type' );
    $this->mblock = $mblock = $this->post = get_post( $id );
    
    if($mblock->post_status !== 'publish' )
      return false;

    $this->attachments = $attachments = explode(',', $this->meta_field($id, 'media_imgs') );
    if( ! $attachments )
      return ( is_wp_debug() ) ? 'Файлов не найдено' : false;
    
    /**
     * Output
     */
    $result = array();
    $result[] = '<section id="mblock-'.$mblock->ID.'">';

    if( $this->meta_field( $id, 'show_title' ) && $mblock->post_title != '' )
      $result[] = '<h3>'. $mblock->post_title .'</h3>';
      
    if( $mblock->post_excerpt )
      $result[] = '<div class="excerpt">' .apply_filters('the_content', $mblock->post_excerpt). "</div>";

    // Items output
    $func = 'render_' . apply_filters( 'dash_to_underscore', $this->meta_field( $id, 'main_type' ) );
    $result[] = $this->$func( false );

    $result[] = '</section>';
    return implode("\n", $result);
  }
}
