jQuery(document).ready(function($) {
    function double_script() {
    }

    $.each(mediablock, function(index, value) {
        var block = $('#mediablock-' + index);
        var items = $('.item', block);

        setTimeout(function(){
            if( value.atts.grid_type != 'gallery' ) {
                block.removeClass('row');
                $('.item', block).each(function(index, el) {
                    $(this).attr('class', 'item');
                });
            }

            $.each(value.props, function(index, val) {
                if( val == 'on' )
                    value.props[ index ] = true;
                else if( val == 'off' )
                    value.props[ index ] = false;

                var int = parseInt(val);
                if( ! isNaN( int ) )
                    value.props[ index ] = int;
            });

            if( value.settings.not_initialize ) {
                return false;
            }

            console.log(value.settings);
            if( value.atts.grid_type != 'gallery' ) {
                if( value.atts.grid_type != 'sync-slider' ) {
                    if( value.props ) {
                        console.log(value.props);
                        eval('block.' + value.settings.init + '(' + JSON.stringify(value.props) + ');' );
                    }
                    else {
                         eval('block.' + value.settings.init + '();' );
                    }
                }
                else {
                    // double_script();
                }
            }
        }, 500);

        // if( value.settings.lazyLoad || value.settings.masonry ) {
        //     if( value.settings.masonry ) {
        //         block.masonry({ itemSelector: '#mediablock-' + index + ' > .item' });
        //     }

        //     if( value.settings.lazyLoad && value.settings.masonry ) {
        //         block.imagesLoaded(function() {
        //             if( value.settings.lazyLoad ) {
        //                 $('img', block).addClass('not-loaded');
        //                 $('img.not-loaded', block).lazyload({
        //                     effect: "fadeIn",
        //                     load: function() {
        //                         $(this).removeClass("not-loaded");
        //                         $(this).addClass("loaded");

        //                         if( typeof block.masonry !== "undefined" ) {
        //                             block.masonry.masonry("reload");
        //                         }
        //                     }
        //                 });
        //                 $('img.not-loaded', block).trigger("scroll");
        //             }
        //         });
        //     }
        // }
    });
});