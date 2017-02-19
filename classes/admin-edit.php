<?php
class isAdminView extends DT_MultiMedia
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
			<select name="type" class="button">
				<option value="owl-carousel">Карусель</option>
				<option value="#">Слайдер</option>
				<option value="#">Галерея</option>
			</select>
			
			<?php $this->get_attachments($post); ?>
			<div class="clear"></div>
		</div>
		<?php
	}

	protected function render_input($name, $type, $value, $placeholder, $options, $target, $is_show){
		//if( isset($_GET['post']) && $val = get_post_meta( $_GET['post'], DT_PREFIX.$name, true ) )
			//$value = $val;

		$name = ( $name ) ? "name='".$name."'": '';
		$target = ( $target ) ? "data-target='".$target."'" : '';
		if($target != '')
			$target .= ($is_show) ? " data-action='show'" : " data-action='hide'";

		$placeholder = ( $placeholder ) ? "placeholder='".$placeholder."'" : '';

		switch ($type) {
			case 'select':
				echo "<select {$name} {$target}>";
				foreach ($options as $id => $option) {
					echo "<option value='{$id}'>{$option}</option>";
				}
				echo "</select>";
				break;

			case 'checkbox':
				$checked = ($value) ? 'checked ' : '';
				echo "<input {$name} {$target} type='{$type}' {$checked}value='on'>";
				break;

			default:
				echo "<input {$name} {$target} type='{$type}' {$placeholder} value='".$value."'>";
				break;
		}
	}
	protected function render_settings($settings){
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
			
			if(isset($_GET['post']))
				$post_id = intval($_GET['post']);

			$values = $this->get_options($post_id, $this->get_media_type($post_id), false );
			if( isset($values[$id]) )
				$default = $values[$id];

			echo "\n<tr id='{$id}'><td>".$value['name']."</td><td>";
			echo $this->render_input($id, $value['type'], $default, $placeholder, $options, $target, $is_show);
			echo "<div class='description'>{$value['desc']}</div></td></tr>";
		}
		echo '</tbody></table>';
		_d($settings);
	}
	function preview_media_main_settings_callback( $post ) {
		$path = DT_MULTIMEDIA_PATH . '/settings/owl-carousel.php';
		if ( is_readable( $path ) )
			require_once( $path );

		if( function_exists('get_dt_multimedia_settings') ){
			$this->render_settings( get_dt_multimedia_settings() );
		}
		else {
			echo "Файл настроек поврежден!";
		}
	}

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

		$attachment_ids = $_POST['attachment_id'];
		$attachment_ids = implode(',', $attachment_ids);
		update_post_meta( $post_id, DT_PREFIX.'media_imgs', $attachment_ids );

		if(isset($_POST['attachment_text']) && is_array($_POST['attachment_text'])){
			$metas = $_POST['attachment_text'];
			foreach ($metas as $id => $meta) {
				wp_update_post( array('ID' => $id, 'post_excerpt' => $meta ) );
			}
		}
		if(!isset($_POST['type']))
			return $post_id;

		$this->get_options($post_id, $_POST['type'], $update=true );
	}
}