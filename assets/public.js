jQuery(document).ready(function($) {
    function double_script() {
    }

    $.each(mediablock, function(index, value) {
        var block = $('#mediablock-' + index);
        var items = $('.item', block);

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

        console.log(value.props);

        if( value.settings.not_initialize ) {
            return false;
        }

        if( value.atts.grid_type != 'gallery' ) {
            if( value.atts.grid_type != 'sync-slider' ){
                eval('block.' + value.settings.init + '(' + JSON.stringify(value.props) + ');' );
            }
            else {
                // double_script();
            }
        }

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