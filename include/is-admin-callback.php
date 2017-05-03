<?php
namespace MB;

class isAdminView extends DT_MediaBlocks
{
	protected $settings;
	protected $post_id;

	function __construct(){
		add_filter( 'clear_checkbox_render', '__return_true', 10, 1 );
		$this->admin_actions();

	}

	function admin_actions(){
		add_action( 'edit_form_after_title', array( $this, 'after_title' ) );
		add_action( 'add_meta_boxes', array( $this, 'blocks_meta_boxes' ) );
		add_action( 'add_meta_boxes' , array( $this, 'remove_default_divs' ), 99 );

		add_action( 'save_post', array( $this, 'validate_main_settings' ) );

		add_action( 'load-post.php', array( $this, 'admin_asssets' ) );
		add_action( 'load-post-new.php', array( $this, 'admin_asssets' ) );

		add_action( 'wp_enqueue_scripts', array($this, 'dtmb_add_ajax_data'), 99 );
		
		add_action( 'wp_ajax_main_settings', array($this, 'sub_settings_callback') );
		add_action( 'wp_ajax_main_settings', array($this, 'side_settings_callback') );
	}

	/**
	 * Custom Meta Box Actions
	 */
	function blocks_meta_boxes( $post_type ){
		add_meta_box('attachments', 'Мультимедиа', array( $this, 'attachments_callback' ), DTM_TYPE, 'normal', 'high');
		add_meta_box('main_settings', 'Настройки', array( $this, 'sub_settings_callback' ), DTM_TYPE, 'normal');
		add_meta_box('side_settings', 'Настройки', array( $this, 'side_settings_callback' ), DTM_TYPE, 'side');
		add_meta_box('mb_postexcerpt', __( 'Контент после заголовка' ), array($this, 'excerpt_box'), DTM_TYPE, 'normal');
	}

	function remove_default_divs() {
		remove_meta_box( 'slugdiv',		 DTM_TYPE, 'normal' ); // ярлык записи,
		remove_meta_box( 'postcustom',	 DTM_TYPE, 'normal' ); // Произвольные поля
		remove_meta_box( 'postexcerpt' , DTM_TYPE, 'normal' );
	}

	/**
	 * Meta Box Render Callbacks
	 */
	function after_title() {
		global $post, $wp_meta_boxes;
		if($post->post_type !== DTM_TYPE)
			return;

		$show_input = array(
			'id' => 'show_title',
			'type' => 'checkbox',
			);

		echo "<div class='wrap-sc'>";
		echo "<label> "._('Show title');
		$value = array( $show_input['id'] => $this->meta_field($post->ID, $show_input['id']) );
		WPForm::render( $show_input, $value, false, array('item_wrap' => array('<span>', '</span>'), ) );
		echo "</label>";

		echo 'Вставьте шорткод в любую запись Вашего сайта';
		echo '<input id="shortcode" readonly="readonly" type="text" value=\'[mblock id="'.$post->ID.'"]\'>';
		echo "</div>";
	}

	protected function get_admin_wrap_attachments($post){
		$ids = $this->meta_field( $post->ID, 'media_imgs' );
		$ids_arr = explode( ',', esc_attr($ids) );
		$style = $this->meta_field($post->ID, 'detail_view') ? 'list' : 'tile';

		echo '<div class="attachments '.$style.'" id="dt-media">';
		if($ids){
			foreach ($ids_arr as $id) { ?>
			<div class="attachment" data-id="<?php echo $id; ?>">
				<?php
					$meta = wp_get_attachment_metadata( $id );
					$attrs = ( $meta['image_meta']['orientation'] == 1 ) ? array('class' => 'portrait') : array();
				?>
				<div class="item">
					<?php
						// wp_get_attachment_metadata( $id )
						$attachment = get_post( $id );
						$image = wp_get_attachment_image($id, 'medium', null, $attrs);
						$link = get_post_meta( $id, 'mb_link', true );
					?>
					<span class="dashicons dashicons-no remove"></span>

					<div class="crop"><?=$image;?></div>

					<input class="item-excerpt" type="text" name="attachment_excerpt[<?php echo $id; ?>]" value="<?php echo $attachment->post_excerpt; ?>">

					<textarea class="item-content" name="attachment_content[<?php echo $id; ?>]" id="" cols="90" rows="4"><?php echo $attachment->post_content; ?></textarea>

					<input class="item-link" type="text" name="attachment_link[<?php echo $id; ?>]" placeholder="#permalink(4)" value="<?=$link;?>">
					<input type="hidden" id="dt-ids" name="attachment_id[]" value="<?php echo $id; ?>">

				</div>
			</div>
			<?php
			} // foreach
		} // if
		echo '</div>';
	}

	function attachments_callback( $post ) {
		wp_enqueue_media();

		//_dump( get_post_meta( $post->ID ) );
		//wp_nonce_field( 'dp_addImages_nonce', 'wp_developer_page_nonce' );
		?>
		<div class="dt-media">
			<div class="hide-if-no-js wp-media-buttons">
			<?php
				//str
				$is_detail_view = $this->meta_field($post->ID, 'detail_view');
			?>
				<input type="hidden" name="detail_view" value="<?=$is_detail_view;?>">
				<button id="detail_view" class="button" type="button">
					<span class="dashicons dashicons-screenoptions <?php
						echo ($is_detail_view) ? '' : 'hidden'; ?>"></span>
					<span class="dashicons dashicons-list-view <?php
						echo ($is_detail_view) ? 'hidden' : ''; ?>"></span>
				</button>
				<button id="upload-images" class="button add_media">
					<span class="wp-media-buttons-icon"></span> Добавить медиафайл
				</button>
			</div>
			<label>Тип мультимедия: </label>
			<?php
				WPForm::render( $this->get_settings_file('general'), array(
					'main_type' => $this->meta_field( $post->ID, 'main_type' ),
					'type'      => $this->meta_field( $post->ID, 'type' )
					), false, array('item_wrap' => array('<span>', '</span>')));
			?>
			<?php $this->get_admin_wrap_attachments($post); ?>
			<div class="clear"></div>
		</div>
		<?php
	}

	function sub_settings_callback( $post ) {
		$post_id = ( isset($post->ID) ) ? $post->ID : intval( $_POST['post_id'] );

		$main_type = _isset_default( $_POST['main_type'], $this->meta_field($post_id, 'main_type') );
		
		if( isset($_POST['type']) )
			$type = $_POST['type'];
		elseif(! $type = $this->meta_field($post_id, 'type') )
			$type = 'owl-carousel';

		echo "<div class='sub-settings-wrp'>";
		WPForm::render( $this->get_settings_file( 'sub/'.$type, $main_type ), $this->meta_field($post_id, $type.'_opt'), true );
		echo "</div>";
	}

	function side_settings_callback( $post ){
		$post_id = ( isset($post->ID) ) ? $post->ID : intval( $_POST['post_id'] );
		
		if( isset($_POST['main_type']) )
			$type = $_POST['main_type'];
		elseif(! $type = $this->meta_field($post->ID, 'main_type') )
			$type = 'carousel';

		$args = array(
			'label_tag' => 'td',
			//'clear_value' => 'true'
			);

		echo "<div class='settings-wrp'>";
		WPForm::render( $this->get_settings_file( 'main/'.$type ), $this->meta_field($post->ID, $type.'_opt'), true, $args );
		echo "</div>";
	}

	function excerpt_box(){
		global $post;

		echo "<label class='screen-reader-text' for='excerpt'> {_('Excerpt')} </label>
		<textarea rows='1' cols='40' name='excerpt' tabindex='6' id='excerpt'>{$post->post_excerpt}</textarea>";
	}

	/**
	 * Enqueue Assets
	 */
	function admin_asssets(){
		$screen = get_current_screen();
		if( $screen->post_type != DTM_TYPE )
			return false;

		if ( ! did_action( 'wp_enqueue_media' ) ) 
			wp_enqueue_media();

		$url = DT_MULTIMEDIA_ASSETS_URL;
		wp_enqueue_style( 'dtm-style', $url.'core/style.css', array(), DT_MediaBlocks::VERSION, 'all' );
		wp_enqueue_script( 'dtm-preview', $url.'core/preview.js', array('jquery'), DT_MediaBlocks::VERSION, true );
		
		wp_localize_script('dtm-preview', 'settings', array( 'nonce' => wp_create_nonce( 'any_secret_string' ) ) ); 
	}

	/**
	 * Validate Post's Data
	 */
	private function check_security( $post_id ){
		// if ( ! isset( $_POST['wp_developer_page_nonce'] ) )
		// return FALSE;
		// $nonce = $_POST['wp_developer_page_nonce'];
		// if ( ! wp_verify_nonce( $nonce, 'dp_addImages_nonce' ) )
		// 	return FALSE;

		// Если это автосохранение ничего не делаем.
		// if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		// 	return FALSE;
	}
	private function validate_media_attachments( $post_id ){
		if( !isset($_POST['attachment_id']) || !is_array($_POST['attachment_id']))
			return $post_id;

		$attachment_ids = $_POST['attachment_id'];
		$attachment_ids = implode(',', $attachment_ids);
		$this->meta_field( $post_id, 'media_imgs', $attachment_ids );

		foreach ($_POST['attachment_excerpt'] as $id => $excerpt) {
			$update = array( 'ID' => $id	);

			if($excerpt)
				$update['post_excerpt'] = $excerpt;

			if($_POST['attachment_content'][$id])
				$update['post_content'] = $_POST['attachment_content'][$id];

			if($_POST['attachment_link'][$id])
				update_post_meta( $id, 'mb_link', $_POST['attachment_link'][$id] );

			if( sizeof($update > 1) )
				wp_update_post( $update );
		}
	}

	function validate_main_settings( $post_id ){
		if( FALSE === $this->check_security($post_id) )
			return $post_id;

		$this->validate_media_attachments($post_id);

		$this->meta_field($post_id, 'show_title', _isset_false($_POST['show_title']) );
		$this->meta_field($post_id, 'detail_view', _isset_false($_POST['detail_view']) );

		if( !isset($_POST['main_type']) || !isset($_POST['type']) )
			return $post_id;

		$main_type = $_POST['main_type'];
		$type = $_POST['type'];

		$this->meta_field($post_id, 'main_type', $main_type);
		$this->meta_field($post_id, 'type', $type);
		
		$this->settings_from_file($post_id, $main_type, false, $_POST );
		$this->settings_from_file($post_id, $type, $main_type, $_POST );

		/**
		 * Create TEMP Style File
		 */
		$asset = $this->get_assets_list();
		if( ! isset($asset[ $type ]) )
			return false;

		$file = get_template_directory() . 'assets/blocks/block-'.$post_id.'.css';
		if ( ! file_exists( $file ) )
			$file = DT_MULTIMEDIA_PATH . 'assets/' . $asset[ $type ]['theme'];
		$out_file = DT_MULTIMEDIA_PATH . 'assets/block-'.$post_id.'.css';

		if ( file_exists( $file ) ){
			$scss = new \scssc();
			$scss->setFormatter('scss_formatter_compressed');
			$compiled = $scss->compile( apply_filters( 'remove_cyrillic', '#mediablock-'.$post_id.' {' . file_get_contents($file) . '}' ) );

			if(!empty($compiled))
				file_put_contents( $out_file, $compiled );
		}

		//file_put_contents(__DIR__ . '/save.log', print_r($_POST, 1));
	}
}

/**
 * Metabox
 */
// add_action( 'load-post.php',     'MB\metabox_action' );
// add_action( 'load-post-new.php', 'MB\metabox_action' );

// function metabox_action(){
//     $screen = get_current_screen();
//     if( !isset($screen->post_type) || $screen->post_type != DTM_TYPE )
//         return false;

//     $boxes = new WPPostBoxes();
//     $boxes->add_box('Тест', 'MB\metabox_render', false, 'high' );
//     $boxes->add_fields( 'RQ_META_NAME' );
// }
// function metabox_render($post, $data){
// 	echo "Some test";
// 	// WPForm::render( $fields, get_post_meta( $post->ID, RQ_META_NAME, true ), true );
//     // wp_nonce_field( $data['args'][0], $data['args'][0].'_nonce' );
// }