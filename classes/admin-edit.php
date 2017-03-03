<?php
class isAdminView extends DT_MediaBlocks
{
	protected $settings;
	protected $post_id;

	function __construct(){	
		$this->preview_hooks();

		add_action( 'load-post.php', array( $this, 'preview_hooks' ) );
		add_action( 'load-post-new.php', array( $this, 'preview_hooks' ) );
	}

	function preview_hooks(){
		add_action( 'add_meta_boxes', array( $this, 'preview_boxes' ) );
		add_action( 'save_post', array( $this, 'validate_main_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'preview_assets' ) );
		add_action( 'admin_menu' , array( $this, 'remove_default_divs' ) );
		add_action( 'edit_form_after_title', array( $this, 'wrap_shortcode' ) );

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

	function get_admin_post_id(){
		if(isset($_GET['post']))
			return intval($_GET['post']);

		global $post;
		if(isset($post->ID))
			return $post->ID;

		return false;
	}
	/**
	  * $active array name > value
	  */ 
	function form_render($render=false, $active=array()){
		if(!$render){ // false or null
			echo 'Настроек не обнаружено.';
			return false;
		}

		if( isset($render['type']) )
			$render = array($render);
		
		$is_table = ( isset($render[0]['label']) || isset($render[0]['desc']) ) ? true : false;
		if($is_table)
			echo '<table valign="top" class="table"><tbody>';
		
		foreach ($render as $input) {
			$entry = '';
			$label = (isset($input['label'])) ? _($input['label']) : false;
			$desc = (isset($input['desc'])) ? _($input['desc']) : false;
			unset($input['label']);
			unset($input['desc']);

			if( !isset($input['name']) )
				$input['name'] = $input['id'];

			$is_default = isset($input['default']) ? true : false;
			switch ($input['type']) {
				case 'checkbox':
					$checked = ( $is_default || isset($active[$input['name']]) ) ? 'checked' : '';
					unset($input['default']);

					$input_html = "
					<input {$input['name']} type='hidden' value=''>
					<input ";
					foreach ($input as $attr => $val) {
						$attr = esc_attr($attr);
						$val  = esc_attr($val);
						$input_html .= " {$attr}='{$val}'";
					}
					$input_html .= "{$checked} value='on'>";
					break;

				case 'select':
					$options = $input['options'];
					if( isset($active[$input['name']]) ){
						$entry = $active[$input['name']];
					}
					elseif($is_default){
						$entry = $input['default'];
						unset($input['default']);
					}
					unset($input['options']);

					$input_html = "<select";
					foreach ($input as $attr => $val) {
						$attr = esc_attr($attr);
						$val  = esc_attr($val);
						$input_html .= " {$attr}='{$val}'";
					}
					$input_html .= ">";
					foreach ($options as $value => $option) {
						$active_str = ($entry == $value) ? " selected": "";
						$input_html .= "<option value='{$value}'{$active_str}>{$option}</option>";
					}
					$input_html .= "</select>";
					break;

				default:
					if( isset($active[$input['name']]) ){
						$entry = $active[$input['name']];
					}
					elseif($is_default){
						$input['placeholder'] = $input['default'];
						unset($input['default']);
					}

					$input_html = "<input";
					foreach ($input as $attr => $val) {
						$attr = esc_attr($attr);
						$val  = esc_attr($val);
						$input_html .= " {$attr}='{$val}'";
					}
					$input_html .= " value='{$entry}'>";
					break;
			}

			if(!$is_table){
				echo $input_html;
			}
			else {
				echo "\n<tr id='{$input['id']}'><td class='name'>{$label}</td>";
				echo "<td>";
				echo $input_html;
				if($desc)
					echo "<div class='description'>{$desc}</div>";
				echo "</td></tr>";
			}
		} // endforeach
		if($is_table)
			echo '</tbody></table>';
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
		$ids = get_post_meta( $post->ID, '_'.DTM_PREFIX.'media_imgs', true );
		$ids = explode(',', esc_attr($ids));
		$style = $this->get_post_meta($post->ID, 'detail_view') ? 'list' : 'tile';
		echo '<div class="attachments '.$style.'" id="dt-media">';
		if($ids[0] != ''){
			foreach ($ids as $id) { ?>
			<div class="attachment" data-id="<?php echo $id; ?>">
				<?php
					$meta = wp_get_attachment_metadata( $id );
					$attrs = ( $meta['image_meta']['orientation'] == 1 ) ? array('class' => 'portrait') : array();
				?>
				<div class="item">
				<?php
					// wp_get_attachment_metadata( $id )
					$attachment = get_post( $id );
				?>
					<span class="dashicons dashicons-no remove"></span>
					<div class="crop">
						<?php
							echo wp_get_attachment_image($id, 'medium', null, $attrs);
						?>
					</div>
					<input type="text" name="attachment_text[<?php echo $id; ?>]" value="<?php echo $attachment->post_excerpt; ?>">
					<textarea name="" id="" cols="90" rows="4"><?php echo $attachment->post_content; ?></textarea>
					<!-- <input type="text"> -->
					<input type="hidden" id="dt-ids" name="attachment_id[]" value="<?php echo $id; ?>">
				</div>
			</div>
			<?php
			} // foreach
		} // if
		echo '</div>';
	}

	function wrap_shortcode() {
		global $post, $wp_meta_boxes;
		if($post->post_type !== DT_MULTIMEDIA_MAIN_TYPE)
			return;

		$show_input = array(
			'id' => 'show_title',
			'type' => 'checkbox',
			);

		echo "<div class='wrap-sc'>";
		echo "<label> "._('Show title');
		$value = array( $show_input['id'] => $this->get_post_meta($post->ID, $show_input['id']) );
		$this->form_render($show_input, $value );
		echo "</label>";

		echo 'Вставьте шорткод в любую запись Вашего сайта';
		echo '<input id="shortcode" readonly="readonly" type="text" value=\'[mblock id="'.$post->ID.'"]\'>';
		echo "</div>";
	}

	function preview_media_edit_callback( $post ) {
		wp_enqueue_media();

		//_dump( get_post_meta( $post->ID ) );
		//wp_nonce_field( 'dp_addImages_nonce', 'wp_developer_page_nonce' );
		?>
		<div class="dt-media">
			<div class="hide-if-no-js wp-media-buttons">
			<?php
				//str
				$is_detail_view = $this->get_post_meta($post->ID, 'detail_view');
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
				$type = $this->get_media_type( $post->ID );
				$this->form_render( $this->get_settings('general'), array(
					'main_type' =>$type[0],
					'type'      =>$type[1]
					));
			?>
			<?php $this->get_attachments($post); ?>
			<div class="clear"></div>
		</div>
		<script type="text/javascript">
			jQuery(function($){
				$('#shortcode').on('click', function(){ $(this).select(); });
				
				$('#main_type').on('change', function(){
					var val = $(this).val();
					$('[name=type]').each(function(){
						if( $(this).hasClass(val) ){
							$(this).slideDown();
							$(this).removeAttr('disabled');
						} else {
							$(this).hide();
							$(this).attr('disabled', 'disable');
						}
					});
				});
				$('#main_type').change();

				$('#detail_view').on('click', function(e){
					e.preventDefault();
					// toggleValue
					if($('[name="detail_view"]').val() == 'on')
						$('[name="detail_view"]').val('')
					else 
						$('[name="detail_view"]').val('on')

					$(this).find('span').each(function(){
						$(this).toggleClass('hidden');
					});
					$('#dt-media').toggleClass('tile');
					$('#dt-media').toggleClass('list');
				});
			});
		</script>
		<?php
	}

	// main settings
	function preview_media_main_settings_callback( $post ) {
		$type_name = 'type';
		$type = $this->get_post_meta($post->ID, $type_name);
		$this->form_render( $this->get_settings($type), $this->set_post_meta($post->ID, $type.'_opt') );
	}
	// side settings
	function preview_media_side_settings_callback( $post ){
		$type_name = 'main_type';
		$type = $this->get_post_meta($post->ID, $type_name); // carousel
		$this->form_render( $this->get_settings($type), $this->set_post_meta($post->ID, $type.'_opt') );
	}

	/**
	 * Block : Validation
	 */
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
		update_post_meta( $post_id, '_'.DTM_PREFIX.'media_imgs', $attachment_ids );

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

		// _dtm_media_imgs
		$this->validate_media_attachments($post_id);

		$this->set_post_meta($post_id, 'show_title', true);
		$this->set_post_meta($post_id, 'detail_view', true);

		if(!isset($_POST['type']) || !isset($_POST['main_type']))
			return $post_id;

		$this->set_post_meta($post_id, 'main_type', true);
		$this->set_post_meta($post_id, 'type', true);
		
		$this->set_meta_settings($post_id, $_POST['main_type'], $_POST );
		$this->set_meta_settings($post_id, $_POST['type'], $_POST );
	}
}

function custom_excerpt_meta_box(){
	global $post; ?>
	<label class="screen-reader-text" for="excerpt"><?php _e('Excerpt') ?></label>
	<textarea rows="1" cols="40" name="excerpt" tabindex="6" id="excerpt"><?php echo $post->post_excerpt; // textarea_escaped ?></textarea>
	<?php
}