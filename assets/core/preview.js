jQuery(function($){
  var frame, media_ids = [],
      metaBox = $('#preview_media_edit.postbox'), // Your meta box id here
      addImgLink = metaBox.find('#upload-images'),
      imgsContainer = metaBox.find('#dt-media.tile');
  
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
      	var url = image[i].url;
      	if( typeof(image[i].sizes.medium) !== undefined )
      		var url = image[i].sizes.medium.url;

      	console.log( image[i] );
      	
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

  // data-toggle
  function setDataToggle(jq){
    var toggle = jq.attr('data-target');
    if(toggle != undefined){
      toggle = toggle.split(', ');
      toggle.forEach(function(item, i){ $('#'+item+' td').slideToggle(); });
      // if( jq.is(':checked') ){
      //   toggle.forEach(function(item, i){ $('#'+item+' td').slideUp(); });
      // }
      // else {
      //   toggle.forEach(function(item, i){ $('#'+item+' td').slideDown(); });
      // }
    }
  }
  

  $('form#post').on('submit', function(){
    $('input[type="checkbox"]', this).each(function(i){
      if( !$(this).is(':checked') )
        $(this).val('').attr("checked", true);
    });
  });

  jQuery(document).ready(function($) {
    $('#preview_media_main_settings input[type="checkbox"]').on('change', function(){
      setDataToggle($(this));
    });
    $('#preview_media_main_settings input[type="checkbox"]').each(function(i){
      if( $(this).is(':checked') && $(this).attr('data-action') == 'hide')
        setDataToggle($(this));
        
      if( ! $(this).is(':checked') && $(this).attr('data-action') == 'show' )
        setDataToggle($(this));
    });

    $('input[type=\'text\'], input[type=\'number\'], textarea').on('focus', function(){
      if($(this).val() == ''){
        $(this).val($(this).attr('placeholder'));
        $(this).select();
      }
    });

  });

});