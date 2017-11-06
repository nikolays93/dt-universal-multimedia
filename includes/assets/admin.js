jQuery(document).ready(function($) {
    var frame, media_ids = [],
        metaBox = $('#attachments.postbox'),
        addImgLink = metaBox.find('#upload-images'),
        imgsContainer = metaBox.find('#mediablocks-media');

    function baseName(str)
    {
        var base = new String(str).substring(str.lastIndexOf('/') + 1); 
        if(base.lastIndexOf(".") != -1)
            base = base.substring(0, base.lastIndexOf("."));
        return base;
    }

    // Получить ссылку на изображение
    function getSize( image ){
        if( 'medium' in image.sizes ) return image.sizes.medium;
        if( 'large' in image.sizes ) return image.sizes.large;
        if( 'thumbnail' in image.sizes ) return image.sizes.thumbnail;
        if( 'full' in image.sizes ) return image.sizes.full;
        return false;
    }

    // Добавить событие удалить изображение при нажатии на крестик
    function setRemoveEvents(){
        $('.remove', imgsContainer).on('click', function(e){
            $(this).closest('.attachment').remove();
        });
    }

    imgsContainer.sortable();
    setRemoveEvents();

    // add atachments
    addImgLink.on( 'click', function( event ){
        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( frame ) { frame.open(); return; }

        frame = wp.media({
            multiple: true,
            library: {type: 'image'}
        });

        frame.on( 'select', function() {
            var attachments = frame.state().get('selection');
            var image = attachments.toJSON();//attachments.models;

            for(var i = 0; i < attachments.length; i++){
                var size = getSize( image[i] );

                imgsContainer.append( Mustache.render(document.getElementById('attachment-tpl').innerHTML, {
                    'filename': baseName( image[i].filename ),
                    'attachment_id': image[i].id,
                    'attachment_class': 'attachment-preview ' + size.orientation
                        + ' type-'+image[i].type
                        + ' subtype-'+image[i].subtype,
                    'attachment_url': size.url,
                    'attachment_excerpt_value': image[i].caption,
                    'attachment_content_value': image[i].description,
                    'attachment_link_value': '',
                    'attachment_blank_label': 'Target blank',
                    'attachment_blank_checked': '',
                }) );

                setRemoveEvents();
            }
        });

        frame.open();
    });

    //
    // Изменить стиль отображения attachments
    //
    $('#detail_view').on('click', function(e){
        e.preventDefault();

        if( localStorage.getItem('detail_mode') ){
            localStorage.removeItem('detail_mode');
            imgsContainer.removeClass('detail');
        }
        else {
            localStorage.setItem('detail_mode', '1' );
            imgsContainer.addClass('detail');
        }
    });

    if( localStorage.getItem('detail_mode') ) {
        imgsContainer.addClass('detail');
    }


    //
    // Выбор типа (показывать нужный под тип при выборе)
    //
    $('#grid_type').on('change', function() {
        var val = $(this).val();

        $('[name="mtypes[lib_type]"]').each(function(){
            if( $(this).hasClass(val) ){
                $(this).removeClass('hidden').removeAttr('disabled').fadeIn().addClass('activated');
            }
            else {
                $(this).hide().addClass('hidden').attr('disabled', 'disable').removeClass('activated');
            }
        });
    } ).trigger('change');

    /**
    * Ajax (обновляет параметры при выборе типа)
    */
    $('#grid_type, #lib_type').on('change', function(){
        $('#json_options.postbox .inside, #grid_options.postbox .inside').css('opacity', '0.6');

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'mblock_options',
                nonce: mb_settings.nonce,
                grid_type: $('#grid_type').val(),
                lib_type: $('#lib_type.activated').val()
            },
            success: function(response){
                var $response = $(response);

                $('#json_options.postbox .inside').html( $response[0] );
                $('#grid_options.postbox .inside').html( $response[1] );
                $('#json_options.postbox .inside, #grid_options.postbox .inside').css('opacity', '1');
            }
        }).fail(function() {
            console.log('Ajax: Fatal Error.');
        });
    });
});
