jQuery(document).ready(function($) {
    var frame, media_ids = [],
        metaBox = $('#attachments.postbox'),
        addImgLink = metaBox.find('#upload-images'),
        imgsContainer = metaBox.find('#dt-media');

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
    function addAttachment(id, img, fields){
        imgsContainer.append(
            '<div class="attachment">\n'
          + '  <div class="item">\n'
          + '    <span class="dashicons dashicons-no remove"></span>\n'
          + '    <div class="crop">' + img + '</div>\n' + fields
          + '  </div>\n'
          + '  <input type="hidden" id="dt-ids" name="attachment_id[]"  value="'+id+'">\n'
          + '</div>\n'
           );

        setRemoveEvents();
    }

    //
    // Получить ссылку на изображение
    //
    function getSizeUrl( image ){
        if( 'medium' in image.sizes ) return image.sizes.medium.url;
        if( 'large' in image.sizes ) return image.sizes.large.url;
        if( 'thumbnail' in image.sizes ) return image.sizes.thumbnail.url;
        if( 'full' in image.sizes ) return image.sizes.full.url;
        return false;
    }

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
                var imgHTML = jQuery('<img/>', {
                    src: getSizeUrl(image[i]),
                    alt: image[i].alt,
                    class: image[i].orientation
                })[0].outerHTML;

                var titleHTML = $('<input>').attr({
                    type: 'text',
                    class: 'item-excerpt',
                    name: 'attachment_excerpt['+image[i].id+']',
                    value: image[i].caption
                })[0].outerHTML;

                var descHTML = $('<textarea></textarea>').attr({
                    class: 'item-content',
                    name: 'attachment_content['+image[i].id+']',
                    cols: 90,
                    rows: 4
                }).text( image[i].description )[0].outerHTML;

                var linkHTML = $('<input>').attr({
                    type: 'text',
                    class: 'item-link',
                    name: 'attachment_link['+image[i].id+']',
                    placeholder: '[link post="4"]',
                    value: ''
                })[0].outerHTML;

                addAttachment(image[i].id, imgHTML, titleHTML + descHTML + linkHTML );
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

        if( localStorage.getItem('mb_view') ){
            localStorage.removeItem('mb_view');
            $(this).closest('.dt-media').removeClass('list');
        }
        else {
            localStorage.setItem('mb_view', '1' );
            $(this).closest('.dt-media').addClass('list');
        }
    });

    if( localStorage.getItem('mb_view') ) {
        $('#detail_view').closest('.dt-media').addClass('list');
    }


    //
    // Выбор типа (показывать нужный под тип при выборе)
    //
    $('#main_type').on('change', function(){
        var val = $(this).val();
        $('[name=type]').each(function(){
            if( $(this).hasClass(val) ){
                $(this).slideDown().addClass('activated').removeAttr('disabled');
            }
            else {
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
            }
        }).fail(function() {
            console.log('Ajax: Fatal Error.');
        });
    });

    // $('#query_select').on('change', function(event) {
    //     $('#dt-media-query').slideToggle();
    // });
});
