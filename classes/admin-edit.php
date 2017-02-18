<?php
// function dp_slider_updated_messages( $messages ) {
// 	global $post, $post_ID;

// 	$messages['review'] = array(
// 	0 => '', // Не используется. Сообщения используются с индекса 1.
// 	1 => sprintf( 'Отзыв обновлен. <a href="%s">Прочитать отзыв</a>', esc_url( get_permalink($post_ID) ) ),
// 	2 => 'Произвольное поле обновлено.',
// 	3 => 'Произвольное поле удалено.',
// 	4 => 'Отзыв обновлен.',
// 	/* %s: дата и время ревизии */
// 	5 => isset($_GET['revision']) ? sprintf( 'Отзыв востановлен из ревизии %s', wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
// 	6 => sprintf( 'Отзыв опубликован. <a href="%s">Перейти к отзыву</a>', esc_url( get_permalink($post_ID) ) ),
// 	7 => 'Отзыв сохранен.',
// 	8 => sprintf( 'Отзыв сохранен. <a target="_blank" href="%s">Предпросмотр отзыва</a>', esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
// 	9 => sprintf( 'Отзыв запланирован на: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Предпросмотр отзыва</a>',
// 		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
// 	10 => sprintf( 'Не утвержденный отзыв (Черновик) сохранен. <a target="_blank" href="%s">Предпросмотр отзыва</a>', esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
// 	);
// 	return $messages;
// }
// add_filter('post_updated_messages', 'dp_slider_updated_messages');
// function add_review_help_text($contextual_help, $screen_id, $screen) {
// 	if ('edit-review' == $screen->id || 'review' == $screen->id ) {
// 		$contextual_help =
// 		'<h4>Используйте ContactForm7</h4><p>Если добавить в форму [text] с именем dp_review и дать ему любое значение (При этом его можно скрыть при помощи css) помимо отправленного сообщения, система создаст "Запись" в категории "Отзывы".</p>

// 		<p>Не работает если опция выключена. При выключении опции данные скрываются (НЕ Удаляются из базы).</p>

// 		<label><strong>К примеру:</strong></label>
// 		<p>[text* your-name][textarea your-message][text dp_review class:hide-me "text с именем dp_review"]</p>
// 		';
// 	}
// 	return $contextual_help;
// }
// add_action( 'contextual_help', 'add_review_help_text', 10, 3 );

/**
* 
*/
class isAdminView extends DT_MultiMedia
{
	function __construct()
	{	
		$this->preview_hooks();

		add_action( 'load-post.php', array( $this, 'preview_hooks' ) );
		add_action( 'load-post-new.php', array( $this, 'preview_hooks' ) );
	}

	function preview_hooks(){
		add_action( 'add_meta_boxes', array( $this, 'preview_boxes' ) );
		add_action( 'save_post', array( $this, 'validate' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'preview_assets' ) );
		add_action( 'admin_menu' , array( $this, 'remove_default_divs' ) );
	}

	function preview_boxes( $post_type ){
		add_meta_box(
			'preview_media_edit',
			'Мультимедиа',
			array( $this, 'preview_media_edit_callback' ),
			DT_MULTIMEDIA_MAIN_TYPE,
			'normal',
			'high'
		);
		add_meta_box(
			'preview_media_main_settings',
			'Настройки',
			array( $this, 'preview_media_main_settings_callback' ),
			DT_MULTIMEDIA_MAIN_TYPE,
			'normal'
		);
	}

	function preview_assets(){
		if ( ! did_action( 'wp_enqueue_media' ) ) 
			wp_enqueue_media();
		
		wp_enqueue_style( 'dt-style',   DT_MULTIMEDIA_ASSETS_URL.'/core/style.css', array(), $this->version);
		wp_enqueue_script('dt-preview', DT_MULTIMEDIA_ASSETS_URL.'/core/preview.js', array('jquery'), $this->version);
	}

	/**
	 * Удаляем стандартные блоки
	 */
	function remove_default_divs() {
		remove_meta_box( 'slugdiv',		DT_MULTIMEDIA_MAIN_TYPE, 'normal' ); // ярлык записи,
		remove_meta_box( 'postcustom',	DT_MULTIMEDIA_MAIN_TYPE, 'normal' ); // Произвольные поля
	}

	protected function get_attachments($post){
		$ids = get_post_meta( $post->ID, 'dt-media-imgs', true );
		$ids = explode(',', esc_attr($ids));

		if($ids[0] != ''){
			echo '<div class="attachments tile" id="dt-media">';
			foreach ($ids as $id) { ?>
			<div class="attachment" data-id="<?php echo $id; ?>">
				<?php
					$meta = wp_get_attachment_metadata( $id );
					$attrs = ( $meta['image_meta']['orientation'] == 1 ) ? array('class' => 'portrait') : array();
				?>
				<div class="item">
					<span class="dashicons dashicons-no remove"></span>
					<div class="crop">
						<?php
							echo wp_get_attachment_image($id, 'medium', null, $attrs);
						?>
					</div>
					<input type="text" name="attachment_text[<?php echo $id; ?>]" value="<?php echo wp_get_attachment_caption($id); ?>">
					<input type="hidden" id="dt-ids" name="attachment_id[]" value="<?php echo $id; ?>">
				</div>
			</div>
			<?php
			} // foreach
			echo '</div>';
		} // if
	}
	function preview_media_edit_callback( $post ) {
		wp_enqueue_media();
		//wp_nonce_field( 'dp_addImages_nonce', 'wp_developer_page_nonce' );
	?>
	<div class="dt-media">
		<div class="hide-if-no-js wp-media-buttons">
			<button class="button" disabled="true">
				<!-- <span class="dashicons dashicons-screenoptions"></span> -->
				<span class="dashicons dashicons-list-view" title="Will be future"></span>
			</button>
			<button id="upload-images" class="button add_media">
				<span class="wp-media-buttons-icon"></span> Добавить медиафайл
			</button>
		</div>
		<label>Тип мультимедия: </label>
		<select class="button">
			<option value="#">Карусель</option>
			<option value="#">Слайдер</option>
			<option value="#">Галерея</option>
		</select>
		
		<?php $this->get_attachments($post); ?>
		<div class="clear"></div>
	</div>
<?php
	}

	function preview_media_main_settings_callback( $post ) {}

	function validate( $post_id ) {
		// if ( ! isset( $_POST['wp_developer_page_nonce'] ) )
			// return $post_id;
		// $nonce = $_POST['wp_developer_page_nonce'];
		// if ( ! wp_verify_nonce( $nonce, 'dp_addImages_nonce' ) )
		// 	return $post_id;

		// Если это автосохранение ничего не делаем.
		// if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		// 	return $post_id;
		if( !isset($_POST['attachment_id']) || !is_array($_POST['attachment_id']))
			return $post_id;

		if(isset($_POST['attachment_text']) && is_array($_POST['attachment_text'])){
			$metas = $_POST['attachment_text'];
			foreach ($metas as $id => $meta) {
				wp_update_post( array('ID' => $id, 'post_excerpt' => $meta ) );
			}
		}

		$attachment_ids = $_POST['attachment_id'];
		$attachment_ids = implode(',', $attachment_ids);
		update_post_meta( $post_id, 'dt-media-imgs', $attachment_ids );
	}
}