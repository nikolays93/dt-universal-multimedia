<?php
class isAdminView extends DT_MediaBlocks
{
	public $post_id;
	public $settings;

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

		add_action( 'before_admin_wrap_attachments', array($this, 'default_media_buttons'), 10, 1 );
	}

	/** Добавить блоки */
	function blocks_meta_boxes( $post_type ){
		add_meta_box('attachments', 'Мультимедиа', array( $this, 'attachments_callback' ), self::POST_TYPE, 'normal', 'high');
		add_meta_box('main_settings', 'Настройки', array( $this, 'sub_settings_callback' ), self::POST_TYPE, 'normal');
		add_meta_box('side_settings', 'Настройки', array( $this, 'side_settings_callback' ), self::POST_TYPE, 'side');
		add_meta_box('mb_postexcerpt', __( 'Контент после заголовка' ), array($this, 'excerpt_box'), self::POST_TYPE, 'normal');
	}

	/** Удалить стандартные блоки */
	function remove_default_divs() {
		remove_meta_box( 'slugdiv',		 self::POST_TYPE, 'normal' ); // ярлык записи,
		remove_meta_box( 'postcustom',	 self::POST_TYPE, 'normal' ); // Произвольные поля
		remove_meta_box( 'postexcerpt' , self::POST_TYPE, 'normal' );
	}

	function after_title() {
		global $post, $wp_meta_boxes;
		if($post->post_type !== self::POST_TYPE)
			return;

		$check = checked( self::meta_field($post->ID, self::SHOW_TITLE_NAME), 'on', false );

		echo "<div class='wrap-sc'>";

		echo "<label> " . __('Показывать заголовок');
		echo "<input type='checkbox' id={self::SHOW_TITLE_NAME}' name='{self::SHOW_TITLE_NAME}' value='on'{$check}>";
		echo "</label>";

		echo 'Вставьте шорткод в любую запись Вашего сайта';
		echo '<input id="shortcode" readonly="readonly" type="text" value=\'[mblock id="'.$post->ID.'"]\'>';
		
		echo "</div>";
	}

	/**
	 * Main Block
	 */
	function default_media_buttons( $post ){
		$is_detail_view = self::meta_field($post->ID, self::VIEW_MODE_NAME);
		?>
			<div class="hide-if-no-js wp-media-buttons">
				<input type="hidden" name="<?php echo self::VIEW_MODE_NAME;?>" value="<?php echo $is_detail_view; ?>">
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
				MB\WPForm::render( $this->parse_settings_file('general'), array(
					'main_type' => self::meta_field( $post->ID, 'main_type' ),
					'type'      => self::meta_field( $post->ID, 'type' )
					), false, array('item_wrap' => array('<span>', '</span>')));
	}

	function attachments_callback( $post ) {
		if ( ! did_action( 'wp_enqueue_media' ) ) 
			wp_enqueue_media();
			//wp_nonce_field( 'dp_addImages_nonce', 'wp_developer_page_nonce' );
		?>
		<div class="dt-media">
			<?php do_action( 'before_admin_wrap_attachments', $post ); ?>
			<div class="clear"></div>

			<?php
			$ids = self::meta_field( $post->ID, 'media_imgs' );
			$ids_arr = explode( ',', esc_attr($ids) );
			$style = self::meta_field($post->ID, self::VIEW_MODE_NAME) ? 'list' : 'tile';

			echo '<div class="attachments '.$style.'" id="dt-media">';
			if( $ids ){
				foreach ($ids_arr as $id) {
					$meta = wp_get_attachment_metadata( $id );
					$attrs = ( $meta['image_meta']['orientation'] == 1 ) ? array('class' => 'portrait') : array();

					// wp_get_attachment_metadata( $id )
					$attachment = get_post( $id );
					$image = wp_get_attachment_image($id, 'medium', null, $attrs);
					$link = get_post_meta( $id, 'mb_link', true );
				?>
				<div class="attachment" data-id="<?php echo $id; ?>">
					<div class="item">
						<span class="dashicons dashicons-no remove"></span>

						<div class="crop"><?php echo $image;?></div>

						<input class="item-excerpt" type="text" name="attachment_excerpt[<?php echo $id; ?>]" value="<?php echo $attachment->post_excerpt; ?>">

						<textarea class="item-content" name="attachment_content[<?php echo $id; ?>]" id="" cols="90" rows="4"><?php echo $attachment->post_content; ?></textarea>

						<input class="item-link" type="text" name="attachment_link[<?php echo $id; ?>]" placeholder="#permalink(4)" value="<?php echo $link;?>">
						<input type="hidden" id="dt-ids" name="attachment_id[]" value="<?php echo $id; ?>">
					</div>
				</div>
				<?php
				} // foreach
			} // if
		?>
			</div><!-- #dt-media -->
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Показывает настройки библиотеки
	 * Обновляется через AJAX
	 */
	function sub_settings_callback( $post ) {
		$post_id = ( isset($post->ID) ) ? $post->ID : intval( $_POST['post_id'] );
		$main_type = isset($_POST['main_type']) ? $_POST['main_type'] : self::meta_field($post_id, 'main_type');
		$type = isset($_POST['type']) ? $_POST['type'] : self::meta_field($post_id, 'type');
		if( empty($type) )
			$type = 'owl-carousel';

		echo "<div class='sub-settings-wrp'>";
		MB\WPForm::render( $this->parse_settings_file( 'sub/'.$type, $main_type ), self::meta_field($post_id, $type.'_opt'), true );
		echo "</div>";
	}

	/**
	 * Показывает под настройки (справа в сайдбаре)
	 * Обновляется через AJAX
	 */
	function side_settings_callback( $post ){
		$post_id = ( isset($post->ID) ) ? $post->ID : intval( $_POST['post_id'] );
		$type = isset($_POST['main_type']) ? $_POST['main_type'] : self::meta_field($post->ID, 'main_type');
		if( empty($type) )
			$type = 'carousel';

		echo "<div class='settings-wrp'>";
		MB\WPForm::render(
			$this->parse_settings_file( 'main/'.$type ),
			self::meta_field($post->ID, $type.'_opt'),
			true,
			array(
				'label_tag' => 'td',
				'clear_value' => false
				)
			);
		echo "</div>";
	}

	/**
	 * Вывод поля "Контент после заголовка" (the_excerpt)
	 */
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
		if( $screen->post_type != self::POST_TYPE )
			return false;

		if ( ! did_action( 'wp_enqueue_media' ) ) 
			wp_enqueue_media();

		wp_enqueue_style( self::PREFIX.'style', MBLOCKS_ASSETS.'/core/style.css', array(), self::VERSION, 'all' );
		wp_enqueue_script( self::PREFIX.'view', MBLOCKS_ASSETS.'/core/view.js', array('jquery'), self::VERSION, true );
		wp_localize_script(self::PREFIX.'view', 'settings', array( 'nonce' => wp_create_nonce( 'any_secret_string' ) ) ); 
	}

	/**
	 * Validate Post's Data
	 *
	 * @todo : set metas with array
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
		self::meta_field( $post_id, 'media_imgs', $attachment_ids );

		foreach ($_POST['attachment_excerpt'] as $id => $excerpt) {
			$update = array( 'ID' => $id	);

			if($excerpt)
				$update['post_excerpt'] = $excerpt;

			if( isset($_POST['attachment_content'][$id]) )
				$update['post_content'] = $_POST['attachment_content'][$id];

			if( isset($_POST['attachment_link'][$id]) )
				update_post_meta( $id, 'mb_link', $_POST['attachment_link'][$id] );

			if( sizeof($update > 1) )
				wp_update_post( $update );
		}
	}
	function validate_main_settings( $post_id ){
		if( FALSE === $this->check_security($post_id) )
			return $post_id;

		$this->validate_media_attachments($post_id);

		/**
		 * @todo change it to locate/storage js
		 */
		self::meta_field($post_id, self::SHOW_TITLE_NAME, _isset_false($_POST[self::SHOW_TITLE_NAME]) );
		self::meta_field($post_id, self::VIEW_MODE_NAME, _isset_false($_POST[self::VIEW_MODE_NAME]) );

		if( !isset($_POST['main_type']) || !isset($_POST['type']) )
			return $post_id;

		$main_type = $_POST['main_type'];
		$type = $_POST['type'];

		self::meta_field($post_id, 'main_type', $main_type);
		self::meta_field($post_id, 'type', $type);
		self::meta_field($post_id, 'query', $_POST['query']);
		
		$this->settings_from_file($post_id, $main_type, false, $_POST );
		$this->settings_from_file($post_id, $type, $main_type, $_POST );

		/**
		 * Create TEMP Style File
		 */
		$asset = self::pre_register_assets( $type );
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

/** for needed page */

// new WPAdminPageRender(
      //   self::Settings,
      //   array(
      //     'parent' => 'options-general.php',
      //     'title' => __('Project Title'),
      //     'menu' => __('Project Title Menu'),
      //     ),
      //   array($this, 'admin_settings_page')
      //   );

    // function admin_settings_page(){
    // $data = array(
    //   array(
    //     'id' => 'few_contacts',
    //     'type' => 'checkbox',
    //     'label' => 'Несколько контактов',
    //     'desc' => 'Использовать несколько контактов',
    //     ),
    //   );

    // WPForm::render(
    //   $data,
    //   WPForm::active(NEW_OPTION, false, true),
    //   true,
    //   array('clear_value' => false)
    //   );

    // submit_button();
    // }