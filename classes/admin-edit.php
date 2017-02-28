<?php
class isAdminView extends DT_MediaBlocks
{
	protected $settings;
	protected $post_id;

	function __construct()
	{	
		$this->preview_hooks();

		add_action( 'load-post.php', array( $this, 'preview_hooks' ) );
		add_action( 'load-post-new.php', array( $this, 'preview_hooks' ) );
	}

	function preview_hooks(){
		add_action( 'add_meta_boxes', array( $this, 'preview_boxes' ) );
		add_action( 'save_post', array( $this, 'validate_main_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'preview_assets' ) );
		add_action( 'admin_menu' , array( $this, 'remove_default_divs' ) );

		// add_action('edit_form_after_title', function() {
		// 	global $post, $wp_meta_boxes;
		// 	do_meta_boxes(get_current_screen(), 'advanced', $post);
		// 	unset($wp_meta_boxes[get_post_type($post)]['advanced']);
		// });
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
		add_meta_box(
			'preview_media_side_settings',
			'Настройки',
			array( $this, 'preview_media_side_settings_callback' ),
			DT_MULTIMEDIA_MAIN_TYPE,
			'side'
		);
		add_meta_box(
			'mb_postexcerpt',
			__( 'Сообщение блока' ),
			'custom_excerpt_meta_box',
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
		remove_meta_box( 'postexcerpt' , DT_MULTIMEDIA_MAIN_TYPE, 'normal' );
	}


	protected function get_attachments($post){
		$ids = get_post_meta( $post->ID, DT_PREFIX.'media_imgs', true );
		$ids = explode(',', esc_attr($ids));

		echo '<div class="attachments tile" id="dt-media">';
		if($ids[0] != ''){
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
		} // if
		echo '</div>';
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
				<option value="owl-carousel">Карусель</option>
				<!-- <option value="slider">Слайдер</option> -->
				<!-- <option value="gallery">Галерея</option> -->
				<!-- <option value="query">Запрос</option> -->
			</select>
			<select name="type" class="button">
				<option value="owl-carousel">Совинная карусель</option>
				<!-- <option value="slick-slider">Скользкий слайдер</option> -->
			</select>
			
			<?php $this->get_attachments($post); ?>
			<div class="clear"></div>
		</div>
		<script type="text/javascript">
			jQuery(function($){
				$('#shortcode').on('click', function(){ $(this).select(); });
			});
		</script>
		<?php
	}

	protected function render_input($name, $type, $value, $placeholder, $options, $target, $is_show){
		$name = ( $name ) ? "name='".$name."'": '';
		$target = ( $target ) ? "data-target='".$target."'" : '';
		if($target != '')
			$target .= ($is_show) ? " data-action='show'" : " data-action='hide'";

		$placeholder = ( $placeholder ) ? "placeholder='".$placeholder."'" : '';

		switch ($type) {
			case 'checkbox':
				$checked = ($value) ? 'checked ' : '';
				echo "<input {$name} {$target} type='{$type}' {$checked}value='on'>";
				break;

			case 'select':
				echo "<select {$name} {$target}>";
				foreach ($options as $id => $option){
					$active = ($value === $id) ? ' selected' : '';
					echo "<option value='{$id}'{$active}>{$option}</option>";
				}
				echo "</select>";
				break;

			default:
				echo "<input {$name} {$target} type='{$type}' {$placeholder} value='".$value."'>";
				break;
		}
	}
	protected function render_settings($settings, $side=false){
		echo '<table valign="top" class="table"><tbody>';
		foreach ($settings as $id => $value){
			$is_show = false;
			$target = false;
			if(isset($value['show'])){
				$target = $value['show'];
				$is_show = true;
			}
			if(isset($value['hide']))
				$target = $value['hide'];

			$placeholder = isset($value['placeholder']) ? $value['placeholder'] : false;
			$options     = isset($value['options']) ? $value['options'] : false;
			$default     = isset($value['default']) ? $value['default'] : false;
			
			if(isset($_GET['post'])){
				$post_id = intval($_GET['post']);
				$values = ($side) ? $this->get_side_options($post_id, $this->get_media_type($post_id), false )
					: $this->get_options($post_id, $this->get_media_type($post_id), false );
			}

			if( isset($values[$id]) )
				$default = $values[$id];

			echo "\n<tr id='{$id}'>";
			echo "<td class='name'>".$value['name']."</td>";
			echo "<td>";
			echo $this->render_input($id, $value['type'], $default, $placeholder, $options, $target, $is_show);
			if(isset($value['desc']))
				echo "<div class='description'>{$value['desc']}</div>";
			echo "</td></tr>";
		}
		echo '</tbody></table>';
		//_d($settings);
	}
	function preview_media_main_settings_callback( $post ) {
		$type = 'owl-carousel';
		$this->render_settings( $this->get_settings($type) );
	}
	function preview_media_side_settings_callback( $post ){
		$type = 'owl-carousel';
		$this->render_settings( $this->get_settings($type, 'side'), true );
	}

	private function check_security( $post_id ){
		// if ( ! isset( $_POST['wp_developer_page_nonce'] ) )
		// return $post_id;
		// $nonce = $_POST['wp_developer_page_nonce'];
		// if ( ! wp_verify_nonce( $nonce, 'dp_addImages_nonce' ) )
		// 	return $post_id;

		// Если это автосохранение ничего не делаем.
		// if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		// 	return $post_id;
	}
	private function validate_media_attachments($post_id){
		if( !isset($_POST['attachment_id']) || !is_array($_POST['attachment_id']))
			return $post_id;

		$attachment_ids = $_POST['attachment_id'];
		$attachment_ids = implode(',', $attachment_ids);
		update_post_meta( $post_id, DT_PREFIX.'media_imgs', $attachment_ids );

		if(isset($_POST['attachment_text']) && is_array($_POST['attachment_text'])){
			$metas = $_POST['attachment_text'];
			foreach ($metas as $id => $meta) {
				wp_update_post( array('ID' => $id, 'post_excerpt' => $meta ) );
			}
		}
	}
	function validate_main_settings( $post_id ) {
		if( FALSE === $this->check_security($post_id) )
			return $post_id;

		$this->validate_media_attachments($post_id);

		//
		// file_put_contents(DT_MULTIMEDIA_PATH.'/debug.log', print_r($_POST, 1) );
		//

		if(!isset($_POST['type']))
			return $post_id;

		update_post_meta( $post_id, '_'.DT_PREFIX.'type', $_POST['type'] );
		
		if(isset($_POST['show_title']))
			update_post_meta( $post_id, '_'.DT_PREFIX.'show_title', $_POST['show_title'] );

		$this->get_options($post_id, $_POST['type'], $update=true );
		$this->get_side_options($post_id, $_POST['type'], $update=true );
	}
}

function wrap_shortcode() {
	global $post, $wp_meta_boxes;
	if($post->post_type !== DT_MULTIMEDIA_MAIN_TYPE)
		return;

	$is_show = get_post_meta( $post->ID, '_'.DT_PREFIX.'show_title', true ) ? ' checked': '';
	echo "<div class='wrap-sc'>";
	echo "<label> Show title <input type='checkbox' name='show_title' value='on'".$is_show."> </label>";
	echo 'Вставьте шорткод в любую запись Вашего сайта';
	echo '<input id="shortcode" readonly="readonly" type="text" value="[mblock id='.$post->ID.']">';
	echo "</div>";
}
add_action( 'edit_form_after_title', 'wrap_shortcode' );

function custom_excerpt_meta_box(){
	global $post; ?>
	<label class="screen-reader-text" for="excerpt"><?php _e('Excerpt') ?></label>
	<textarea rows="1" cols="40" name="excerpt" tabindex="6" id="excerpt"><?php echo $post->post_excerpt; // textarea_escaped ?></textarea>
	<?php
}