<?php
namespace MB;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_shortcode( 'mblock', 'MB\mblock_sc' );
function mblock_sc( $atts ){
  if( ! $id = absint($atts['id']) )
    return false;

  $mblock = new MediaBlock( $id, apply_filters( 'custom_mblock_attributs', $atts ) );

  return $mblock->render();
}

add_filter( 'default_columns', 'MB\slider_default_columns', 10, 2 );
function slider_default_columns( $columns, $main_type ){
  if($main_type == 'slider')
    return 1;

  return $columns;
}

add_filter( 'type_to_lib', 'MB\type_to_lib', 10, 1 );
function type_to_lib( $type ){
  switch ( $type ) {
    case 'owl-carousel':
      $type = 'owlCarousel';
      break;
    case 'cloud9carousel':
      $type = 'Cloud9Carousel';
      break;
  }

  return $type;
}

/**
 * mblock_html > render_$type > render_attachments
 */
class MediaBlock extends DT_MediaBlocks {
  public $id;
  public $post;
  public $atts = array('status' => 'public', 'tag_title' => 'h3');

  public $main_type = 'carousel'; // default type
  public $sub_type  = 'slick';

  public $attach_ids = array();
  public $default_att_width = 1110;
  public $default_att_height = 450;
  public $default_att_size = 'medium';

  public $default_columns = 4;

  // use for double block
  public $double = false;

  function __construct( $post_id = false, $atts = array() ){
    if( !$post_id )
      return false;

    if( is_array($atts) ){
      $atts = array_filter($atts, 'sanitize_text_field');
      $this->atts = array_merge($this->atts, $atts);
    }

    $this->id = $post_id;
    $this->post = get_post( $this->id );

    if($this->post->post_status !== 'publish' )
      return false;

    $this->main_type = $this->meta_field( $this->id, 'main_type' );
    $this->sub_type  = $this->meta_field( $this->id, 'type' );

    if( false === $this->set_attachment_ids() )
      return ( is_wp_debug() ) ? 'Файлов не найдено' : false;
  }

  public function render(){
    // if( empty($settings['exclude_styles']) )
    //   $this->load_style();

    if( empty($settings['exclude_assets']) )
      $this->load_block_assets();

    return $this->mblock_html( $this->atts );
  }

  /**
   * Load Assets
   * @todo : previosly register assets
   * @todo : add style for id ( from scss )
   */
  function load_block_assets(){
    $url = DT_MULTIMEDIA_ASSETS_URL;
    $dir = $this->sub_type . '/';
    
    $asset = $this->get_assets_list( $this->sub_type );
    if( $asset ){
      if( isset($asset['js']) )
        wp_enqueue_script( $this->sub_type, $url.$dir.$asset['js'], array('jquery'), $asset['ver'], true );
      if( isset($asset['core']) )
        wp_enqueue_style ( $this->sub_type.'-core', $url.$dir.$asset['core'], array(), $asset['ver'], 'all' );
    }

    if( !in_array($this->main_type, array('sync-slider', 'gallery')) ){
      $init = apply_filters('type_to_lib', $this->sub_type);
      $filename = apply_filters( 'dash_to_underscore', $this->main_type );
      $init_settings = $this->settings_from_file($this->id, $this->sub_type, $filename);
      $trigger = "#mediablock-{$this->id}.{$this->main_type}";

      JQScript::init($trigger, 'removeClass', 'row');
      JQScript::init($trigger . ' .item', 'attr', 'class", "item');
      JQScript::init($trigger, $init, $init_settings );
    }
    else {
      JQScript::custom($this->double_script());
    }
  }

  /**
   * HTML Output
   */
  protected function mblock_html( $atts ){
    $result = array();
    $result[] = '<section id="mblock-'.$this->id.'">';

    if( $this->meta_field( $this->id, 'show_title' ) && !empty($this->post->post_title) )
      $result[] = "<{$atts['tag_title']}>{$this->post->post_title}</{$atts['tag_title']}>";

    if( $this->post->post_excerpt )
      $result[] = '<div class="excerpt">' .apply_filters('the_content', $this->post->post_excerpt). "</div>";

    // Items output (call render_$type)
    $func = sanitize_text_field('render_' . apply_filters( 'dash_to_underscore', $this->main_type ));
    $result[] = $this->$func();

    $result[] = '</section>';
    return implode("\n", $result);
  }

  function set_attachment_ids(){
    if( ! $this->id )
      return false;

    $query_options = $this->meta_field($this->id, 'query');

    if(!empty($query_options['enable'])){
      $query = new \WP_Query( array(
        'post_type' => $query_options['type'], //post, page, post, product..
        'posts_per_page' => $query_options['qty'],
        'tax_query' => array(
          array(
            'taxonomy' => $query_options['tax'],
            'terms'    => $query_options['term'],
            ),
          ),
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
      $this->attachments = explode(',', $this->meta_field($this->id, 'media_imgs') );
    }

    if( !$this->attachments || sizeof($this->attachments) < 1 )
      return false;

    return true;
  }

  /**
   * HTML Output Attachments
   */
  protected function render_attachments_html( $settings, $item_wrap, $item ){
    $result = array();
    $result[] = $item_wrap[0];
    foreach ($this->attachments as $attachment) {
        $att = get_post( $attachment );
        
        /** Get Caption */
        $caption = empty($settings['image_captions']) ? '' : sprintf('<div id="caption">%s</div>',
            apply_filters( 'the_content', $att->post_excerpt )
            );
        
        /** Get Link */
        $link = array('', '');
        if( ! empty($settings['lightbox']) && ($settings['columns'] == 1 || ! $this->double) ){
          $link = array("<a rel='group-{$this->id}' href='{$att->guid}' class='{$settings['lightbox']}'>", "</a>");
        }
        elseif( $metalink = esc_attr( get_post_meta( $attachment, 'mb_link', true ) ) ){
          $url = ( preg_match("/permalink\(([0-9]{1,40})\)/i", $metalink, $output) && isset($output[1]) ) ?
            get_permalink( (int)$output[1] ) : $metalink;

          $link = array("<a href='{$url}' class='mediablock-link'>", "</a>");
        }

        /** Set Template */
        $result[] = $item[0];
        $result[] = '   '.$link[0];
        $result[] = '   '.wp_get_attachment_image( $attachment, $settings['items_size'] ); //,null,array(attrs)
        $result[] = '   '.$caption;
        $result[] = '   '.$link[1];
        $result[] = $item[1];
    }
    $result[] = $item_wrap[1];

    return implode("\n", $result);
  }

  protected function check_items_size( $width, $height, $items_size ){
    if( ! $items_size ){
      if( $width || $height ){
        if( !$width )
          $width = apply_filters( 'default_att_width', $this->default_att_width);
        if( !$height )
          $height = apply_filters( 'default_att_height', $this->default_att_height);

        $items_size = array( $width, $height );
      }
      else {
        $items_size = apply_filters('default_att_size', $this->default_att_size);
      }
    }

    // @todo: fix it
    _isset_default($height, apply_filters( 'default_att_height', $this->default_att_height) );
    if( $this->main_type == 'carousel-3d' ){
      echo "<style> #mediablock-{$this->id} { height:{$height}px; } </style>";
    }
    return $items_size;
  }

  protected function render_attachments(){
    if( ! $settings = $this->settings_from_file( $this->id, $this->main_type ) )
      return false;

    $settings['items_size'] = $this->check_items_size(
      _isset_false($settings['width']),
      _isset_false($settings['height']),
      _isset_false($settings['items_size']));
    
    _isset_default( $settings['columns'], apply_filters( 'default_columns', $this->default_columns, $this->main_type ) );

    // need for gallery or "no js"
    $item_class = get_column_class( $settings['columns'] );

    // .fancybox usually use for modal trigger
    $class_type = str_replace('fancybox', 'fancy', $this->sub_type);
    
    $item_wrap = array(
      "<div id='mediablock-{$this->id}' class='media-block row {$this->main_type} {$class_type}'>",
      "</div>");
    $item = array("<div class='item {$item_class}'>", "</div>");

    return $this->render_attachments_html($settings, $item_wrap, $item );
  }

  /**
   * Render MBlocks Functions
   * 
   * @param  $type            string sub_type ( fancy, owl, slick.. )
   * @param  $mblock          WP_Post
   * @param  $attachments     string att ids split ","
   * @param  $not_init_script boolean print initialize script
   * @return $sync            html output
   */
  function render_carousel(){

    return $this->render_attachments('carousel');
  }

  function render_slider(){

      return $this->render_attachments('slider');
  }
  
  # @todo : get variables from class props
  function render_sync_slider(){
    $this->main_type = 'sync-slider';

    $out = $this->render_slider();
    $out .= $this->render_carousel();

    return $out;
  }

  function render_carousel_3d(){

    return $this->render_attachments('carousel-3d');
  }

  function render_gallery(){

    return $this->render_attachments('gallery');
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
}


