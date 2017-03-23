jQuery(function($){
  var frame, media_ids = [],
      metaBox = $('#attachments.postbox'), // Your meta box id here
      addImgLink = metaBox.find('#upload-images'),
      imgsContainer = metaBox.find('#dt-media');
  
  function setRemoveTrigger(){
		jQuery('.remove', imgsContainer).on('click', function(e){
			jQuery(this).closest('.attachment').remove();
		});
	}
	function addAttachment(id, img, fields){
		// imgsContainer.append('<tr><td class="thumbnail">'+img+'</td><td>'+fields+'</td></tr>');
		var exists = [];
		$('#dt-ids').each(function(i){
			exists.push( $(this).val() );
		});

		imgsContainer.append(
			'<div class="attachment">\n'
			+'	<div class="item">\n'
			+'		<span class="dashicons dashicons-no remove"></span>\n'
			+'		<div class="crop">' + img + '</div>\n' + fields
			+'	</div>\n'
			+'</div>\n'
			);
		setRemoveTrigger();
	}

	imgsContainer.sortable();

  function getSizeUrl( image ){
    if( 'thumbnail' in image.sizes )
      return image.sizes.thumbnail.url;

    if( 'medium' in image.sizes )
      return image.sizes.medium.url;

    if( 'large' in image.sizes )
      return image.sizes.large.url;

    if( 'full' in image.sizes )
      return image.sizes.full.url;
  }

  // add atachments
  addImgLink.on( 'click', function( event ){
    event.preventDefault();
    // If the media frame already exists, reopen it.
    if ( frame ) {
      frame.open();
      return;
    }
    
    frame = wp.media({
      multiple: true,
      //frame: 'post',
      library: {type: 'image'}
    });
    
    frame.on( 'select', function() {
      var attachments = frame.state().get('selection');
      var image = attachments.toJSON();//attachments.models;

      for(var i = 0; i < attachments.length; i++){
        var url = getSizeUrl(image[i]);

      	var imgHTML    = '<img src="'+url+'" alt="'+image[i].alt+'" class="'+image[i].orientation+'" />';
      	var titleHTML  = '<input type="text" name="attachment_text['+image[i].id+']" value="'+image[i].caption+'">';
      	var descHTML   = '<textarea>'+image[i].description+'</textarea>';
      	var hiddenHTML = '<input type="hidden" id="dt-ids" name="attachment_id[]"  value="'+image[i].id+'">';

      	addAttachment(image[i].id, imgHTML, titleHTML + hiddenHTML ); //descHTML +
      	// var sizes = images[i].changed.sizes;

      	// var img = images[i].changed;
      	// var image_caption = images[i].changed.caption;
      	// var image_title = images[i].changed.title;
      	// console.log(img);

      	// $('#uploaded-images').prepend("<input type='text' name='uploadedImages[]' value='"+img.url+"'>");
      }
    	// attachments.map(function(attachment) {
    	// 	attachment = attachment.toJSON();
    	// 	media_ids.push(attachment.id);
    	// });

    	
    });

    // Finally, open the modal on click
    frame.open();
  });
  setRemoveTrigger();

  //
  //  Placeholder to value
  //
  $('input[type=\'text\'], input[type=\'number\'], textarea').on('focus', function(){
    if($(this).val() == ''){
      $(this).val($(this).attr('placeholder'));
      $(this).select();
    }
  });

  $('#shortcode').on('click', function(){ $(this).select(); });
  
  /**
   * Изменить стиль отображения attachments 
   */
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

  /**
   * Выбор типа
   */
  $('#main_type').on('change', function(){
    var val = $(this).val();
    $('[name=type]').each(function(){
      if( $(this).hasClass(val) ){
        $(this).slideDown()
          .addClass('activated')
          .removeAttr('disabled');
      } else {
        $(this).hide()
          .removeClass('activated')
          .attr('disabled', 'disable');
      }
    });
  } ).trigger('change');

  /**
   * Скрыть под настройки data-hide, data-view, custom стиль
   */
  function doDataAction(target, action='toggle'){
    if(target != undefined){
      target = target.split(', ');
      target.forEach(function(item, i){
        if(action == 'toggle' )
          $('#'+item+' td, #'+item+' th').slideToggle();
        else if(action == 'show')
          $('#'+item+' td, #'+item+' th').slideDown();
        else if(action == 'hide')
          $('#'+item+' td, #'+item+' th').slideUp();
      });
    }
  }
  function checkHiddens(){
    var data = ["data-show", "data-hide"];
    data.forEach(function(attr, i){
      $('['+attr+']').on('change', function(){
        doDataAction( $(this).attr(attr) );
      });
    });

    $('[data-show]').each(function(){
      if(! $(this).is(':checked') ){
        doDataAction( $(this).attr('data-show') );
      }
    });
    $('[data-hide]').each(function(){
      if( $(this).is(':checked') ){
        doDataAction( $(this).attr('data-hide') );
      }
    });
  }
  checkHiddens();
  
  $('select#block_template').on('change', function(){
    var row = 'tr#style_path > td';
    if( $(this).val() == 'custom' ){
      $(row).slideDown();
    } else {
      $(row).slideUp();
    }
  }).trigger('change');
  

  /**
   * Ajax
   */
  $('#main_type, #type').on('change', function(){
    $.ajax({
      type: 'POST', url: settings.url, data: {
        action: 'main_settings',
        nonce: settings.nonce,
        main_type: $('#main_type').val(),
        type: $('#type.activated').val()
      },
      success: function(response){
        var $response = $(response);
        // console.log(response);

        $('#main_settings.postbox .inside').html( $response[0] );
        $('#side_settings.postbox .inside').html( $response[1] );
        checkHiddens();
      }
    }).fail(function() { console.log('Ajax error!'); });
  });
});
