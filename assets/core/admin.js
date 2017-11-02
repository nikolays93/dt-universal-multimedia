jQuery(document).ready(function($) {
    var frame, media_ids = [],
        metaBox = $('#attachments.postbox'),
        addImgLink = metaBox.find('#upload-images'),
        imgsContainer = metaBox.find('#dt-media');

    function baseName(str)
    {
        var base = new String(str).substring(str.lastIndexOf('/') + 1); 
        if(base.lastIndexOf(".") != -1)
            base = base.substring(0, base.lastIndexOf("."));
        return base;
    }

    //
    // Получить ссылку на изображение
    //
    function getSize( image ){
        if( 'medium' in image.sizes ) return image.sizes.medium;
        if( 'large' in image.sizes ) return image.sizes.large;
        if( 'thumbnail' in image.sizes ) return image.sizes.thumbnail;
        if( 'full' in image.sizes ) return image.sizes.full;
        return false;
    }

    //
    // Добавить событие удалить изображение при нажатии на крестик
    //
    function setRemoveEvents(){
        $('.remove', imgsContainer).on('click', function(e){
            $(this).closest('.attachment').remove();
        });
    }

    //
    // Шаблон для вставки изображения
    //
    function addAttachment( image ) {
        console.log( image );
        var size = getSize(image);
        var file = baseName( image.filename );

        var caption = $('<input>').attr({
            type: 'text',
            class: 'item-excerpt',
            name: 'attachment_excerpt['+image.id+']',
            value: image.caption
        })[0].outerHTML;

        var descHTML = $('<textarea></textarea>').attr({
            class: 'item-content',
            name: 'attachment_content['+image.id+']',
            placeholder: 'The some contents..',
            cols: 90,
            rows: 4
        }).text( image.description )[0].outerHTML;

        var linkHTML = $('<input>').attr({
            type: 'text',
            class: 'item-link',
            name: 'attachment_link['+image.id+']',
            placeholder: '[link post="4"]',
            value: ''
        })[0].outerHTML;

        imgsContainer.append(
        '<li tabindex="0" aria-label="'+ file +'" data-id="'+ image.id +'" class="attachment">\n'
      + '    <div class="thumbnail-wrap">'
      + '        <div class="attachment-preview '+ size.orientation
      + '     type-'+image.type+' subtype-'+image.subtype+'">\n'
      + '            <div class="thumbnail">'
      + '                <div class="centered">'
      + '                    <img src="'+ size.url +'" alt="" />'
      + '                </div>'
      + '            </div>'
      + '        </div>'
      + '        <button type="button" class="check remove" tabindex="-1">'
      + '            <span class="media-modal-icon"></span>'
      + '        </button>'
      + caption
      + '        <input type="hidden" id="attachments" name="attachment_id[]" value="'+ image.id +'">'
      + '    </div>'
      + descHTML
      + '<div class="item-link-wrap">'
      + linkHTML
      + '    <label class="open-blank">Target blank <input type="checkbox"></label>'
      + '</div>'
      + '</li>' );

        setRemoveEvents();
    }

    // function addAttachment(id, img, fields){
        // (
        //     '<div class="attachment">\n'
        //   + '  <div class="item">\n'
        //   + '    <span class="dashicons dashicons-no remove"></span>\n'
        //   + '    <div class="crop">' + img + '</div>\n' + fields
        //   + '  </div>\n'
        //   + '  <input type="hidden" id="dt-ids" name="attachment_id[]"  value="'+id+'">\n'
        //   + '</div>\n'
        //    );
    // }

    imgsContainer.sortable();
    setRemoveEvents();

    //
    // add atachments
    //
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
                // addAttachment(image[i].id, imgHTML, titleHTML + descHTML + linkHTML );
                addAttachment( image[i] );
            }
        });

        // Finally, open the modal on click
        frame.open();
    });

    //
    //  Placeholder to value
    //
    $('input[type=\'text\'], input[type=\'number\'], textarea').on('focus', function(){
        if($(this).val() == ''){
            $(this).val($(this).attr('placeholder'));
            $(this).select();
        }
    });

    //
    // Выделить содержимое поля с шорткодом при нажатии для легкого копирования
    //
    $('#shortcode').on('click', function(){ $(this).select(); });

    //
    // Изменить стиль отображения attachments
    //
    $('#detail_view').on('click', function(e){
        e.preventDefault();

        if( localStorage.getItem('detail_mode') ){
            localStorage.removeItem('detail_mode');
            $('#dt-media').removeClass('detail');
        }
        else {
            localStorage.setItem('detail_mode', '1' );
            $('#dt-media').addClass('detail');
        }
    });

    if( localStorage.getItem('detail_mode') ) {
        $('#dt-media').addClass('detail');
    }


    //
    // Выбор типа (показывать нужный под тип при выборе)
    //
    $('#main_type').on('change', function(){
        var val = $(this).val();
        $('[name=type]').each(function(){
            if( $(this).hasClass(val) ){
                console.log('1');
                $(this).slideDown().addClass('activated').removeAttr('disabled');
            }
            else {
                console.log('0');
                $(this).hide().removeClass('activated').attr('disabled', 'disable');
            }
        });
    } ).trigger('change');

    //
    // Показывать поле ввода при выборе своего стиля (для указания стиля)
    //
    $('select#block_template').on('change', function(){
        var row = 'tr#style_path > td';

        if( $(this).val() == 'custom' ){
            $(row).slideDown();
        } else {
            $(row).slideUp();
        }
    }).trigger('change');

    /**
    * Ajax (обновляет параметры при выборе типа)
    */
    $('#main_type, #type').on('change', function(){
        $('#main_settings.postbox .inside, #side_settings.postbox .inside').css('opacity', '0.6');
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'main_settings',
                nonce: mb_settings.nonce,
                main_type: $('#main_type').val(),
                type: $('#type.activated').val()
            },
            success: function(response){
                var $response = $(response);

                $('#main_settings.postbox .inside').html( $response[0] );
                $('#side_settings.postbox .inside').html( $response[1] );
                $('#main_settings.postbox .inside, #side_settings.postbox .inside').css('opacity', '1');
            }
        }).fail(function() {
            console.log('Ajax: Fatal Error.');
        });
    });

    // $('#query_select').on('change', function(event) {
    //     $('#dt-media-query').slideToggle();
    // });
});
