/**
 * meta-items.js
 *
 * Adding / Removing Items to a Meta Box
 *
 * Instructions:
 * Use cpt/properties.php or page-templates/about.php as an example
 */

jQuery( document ).ready( function( $ ) {
    // Timeline
    $('.add-item.last i').on('click', function(){
        // which add-item button did we click?
        // we need to find out the parent so we can do multiple meta-item areas on one page
        item_parent = $(this).parent().parent();
        items = $('.item', item_parent);

        item_count = items.length;

        item_prevAll = items.prevAll().length;
        // clone the html
        new_item = items.eq(0).clone();
        // clear the values
        // Set the IDs
        // Change the array #
        $(new_item).each(function(){
            $(this).removeClass('item-0').addClass('item-'+item_count);
            $('input, textarea, select', this).each(function(){
                if($(this).prop('type') === 'button') {
                    // don't clear the value for buttons (like the select image button for image uploader)
                    // breaks out of this loop and continues on to the next one
                    return true;
                } else {
                    if($(this).hasClass('icon-dropdown')) {
                        // if the icon grid is there, remove the selected icon
                        $('.icon-grid li', new_item).removeClass('selected');
                    }
                    $(this).val('');
                }
                new_array_name = $(this).attr('name').replace('0', item_count);

                // set the name
                $(this).attr('name', new_array_name);

            });

            // if we're using an image uploader, we need to change that divs value as well
            if($(this).children().hasClass('image-uploader')) {
                $('.image-uploader', this).attr('id', 'item-image-'+item_count);
                $('#thumbs img', this).remove();
            }
            // if we're using a wp_editor, we need to change that divs value as well
            if($(this).children().hasClass('wp-editor-wrap')) {
                editorID = $('.wp-editor-wrap', this).attr('id');
                clone_eq_string = item_count.toString();
                newEditorID = editorID.replace('0', clone_eq_string);
                $('.wp-editor-wrap', this).attr('id', newEditorID);
            }
        });

        // insert stuff into the dom
        // has to be AFTER we're done modifying everything from the clone
        $(this).parent().before(new_item);

        the_clone = item_prevAll + 1;
        // If we're on a timeline element

        items.eq(the_clone).hide();
        items.eq(the_clone).fadeIn('slow');

    });

    $(document).on('click', '.item .add-item i', function(){
        // strategy
        //  - Clone the current one (Because that's what's being added numerically)
        //  - increase all the following item numbers up by 1
        item_parent = $(this).parent().parent().parent();
        items = $('.item', item_parent);

        // This is the number of the cloned item
        clone_eq = $(this).parent().parent('.item').prevAll().length;
        clickedItem = $('.item', item_parent).eq(clone_eq);
        console.log(clone_eq);
        // clone the clicked on item
        new_item = $(this).parent().parent().clone();

        // insert it before the item we clicked on. this is now the item:eq(clone_eq)
        clickedItem.before(new_item);
        // Here it is!
        the_clone = $('.item:eq('+clone_eq+')', item_parent);
        // make it come in nice n easy
        the_clone.hide();
        the_clone.fadeIn('slow');
        // OR more visually, the_clone = $(this).parent('.item').prev('.item');

        // clear all the values of the cloned item
        the_clone.each(function() {
            $('input, textarea, select', this).each(function(){
                if($(this).prop('type') === 'button') {
                    // don't clear the value for buttons (like the select image button for image uploader)
                    // breaks out of this loop and continues on to the next one
                    return true;
                } else {
                    if($(this).hasClass('icon-dropdown')) {
                        // if the icon grid is there, remove the selected icon
                        $('.icon-grid li', new_item).removeClass('selected');
                    }

                    $(this).val('');

                }
            });

            // if we're using an image uploader, we need to change that divs value as well
            if($(this).children().hasClass('image-uploader')) {
                $('.image-uploader', this).attr('id', 'item-image-'+clone_eq);
                $('#thumbs img', this).remove();
            }
            // if we're using a wp_editor, we need to change that divs value as well
            if($(this).children().hasClass('wp-editor-wrap')) {
                editorID = $('.wp-editor-wrap', this).attr('id');
                clone_eq_old = clone_eq - 1;
                clone_eq_string = clone_eq.toString();
                clone_eq_old_string = clone_eq_old.toString();
                newEditorID = editorID.replace(clone_eq_old_string, clone_eq_string);
                $('.wp-editor-wrap', this).attr('id', newEditorID);
            }
        });

        // let's set an i so it doesn't feel weird
        i = clone_eq;
        // get all .item items AFTER the cloned item and increase their values by 1
        the_clone.nextAll('.item').each( function() {
            new_item_num = i+1;
            $(this).removeClass('item-'+i).addClass('item-'+new_item_num);
            $('input, textarea, select', this).each(function(){
                if($(this).prop('type') === 'button') {
                    // don't clear the value for buttons (like the select image button for image uploader)
                    // breaks out of this loop and continues on to the next one
                    return true;
                } else {
                    new_array_name = $(this).attr('name').replace(i, new_item_num);
                    $(this).attr('name', new_array_name);

                    // if the icon grid is there, remove the selected class
                    $('.icon-grid li', this).addClass('selected');
                }
            });

            i++;
        });

    });

    // hover effect for displaying remove button
    $(document).on("mouseenter", ".item", function(){
            $('.remove-item', this).fadeIn();
    });

    $(document).on("mouseleave", ".item", function(){
            $('.remove-item', this).fadeOut();
    });

    // Remove Event
    $(document).on('click', '.remove-item i', function(){
        // fade it out
        $(this).parent().parent().fadeOut("slow", function(){
            // remove it once the animation is complete
            $(this).remove();
        });
    });

});
