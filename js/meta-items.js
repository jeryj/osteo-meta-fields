/**
 * meta-items.js
 *
 * Adding / Removing Items to a Meta Box
 *
 * Instructions:
 * Use cpt/properties.php or page-templates/about.php as an example
 */

jQuery( document ).ready( function( $ ) {


    $(document).on('click', '.add-item i', function(){
        // strategy
        //  - Clone the current one (Because that's what's being added numerically)
        //  - increase all the following item numbers up by 1
        // Check to see if the last add item button was clicked
        if($(this).parent().hasClass('last')) {
            // clone the one that is at position 0
            item_parent = $(this).parent().parent();
            lastClicked = true;
        } else {
            item_parent = $(this).parent().parent().parent();
            lastClicked = false;
        }

        // get all the items in the whole thang
        items = $('.item', item_parent);

        // This is the number of the cloned item
        if(lastClicked === true) {
            // remember which number got cloned (for replacing numbers later)
            clone_source = '0';
            // set the number for the clone by counting all the items and adding one since we're adding it to the end
            clone_eq = items.prevAll().length + 1;
            // the clone. we're just cloning the very first one and changing the 0's out.
            new_item = items.eq(0).clone();
        } else {
            // set the number for the clone by counting all the items before this one
            clone_eq = $(this).parent().parent('.item').prevAll().length;
            // so we know where we came from
            clickedItem = $('.item', item_parent).eq(clone_eq);
            // set it as a string for replacing later
            clone_source = clone_eq.toString();
            // the clone
            new_item = $(this).parent().parent().clone();
        }

        console.log('clone_eq = '+clone_eq);
        console.log('clone_source = '+clone_source);

        // clear all the values of the cloned item
        new_item.each(function() {
            clearValues(clone_source, new_item);
            // if we're using an image uploader or wp-editor, we need to change that divs id number as well
            replaceItemDivNumbers(clone_source, clone_eq, new_item);
        });

        if(lastClicked === true) {
            // insert stuff into the dom
            // has to be AFTER we're done modifying everything from the clone
            $(this).parent().before(new_item);
        } else {
            // insert it before the item we clicked on. this is now the item:eq(clone_eq)
            clickedItem.before(new_item);
        }

        // Here it is!
        the_clone = $('.item:eq('+clone_eq+')', item_parent);
        // make it come in nice n easy
        the_clone.hide();
        the_clone.fadeIn('slow');
        // OR more visually, the_clone = $(this).parent('.item').prev('.item');

        // incease all the numbers of the one following the cloned one
        if(lastClicked === false) {
            increaseFollowing(clone_eq, the_clone);
        }

    });


    function clearValues(clone_source, the_clone) {
        $('input, textarea, select', the_clone).each(function(){
            if($(this).prop('type') === 'button') {
                // don't clear the value for buttons (like the select image button for image uploader)
                // breaks out of this loop and continues on to the next one
                return true;
            } else {
                if($(this).hasClass('icon-dropdown')) {
                    // if the icon grid is there, remove the selected icon
                    $('.icon-grid li', the_clone).removeClass('selected');
                }

                $(this).val('');
                // if a checkbox
                $(this).removeAttr('checked');
            }
            // replace the name with the new number
            new_array_name = $(this).attr('name').replace(clone_source, clone_eq);

            // set the name
            $(this).attr('name', new_array_name);

        });
    }

    function increaseFollowing(clone_eq, the_clone) {
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

            if($(this).children().hasClass('image-uploader')) {
                replaceImageDivs(i, new_item_num, this);
            }
            // if we're using a wp_editor, we need to change that divs value as well
            if($(this).children().hasClass('wp-editor-wrap')) {
                replaceEditorDivs(i, new_item_num, this);
            }

            i++;
        });
    }


    function replaceItemDivNumbers(clone_source, clone_eq, the_clone) {
        clone_eq_string = clone_eq.toString();
        //replace the item div
        $(the_clone).removeClass('item-'+clone_source).addClass('item-'+clone_eq_string);

        if($(the_clone).children().hasClass('image-uploader')) {
            replaceImageDivs(clone_source, clone_eq_string, the_clone);
        }
        // if we're using a wp_editor, we need to change that divs value as well
        if($(the_clone).children().hasClass('wp-editor-wrap')) {
            replaceEditorDivs(clone_source, clone_eq_string, the_clone);
        }
    }

    function replaceImageDivs(clone_source, clone_eq_string, the_clone) {
        $('.image-uploader', the_clone).each(function() {
            imageID = $(this).attr('id');
            newImageID = imageID.replace(clone_source, clone_eq_string);
            $(this).attr('id', newImageID);
            // remove the thumbs
            $('#thumbs .thumb', this).remove();
        });
    }

    function replaceEditorDivs(clone_source, clone_eq_string, the_clone) {
        editorID = $('.wp-editor-wrap', the_clone).attr('id');
        newEditorID = editorID.replace(clone_source, clone_eq_string);
        $('.wp-editor-wrap', the_clone).attr('id', newEditorID);
    }



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
