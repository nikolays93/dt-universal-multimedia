<?php

add_filter( 'queries_forms', '_queries_forms', 10, 1 );
function _queries_forms( $form ){
	$public_args = array('public' => 1, 'show_ui' => 1);
	$qry = MB\WPForm::active('_'.DT_MediaBlocks::PREFIX.'query', false, true, true);

	$public_tax = get_taxonomies( $public_args );
	$tax_inc_type = get_object_taxonomies( isset($qry['type']) ? $qry['type'] : 'post' );

	$types = array('all' => __('all taxanomies') );
	foreach ($public_tax as $tax_key => $tax_name) {
		if(in_array($tax_name, $tax_inc_type))
			$types[$tax_key] = $tax_name;
	}

	$form = array(
		array(
			'id' => 'type',
			'label' => 'Тип запроса',
			'type' => 'select',
			'options' => array(
				'Типы записей' => get_post_types( $public_args ),
				//'Таксаномии'   => $public_tax,
				),
			),
		array(
			'id' => 'tax',
			'label' => 'Таксаномия',
			'type' => 'select',
			'options' => $types,
			),
		);

	return $form;
}
add_filter( 'queries_forms', '_default_queries_forms', 15, 1 );
function _default_queries_forms( $form ){
	$default_form = array(
		array(
			'id' => 'best',
			'label' => 'Сначала бестселлеры',
			'type' => 'checkbox',
			),
		array(
			'id' => 'sort',
			'label' => 'Сортировка',
			'type' => 'select',
			'options' => array(
				'ASC'  => 'ASC',
				'DESC' => 'DESC',
				),
			),
		array(
			'id' => 'qty',
			'label' => 'Количество',
			'type' => 'number',
			'default' => '5',
			),
		);

	return array_merge($form, $default_form);
}

add_filter( 'render_terms', '_terms_fieldgroup', 10 );
function _terms_fieldgroup(){ ?>
	<?php
	$qry = MB\WPForm::active('_'.DT_MediaBlocks::PREFIX.'query', false, true, true);

	$args = array(
		'taxonomy'     => isset($qry['tax']) ? $qry['tax'] : 'category',
		'hide_empty'   => false,
		'hierarchical' => false,
		);

	$terms = get_terms( $args );
	$terms_arr = array();
	if( !is_wp_error( $terms ) ){
		foreach ($terms as $term) {
			$terms_arr[] = array(
				'type'  => 'checkbox',
				'id'    => 'term_' . $term->term_id,
				'name'  => 'term][',
				'label' => $term->name,
				'value' => $term->term_id,
				'check_active' => 'value',
				);
		}
	}

	return $terms_arr;
}

add_action('before_admin_wrap_attachments', 'get_admin_pre_wrap_attachments', 10, 1 );
function get_admin_pre_wrap_attachments( $post ){
	$qry = MB\WPForm::active('_'.DT_MediaBlocks::PREFIX.'query', false, true, true);
	$enable = isset($qry['enable']) ? $qry['enable'] : false;
	$display = $enable ? 'block' : 'none';
		?>
			<div id="dt-media-query" style="padding: 5px 15px;display: <?php echo $display; ?>;">
				<div class="col-2" style="width: 50%; float: left;">
					<?php
					MB\WPForm::render(
						apply_filters( 'queries_forms', array() ),
						MB\WPForm::active('_'.DT_MediaBlocks::PREFIX.'query', false, true, true),
						true,
						array(
							'item_wrap'  => array('<span>', '</span>'),
							'admin_page' => 'query',
							)
						);
					?>
				</div>
				<div class="col-2" style="width: 50%; float: left;">

					<p>Термины:</p>
					<?php
					if( $render_terms = apply_filters( 'render_terms', array() ) )
						MB\WPForm::render( $render_terms,
							MB\WPForm::active('_'.DT_MediaBlocks::PREFIX.'query', 'term', true, true),
							false,
							array('admin_page' => 'query', 'clear_value' => false)

							);
					else
						echo "Нет терминов.";
					?>
				</div>
			</div>
			<div class="clear"></div>
			<h3>Превью:</h3>
		<?php
}
add_action( 'mb_media_buttons', 'add_select_query', 20, 1 );
function add_select_query( $post ){
	$qry = MB\WPForm::active('_'.DT_MediaBlocks::PREFIX.'query', false, true, true);
	$enable = isset($qry['enable']) ? $qry['enable'] : false;
	?>
	<div>
		Использовать запрос к записям <input type="checkbox" id="query_select" name="query[enable]" value="on" <?php checked($enable, 'on', 1 ); ?>>
	</div>
	<?php
}
