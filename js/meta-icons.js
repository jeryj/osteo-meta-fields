/**
 * meta-icons.js
 *
 * Icon select grid for meta-items and meta-fields
 *
 */

jQuery( document ).ready( function( $ ) {
    // Select Icon
    $(document).on('click', '.icon-grid li', function(){
        if($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            $(this).parent().siblings('.icon-dropdown').val('');
        } else {
            $(this).siblings().removeClass('selected');
            $(this).addClass('selected');
            the_icon = $('i', this).attr('class');
            // set the value on the dropdown
            $(this).parent().siblings('.icon-dropdown').val(the_icon);
        }

    });

    // On Page Load Select Icon
    $('.icon-selector').each(function(){
        the_icon = $('.icon-dropdown', this).val();
        if(the_icon !== 'none') {
            $('.icon-grid li i', this).each(function(){
                if($(this).hasClass(the_icon)) {
                    $(this).parent().addClass('selected');
                }
            });
        } else {
            // set the default one, if you want to
            // $('i.icon-history_record', this).parent().trigger('click');
        }
    });
});
